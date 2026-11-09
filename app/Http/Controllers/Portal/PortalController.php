<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * The barbers' portal screens.
 *
 * Every query starts from the signed-in employee and goes through
 * Booking::forEmployee(). No action here reads an employee id, a slug or a
 * booking id from the request, so there is nothing for one barber to tamper
 * with to reach another's day.
 */
class PortalController extends Controller
{
    private const PER_PAGE = 15;

    public function today()
    {
        $me = Auth::guard('employee')->user();
        $today = Carbon::today();

        $bookings = $this->onDay(Booking::forEmployee($me->id), $today)
            ->with('service')
            ->orderBy('time')
            ->get();

        /*
         * Bookings nobody is assigned to yet. Shown as a bare count, not a
         * list: it tells a barber the shop has unclaimed work today without
         * turning the portal into a place where they can see, or take,
         * someone else's customers.
         */
        $unassigned = $this->onDay(Booking::query(), $today)
            ->whereNull('barber_id')
            ->where('status', '!=', Booking::STATUS_CANCELLED)
            ->count();

        return view('portal.today', [
            'bookings' => $bookings,
            'unassigned' => $unassigned,
            'today' => $today,
        ]);
    }

    public function bookings(Request $request)
    {
        $me = Auth::guard('employee')->user();

        // The tab is the only input, and anything but "past" means upcoming.
        $tab = $request->query('tab') === 'past' ? 'past' : 'upcoming';
        $today = Carbon::today()->toDateString();

        $query = Booking::forEmployee($me->id)->with('service');

        if ($tab === 'past') {
            $query->where('date', '<', $today)
                ->orderByDesc('date')
                ->orderByDesc('time');
        } else {
            $query->where('date', '>=', $today)
                ->orderBy('date')
                ->orderBy('time');
        }

        return view('portal.bookings', [
            'bookings' => $query->paginate(self::PER_PAGE)->withQueryString(),
            'tab' => $tab,
        ]);
    }

    /**
     * The service worker, served from /me/sw.js.
     *
     * A worker's scope cannot rise above the path it is served from, so the
     * file sitting at /portal_assets/sw.js could only ever control
     * /portal_assets — not the portal. Serving the same file from under /me
     * gives it the scope it needs, and Chrome needs the start_url to be in
     * scope before it will offer to install the portal at all.
     *
     * The file itself stays under public/ so deploy.sh mirrors it into the
     * document root with everything else.
     *
     * Public on purpose: it contains no data, and a worker that 302s to the
     * login page is a worker that never registers.
     */
    public function serviceWorker()
    {
        $path = public_path('portal_assets/sw.js');

        abort_unless(is_file($path), 404);

        return response(file_get_contents($path), 200, [
            'Content-Type' => 'application/javascript; charset=utf-8',
            // Lets the worker claim a scope broader than its own directory.
            'Service-Worker-Allowed' => '/me/',
            // The worker is how the shell gets updated; it must not be the
            // one thing that cannot be.
            'Cache-Control' => 'no-cache, must-revalidate',
        ]);
    }

    /** Placeholder. Money is a later phase; nothing here computes any. */
    public function earnings()
    {
        return view('portal.earnings');
    }

    public function profile()
    {
        return view('portal.profile', [
            'me' => Auth::guard('employee')->user(),
        ]);
    }

    /**
     * Bookings whose date falls on one day.
     *
     * A half-open range rather than `where('date', $day)`. The `date` cast
     * writes a full datetime, so equality matches only because MySQL happens
     * to truncate it — on SQLite the same query returns nothing at all. This
     * is the shape BookingSlotService already uses, for the same reason.
     */
    private function onDay($query, Carbon $day)
    {
        return $query
            ->where('date', '>=', $day->toDateString())
            ->where('date', '<', $day->copy()->addDay()->toDateString());
    }
}
