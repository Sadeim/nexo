<?php

namespace Tests\Feature\Portal;

use App\Support\BuildVersion;
use Tests\TestCase;

/**
 * How an installed portal finds out it is out of date.
 *
 * A PWA on a home screen has no address bar, no reload button and nothing to
 * pull down on. Whatever shell it registered on the day it was installed is
 * the shell it keeps — unless the worker changes, which is the one signal a
 * browser acts on by itself. So the chain this file guards is:
 *
 *   deploy writes the commit -> the route stamps it into sw.js -> the bytes
 *   differ -> a worker installs and waits -> the barber is offered Update ->
 *   the tap activates it -> the page reloads ONCE, because it was asked to.
 *
 * Every link matters. Break the stamp and nothing updates, ever. Break the
 * waiting and the portal reloads under whoever is typing into it.
 */
class PortalUpdateTest extends TestCase
{
    private ?string $savedVersionFile = null;

    protected function setUp(): void
    {
        parent::setUp();

        // The real file belongs to this machine's checkout; tests write over
        // it and must hand it back.
        $file = BuildVersion::path();
        $this->savedVersionFile = is_file($file) ? file_get_contents($file) : null;

        BuildVersion::forget();
    }

    protected function tearDown(): void
    {
        $file = BuildVersion::path();

        if ($this->savedVersionFile === null) {
            @unlink($file);
        } else {
            file_put_contents($file, $this->savedVersionFile);
        }

        BuildVersion::forget();

        parent::tearDown();
    }

    private function withBuild(?string $version): void
    {
        $file = BuildVersion::path();

        if ($version === null) {
            @unlink($file);
        } else {
            file_put_contents($file, $version);
        }

        BuildVersion::forget();
    }

    private function worker(): string
    {
        return $this->get('/me/sw.js')->assertOk()->getContent();
    }

    private function source(): string
    {
        return file_get_contents(public_path('portal_assets/sw.js'));
    }

    /**
     * One `self.addEventListener('x', …)` block of the worker, as text.
     *
     * Crude on purpose — the point is to assert that skipWaiting() is in one
     * handler and nowhere near another, which a whole-file search cannot say.
     */
    private function handler(string $event): string
    {
        $source = $this->source();
        $start = strpos($source, "self.addEventListener('".$event."'");

        $this->assertNotFalse($start, "The worker has no $event handler.");

        /*
         * Up to the handler's own closing brace at column 0 — NOT up to the
         * next listener. The comment between install() and message() is
         * eight lines about why install must not call skipWaiting(), and a
         * slice that swallowed it would fail the very assertion it explains.
         */
        $end = strpos($source, "\n});", $start);

        $this->assertNotFalse($end, "The $event handler does not end where expected.");

        return substr($source, $start, $end - $start);
    }

    // ---------- the route ----------

    public function test_the_worker_is_never_stored_anywhere(): void
    {
        $response = $this->get('/me/sw.js');

        $response->assertOk();

        /*
         * This host answers static files with seven days of Cache-Control and
         * no revalidation. sw.js is the file the portal checks to learn it is
         * out of date, so it is the one file that must never come from a
         * cache — the browser's, or the CDN's.
         */
        $cacheControl = $response->headers->get('Cache-Control');

        foreach (['no-cache', 'no-store', 'must-revalidate'] as $directive) {
            $this->assertStringContainsString($directive, $cacheControl,
                "sw.js is served without $directive, so it can be answered from a cache.");
        }

        // Directives rather than the whole string: Symfony reorders them and
        // adds `private` of its own accord, which changes nothing here.
        $this->assertStringNotContainsString('max-age', $cacheControl);

        $this->assertStringContainsString('application/javascript',
            $response->headers->get('Content-Type'));

        // Without this the worker's scope could not rise above /portal_assets.
        $response->assertHeader('Service-Worker-Allowed', '/me');
    }

    // ---------- the scope reaches the screens ----------

    /**
     * A scope is a plain string prefix, and '/me/' does not match '/me'.
     *
     * The worker was registered at '/me/' for its first week, which left
     * Today — the screen a barber sits on all day, and the address the
     * installed app starts at — outside the worker's scope entirely. The
     * page was never controlled, so there was never a waiting worker to
     * offer, and no barber on Today would ever have been told about a new
     * version.
     */
    public function test_the_scope_covers_the_screen_a_barber_sits_on(): void
    {
        $js = $this->portalJs();

        $this->assertStringContainsString("{ scope: '/me'", $js);
        $this->assertStringNotContainsString("scope: '/me/'", $js);

        $manifest = $this->get(route('portal.manifest'))->json();

        $this->assertSame('/me', $manifest['scope']);

        // The rule itself: start_url has to be inside scope, as a prefix.
        $this->assertStringStartsWith($manifest['scope'], $manifest['start_url']);

        foreach (['portal.today', 'portal.bookings', 'portal.profile', 'portal.login'] as $screen) {
            $this->assertStringStartsWith('/me', parse_url(route($screen), PHP_URL_PATH));
        }
    }

    /**
     * The price of a scope without its trailing slash: it also covers
     * anything else beginning with those two letters. Nothing does, and this
     * is what notices if something ever starts to.
     */
    public function test_nothing_outside_the_portal_falls_inside_the_scope(): void
    {
        $strays = collect(\Illuminate\Support\Facades\Route::getRoutes()->getRoutes())
            ->map(fn ($route) => $route->uri())
            ->filter(fn ($uri) => str_starts_with($uri, 'me'))
            ->reject(fn ($uri) => $uri === 'me' || str_starts_with($uri, 'me/'))
            ->values()
            ->all();

        $this->assertSame([], $strays,
            "These routes fall inside the portal's service-worker scope: ".implode(', ', $strays));
    }

    /**
     * And the registration the portal used to have is taken away, or the
     * browser — which picks the longest matching scope — would go on letting
     * '/me/' control every sub-screen.
     */
    public function test_the_old_registration_is_retired(): void
    {
        $js = $this->portalJs();

        $this->assertStringContainsString('getRegistrations()', $js);
        $this->assertStringContainsString("one.scope === window.location.origin + '/me/'", $js);
        $this->assertStringContainsString('one.unregister();', $js);
    }

    public function test_the_worker_is_served_without_signing_in(): void
    {
        // A worker that 302s to the login page is a worker that never
        // registers, which is a portal that can never update itself.
        $this->get('/me/sw.js')->assertOk();
        $this->get('/me/manifest.webmanifest')->assertOk();
    }

    // ---------- the stamp ----------

    public function test_the_worker_carries_the_build_it_belongs_to(): void
    {
        $this->withBuild('abc123def456');

        $worker = $this->worker();

        $this->assertStringContainsString("const VERSION = 'abc123def456';", $worker);
        $this->assertStringContainsString("const CACHE = 'nexo-portal-' + VERSION;", $worker);
    }

    /**
     * The placeholder must never reach a phone. If it did, every release
     * would be the same worker and nothing would ever update — silently,
     * which is the worst way for this to fail.
     */
    public function test_the_placeholder_never_leaves_the_server(): void
    {
        foreach (['deadbeef0001', null] as $build) {
            $this->withBuild($build);

            $this->assertStringNotContainsString('__BUILD_VERSION__', $this->worker(),
                'The build placeholder was served as-is.');
        }

        // It is still in the file on disk: that file is a template.
        $this->assertStringContainsString('__BUILD_VERSION__', $this->source());
    }

    /** The whole point: a deploy is a different file, so it is a new worker. */
    public function test_a_new_build_is_a_different_worker(): void
    {
        $this->withBuild('1111aaaa1111');
        $first = $this->worker();

        $this->withBuild('2222bbbb2222');
        $second = $this->worker();

        $this->assertNotSame($first, $second,
            'Two builds served byte-identical workers — no phone would ever update.');

        $this->assertStringContainsString('nexo-portal-', $first);
        $this->assertNotSame(
            $this->cacheNameOf($first),
            $this->cacheNameOf($second),
            'Both builds share a cache name, so the old shell is never evicted.'
        );
    }

    private function cacheNameOf(string $worker): string
    {
        preg_match("/const VERSION = '([^']*)';/", $worker, $m);

        return 'nexo-portal-'.($m[1] ?? '');
    }

    /**
     * No build file — local development, or a deploy whose version write
     * failed. The portal must still name itself something, and something
     * that changes when the shell does.
     */
    public function test_without_a_build_file_the_shell_names_itself(): void
    {
        $this->withBuild(null);

        $version = BuildVersion::current();

        $this->assertMatchesRegularExpression('/^[a-f0-9]{12}$/', $version,
            'The fallback is not a fingerprint of the shell.');

        BuildVersion::forget();

        $this->assertSame($version, BuildVersion::current(),
            'The fallback is not stable, so every request would look like a new build.');
    }

    /**
     * A process that has already answered must not keep answering with the
     * build it first saw.
     *
     * PHP-FPM processes outlive a deploy. If the version were remembered for
     * the life of the process, half the server would serve the new worker
     * and half the old one, and a phone asking twice would be told it was
     * out of date twice — a different script every time. The file is read on
     * every call for exactly this reason, and nothing here calls forget().
     */
    public function test_a_deploy_is_seen_without_restarting_anything(): void
    {
        $this->withBuild('aaaa11112222');
        $this->assertSame('aaaa11112222', BuildVersion::current());

        // No forget(): this is the deploy writing the file under a process
        // that has already served sw.js once.
        file_put_contents(BuildVersion::path(), 'bbbb33334444');

        $this->assertSame('bbbb33334444', BuildVersion::current(),
            'The build version is cached for the life of the process, so sw.js '
            .'will flap between two builds after a deploy.');

        $this->assertStringContainsString("const VERSION = 'bbbb33334444';", $this->worker());
    }

    public function test_an_empty_build_file_falls_back_rather_than_naming_nothing(): void
    {
        $this->withBuild("   \n");

        $this->assertNotSame('', BuildVersion::current());
        $this->assertStringNotContainsString("const VERSION = '';", $this->worker());
    }

    /**
     * The version is interpolated into a JavaScript string literal. Whatever
     * is in that file, what comes out has to be a commit-shaped word.
     */
    public function test_a_hostile_build_file_cannot_write_javascript(): void
    {
        $this->withBuild("'; self.registration.unregister(); //");

        $worker = $this->worker();

        // The punctuation is what makes it code; strip that and all that is
        // left is a long silly word inside a string literal.
        $this->assertStringNotContainsString('self.registration.unregister', $worker);

        // Still exactly one declaration, not one plus whatever the file said.
        $this->assertSame(1, substr_count($worker, 'const VERSION'));

        preg_match("/const VERSION = '([^']*)';/", $worker, $m);

        $this->assertMatchesRegularExpression('/^[a-z0-9]+$/', $m[1] ?? '',
            'The build version reached the worker without being sanitised.');
    }

    // ---------- the worker waits ----------

    /**
     * install() must NOT skipWaiting.
     *
     * A worker that skips waiting takes the page the moment it installs, and
     * the reload that follows throws away whatever the barber had on screen.
     * The portal has been there: a pull-to-refresh armed on every screen
     * emptied the password form every time an iPhone user reached for the
     * keyboard, and cost a barber an afternoon.
     */
    public function test_a_new_worker_waits_instead_of_taking_over(): void
    {
        $this->assertStringNotContainsString('skipWaiting', $this->handler('install'),
            'The worker activates itself on install, so the portal can change mid-screen.');
    }

    public function test_it_takes_over_only_when_the_page_asks(): void
    {
        $message = $this->handler('message');

        $this->assertStringContainsString('SKIP_WAITING', $message);
        $this->assertStringContainsString('self.skipWaiting()', $message);
    }

    public function test_activating_throws_away_every_other_build(): void
    {
        $activate = $this->handler('activate');

        $this->assertStringContainsString('keys.filter((k) => k !== CACHE)', $activate);
        $this->assertStringContainsString('caches.delete(k)', $activate);

        // Without claim() the tap appears to do nothing: the new worker would
        // control no page until the next navigation.
        $this->assertStringContainsString('self.clients.claim()', $activate);
    }

    // ---------- the page offers it ----------

    private function portalJs(): string
    {
        return file_get_contents(public_path('portal_assets/js/portal.js'));
    }

    public function test_the_page_asks_the_server_rather_than_its_cache(): void
    {
        $js = $this->portalJs();

        $this->assertStringContainsString("updateViaCache: 'none'", $js,
            'The update check is answered by the seven-day HTTP cache.');

        $this->assertStringContainsString('registration.update()', $js);
    }

    public function test_the_page_checks_again_when_it_comes_back_to_the_foreground(): void
    {
        $js = $this->portalJs();

        // An installed app is opened far more often than it is loaded.
        $this->assertStringContainsString("addEventListener('visibilitychange'", $js);
        $this->assertStringContainsString("document.visibilityState === 'visible'", $js);
    }

    public function test_a_waiting_worker_is_offered_not_applied(): void
    {
        $js = $this->portalJs();

        $this->assertStringContainsString('New version available', $js);
        $this->assertStringContainsString("postMessage({ type: 'SKIP_WAITING' })", $js);

        // Both ways in: one that installed while the page was open, and one
        // left waiting from a previous visit.
        $this->assertStringContainsString("addEventListener('updatefound'", $js);
        $this->assertStringContainsString('reg.waiting', $js);
    }

    /**
     * The reload is the dangerous half. It happens on controllerchange, and
     * only after the barber has tapped — not when another tab updates, and
     * not when the first worker this phone ever had claims the page.
     */
    public function test_the_page_reloads_once_and_only_on_the_tap(): void
    {
        $js = $this->portalJs();

        $this->assertStringContainsString("addEventListener('controllerchange'", $js);

        $start = strpos($js, "addEventListener('controllerchange'");
        $handler = substr($js, $start, 900);

        $this->assertStringContainsString('if (!wanted || reloaded)', $handler,
            'The reload is not gated on the barber having asked for it.');

        $this->assertStringContainsString('reloaded = true;', $handler,
            'Nothing stops the page reloading more than once.');

        // The flag is set by the button and nowhere else.
        $this->assertSame(1, substr_count($js, 'wanted = true;'));
    }

    public function test_the_update_bar_is_styled(): void
    {
        $css = file_get_contents(public_path('portal_assets/css/portal.css'));

        $this->assertStringContainsString('.p-update {', $css,
            'The update bar has no styles, so it renders as unstyled text over the tab bar.');

        // Above the tab bar, never over it: the tab bar is the only way out
        // of a screen in an installed app.
        $this->assertStringContainsString('var(--tabbar-height)', substr($css, strpos($css, '.p-update {'), 600));
    }

    // ---------- the deploy writes it ----------

    public function test_the_deploy_script_writes_the_build_version(): void
    {
        $deploy = file_get_contents(base_path('deploy/deploy.sh'));

        $this->assertStringContainsString('deploy/write-build-version.sh', $deploy,
            'A deploy no longer stamps the build, so no installed phone will ever update.');

        // After the pull, so it is the commit that was actually deployed.
        $this->assertGreaterThan(
            strpos($deploy, 'git -C "$APP_DIR" pull --ff-only'),
            strpos($deploy, 'deploy/write-build-version.sh'),
            'The build version is written before the pull, so it names the previous release.'
        );
    }

    public function test_the_build_version_script_writes_a_usable_version(): void
    {
        if (! function_exists('exec')) {
            $this->markTestSkipped('exec() is disabled.');
        }

        $root = base_path();
        $out = [];
        $status = 1;

        exec('bash '.escapeshellarg($root.'/deploy/write-build-version.sh')
            .' '.escapeshellarg($root).' 2>&1', $out, $status);

        $this->assertSame(0, $status, implode("\n", $out));

        $written = trim(file_get_contents(BuildVersion::path()));

        $this->assertMatchesRegularExpression('/^[A-Za-z0-9]{1,40}$/', $written);
        $this->assertSame($written, trim(implode("\n", $out)),
            'The script printed something other than what it wrote.');

        // And the app agrees with the file.
        BuildVersion::forget();
        $this->assertSame(strtolower($written), BuildVersion::current());
    }

    /** A checkout with no git history still deploys — with a worse id. */
    public function test_the_build_version_script_survives_without_git(): void
    {
        if (! function_exists('exec')) {
            $this->markTestSkipped('exec() is disabled.');
        }

        $dir = sys_get_temp_dir().'/nexo-build-'.uniqid();
        mkdir($dir.'/storage/app', 0777, true);

        try {
            $out = [];
            $status = 1;

            exec('bash '.escapeshellarg(base_path('deploy/write-build-version.sh'))
                .' '.escapeshellarg($dir).' 2>&1', $out, $status);

            $this->assertSame(0, $status, implode("\n", $out));
            $this->assertFileExists($dir.'/storage/app/build-version');

            $this->assertMatchesRegularExpression(
                '/^\d{14}$/',
                trim(file_get_contents($dir.'/storage/app/build-version')),
                'Without git the fallback is not a timestamp.'
            );
        } finally {
            @unlink($dir.'/storage/app/build-version');
            @rmdir($dir.'/storage/app');
            @rmdir($dir.'/storage');
            @rmdir($dir);
        }
    }

    /** It is per-server state. A commit must never carry one machine's id. */
    public function test_the_build_version_file_is_not_committed(): void
    {
        if (! function_exists('exec')) {
            $this->markTestSkipped('exec() is disabled.');
        }

        $out = [];
        $status = 1;

        exec('git -C '.escapeshellarg(base_path())
            .' check-ignore '.escapeshellarg(BuildVersion::path()).' 2>&1', $out, $status);

        $this->assertSame(0, $status,
            'storage/app/build-version is not git-ignored, so a deploy id could be committed.');
    }
}
