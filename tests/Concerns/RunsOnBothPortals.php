<?php

namespace Tests\Concerns;

/**
 * Runs a test against both mounts of the employee portal.
 *
 * /me and /work are the same routes, controllers, middleware and guard behind
 * two prefixes. That is only true for as long as nothing quietly diverges, so
 * the behaviour tests take the route-name prefix as a parameter and run twice
 * rather than testing the phone and trusting the desk.
 *
 * The parameter is the route-NAME prefix ('portal.' / 'work.'), not the URL:
 * tests build their URLs with route(), so a change to either path prefix does
 * not touch a single test.
 */
trait RunsOnBothPortals
{
    /** @return array<string, array{string}> */
    public static function portals(): array
    {
        return [
            'phone (/me)' => ['portal.'],
            'desk (/work)' => ['work.'],
        ];
    }
}
