<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Services\BookingSlotService;

/**
 * Public barber profiles.
 */
class BarberController extends Controller
{
    /**
     * One barber's page.
     *
     * Resolved through publicBarbers(), so an unpublished or deactivated
     * barber is a 404 rather than a page that merely looks empty — the URL
     * stays guessable after someone is un-published, and a stale link should
     * not confirm who used to work here.
     */
    public function show(string $slug)
    {
        $barber = Employee::publicBarbers()->where('slug', $slug)->firstOrFail();

        return view('frontend.barber', [
            'barber'      => $barber,
            'specialties' => $barber->specialtyServices(),
            // Read from the service rather than restated here, so the page
            // cannot drift out of step with what the booking form accepts.
            'shopHours'   => app(BookingSlotService::class)->shopHours(),
        ]);
    }
}
