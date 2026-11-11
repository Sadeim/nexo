<?php

namespace Tests\Feature\Portal;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The icon an installed portal wears on a home screen.
 *
 * Two things can go wrong here and both are invisible from a test that only
 * checks the files exist. A dark mark on a dark field is a blank square — the
 * shop's logo is evergreen on transparency, which is exactly the colour it is
 * about to be placed on. And a maskable icon whose mark runs past the safe
 * circle is an icon with its corners bitten off on any Android that crops to
 * a circle. So this file looks at the pixels.
 */
class PortalAppIconTest extends TestCase
{
    use RefreshDatabase;

    private const EVERGREEN = [40, 51, 38];
    private const IVORY = [248, 237, 210];

    /** @return array<string, int> */
    public static function sizes(): array
    {
        return [
            'icon-192.png' => 192,
            'icon-512.png' => 512,
            'icon-maskable-512.png' => 512,
            'apple-touch-icon.png' => 180,
        ];
    }

    private function path(string $name): string
    {
        return public_path('portal_assets/icons/'.$name);
    }

    private function image(string $name)
    {
        $im = @imagecreatefrompng($this->path($name));

        $this->assertNotFalse($im, "$name is not a readable PNG.");

        return $im;
    }

    private function rgb($im, int $x, int $y): array
    {
        $c = imagecolorat($im, $x, $y);

        return [($c >> 16) & 0xFF, ($c >> 8) & 0xFF, $c & 0xFF];
    }

    private function near(array $a, array $b, int $tolerance = 12): bool
    {
        return abs($a[0] - $b[0]) <= $tolerance
            && abs($a[1] - $b[1]) <= $tolerance
            && abs($a[2] - $b[2]) <= $tolerance;
    }

    // ---------- the files ----------

    public function test_every_icon_is_the_size_it_claims(): void
    {
        foreach (self::sizes() as $name => $size) {
            $this->assertFileExists($this->path($name));

            [$w, $h] = getimagesize($this->path($name));

            $this->assertSame([$size, $size], [$w, $h], "$name is $w x $h.");
        }
    }

    /**
     * The original is committed alongside what was made from it. Without it
     * the next person regenerating these has nothing to regenerate from, and
     * the shop's own logo lives only in an uploads folder.
     */
    public function test_the_source_mark_is_committed(): void
    {
        $this->assertFileExists($this->path('icon-source.png'));
        $this->assertFileExists(base_path('deploy/make-icons.py'));
    }

    // ---------- the pixels ----------

    /**
     * The field is painted, every time.
     *
     * The source mark is evergreen on transparency. Composited onto an
     * evergreen background as-is it would be a square of nothing — and iOS,
     * which does not honour transparency at all, would turn any unpainted
     * corner black.
     */
    public function test_every_icon_is_painted_on_the_brand_background(): void
    {
        foreach (self::sizes() as $name => $size) {
            $im = $this->image($name);

            foreach ([[0, 0], [$size - 1, 0], [0, $size - 1], [$size - 1, $size - 1]] as [$x, $y]) {
                $this->assertTrue($this->near($this->rgb($im, $x, $y), self::EVERGREEN),
                    "$name has a corner that is not the brand background.");
            }

            imagedestroy($im);
        }
    }

    /** And there is a mark on it, in the colour the portal is written in. */
    public function test_every_icon_carries_the_mark_in_ivory(): void
    {
        foreach (self::sizes() as $name => $size) {
            $im = $this->image($name);

            $ivory = 0;

            for ($y = 0; $y < $size; $y += 2) {
                for ($x = 0; $x < $size; $x += 2) {
                    if ($this->near($this->rgb($im, $x, $y), self::IVORY, 24)) {
                        $ivory++;
                    }
                }
            }

            $sampled = ceil($size / 2) ** 2;

            // A real mark covers a good part of the square; a stray
            // anti-aliased pixel or two does not.
            $this->assertGreaterThan($sampled * 0.08, $ivory,
                "$name has almost no mark on it.");

            $this->assertLessThan($sampled * 0.6, $ivory,
                "$name is mostly mark — there is no field left around it.");

            imagedestroy($im);
        }
    }

    /**
     * The maskable one stays inside the safe circle.
     *
     * Android may crop a maskable icon to any shape it likes within a circle
     * of 80% of the width. Anything the mark puts outside that is a corner
     * the launcher is free to bite off.
     */
    public function test_the_maskable_icon_respects_the_safe_zone(): void
    {
        $size = 512;
        $im = $this->image('icon-maskable-512.png');

        $centre = $size / 2;
        $safe = $size * 0.4;          // radius: 80% of the width, halved
        $outside = 0;

        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                if ($this->near($this->rgb($im, $x, $y), self::EVERGREEN, 20)) {
                    continue;
                }

                $distance = sqrt(($x - $centre) ** 2 + ($y - $centre) ** 2);

                if ($distance > $safe) {
                    $outside++;
                }
            }
        }

        imagedestroy($im);

        $this->assertSame(0, $outside,
            "$outside pixels of the maskable mark fall outside the 80% safe circle.");
    }

    /**
     * The "any" icons are NOT padded to the maskable's inset — that padding
     * exists to survive a crop, and an icon that is never cropped and wears
     * it anyway is a small logo adrift in a big square.
     */
    public function test_the_plain_icons_are_not_padded_like_the_maskable_one(): void
    {
        $plain = $this->markBox('icon-512.png', 512);
        $maskable = $this->markBox('icon-maskable-512.png', 512);

        $this->assertGreaterThan(
            ($maskable[3] - $maskable[1]) * 1.1,
            $plain[3] - $plain[1],
            'The plain 512 icon is padded as if it were going to be cropped.'
        );
    }

    /**
     * The icons are made from the committed source, not drawn by hand.
     *
     * The mark's proportions are its fingerprint: scale it and centre it and
     * the ratio survives. If someone replaces icon-source.png and forgets to
     * run deploy/make-icons.py, these stop agreeing.
     */
    public function test_the_icons_are_made_from_the_committed_source(): void
    {
        $source = $this->sourceMarkRatio();

        foreach (['icon-192.png' => 192, 'icon-512.png' => 512, 'icon-maskable-512.png' => 512] as $name => $size) {
            [$left, $top, $right, $bottom] = $this->markBox($name, $size);

            $ratio = ($right - $left) / ($bottom - $top);

            $this->assertEqualsWithDelta($source, $ratio, 0.04,
                "$name is not the shape of the committed source mark.");
        }
    }

    /** The bounding box of everything that is not the background. */
    private function markBox(string $name, int $size): array
    {
        $im = $this->image($name);

        $left = $size;
        $top = $size;
        $right = 0;
        $bottom = 0;

        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                if ($this->near($this->rgb($im, $x, $y), self::EVERGREEN, 20)) {
                    continue;
                }

                $left = min($left, $x);
                $right = max($right, $x);
                $top = min($top, $y);
                $bottom = max($bottom, $y);
            }
        }

        imagedestroy($im);

        $this->assertLessThan($right, $left, "$name has no mark to measure.");

        return [$left, $top, $right, $bottom];
    }

    /** Width over height of the opaque part of the committed source. */
    private function sourceMarkRatio(): float
    {
        $im = $this->image('icon-source.png');

        $w = imagesx($im);
        $h = imagesy($im);

        $left = $w;
        $top = $h;
        $right = 0;
        $bottom = 0;

        // One column short on the right: the uploaded file is clipped there
        // and carries a sliver of the next letter of the wordmark it was cut
        // out of. deploy/make-icons.py drops the same column.
        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w - 1; $x++) {
                if ((imagecolorat($im, $x, $y) >> 24) > 100) {
                    continue; // fully transparent
                }

                $left = min($left, $x);
                $right = max($right, $x);
                $top = min($top, $y);
                $bottom = max($bottom, $y);
            }
        }

        imagedestroy($im);

        return ($right - $left) / ($bottom - $top);
    }

    // ---------- the manifest ----------

    public function test_the_manifest_names_the_shop(): void
    {
        $manifest = $this->get(route('portal.manifest'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/manifest+json; charset=utf-8')
            ->json();

        $this->assertSame('Nexo Barbershop', $manifest['name']);
        $this->assertSame('Nexo', $manifest['short_name']);
        $this->assertSame('standalone', $manifest['display']);
    }

    /** One brand: the manifest's colours are the stylesheet's token. */
    public function test_the_manifest_is_evergreen(): void
    {
        $css = file_get_contents(public_path('portal_assets/css/portal.css'));

        preg_match('/--evergreen:\s*(#[0-9A-Fa-f]{6});/', $css, $m);

        $evergreen = strtolower($m[1] ?? '');

        $this->assertNotSame('', $evergreen, 'The stylesheet has no --evergreen token.');

        $manifest = $this->get(route('portal.manifest'))->json();

        $this->assertSame($evergreen, strtolower($manifest['theme_color']));
        $this->assertSame($evergreen, strtolower($manifest['background_color']));

        $this->assertStringContainsString('content="'.$m[1].'"',
            $this->get(route('portal.login'))->assertOk()->getContent(),
            'The portal head serves a theme-color that is not the brand one.');
    }

    /**
     * Every icon URL in the manifest carries the fingerprint of the file it
     * points at.
     *
     * Without it a phone keeps the icon it first fetched for a week — this
     * host sends seven days of Cache-Control on static files with no
     * revalidation — and an installed app keeps it until the manifest says
     * otherwise.
     */
    public function test_the_manifest_fingerprints_its_icons(): void
    {
        $manifest = $this->get(route('portal.manifest'))->json();

        $this->assertNotEmpty($manifest['icons']);

        foreach ($manifest['icons'] as $icon) {
            $parts = parse_url($icon['src']);

            parse_str($parts['query'] ?? '', $query);

            $file = public_path(ltrim($parts['path'], '/'));

            $this->assertFileExists($file, $icon['src'].' points at nothing.');

            $this->assertSame(substr(md5_file($file), 0, 8), $query['v'] ?? null,
                $icon['src'].' is not fingerprinted with its own contents.');
        }

        // The three a manifest needs: two plain, one maskable.
        $purposes = array_column($manifest['icons'], 'purpose');

        $this->assertContains('any', $purposes);
        $this->assertContains('maskable', $purposes);
    }

    public function test_the_touch_icon_is_linked_and_fingerprinted(): void
    {
        $body = $this->get(route('portal.login'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression(
            '#rel="apple-touch-icon" href="[^"]*/portal_assets/icons/apple-touch-icon\.png\?v=([0-9a-f]{8})"#',
            $body
        );

        preg_match('#apple-touch-icon\.png\?v=([0-9a-f]{8})#', $body, $m);

        $this->assertSame(
            substr(md5_file($this->path('apple-touch-icon.png')), 0, 8),
            $m[1],
            'The touch icon link does not match the file on disk.'
        );
    }

    public function test_the_page_links_the_manifest_route(): void
    {
        $this->get(route('portal.login'))
            ->assertOk()
            ->assertSee('rel="manifest" href="'.route('portal.manifest').'"', false);
    }

    /** The desk is not installable and has neither. */
    public function test_the_desk_has_no_manifest(): void
    {
        $this->get('/work/manifest.webmanifest')->assertNotFound();

        $this->get(route('work.login'))
            ->assertOk()
            ->assertDontSee('rel="manifest"', false);
    }
}
