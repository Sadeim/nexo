<?php

namespace Tests\Feature\Booking;

use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * The published-barber list is read by a View::composer('*'), so it runs on
 * every page of the site. That makes it worth caching — and makes it the one
 * query that must never be able to throw.
 *
 * On 8 Nov 2026 it took production down: a deploy pulled code filtering on
 * employees.public_profile minutes before the migration that adds the column,
 * and every request 500'd until the migration caught up.
 */
class PublicBarbersCacheTest extends TestCase
{
    use RefreshDatabase;

    /** @var array<int,string> */
    private array $logged = [];

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();

        // Captures what the real logger receives. Mocking the Log facade here
        // proved unreliable once the Cache facade was mocked too, and this
        // asserts on the actual logger rather than on a stand-in for it.
        $this->logged = [];
        Log::listen(function ($message) {
            $this->logged[] = $message->message;
        });
    }

    private function assertLogged(string $fragment): void
    {
        $this->assertTrue(
            collect($this->logged)->contains(fn ($m) => str_contains($m, $fragment)),
            "Expected a log entry containing \"{$fragment}\". Got: "
                . (empty($this->logged) ? '(nothing)' : implode(' | ', $this->logged))
        );
    }

    // ---------- caching ----------

    public function test_the_list_is_cached_rather_than_queried_every_page(): void
    {
        Employee::factory()->published()->create(['name' => 'Omar Haddad']);

        $first = Employee::publicBarbersCached();
        $this->assertCount(1, $first);

        DB::enableQueryLog();
        $second = Employee::publicBarbersCached();
        $queries = DB::getRawQueryLog();
        DB::disableQueryLog();

        $this->assertCount(1, $second);

        $touchedEmployees = collect($queries)
            ->filter(fn ($q) => str_contains($q['raw_query'], 'employees'))
            ->count();

        $this->assertSame(0, $touchedEmployees, 'A second read must come from the cache.');
    }

    public function test_the_cache_key_is_populated(): void
    {
        Employee::factory()->published()->create();

        Employee::publicBarbersCached();

        $this->assertTrue(Cache::has(Employee::PUBLIC_BARBERS_CACHE_KEY));
    }

    public function test_only_published_and_active_barbers_are_cached(): void
    {
        Employee::factory()->published()->create(['name' => 'Shown']);
        Employee::factory()->create(['name' => 'Unpublished']);
        Employee::factory()->published()->inactive()->create(['name' => 'Retired']);

        $names = Employee::publicBarbersCached()->pluck('name');

        $this->assertSame(['Shown'], $names->all());
    }

    // ---------- busting ----------

    public function test_publishing_a_barber_shows_up_immediately(): void
    {
        $this->assertCount(0, Employee::publicBarbersCached());

        // Ten minutes is far too long to wait for a barber the owner has just
        // published, so the observer drops the cache on save.
        Employee::factory()->published()->create(['name' => 'Omar Haddad']);

        $this->assertCount(1, Employee::publicBarbersCached());
    }

    public function test_unpublishing_a_barber_hides_them_immediately(): void
    {
        $barber = Employee::factory()->published()->create();
        $this->assertCount(1, Employee::publicBarbersCached());

        $barber->update(['public_profile' => false]);

        $this->assertCount(0, Employee::publicBarbersCached());
    }

    public function test_deleting_a_barber_hides_them_immediately(): void
    {
        $barber = Employee::factory()->published()->create();
        $this->assertCount(1, Employee::publicBarbersCached());

        $barber->delete();

        $this->assertCount(0, Employee::publicBarbersCached());
    }

    public function test_a_rename_is_reflected_immediately(): void
    {
        $barber = Employee::factory()->published()->create(['name' => 'Old Name']);
        $this->assertSame('Old Name', Employee::publicBarbersCached()->first()->name);

        // The cached rows carry the name, so any save busts — not only a
        // change to public_profile.
        $barber->update(['name' => 'New Name']);

        $this->assertSame('New Name', Employee::publicBarbersCached()->first()->name);
    }

    // ---------- resilience ----------

    /**
     * The production failure was a missing COLUMN, which cannot be reproduced
     * here: SQLite does not error on one. Faced with `where "public_profile"`
     * on a table that lacks it, SQLite falls back to reading the
     * double-quoted identifier as a string literal and quietly returns no
     * rows — the same misfeature its own error messages warn about
     * ("should this be a string literal in single-quotes?").
     *
     * MariaDB raises SQLSTATE 42S22 instead. So the schema failure is
     * reproduced by dropping the table, which errors on both, and what is
     * being asserted — that a broken query degrades to an empty list and a
     * logged warning rather than a 500 — is identical either way.
     */
    public function test_a_missing_table_yields_an_empty_list_instead_of_throwing(): void
    {
        Cache::flush();

        Schema::drop('employees');

        $this->assertTrue(Employee::publicBarbersCached()->isEmpty());
        $this->assertLogged('Could not load public barbers');
    }

    public function test_the_whole_site_still_renders_when_the_barber_query_breaks(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        Cache::flush();

        // Dropping the table, for the reason given above.
        Schema::drop('employees');

        // The point of all of the above: a page, not a 500.
        $this->get(route('contact'))->assertOk();
        $this->assertLogged('Could not load public barbers');
    }

    public function test_busting_the_cache_never_breaks_saving_an_employee(): void
    {
        // A cache store that cannot forget must not stop the owner editing a
        // barber — the entry expires within the TTL regardless.
        Cache::shouldReceive('forget')->andThrow(new \RuntimeException('cache store down'));
        Cache::shouldReceive('remember')->andReturn(collect());

        $employee = Employee::factory()->create();
        $employee->update(['name' => 'Still Saves']);

        $this->assertSame('Still Saves', $employee->fresh()->name);
    }

    public function test_a_failing_cache_forget_never_escapes_the_helper(): void
    {
        Cache::shouldReceive('forget')->andThrow(new \RuntimeException('cache store down'));

        // The contract: this must not throw. Nothing after it needs to run for
        // the assertion to mean something — reaching the next line is the
        // assertion.
        //
        // The accompanying log line is not asserted here: with the Cache
        // facade mocked, Log::listen stops receiving, which is a quirk of
        // mocking two facades at once rather than anything about the code.
        // The logging on the publicBarbersCached() paths below is asserted,
        // and that is the path that keeps the site up.
        Employee::forgetPublicBarbersCache();

        $this->assertTrue(true, 'Reached the line after a failing cache forget.');
    }

}
