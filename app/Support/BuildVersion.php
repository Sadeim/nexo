<?php

namespace App\Support;

/**
 * Which build is on the server, as a short string safe to put in a cache name.
 *
 * The service worker is named after this. That is the whole point: a worker
 * whose bytes are identical to the installed one is, to the browser, the same
 * worker — nothing installs, nothing activates, and the phone keeps the shell
 * it already has. Writing the commit into the worker makes every deploy a
 * different file and therefore a new worker, which is the only reliable way to
 * tell an installed PWA that it is out of date.
 *
 * It is written by deploy/write-build-version.sh, which runs straight after
 * the pull so the version is the commit that was just deployed. The file is in
 * storage/, which git ignores, so a checkout never carries another machine's
 * build id around.
 *
 * Nothing here ever throws. A portal that cannot name its build should fall
 * back to a worse version string, not to a 500 on the one file that would let
 * it recover.
 */
class BuildVersion
{
    /** Relative to storage/app. */
    public const FILE = 'build-version';

    /**
     * The directory the fallback hashes.
     *
     * The shell, which is what the worker caches: if any of it changed, the
     * worker should be new even on a machine with no build file.
     */
    private const SHELL = 'portal_assets';

    private static ?string $version = null;

    public static function current(): string
    {
        return self::$version ??= self::fromFile() ?? self::fromShell();
    }

    /** For tests, which write the file and then expect to be believed. */
    public static function forget(): void
    {
        self::$version = null;
    }

    public static function path(): string
    {
        return storage_path('app/'.self::FILE);
    }

    /**
     * The deployed commit, if deploy.sh managed to write it.
     *
     * Sanitised rather than trusted: this string is interpolated into
     * JavaScript, so it is reduced to the characters a commit hash can
     * actually contain before it goes anywhere near the worker.
     */
    private static function fromFile(): ?string
    {
        $file = self::path();

        if (! is_file($file) || ! is_readable($file)) {
            return null;
        }

        $raw = @file_get_contents($file);

        if ($raw === false) {
            return null;
        }

        $clean = strtolower(preg_replace('/[^A-Za-z0-9]/', '', substr(trim($raw), 0, 40)));

        return $clean === '' ? null : $clean;
    }

    /**
     * No build file: a fingerprint of the shell itself.
     *
     * Local development, and the first request after a deploy whose version
     * write failed. Hashes the file contents rather than mtimes, for the same
     * reason App\Support\Asset does — a deploy that copies an unchanged file
     * must not look like a new build.
     */
    private static function fromShell(): string
    {
        $root = public_path(self::SHELL);

        if (! is_dir($root)) {
            return 'dev';
        }

        $parts = [];

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($files as $file) {
            if ($file->isFile()) {
                $parts[] = $file->getPathname().':'.md5_file($file->getPathname());
            }
        }

        // Sorted, because the iterator's order is the filesystem's and two
        // servers holding identical files must agree on the version.
        sort($parts);

        return substr(md5(implode('|', $parts)), 0, 12);
    }
}
