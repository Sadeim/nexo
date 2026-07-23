<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * DATA MIGRATION — copy legacy prices out of `services.description` into
 * the new `services.price` column.
 *
 * SAFETY RULES (agreed with the team):
 *   - Only copy when the description is an UNAMBIGUOUS positive number
 *     (matches ^\d+(\.\d{1,2})?$ after trimming, and value > 0).
 *   - Anything non-numeric ("10-20"), zero, negative, or otherwise unclear
 *     is LEFT AS NULL. We never guess a price.
 *   - `description` is never modified or dropped.
 *   - Idempotent: only rows whose price is still NULL are considered, so
 *     re-running (or migrate/rollback/migrate) will not clobber values a
 *     human has since entered.
 *
 * A human-readable report is printed to the console during `up()`.
 */
return new class extends Migration
{
    /**
     * Strict "clearly a positive price" test. Deliberately conservative.
     */
    private function looksLikeCleanPositivePrice(?string $raw): bool
    {
        if ($raw === null) {
            return false;
        }
        $value = trim($raw);

        // digits, optionally a decimal point with 1-2 digits. No signs, no
        // ranges, no letters, no thousands separators.
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $value)) {
            return false;
        }

        return (float) $value > 0;
    }

    public function up(): void
    {
        // Guard: if the price column is missing, do nothing.
        if (!DB::getSchemaBuilder()->hasColumn('services', 'price')) {
            return;
        }

        $services = DB::table('services')
            ->select('id', 'name', 'description', 'price')
            ->orderBy('id')
            ->get();

        $migrated = [];
        $skipped  = [];

        foreach ($services as $service) {
            // Idempotent: never touch a row that already has a price.
            if ($service->price !== null) {
                continue;
            }

            $raw = $service->description;

            if ($this->looksLikeCleanPositivePrice($raw)) {
                $value = round((float) trim($raw), 2);
                DB::table('services')->where('id', $service->id)->update([
                    'price' => $value,
                ]);
                $migrated[] = [
                    'id'    => $service->id,
                    'name'  => $service->name,
                    'value' => number_format($value, 2),
                    // Flag low values as "possibly test data" for human review,
                    // but do NOT blank them — they are still valid numbers.
                    'note'  => $value <= 10 ? 'LOW — verify (possible test data)' : '',
                ];
            } else {
                $skipped[] = [
                    'id'     => $service->id,
                    'name'   => $service->name,
                    'raw'    => $raw === null ? '(null)' : $raw,
                    'reason' => $raw === null || trim((string) $raw) === ''
                        ? 'empty'
                        : 'non-numeric / unclear',
                ];
            }
        }

        $this->printReport($migrated, $skipped);
    }

    /**
     * We only added data; nothing to reverse safely. Rolling this back must
     * not delete prices a human may have entered afterwards, so it is a no-op.
     */
    public function down(): void
    {
        // no-op on purpose
    }

    private function printReport(array $migrated, array $skipped): void
    {
        $line = str_repeat('=', 72);
        echo PHP_EOL . $line . PHP_EOL;
        echo 'SERVICE PRICE MIGRATION REPORT' . PHP_EOL;
        echo $line . PHP_EOL;

        echo PHP_EOL . 'MIGRATED (' . count($migrated) . '):' . PHP_EOL;
        if ($migrated) {
            echo sprintf("  %-4s %-28s %-10s %s", 'ID', 'NAME', 'PRICE $', 'NOTE') . PHP_EOL;
            foreach ($migrated as $r) {
                echo sprintf("  %-4s %-28s %-10s %s", $r['id'], mb_strimwidth($r['name'], 0, 27, ''), $r['value'], $r['note']) . PHP_EOL;
            }
        } else {
            echo '  (none)' . PHP_EOL;
        }

        echo PHP_EOL . 'LEFT NULL — NEEDS MANUAL REVIEW (' . count($skipped) . '):' . PHP_EOL;
        if ($skipped) {
            echo sprintf("  %-4s %-28s %-14s %s", 'ID', 'NAME', 'RAW VALUE', 'REASON') . PHP_EOL;
            foreach ($skipped as $r) {
                echo sprintf("  %-4s %-28s %-14s %s", $r['id'], mb_strimwidth($r['name'], 0, 27, ''), $r['raw'], $r['reason']) . PHP_EOL;
            }
        } else {
            echo '  (none)' . PHP_EOL;
        }

        echo PHP_EOL . $line . PHP_EOL;
        echo 'Services with price = NULL are NOT sellable in the POS until a' . PHP_EOL;
        echo 'human sets a real price (Admin > Services).' . PHP_EOL;
        echo $line . PHP_EOL . PHP_EOL;
    }
};
