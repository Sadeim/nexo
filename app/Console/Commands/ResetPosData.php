<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\EmployeePayment;
use App\Models\PosApiWebhookEvent;
use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Models\PosService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Wipe the POS transaction history so the shop can start from a clean slate,
 * WITHOUT touching the catalogue or the staff list.
 *
 * Deleted:  pos_orders, pos_order_items, employee_payments,
 *           pos_api_webhook_events
 * Kept:     employees (names + commission), pos_services (services + prices),
 *           pos_api_tokens (tablets stay signed in), settings (card fee, ...)
 *
 * Irreversible. Always prints what it is about to remove and asks first;
 * --dry-run shows the counts and stops.
 */
class ResetPosData extends Command
{
    protected $signature = 'nexo-pos:reset-data
        {--dry-run : Show what would be deleted and stop}
        {--force : Skip the confirmation prompt (for non-interactive runs)}';

    protected $description = 'Delete all POS sales history. Keeps employees, services and prices.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $counts = [
            ['pos_order_items',        PosOrderItem::count()],
            ['pos_orders',             PosOrder::count()],
            ['employee_payments',      EmployeePayment::count()],
            ['pos_api_webhook_events', PosApiWebhookEvent::count()],
        ];

        $kept = [
            ['employees',    Employee::count()],
            ['pos_services', PosService::count()],
        ];

        $this->newLine();
        $this->error('  WILL BE DELETED  ');
        $this->table(['Table', 'Rows'], $counts);

        $this->info('  WILL BE KEPT  ');
        $this->table(['Table', 'Rows'], $kept);
        $this->line('  Also kept: pos_api_tokens (tablets stay signed in), settings (card fee).');
        $this->newLine();

        $totalToDelete = array_sum(array_column($counts, 1));

        if ($totalToDelete === 0) {
            $this->info('Nothing to delete — the POS history is already empty.');
            return self::SUCCESS;
        }

        if ($dryRun) {
            $this->comment('Dry run — nothing written.');
            return self::SUCCESS;
        }

        if (!$this->option('force')
            && !$this->confirm("Permanently delete {$totalToDelete} row(s)? This cannot be undone.", false)) {
            $this->comment('Aborted — nothing was deleted.');
            return self::SUCCESS;
        }

        // Children before parents so foreign keys stay satisfied.
        DB::transaction(function () {
            PosOrderItem::query()->delete();
            PosOrder::query()->delete();
            EmployeePayment::query()->delete();
            PosApiWebhookEvent::query()->delete();
        });

        // Start ids back at 1 so the fresh history reads cleanly.
        foreach (['pos_order_items', 'pos_orders', 'employee_payments', 'pos_api_webhook_events'] as $table) {
            if (Schema::hasTable($table)) {
                try {
                    DB::statement("ALTER TABLE `{$table}` AUTO_INCREMENT = 1");
                } catch (\Throwable $e) {
                    // Not fatal — the data is gone either way.
                    $this->warn("Could not reset AUTO_INCREMENT on {$table}: {$e->getMessage()}");
                }
            }
        }

        $this->newLine();
        $this->info("Deleted {$totalToDelete} row(s). Employees and services are untouched.");

        return self::SUCCESS;
    }
}
