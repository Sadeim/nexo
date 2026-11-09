<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route as RouteFacade;

/**
 * Which portal a request is in, and how to stay in it.
 *
 * The portal is served twice: /me for phones and /work for a desk. Same
 * controllers, same middleware, same `employee` guard and the same session —
 * only the chrome differs. What that costs is a way to answer "which one am I
 * in?" without every controller, middleware and view having to care, and a
 * way to build a link that lands in the version the barber is already using.
 * Both live here, so there is one answer rather than one per caller.
 */
class Portal
{
    public const MOBILE = 'mobile';
    public const DESKTOP = 'desktop';

    /**
     * Below this, /work hands over to /me.
     *
     * The desktop layout is a sidebar plus a table and has nothing sensible to
     * do in a phone-width window, so rather than reflow into something that is
     * neither, it sends the barber to the screen built for that width.
     */
    public const MIN_DESKTOP_WIDTH = 1024;

    /**
     * The route-name prefix and the URL prefix for each version.
     *
     * Adding a version means adding a line here and a layout; the routes
     * themselves are declared once in routes/portal.php.
     */
    public const VERSIONS = [
        self::MOBILE => ['name' => 'portal.', 'path' => 'me'],
        self::DESKTOP => ['name' => 'work.', 'path' => 'work'],
    ];

    /**
     * Which version the current request is in.
     *
     * Read from the route group's attribute. The key has no dot in it on
     * purpose: Route::getAction() resolves its argument with dot notation, so
     * 'portal.layout' would be looked up as a nested ['portal']['layout'] and
     * always come back null.
     */
    public static function variant(?Request $request = null): string
    {
        $route = ($request ?? request())?->route();

        $variant = $route?->getAction('portal_layout');

        return isset(self::VERSIONS[$variant]) ? $variant : self::MOBILE;
    }

    public static function isDesktop(?Request $request = null): bool
    {
        return self::variant($request) === self::DESKTOP;
    }

    /** The route-name prefix for a version — 'portal.' or 'work.'. */
    public static function namePrefix(?string $variant = null): string
    {
        $variant = $variant ?? self::variant();

        return self::VERSIONS[$variant]['name'] ?? self::VERSIONS[self::MOBILE]['name'];
    }

    /**
     * A URL for a portal screen in the version the request is already in.
     *
     * Views, controllers and middleware all call this instead of route()
     * directly, which is what keeps a barber on /work from being bounced onto
     * /me by a link in the page they are looking at.
     */
    public static function route(string $screen, array $parameters = [], ?string $variant = null): string
    {
        return route(self::namePrefix($variant).$screen, $parameters);
    }

    /** The Blade layout for the current version. */
    public static function layout(?Request $request = null): string
    {
        return 'portal.layouts.'.self::variant($request);
    }

    /**
     * The same screen in the other version, query string and all.
     *
     * This is the redirect rule the narrow-viewport handover uses: a browser
     * too narrow for /work is sent to the /me URL for the page it asked for,
     * not to the portal's front door, so a bookmarked "past bookings, page 3"
     * survives the handover.
     *
     * Returns null when the current request is not a portal screen at all.
     */
    public static function counterpartUrl(?Request $request = null): ?string
    {
        $request = $request ?? request();
        $route = $request?->route();
        $name = $route?->getName();

        if (! $name) {
            return null;
        }

        $here = self::variant($request);
        $there = $here === self::DESKTOP ? self::MOBILE : self::DESKTOP;

        $screen = self::screenFrom($name);

        if ($screen === null || ! RouteFacade::has(self::namePrefix($there).$screen)) {
            return null;
        }

        return self::route($screen, $request->query(), $there);
    }

    /**
     * 'work.bookings' -> 'bookings'. Null if the name belongs to neither
     * version, so a non-portal route can never be mapped onto one.
     */
    public static function screenFrom(string $routeName): ?string
    {
        foreach (self::VERSIONS as $version) {
            if (str_starts_with($routeName, $version['name'])) {
                return substr($routeName, strlen($version['name']));
            }
        }

        return null;
    }

    /** Every URL path prefix the portal answers on, for middleware matching. */
    public static function pathPatterns(): array
    {
        $patterns = [];

        foreach (self::VERSIONS as $version) {
            $patterns[] = $version['path'];
            $patterns[] = $version['path'].'/*';
        }

        return $patterns;
    }
}
