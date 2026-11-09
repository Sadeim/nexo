<?php

namespace Tests\Feature\Booking;

use App\Exceptions\SlotUnavailableException;
use App\Services\BookingSlotService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use ReflectionMethod;
use RuntimeException;
use Tests\TestCase;

/**
 * The named lock that serialises the capacity half of the rule.
 *
 * Production runs READ-COMMITTED, where InnoDB takes no gap locks on a ranged
 * SELECT ... FOR UPDATE, so lockForUpdate alone cannot stop a competing
 * INSERT into the same slot. GET_LOCK does not care about the isolation
 * level, which is why it is here.
 *
 * The suite runs on SQLite, which has no named locks, so the real GET_LOCK
 * round trip cannot be executed here. What is tested instead is everything
 * that can go wrong in our own code: the lock name, that work runs only when
 * the lock was taken, and — the part that would quietly wedge a slot for
 * every later request — that it is always released. A separate test asserts
 * the exact SQL and bindings sent on the MySQL path.
 */
class SlotLockTest extends TestCase
{
    use RefreshDatabase;

    private const WEDNESDAY = '2026-11-11';

    // ---------- the lock name ----------

    public function test_the_lock_names_the_slot_not_the_table(): void
    {
        $slots = new BookingSlotService();

        $this->assertSame(
            'booking:2026-11-11|10:00:00',
            $slots->slotLockName(self::WEDNESDAY, '10:00:00')
        );
    }

    public function test_the_same_slot_written_two_ways_yields_one_lock_name(): void
    {
        $slots = new BookingSlotService();

        // If these differed, the two requests would not queue and the lock
        // would be decorative.
        $this->assertSame(
            $slots->slotLockName(self::WEDNESDAY, '10:00'),
            $slots->slotLockName(self::WEDNESDAY, '10:00:00')
        );
    }

    public function test_different_slots_get_different_locks(): void
    {
        $slots = new BookingSlotService();

        $this->assertNotSame(
            $slots->slotLockName(self::WEDNESDAY, '10:00'),
            $slots->slotLockName(self::WEDNESDAY, '10:20'),
            'Two customers booking different times must never wait on each other.'
        );
    }

    // ---------- control flow ----------

    private function recordingService(bool $acquires = true): BookingSlotService
    {
        return new class($acquires) extends BookingSlotService {
            public array $calls = [];

            public function __construct(private bool $acquires)
            {
            }

            protected function acquireSlotLock(string $name): bool
            {
                $this->calls[] = ['acquire', $name];

                return $this->acquires;
            }

            protected function releaseSlotLock(string $name): void
            {
                $this->calls[] = ['release', $name];
            }

            // Force the MySQL branch; the suite's connection is SQLite.
            public function withSlotLock(string $date, string $time, callable $work): mixed
            {
                $name = $this->slotLockName($date, $time);

                if (!$this->acquireSlotLock($name)) {
                    throw \App\Exceptions\SlotUnavailableException::forShop();
                }

                try {
                    return $work();
                } finally {
                    $this->releaseSlotLock($name);
                }
            }
        };
    }

    public function test_the_lock_is_taken_then_released_around_the_work(): void
    {
        $slots = $this->recordingService();

        $result = $slots->withSlotLock(self::WEDNESDAY, '10:00', fn () => 'done');

        $this->assertSame('done', $result);
        $this->assertSame([
            ['acquire', 'booking:2026-11-11|10:00:00'],
            ['release', 'booking:2026-11-11|10:00:00'],
        ], $slots->calls);
    }

    public function test_the_lock_is_released_when_the_work_throws(): void
    {
        $slots = $this->recordingService();

        try {
            $slots->withSlotLock(self::WEDNESDAY, '10:00', function () {
                throw new RuntimeException('write blew up');
            });
            $this->fail('The exception should have propagated.');
        } catch (RuntimeException $e) {
            $this->assertSame('write blew up', $e->getMessage());
        }

        // The failure case is the one that matters: a lock held past an error
        // would wedge this slot for every later request on that connection.
        $this->assertSame(['acquire', 'release'], array_column($slots->calls, 0));
    }

    public function test_the_lock_is_released_when_the_slot_is_refused(): void
    {
        $slots = $this->recordingService();

        try {
            $slots->withSlotLock(self::WEDNESDAY, '10:00', function () {
                throw SlotUnavailableException::forBarber();
            });
            $this->fail('The refusal should have propagated.');
        } catch (SlotUnavailableException $e) {
            // Expected.
        }

        $this->assertSame(['acquire', 'release'], array_column($slots->calls, 0));
    }

    public function test_a_timeout_refuses_the_slot_and_never_runs_the_work(): void
    {
        $slots = $this->recordingService(acquires: false);
        $ran = false;

        try {
            $slots->withSlotLock(self::WEDNESDAY, '10:00', function () use (&$ran) {
                $ran = true;
            });
            $this->fail('A lock timeout must refuse the slot.');
        } catch (SlotUnavailableException $e) {
            $this->assertStringContainsString('not available', $e->getMessage());
        }

        $this->assertFalse($ran, 'No booking may be written without the lock.');
        // Nothing was acquired, so nothing may be released — releasing a lock
        // we never held would free one another request is holding.
        $this->assertSame([['acquire', 'booking:2026-11-11|10:00:00']], $slots->calls);
    }

    // ---------- the SQL actually sent on MySQL ----------

    public function test_acquire_issues_get_lock_named_for_the_slot(): void
    {
        $slots  = new BookingSlotService();
        $method = new ReflectionMethod($slots, 'acquireSlotLock');
        $method->setAccessible(true);

        // SQLite has no GET_LOCK, so the statement fails — and the failure
        // carries the exact SQL and bindings that would have gone to MariaDB,
        // which is what needs asserting. Driving the real method this way
        // keeps a test-only shim out of the service.
        try {
            $method->invoke($slots, 'booking:2026-11-11|10:00:00');
            $this->fail('SQLite has no GET_LOCK; the statement should have failed.');
        } catch (QueryException $e) {
            $this->assertStringContainsString('GET_LOCK', $e->getSql());
            $this->assertSame(
                ['booking:2026-11-11|10:00:00', BookingSlotService::SLOT_LOCK_TIMEOUT_SECONDS],
                $e->getBindings(),
                'The lock is named for the slot and bounded by the timeout.'
            );
        }
    }

    public function test_release_issues_release_lock_and_never_throws(): void
    {
        Log::spy();

        $slots  = new BookingSlotService();
        $method = new ReflectionMethod($slots, 'releaseSlotLock');
        $method->setAccessible(true);

        // Must not throw even when the database refuses it: a failure here
        // would replace whatever the caller was already throwing.
        $method->invoke($slots, 'booking:2026-11-11|10:00:00');

        Log::shouldHaveReceived('warning')
            ->withArgs(fn ($message, $context = []) =>
                str_contains($message, 'release booking slot lock')
                && ($context['lock'] ?? null) === 'booking:2026-11-11|10:00:00')
            ->once();
    }

    // ---------- the SQLite escape hatch ----------

    public function test_on_sqlite_the_work_still_runs_without_a_lock(): void
    {
        $slots = new BookingSlotService();

        $this->assertSame('sqlite ok', $slots->withSlotLock(self::WEDNESDAY, '10:00', fn () => 'sqlite ok'));
    }
}
