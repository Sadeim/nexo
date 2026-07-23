<?php

namespace App\Console\Commands;

use App\Services\Pos\Payment\Exceptions\PlutoPayException;
use App\Services\Pos\Payment\PlutoPayClient;
use Illuminate\Console\Command;

/**
 * Lists PlutoPay terminals + readers so you can copy the correct
 * PLUTOPAY_TERMINAL_ID / PLUTOPAY_READER_ID into .env.
 *
 * TEST MODE only (the client's sk_test_ guard applies). Read-only.
 */
class PlutoPayDevices extends Command
{
    protected $signature = 'pos:plutopay-devices';

    protected $description = 'List PlutoPay terminals and readers (TEST MODE) to find their IDs.';

    public function handle(PlutoPayClient $pluto): int
    {
        try {
            $this->info('Terminals (GET /v1/terminals):');
            $this->dumpList($pluto->listTerminals());

            $this->newLine();
            $this->info('Readers (GET /v1/terminal/readers):');
            $this->dumpList($pluto->listReaders());
        } catch (PlutoPayException $e) {
            $this->error('PlutoPay error: ' . $e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function dumpList(array $rows): void
    {
        if ($rows === []) {
            $this->line('  (none — register a device first)');
            return;
        }

        foreach ($rows as $row) {
            $id   = $row['id']   ?? '(no id)';
            $name = $row['name'] ?? ($row['label'] ?? '');
            $this->line("  - {$id}  {$name}");
        }
    }
}
