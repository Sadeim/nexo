<?php

namespace Tests\Feature;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

/**
 * What the dashboard tells a user when something blew up.
 *
 * The check used to be `env('APP_ENV') == 'production'`. Production caches
 * its config, and once config is cached Laravel never loads .env, so env()
 * outside a config file returns NULL — the comparison was always false and
 * the raw exception message went to the browser whatever APP_ENV said.
 * Confirmed on the live site on 10 Nov 2026: env('APP_ENV') was NULL while
 * config('app.env') was 'local'.
 */
class ProductionErrorMessageTest extends TestCase
{
    private function subject(): Controller
    {
        return new class extends Controller {};
    }

    public function test_production_hides_the_exception_from_the_browser(): void
    {
        config(['app.env' => 'production']);

        $message = $this->subject()->exMessage(
            new \RuntimeException('SQLSTATE[42S22]: Column not found: secret_column')
        );

        $this->assertSame(__('admin.form.some_errors'), $message);
        $this->assertStringNotContainsString('SQLSTATE', $message);
        $this->assertStringNotContainsString('secret_column', $message);
    }

    /**
     * Hidden from the browser, kept in the log. Several callers catch, call
     * this and return without logging anything themselves, so without this
     * the fix would trade a leaked message for no record at all.
     */
    public function test_production_still_records_what_happened(): void
    {
        config(['app.env' => 'production']);

        Log::spy();

        $this->subject()->exMessage(new \RuntimeException('the real reason'));

        Log::shouldHaveReceived('error')
            ->withArgs(fn ($message, $context = []) => str_contains($message, 'the real reason'))
            ->once();
    }

    public function test_developers_still_see_the_real_message(): void
    {
        config(['app.env' => 'local']);

        $this->assertSame(
            'the real reason',
            $this->subject()->exMessage(new \RuntimeException('the real reason'))
        );
    }

    /**
     * The regression itself: cached config means no .env, and this must not
     * go back to reading env() at runtime.
     */
    public function test_it_does_not_read_the_environment_through_env(): void
    {
        $source = file_get_contents(app_path('Http/Controllers/Controller.php'));

        $body = substr($source, strpos($source, 'function exMessage'));
        $body = substr($body, 0, strpos($body, "\n    }"));

        $this->assertStringNotContainsString('env(', $body,
            'exMessage() reads env() again, which is NULL under a cached config.');
    }
}
