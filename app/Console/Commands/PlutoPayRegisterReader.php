<?php

namespace App\Console\Commands;

use App\Services\Pos\Payment\Exceptions\PlutoPayException;
use App\Services\Pos\Payment\PlutoPayClient;
use Illuminate\Console\Command;

/**
 * Creates (or reuses) a SIMULATED PlutoPay reader in TEST MODE and prints its
 * id for PLUTOPAY_READER_ID / PLUTOPAY_TERMINAL_ID.
 *
 * Verified against the live test API: a simulated device is created via
 * POST /v1/terminals with {name, location, simulated:true} — no registration_code
 * (that is only for pairing a physical reader). The id is a UUID used as BOTH
 * terminal_id and reader_id.
 *
 * By default it reuses an existing reader if one is present; pass --force to
 * always create a new one.
 */
class PlutoPayRegisterReader extends Command
{
    protected $signature = 'pos:plutopay-register-reader
        {--name=Nexo Simulated Reader : Display name for the reader}
        {--location=Main Store : Location label required by PlutoPay}
        {--force : Create a new reader even if one already exists}';

    protected $description = 'Create/reuse a simulated PlutoPay reader (TEST MODE) and print its id.';

    public function handle(PlutoPayClient $pluto): int
    {
        try {
            // Reuse an existing reader unless --force.
            if (!$this->option('force')) {
                $existing = $pluto->listReaders();
                if ($existing !== []) {
                    $first = $existing[0];
                    $this->info('Reusing existing reader (pass --force to create a new one):');
                    $this->printEnvHint((string) ($first['id'] ?? ''), (string) ($first['name'] ?? ''));
                    return self::SUCCESS;
                }
            }

            $device = $pluto->registerSimulatedTerminal(
                (string) $this->option('name'),
                (string) $this->option('location'),
            );
        } catch (PlutoPayException $e) {
            $this->error('PlutoPay error: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->info('Simulated reader created.');
        $this->printEnvHint($device['id'], $device['name']);

        return self::SUCCESS;
    }

    private function printEnvHint(string $id, string $name = ''): void
    {
        $this->line('  id   : ' . $id . ($name !== '' ? "  ({$name})" : ''));
        $this->newLine();
        $this->line('Put this id in BOTH .env keys, then run: php artisan config:clear');
        $this->line('  PLUTOPAY_READER_ID=' . $id);
        $this->line('  PLUTOPAY_TERMINAL_ID=' . $id);
    }
}
