<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Employee;
use App\Services\BookingSlotService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * The portal screens.
 *
 * Every query starts from the signed-in employee and goes through
 * Booking::visibleTo(), which decides — from the employee's own row, never
 * from the request — whether that means their own day or the whole shop's.
 *
 * A manager may narrow the view with ?barber=, and that is the only id-shaped
 * thing any action here reads. It is resolved against the roster before it can
 * reach a query, and for a barber it is dropped before it is even looked at:
 * there is still nothing a barber can put in a URL to reach another's day.
 */
class PortalController extends Controller
{
    private const PER_PAGE = 15;

    public function today(Request $request)
    {
        $me = Auth::guard('employee')->user();
        $today = Carbon::today();

        $bookings = $this->onDay(Booking::visibleTo($me), $today)
            ->with(['service', 'barber'])
            ->orderBy('time')
            ->get();

        /*
         * Bookings nobody is assigned to yet.
         *
         * A barber gets a bare count: it tells them the shop has unclaimed
         * work today without turning the portal into a place where they can
         * see, or take, someone else's customers. A manager gets the bookings
         * themselves, because deciding who takes them is their job.
         */
        $unassigned = $this->onDay(Booking::unassigned(), $today)->count();

        if (! $me->isManager()) {
            return view('portal.today', $this->todayPayload($today, $unassigned, [
                'bookings' => $bookings,
                // Desktop draws a timeline; the phone ignores this and lists
                // the bookings straight. Built here rather than in the view
                // because it is the shop's opening hours, not presentation.
                'timeline' => $this->timeline($today, $bookings),
            ]));
        }

        /*
         * The manager's day, as one block per barber.
         *
         * The roster drives the order — sort_order, the same order the POS
         * panel and the website use — rather than whatever order the bookings
         * came back in, so the blocks are in the same place every morning.
         */
        $roster = $this->roster($bookings->pluck('barber_id')->filter()->unique()->all());
        $filter = $this->barberFilter($request, $me, $roster);

        $byBarber = $bookings->groupBy('barber_id');
        $groups = [];

        foreach ($roster as $barber) {
            if ($filter !== 'all' && $filter !== (string) $barber->id) {
                continue;
            }

            $theirs = $byBarber->get($barber->id, collect());

            /*
             * An empty block is noise on the All view and the whole point of
             * a filtered one: "Ziad, nothing today" is the answer to the
             * question the chip just asked.
             */
            if ($theirs->isEmpty() && $filter === 'all') {
                continue;
            }

            $groups[] = [
                'key' => (string) $barber->id,
                'label' => $barber->name,
                'bookings' => $theirs->values(),
            ];
        }

        if ($filter === 'all' || $filter === 'unassigned') {
            // Same rule as the count above, so the header and the block can
            // never disagree about what "unassigned" means.
            $waiting = $bookings
                ->whereNull('barber_id')
                ->where('status', '!=', Booking::STATUS_CANCELLED)
                ->values();

            if ($waiting->isNotEmpty() || $filter === 'unassigned') {
                $groups[] = [
                    'key' => 'unassigned',
                    'label' => 'Unassigned',
                    'bookings' => $waiting,
                ];
            }
        }

        return view('portal.today', $this->todayPayload($today, $unassigned, [
            'groups' => $groups,
            'barbers' => $roster,
            'filter' => $filter,
            // Derived from the blocks rather than counted separately: the
            // header's "N today" is then the sum of what is on the screen.
            'bookings' => collect($groups)->flatMap(fn ($group) => $group['bookings']),
        ]));
    }

    /**
     * Today's view data, with every key always present.
     *
     * The screen renders two quite different things — a barber's timeline, a
     * manager's blocks — and a Blade branch that is not taken still has to
     * compile. Defaulting them here means the view never has to guess which
     * variables it was given.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function todayPayload(Carbon $today, int $unassigned, array $overrides): array
    {
        return array_replace([
            'today' => $today,
            'unassigned' => $unassigned,
            'bookings' => collect(),
            'timeline' => [],
            'groups' => [],
            'barbers' => collect(),
            'filter' => 'all',
        ], $overrides);
    }

    public function bookings(Request $request)
    {
        $me = Auth::guard('employee')->user();

        // The tab is the only input, and anything but "past" means upcoming.
        $tab = $request->query('tab') === 'past' ? 'past' : 'upcoming';
        $today = Carbon::today()->toDateString();

        $query = Booking::visibleTo($me)->with('service');

        /*
         * The chips list the active roster only. Unlike Today, nothing is
         * hidden by that: an inactive barber's bookings still appear under
         * All, with their name in the Barber column — they are just not
         * offered as a filter.
         */
        $roster = $me->isManager() ? $this->roster() : collect();
        $filter = $this->barberFilter($request, $me, $roster);

        if ($me->isManager()) {
            $query->with('barber');
            $this->narrowToBarber($query, $filter);
        }

        /*
         * Each tab has the order that makes sense for it — the next
         * appointment first, the most recent one first — and `dir` overrides
         * that for the desktop table's sortable date column. Anything else in
         * the parameter falls back to the tab's own default rather than
         * reaching the query.
         */
        $default = $tab === 'past' ? 'desc' : 'asc';
        $dir = in_array($request->query('dir'), ['asc', 'desc'], true)
            ? $request->query('dir')
            : $default;

        $query->where('date', $tab === 'past' ? '<' : '>=', $today)
            ->orderBy('date', $dir)
            ->orderBy('time', $dir);

        return view('portal.bookings', [
            'bookings' => $query->paginate(self::PER_PAGE)->withQueryString(),
            'tab' => $tab,
            'dir' => $dir,
            'barbers' => $roster,
            'filter' => $filter,
        ]);
    }

    /**
     * The barbers a manager may filter by.
     *
     * The active roster, in the order the rest of the system uses, plus
     * anyone in $alsoInclude — which is how a barber deactivated halfway
     * through the day still gets a block for the appointments they are
     * holding. Work that exists must not vanish from the one screen whose job
     * is to show the whole day.
     *
     * @param  list<int>  $alsoInclude
     * @return \Illuminate\Support\Collection<int, Employee>
     */
    private function roster(array $alsoInclude = [])
    {
        return Employee::query()
            ->where(function ($q) use ($alsoInclude) {
                $q->where('is_active', true);

                if ($alsoInclude !== []) {
                    $q->orWhereIn('id', $alsoInclude);
                }
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            // Three columns, so a password hash never travels as far as a
            // view that only ever prints a name.
            ->get(['id', 'name', 'sort_order']);
    }

    /**
     * Which barber the manager is looking at: an id, 'unassigned', or 'all'.
     *
     * Returns 'all' for anyone who is not a manager, before the parameter is
     * so much as read — a barber cannot widen or redirect their own view with
     * a query string, whatever they put in it. For a manager the value is
     * matched against the roster, so an id that is not on it falls back to
     * All rather than reaching a query.
     */
    private function barberFilter(Request $request, Employee $me, $roster): string
    {
        if (! $me->isManager()) {
            return 'all';
        }

        $asked = (string) $request->query('barber', 'all');

        if ($asked === 'unassigned') {
            return 'unassigned';
        }

        $known = $roster->contains(fn ($barber) => (string) $barber->id === $asked);

        return $known ? $asked : 'all';
    }

    /** Applies a resolved filter. Only ever called with a manager's value. */
    private function narrowToBarber($query, string $filter): void
    {
        if ($filter === 'unassigned') {
            $query->unassigned();
        } elseif ($filter !== 'all') {
            $query->forEmployee($filter);
        }
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
     * The shop's slots for a day, each with the bookings that start in it.
     *
     * The timeline is the whole working day, not only the hours with work in
     * them: a barber reading it needs to see the gaps as much as the
     * appointments. Opening hours come from BookingSlotService so this cannot
     * drift from what the booking form offers customers.
     *
     * @return list<array{label: string, minute: int, bookings: \Illuminate\Support\Collection}>
     */
    private function timeline(Carbon $day, $bookings): array
    {
        $slots = app(BookingSlotService::class);

        [$open, $lastStart] = $slots->workingWindow($day->toDateString());

        // Grouped once, so the loop below is a lookup rather than a scan.
        $byMinute = collect($bookings)->groupBy(function ($booking) {
            $at = Carbon::parse($booking->time);

            return $at->hour * 60 + $at->minute;
        });

        $rows = [];

        for ($minute = $open; $minute <= $lastStart; $minute += BookingSlotService::BOOKING_DURATION_MINUTES) {
            $rows[] = [
                'minute' => $minute,
                'label' => Carbon::createFromTime(intdiv($minute, 60), $minute % 60)->format('g:i A'),
                'bookings' => $byMinute->get($minute, collect()),
            ];
        }

        return $rows;
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
