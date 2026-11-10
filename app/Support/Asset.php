<?php

namespace App\Support;

/**
 * A URL for a file under public/, with the file's own fingerprint on it.
 *
 * This host answers static files with `Cache-Control: public, max-age=604800`
 * and no revalidation. Seven days. A browser that has loaded a script will go
 * on using it for a week whatever the server now holds, which means a deploy
 * can ship new server behaviour to a page still running last week's
 * JavaScript against it.
 *
 * That is not a hypothetical: it bit the portal on 9 Nov 2026, and it is
 * exactly the shape of the booking modal's two-step release — new server,
 * old script, a form that posts the wrong fields and a customer who cannot
 * book.
 *
 * Eight characters of the file's md5 solve it. A changed file is a changed
 * URL and therefore a miss in the browser cache, the CDN and any service
 * worker at once; an unchanged file keeps its URL and stays cached, which is
 * what the seven days should have been buying all along.
 *
 * Of the contents, not the mtime: a deploy that copies an unchanged file must
 * not make every visitor download it again.
 */
class Asset
{
    /** @var array<string, string> */
    private static array $fingerprints = [];

    public static function url(string $path): string
    {
        if (! isset(self::$fingerprints[$path])) {
            $file = public_path($path);

            // A missing file still has to render a URL — a 404 on a script is
            // a broken page, an exception in a layout is no page at all.
            self::$fingerprints[$path] = is_file($file)
                ? substr(md5_file($file), 0, 8)
                : 'missing';
        }

        return asset($path).'?v='.self::$fingerprints[$path];
    }
}
