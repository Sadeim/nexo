<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Booking;
use App\Models\Employee;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;
use Tests\Concerns\SeedsAccessControl;
use Tests\TestCase;

/**
 * Assigning a barber to an existing booking from the dashboard, over HTTP.
 *
 * The write goes through the same guarded path as a customer booking, so the
 * interesting case is the refusal: the owner picks a barber who is already
 * taken at that time and must be told so with the booking left alone.
 */
class AssignBarberTest extends TestCase
{
    use RefreshDatabase;
    use SeedsAccessControl;

    private const WEDNESDAY = '2026-11-11';

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedAccessControl();
        $this->travelTo('2026-11-10 08:00:00');
    }

    private function owner(): Admin
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = Admin::create([
            'name'     => 'Owner',
            'email'    => 'owner_' . uniqid() . '@nexo.local',
            'password' => Hash::make('Password123'),
            'status'   => 1,
        ]);

        $admin->assignRole(Role::where('name', 'admin')->where('guard_name', 'admin')->firstOrFail());

        return $admin;
    }

    private function assign(Booking $booking, $barberId)
    {
        return $this->actingAs($this->owner(), 'admin')
            ->postJson(route('admin.bookings.assign_barber', $booking->id), [
                'barber_id' => $barberId,
            ]);
    }

    public function test_an_unassigned_booking_can_be_given_to_a_free_barber(): void
    {
        $barber  = Employee::factory()->published()->create();
        $booking = Booking::factory()->at(self::WEDNESDAY, '10:00:00')->create();

        $this->assign($booking, $barber->id)->assertOk();

        $fresh = $booking->fresh();
        $this->assertSame($barber->id, $fresh->barber_id);
        $this->assertSame(self::WEDNESDAY . '|10:00:00|' . $barber->id, $fresh->slot_key);
    }

    public function test_assigning_a_barber_who_is_already_booked_is_refused(): void
    {
        $barber = Employee::factory()->published()->create();
        Employee::factory()->published()->create(); // capacity 2 — the shop is not full

        Booking::factory()->at(self::WEDNESDAY, '10:00:00')->create(['barber_id' => $barber->id]);
        $booking = Booking::factory()->at(self::WEDNESDAY, '10:00:00')->create();

        $response = $this->assign($booking, $barber->id);

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'Barber already booked at this time']);

        $fresh = $booking->fresh();
        $this->assertNull($fresh->barber_id, 'A refused assignment must not change the row.');
        $this->assertNull($fresh->slot_key);
    }

    public function test_a_booking_can_be_handed_back_to_any_available_barber(): void
    {
        $barber  = Employee::factory()->published()->create();
        $booking = Booking::factory()->at(self::WEDNESDAY, '10:00:00')->create(['barber_id' => $barber->id]);

        $this->assign($booking, null)->assertOk();

        $fresh = $booking->fresh();
        $this->assertNull($fresh->barber_id);
        $this->assertNull($fresh->slot_key, 'Releasing a barber must free their slot.');
    }

    public function test_reassigning_between_two_barbers_moves_the_slot(): void
    {
        $omar = Employee::factory()->published()->create();
        $sami = Employee::factory()->published()->create();

        $booking = Booking::factory()->at(self::WEDNESDAY, '10:00:00')->create(['barber_id' => $omar->id]);

        $this->assign($booking, $sami->id)->assertOk();

        $this->assertSame(
            self::WEDNESDAY . '|10:00:00|' . $sami->id,
            $booking->fresh()->slot_key
        );

        // Omar's slot is free again, so another booking may claim it.
        $other = Booking::factory()->at(self::WEDNESDAY, '10:00:00')->create();
        $this->assign($other, $omar->id)->assertOk();
    }

    public function test_an_unpublished_barber_cannot_be_assigned(): void
    {
        $hidden  = Employee::factory()->create();
        $booking = Booking::factory()->at(self::WEDNESDAY, '10:00:00')->create();

        $this->assign($booking, $hidden->id)
            ->assertStatus(422)
            ->assertJsonValidationErrors('barber_id');
    }

    public function test_a_cashier_cannot_assign_barbers(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $cashier = Admin::create([
            'name'     => 'Cashier',
            'email'    => 'cashier_' . uniqid() . '@nexo.local',
            'password' => Hash::make('Password123'),
            'status'   => 1,
        ]);
        $cashier->assignRole(Role::where('name', 'cashier')->where('guard_name', 'admin')->firstOrFail());

        $barber  = Employee::factory()->published()->create();
        $booking = Booking::factory()->at(self::WEDNESDAY, '10:00:00')->create();

        $this->actingAs($cashier, 'admin')
            ->postJson(route('admin.bookings.assign_barber', $booking->id), ['barber_id' => $barber->id])
            ->assertStatus(403);

        $this->assertNull($booking->fresh()->barber_id);
    }
}
