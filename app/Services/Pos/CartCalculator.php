<?php

namespace App\Services\Pos;

use App\Services\Pos\Exceptions\InvalidPriceException;

/**
 * The single source of truth for POS money math.
 *
 * Design rules:
 *   - Money is handled as INTEGER CENTS internally. We never add or multiply
 *     floats, so there are no floating-point rounding surprises.
 *   - Every price that enters the cart is validated and normalized here.
 *   - The server ALWAYS recomputes totals from validated inputs; it never
 *     trusts a subtotal/total sent by the browser.
 *
 * This class is intentionally free of framework/DB dependencies so it can be
 * unit-tested in isolation. Controllers resolve service prices from the DB and
 * hand plain values to this calculator.
 */
class CartCalculator
{
    /**
     * Upper sanity bound for a single unit price, in dollars.
     * Anything above this is rejected as unreasonable.
     */
    public const MAX_UNIT_PRICE = 1_000_000.0;

    /** Maximum quantity for a single line. */
    public const MAX_QUANTITY = 10_000;

    /**
     * Validate and normalize a user-supplied price into integer cents.
     *
     * Accepts: positive numeric strings/numbers like "150", "9.99", 12, 12.5.
     * Rejects: null, empty, non-numeric ("10-20", "abc"), zero, negative,
     *          NaN/INF, and values above MAX_UNIT_PRICE.
     *
     * @throws InvalidPriceException
     */
    public function normalizePriceToCents(mixed $price): int
    {
        return $this->normalize($price, allowZero: false);
    }

    /**
     * Same as normalizePriceToCents but permits an explicit zero.
     *
     * Used ONLY for a per-line custom (override) price that the cashier set
     * deliberately (a comp/free line), after confirming in the UI. Negative,
     * non-numeric and over-max values are still rejected.
     *
     * @throws InvalidPriceException
     */
    public function normalizeCustomPriceToCents(mixed $price): int
    {
        return $this->normalize($price, allowZero: true);
    }

    /**
     * Shared price normalization. Money never touches float arithmetic beyond
     * this single, rounded conversion to integer cents.
     *
     * @throws InvalidPriceException
     */
    private function normalize(mixed $price, bool $allowZero): int
    {
        if ($price === null || $price === '') {
            throw new InvalidPriceException('Price is required.');
        }

        if (is_string($price)) {
            $price = trim($price);
            // Strict decimal: digits with optional 1-2 decimal places. No signs,
            // ranges, currency symbols, or thousands separators.
            if (!preg_match('/^\d+(\.\d{1,2})?$/', $price)) {
                throw new InvalidPriceException('Price must be a valid number.');
            }
        } elseif (is_int($price) || is_float($price)) {
            if (!is_finite((float) $price)) {
                throw new InvalidPriceException('Price must be a finite number.');
            }
        } else {
            throw new InvalidPriceException('Price must be a valid number.');
        }

        $value = (float) $price;

        if ($value < 0) {
            throw new InvalidPriceException('Price cannot be negative.');
        }

        if (!$allowZero && $value == 0.0) {
            throw new InvalidPriceException('Price must be greater than zero.');
        }

        if ($value > self::MAX_UNIT_PRICE) {
            throw new InvalidPriceException('Price exceeds the maximum allowed value.');
        }

        // Convert to cents with correct rounding, no accumulated float error.
        return (int) round($value * 100);
    }

    /**
     * Validate a quantity: a positive integer within bounds.
     *
     * @throws InvalidPriceException
     */
    public function normalizeQuantity(mixed $quantity): int
    {
        if (is_string($quantity)) {
            $quantity = trim($quantity);
            if (!preg_match('/^\d+$/', $quantity)) {
                throw new InvalidPriceException('Quantity must be a whole number.');
            }
        } elseif (!is_int($quantity)) {
            if (is_float($quantity) && floor($quantity) === $quantity) {
                $quantity = (int) $quantity;
            } else {
                throw new InvalidPriceException('Quantity must be a whole number.');
            }
        }

        $quantity = (int) $quantity;

        if ($quantity < 1) {
            throw new InvalidPriceException('Quantity must be at least 1.');
        }

        if ($quantity > self::MAX_QUANTITY) {
            throw new InvalidPriceException('Quantity exceeds the maximum allowed value.');
        }

        return $quantity;
    }

    /**
     * Line total in cents = unit_price_cents * quantity.
     */
    public function lineTotalCents(int $unitPriceCents, int $quantity): int
    {
        return $unitPriceCents * $quantity;
    }

    /**
     * Compute the whole cart from raw line inputs.
     *
     * Each input line is: ['unit_price' => mixed, 'quantity' => mixed, ...extra]
     * Extra keys (e.g. service_id, service_name, original_price) are passed
     * through untouched so callers can persist snapshots.
     *
     * @param  array<int, array<string, mixed>>  $lines
     * @return array{
     *     items: array<int, array<string,mixed>>,
     *     subtotal_cents: int,
     *     total_cents: int,
     *     subtotal: string,
     *     total: string
     * }
     * @throws InvalidPriceException
     */
    public function calculate(array $lines): array
    {
        if (empty($lines)) {
            throw new InvalidPriceException('Cart is empty.');
        }

        $items = [];
        $subtotalCents = 0;

        foreach ($lines as $line) {
            // A line flagged allow_zero carries a deliberate custom price that
            // may be 0 (comp/free). Default lines must be strictly positive.
            $unitCents = ($line['allow_zero'] ?? false)
                ? $this->normalizeCustomPriceToCents($line['unit_price'] ?? null)
                : $this->normalizePriceToCents($line['unit_price'] ?? null);
            $quantity  = $this->normalizeQuantity($line['quantity'] ?? 1);
            $lineCents = $this->lineTotalCents($unitCents, $quantity);

            $subtotalCents += $lineCents;

            $items[] = array_merge($line, [
                'unit_price'      => $this->centsToDecimalString($unitCents),
                'quantity'        => $quantity,
                'line_total'      => $this->centsToDecimalString($lineCents),
                'unit_price_cents' => $unitCents,
                'line_total_cents' => $lineCents,
            ]);
        }

        // No tax/discount in this phase, so total == subtotal. Kept separate so
        // future tax/discount logic has one obvious place to live.
        $totalCents = $subtotalCents;

        return [
            'items'          => $items,
            'subtotal_cents' => $subtotalCents,
            'total_cents'    => $totalCents,
            'subtotal'       => $this->centsToDecimalString($subtotalCents),
            'total'          => $this->centsToDecimalString($totalCents),
        ];
    }

    /**
     * Format integer cents as a 2-decimal string suitable for DECIMAL columns
     * and display (e.g. 12345 -> "123.45").
     */
    public function centsToDecimalString(int $cents): string
    {
        $sign = $cents < 0 ? '-' : '';
        $abs = abs($cents);
        $dollars = intdiv($abs, 100);
        $remainder = $abs % 100;

        return $sign . $dollars . '.' . str_pad((string) $remainder, 2, '0', STR_PAD_LEFT);
    }
}
