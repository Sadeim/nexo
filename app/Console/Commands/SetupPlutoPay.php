<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * One-shot helper that writes the Nexo POS PlutoPay credentials into .env
 * idempotently — replaces any existing key, appends any missing one.
 *
 * Usage:
 *   php artisan nexo-pos:setup-plutopay \
 *     --secret=sk_test_... \
 *     --pk=pk_test_... \
 *     --webhook=whsec_... \
 *     --terminal=<uuid> \
 *     --reader=tmr_...
 */
class SetupPlutoPay extends Command
{
    protected $signature = 'nexo-pos:setup-plutopay
        {--secret= : Secret key (sk_test_... or sk_live_...)}
        {--pk= : Publishable key (pk_test_... / pk_live_...)}
        {--webhook= : Webhook signing secret (whsec_...)}
        {--terminal= : PlutoPay terminal UUID (id from GET /v1/terminal/readers)}
        {--reader= : Processor reader id (tmr_... — the physical reader)}
        {--base-url=https://plutopayus.com/api : PlutoPay API base URL}
        {--currency=usd : ISO currency code}
        {--no-cache : Skip php artisan config:cache after writing}';

    protected $description = 'Write Nexo POS PlutoPay credentials into .env idempotently.';

    public function handle(): int
    {
        $envPath = base_path('.env');
        if (!file_exists($envPath)) {
            $this->error('.env not found at ' . $envPath);
            return self::FAILURE;
        }

        $vars = array_filter([
            'NEXO_POS_PLUTOPAY_BASE_URL'        => $this->option('base-url'),
            'NEXO_POS_PLUTOPAY_SECRET_KEY'      => $this->option('secret'),
            'NEXO_POS_PLUTOPAY_PUBLISHABLE_KEY' => $this->option('pk'),
            'NEXO_POS_PLUTOPAY_WEBHOOK_SECRET'  => $this->option('webhook'),
            'NEXO_POS_PLUTOPAY_TERMINAL_ID'     => $this->option('terminal'),
            'NEXO_POS_PLUTOPAY_READER_ID'       => $this->option('reader'),
            'NEXO_POS_PLUTOPAY_CURRENCY'        => $this->option('currency'),
        ], fn($v) => $v !== null && $v !== '');

        if (empty($vars)) {
            $this->error('Pass at least one option, e.g. --secret=sk_test_...');
            return self::FAILURE;
        }

        // Sanity check: refuse to write a non-test key while we're locked to TEST MODE.
        if (isset($vars['NEXO_POS_PLUTOPAY_SECRET_KEY'])
            && !str_starts_with($vars['NEXO_POS_PLUTOPAY_SECRET_KEY'], 'sk_test_')
            && !str_starts_with($vars['NEXO_POS_PLUTOPAY_SECRET_KEY'], 'sk_live_')) {
            $this->error('Secret key must start with sk_test_ or sk_live_.');
            return self::FAILURE;
        }

        $env = file_get_contents($envPath);
        $appended = [];

        foreach ($vars as $key => $value) {
            $line = $key . '=' . $this->quoteIfNeeded($value);

            $pattern = '/^' . preg_quote($key, '/') . '=.*$/m';
            if (preg_match($pattern, $env)) {
                $env = preg_replace($pattern, $line, $env);
            } else {
                $appended[] = $line;
            }
        }

        if (!empty($appended)) {
            $env = rtrim($env, "\n") . "\n\n# Nexo POS PlutoPay (added by nexo-pos:setup-plutopay)\n"
                . implode("\n", $appended) . "\n";
        }

        file_put_contents($envPath, $env);

        $this->info('Wrote ' . count($vars) . ' variable(s) to .env');
        foreach ($vars as $k => $v) {
            $shown = str_contains($k, 'SECRET') || str_contains($k, 'WEBHOOK')
                ? substr($v, 0, 12) . '…'
                : $v;
            $this->line("  {$k} = {$shown}");
        }

        if (!$this->option('no-cache')) {
            $this->line('');
            $this->info('Refreshing config cache...');
            $this->call('config:cache');
        } else {
            $this->warn('Remember to run: php artisan config:cache');
        }

        return self::SUCCESS;
    }

    private function quoteIfNeeded(string $value): string
    {
        // Values with spaces or # must be double-quoted so Laravel's dotenv parser
        // keeps them intact. Everything else can be written bare.
        if (preg_match('/[\s#"\']/', $value)) {
            return '"' . addcslashes($value, '"\\') . '"';
        }
        return $value;
    }
}
