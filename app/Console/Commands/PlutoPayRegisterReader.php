<?php

namespace App\Console\Commands;

use App\Services\Pos\Payment\Exceptions\PlutoPayException;
use App\Services\Pos\Payment\PlutoPayClient;
use Illuminate\Console\Command;

/**
 * Registers a (simulated, in TEST MODE) PlutoPay reader via POST /v1/terminals
 * and prints its id so you can put it in PLUTOPAY_READER_ID / PLUTOPAY_TERMINAL_ID.
 *
 * The registration_code for a simulated reader is shown in the PlutoPay
 * dashboard (Test mode -> Terminal/Readers -> Register). Pass it with --code.
 *
 * Example:
 *   php artisan pos:plutopay-register-reader --name="Nexo Sim" --code=SIMULATED_CODE
 */
class PlutoPayRegisterReader extends Command
{
    protected $signature = 'pos:plutopay-register-reader
        {--name=Nexo Simulated Reader : Display name for the reader}
        {--code= : registration_code from the PlutoPay dashboard (simulated reader)}';

    protected $description = 'Register a simulated PlutoPay reader (TEST MODE) and print its id.';

    public function handle(PlutoPayClient $pluto): int
    {
        $code = (string) $this->option('code');
        if ($code === '') {
            $this->error('Missing --code. Copy the simulated reader registration_code from the PlutoPay dashboard (Test mode → Terminal/Readers → Register).');
            return self::FAILURE;
        }

        try {
            $device = $pluto->registerTerminal((string) $this->option('name'), $code);
        } catch (PlutoPayException $e) {
            $this->error('PlutoPay error: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->info('Reader registered.');
        $this->line('  id   : ' . $device['id']);
        $this->line('  name : ' . $device['name']);
        $this->newLine();
        $this->line('Put this id in BOTH .env keys, then run: php artisan config:clear');
        $this->line('  PLUTOPAY_READER_ID=' . $device['id']);
        $this->line('  PLUTOPAY_TERMINAL_ID=' . $device['id']);

        return self::SUCCESS;
    }
}
