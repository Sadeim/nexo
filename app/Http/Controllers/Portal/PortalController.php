<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Employee;
use App\Services\BookingSlotService;
use App\Support\Asset;
use App\Support\BuildVersion;
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

    /**
     * The signed-in employee's own day — a manager's included.
     *
     * Managers used to see the whole shop here. They no longer do: Eddie owns
     * the place and also cuts hair, and when he opens Today he is a barber
     * looking at his own chair. The shop's day lives on its own screen, All
     * Bookings, which is a different question asked deliberately rather than
     * one that quietly replaces his.
     */
    public function today()
    {
        $me = Auth::guard('employee')->user();
        $today = Carbon::today();

        $bookings = $this->onDay(Booking::forEmployee($me->id), $today)
            ->with('service')
            ->orderBy('time')
            ->get();

        /*
         * Bookings nobody is assigned to yet. A bare count: it tells anyone
         * the shop has unclaimed work today without turning this screen into
         * a place to see, or take, someone else's customers. A manager who
         * wants the detail has All Bookings.
         */
        $unassigned = $this->onDay(Booking::unassigned(), $today)->count();

        return view('portal.today', [
            'bookings' => $bookings,
            'unassigned' => $unassigned,
            'today' => $today,
            // Desktop draws a timeline; the phone ignores this and lists the
            // bookings straight. Built here rather than in the view because
            // it is the shop's opening hours, not presentation.
            'timeline' => $this->timeline($today, $bookings),
        ]);
    }

    /**
     * The signed-in employee's own bookings. No manager branch here either —
     * see today().
     */
    public function bookings(Request $request)
    {
        $me = Auth::guard('employee')->user();

        // The tab is the only input, and anything but "past" means upcoming.
        $tab = $request->query('tab') === 'past' ? 'past' : 'upcoming';
        $today = Carbon::today()->toDateString();

        $query = Booking::forEmployee($me->id)->with('service');

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
        ]);
    }

    /**
     * All Bookings: the whole shop on one day, for a manager.
     *
     * This is where Booking::visibleTo() earns its keep — it is now the only
     * portal screen that asks for anything beyond the signed-in employee's
     * own work, and the manager middleware in front of the route is what
     * decides who may ask.
     */
    public function team(Request $request)
    {
        $me = Auth::guard('employee')->user();

        $day = $this->dayFrom($request);

        $bookings = $this->onDay(Booking::visibleTo($me), $day)
            ->with(['service', 'barber'])
            ->orderBy('time')
            ->get();

        // The roster includes anyone holding work that day even if they have
        // since been deactivated: the screen whose job is the whole day must
        // not quietly lose part of it.
        $roster = $this->roster($bookings->pluck('barber_id')->filter()->unique()->all());
        $filter = $this->barberFilter($request, $me, $roster);

        $byBarber = $bookings->groupBy('barber_id');
        $groups = [];

        foreach ($roster as $barber) {
            if ($filter !== 'all' && $filter !== (string) $barber->id) {
                continue;
            }

            $theirs = $byBarber->get($barber->id, collect());

            // An empty block is noise on the All view and the whole point of
            // a filtered one: "Ziad, nothing today" answers the chip.
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
            // Same rule as the count on Today, so the two screens can never
            // disagree about what "unassigned" means.
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

        return view('portal.team', [
            'day' => $day,
            'groups' => $groups,
            'barbers' => $roster,
            'filter' => $filter,
            // Derived from the blocks, so the header count is the sum of what
            // is actually on the screen.
            'bookings' => collect($groups)->flatMap(fn ($group) => $group['bookings']),
            'unassigned' => $this->onDay(Booking::unassigned(), $day)->count(),
            // For the previous/next arrows, so the view does no date maths.
            'previousDay' => $day->copy()->subDay()->toDateString(),
            'nextDay' => $day->copy()->addDay()->toDateString(),
            'isToday' => $day->isSameDay(Carbon::today()),
        ]);
    }

    /**
     * The day All Bookings is showing: ?date=, or today.
     *
     * Anything unparseable falls back to today rather than erroring — a
     * mistyped URL should show the shop's day, not an error. Clamped to a
     * sane range so the arrows cannot be used to walk the table a decade at
     * a time.
     */
    private function dayFrom(Request $request): Carbon
    {
        $asked = (string) $request->query('date', '');

        if ($asked === '') {
            return Carbon::today();
        }

        try {
            $day = Carbon::createFromFormat('Y-m-d', $asked);
        } catch (\Throwable $e) {
            return Carbon::today();
        }

        // createFromFormat is forgiving; comparing the round trip is what
        // rejects '2026-13-45' and '2026-1-1'.
        if (! $day || $day->format('Y-m-d') !== $asked) {
            return Carbon::today();
        }

        $day = $day->startOfDay();

        return $day->between(
            Carbon::today()->subYears(5),
            Carbon::today()->addYears(2)
        ) ? $day : Carbon::today();
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

    /**
     * The service worker, served from /me/sw.js.
     *
     * A worker's scope cannot rise above the path it is served from, so the
     * file sitting at /portal_assets/sw.js could only ever control
     * /portal_assets — not the portal. Serving the same file from under /me
     * gives it the scope it needs, and Chrome needs the start_url to be in
     * scope before it will offer to install the portal at all.
     *
     * Served from /me/, its scope would still stop at /me/ — which excludes
     * /me itself, the screen the app starts on. Service-Worker-Allowed below
     * is what lifts it the last character.
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

        /*
         * The one thing the route does to the file: stamp the build on it.
         *
         * A browser installs a new worker only when the script's bytes differ
         * from the one it holds. Without the stamp, sw.js is identical from
         * one release to the next and an installed portal would go on running
         * last month's shell forever. With it, every deploy is a new worker —
         * see the comment at the top of sw.js.
         */
        $source = str_replace(
            '__BUILD_VERSION__',
            BuildVersion::current(),
            file_get_contents($path)
        );

        return response($source, 200, [
            'Content-Type' => 'application/javascript; charset=utf-8',
            /*
             * Lets the worker claim a scope broader than its own directory.
             *
             * '/me', not '/me/'. A scope is a plain string prefix, and
             * '/me/' does not match '/me' — so Today, the screen a barber
             * sits on all day and the address the installed app starts at,
             * was outside the worker's scope entirely. It was never
             * controlled, never offered an update, and the shell cache did
             * nothing for it.
             */
            'Service-Worker-Allowed' => '/me',
            /*
             * Never stored, anywhere. This is the file the portal checks to
             * find out it is out of date, so it is the one file that must not
             * be answered from a cache — not the browser's, and not this
             * host's CDN, which holds static files for seven days without
             * revalidating. portal.js asks for the same thing from its end
             * with updateViaCache: 'none'.
             */
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }

    /**
     * The PWA manifest, served from /me/ rather than as a static file.
     *
     * It has to be rendered for one reason: the icon URLs. This host answers
     * static files with a seven-day Cache-Control and no revalidation, so a
     * phone that has fetched icon-192.png keeps that image for a week
     * whatever the shop's logo now is — and an installed app keeps it until
     * the manifest tells it otherwise. Running the paths through
     * App\Support\Asset gives each one the fingerprint of its own contents,
     * which makes a new icon a new URL and therefore a new download.
     *
     * The JSON on disk stays the source of truth; this only rewrites the
     * srcs, so there is still one place to edit the app's name or colours.
     *
     * Public, like the worker: a manifest behind a login is a manifest the
     * browser fetches and discards, and it contains nobody's data.
     */
    public function manifest()
    {
        $path = public_path('portal_assets/manifest.webmanifest');

        abort_unless(is_file($path), 404);

        $manifest = json_decode(file_get_contents($path), true);

        abort_unless(is_array($manifest), 500);

        foreach ($manifest['icons'] ?? [] as $i => $icon) {
            if (isset($icon['src'])) {
                $manifest['icons'][$i]['src'] = Asset::url(ltrim($icon['src'], '/'));
            }
        }

        return response(json_encode($manifest, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT), 200, [
            'Content-Type' => 'application/manifest+json; charset=utf-8',
            // Re-read on every load, so a new icon arrives on the next visit
            // rather than whenever the browser next feels like asking.
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
