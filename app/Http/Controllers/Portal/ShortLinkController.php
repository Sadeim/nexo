<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * /app — a short link to the portal, and nothing else.
 *
 * "nexobarbers.com/app" is what fits on a card, in a text message, and in the
 * sentence a barber is told once and has to remember. /me is the address the
 * portal actually lives at and stays the address it lives at: phones that
 * already have the app installed hold /me in their manifest, their scope and
 * their service worker registration, so moving the portal here would uninstall
 * it from every phone that has it.
 *
 * So this is a redirect and only a redirect. Nothing is served under /app and
 * nothing ever should be — a second mount of the portal would be a second URL
 * to get the scope, the session cookie and the worker's registration wrong at.
 */
class ShortLinkController extends Controller
{
    public function __invoke(Request $request, ?string $path = null)
    {
        $target = '/me'.($path === null || $path === '' ? '' : '/'.$path);

        /*
         * The query string travels. /app/bookings?tab=past has to arrive as
         * /me/bookings?tab=past or the link is a link to the wrong screen —
         * and the handover from /work to /me already carries query strings
         * for exactly the same reason.
         */
        $query = $request->getQueryString();

        return redirect()->to($target.($query === null || $query === '' ? '' : '?'.$query), 302);
    }
}
