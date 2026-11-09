<?php

namespace Tests\Feature\Booking;

use App\Models\Employee;
use App\Models\Service;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The public barber surfaces: the homepage section and the profile page.
 *
 * Seeds the CMS fixtures because the homepage renders sliders, settings and
 * sections — the same reason the homepage smoke test does.
 */
class BarberPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    // ---------- the public scope ----------

    public function test_a_published_barber_has_a_page(): void
    {
        $barber = Employee::factory()->published()->create(['name' => 'Omar Haddad']);

        $this->get(route('barbers.show', $barber->slug))
            ->assertOk()
            ->assertSee('Omar Haddad')
            ->assertSee('Master Barber');
    }

    public function test_an_unpublished_barber_is_a_404(): void
    {
        $barber = Employee::factory()->create(); // active, not published

        $this->get(route('barbers.show', $barber->slug))->assertNotFound();
    }

    public function test_an_inactive_barber_is_a_404(): void
    {
        $barber = Employee::factory()->published()->inactive()->create();

        $this->get(route('barbers.show', $barber->slug))->assertNotFound();
    }

    public function test_an_unknown_slug_is_a_404(): void
    {
        $this->get(route('barbers.show', 'nobody-here'))->assertNotFound();
    }

    public function test_unpublished_barbers_are_hidden_from_the_homepage(): void
    {
        Employee::factory()->published()->create(['name' => 'Shown Barber']);
        Employee::factory()->create(['name' => 'Hidden Barber']);
        Employee::factory()->published()->inactive()->create(['name' => 'Retired Barber']);

        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertSee('OUR BARBERS')
            ->assertSee('Shown Barber')
            ->assertDontSee('Hidden Barber')
            ->assertDontSee('Retired Barber');
    }

    public function test_the_section_is_absent_entirely_when_nobody_is_published(): void
    {
        Employee::factory()->count(3)->create();

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('OUR BARBERS');
    }

    // ---------- page contents ----------

    public function test_the_page_lists_specialties_with_prices(): void
    {
        $cut   = Service::create(['name' => 'Skin Fade', 'price' => 35]);
        $beard = Service::create(['name' => 'Beard Trim', 'price' => 20]);
        Service::create(['name' => 'Not A Specialty', 'price' => 99]);

        $barber = Employee::factory()->published()->create([
            'specialties' => [$cut->id, $beard->id],
        ]);

        $this->get(route('barbers.show', $barber->slug))
            ->assertOk()
            ->assertSee('Skin Fade')
            ->assertSee('$35.00')
            ->assertSee('Beard Trim')
            ->assertDontSee('Not A Specialty');
    }

    public function test_the_page_shows_shop_hours_from_the_service(): void
    {
        $barber = Employee::factory()->published()->create();

        $this->get(route('barbers.show', $barber->slug))
            ->assertOk()
            ->assertSee('SHOP HOURS')
            ->assertSee('9:00 AM')
            // Last slot starts 7:40 PM and runs 20 minutes.
            ->assertSee('8:00 PM');
    }

    public function test_the_page_sets_its_own_title_and_meta(): void
    {
        $barber = Employee::factory()->published()->create([
            'name' => 'Sami Nasser',
            'bio'  => 'Fifteen years of scissor work and a very steady hand.',
        ]);

        $response = $this->get(route('barbers.show', $barber->slug));

        $response->assertOk()
            ->assertSee('<title>Sami Nasser — Barber at Nexo Barbershop</title>', false)
            ->assertSee('Fifteen years of scissor work', false)
            ->assertSee('og:image', false);
    }

    public function test_a_barber_without_a_photo_falls_back_to_initials(): void
    {
        $barber = Employee::factory()->published()->create(['name' => 'Omar Haddad', 'photo_path' => null]);

        $this->get(route('barbers.show', $barber->slug))
            ->assertOk()
            ->assertSee('OH');
    }

    public function test_the_bio_is_escaped_not_rendered(): void
    {
        $barber = Employee::factory()->published()->create([
            'bio' => 'Careful with <script>alert(1)</script> markup.',
        ]);

        $response = $this->get(route('barbers.show', $barber->slug));

        $response->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;', false);
    }

    public function test_the_instagram_handle_renders_as_a_link(): void
    {
        $barber = Employee::factory()->published()->create(['instagram' => 'omarcuts']);

        $this->get(route('barbers.show', $barber->slug))
            ->assertOk()
            ->assertSee('https://instagram.com/omarcuts', false);
    }

    // ---------- the booking modal ----------

    public function test_the_modal_offers_every_published_barber_on_any_page(): void
    {
        Employee::factory()->published()->create(['name' => 'Omar Haddad']);
        Employee::factory()->create(['name' => 'Hidden Barber']);

        // The modal lives in the footer, so it renders here too.
        $response = $this->get(route('contact'));

        $response->assertOk()
            ->assertSee('Any available barber')
            ->assertSee('Omar Haddad')
            ->assertDontSee('Hidden Barber');
    }

    public function test_a_barber_slug_in_the_query_preselects_and_locks_the_select(): void
    {
        $barber = Employee::factory()->published()->create(['name' => 'Omar Haddad']);

        $response = $this->get(route('home', ['barber' => $barber->slug]));

        $response->assertOk()
            ->assertSee('lockedBarberId: ' . $barber->id, false)
            ->assertSee('Omar Haddad');
    }

    public function test_an_unknown_barber_slug_in_the_query_is_ignored(): void
    {
        Employee::factory()->published()->create();

        $this->get(route('home', ['barber' => 'does-not-exist']))
            ->assertOk()
            ->assertSee('lockedBarberId: null', false);
    }

    public function test_an_unpublished_barber_slug_cannot_be_preselected(): void
    {
        $hidden = Employee::factory()->create();

        $this->get(route('home', ['barber' => $hidden->slug]))
            ->assertOk()
            ->assertSee('lockedBarberId: null', false);
    }
}
