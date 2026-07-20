<?php

namespace Tests\Unit\Pos;

use App\Services\Pos\CartCalculator;
use App\Services\Pos\Exceptions\InvalidPriceException;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the sensitive money logic. This is the only automated coverage the
 * team asked for, and it is deliberately thorough because it handles money.
 *
 * Pure unit test (extends PHPUnit TestCase, no DB/framework boot).
 */
class CartCalculatorTest extends TestCase
{
    private CartCalculator $calc;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calc = new CartCalculator();
    }

    /* ------------------------------------------------------------------ *
     *  Price normalization
     * ------------------------------------------------------------------ */

    public function test_valid_integer_price_becomes_cents(): void
    {
        $this->assertSame(20000, $this->calc->normalizePriceToCents('200'));
        $this->assertSame(15000, $this->calc->normalizePriceToCents(150));
    }

    public function test_valid_decimal_price_becomes_cents(): void
    {
        $this->assertSame(999, $this->calc->normalizePriceToCents('9.99'));
        $this->assertSame(1250, $this->calc->normalizePriceToCents(12.5));
    }

    public function test_zero_price_is_rejected(): void
    {
        $this->expectException(InvalidPriceException::class);
        $this->calc->normalizePriceToCents('0');
    }

    public function test_negative_price_is_rejected(): void
    {
        $this->expectException(InvalidPriceException::class);
        $this->calc->normalizePriceToCents('-50');
    }

    public function test_non_numeric_price_is_rejected(): void
    {
        $this->expectException(InvalidPriceException::class);
        $this->calc->normalizePriceToCents('10-20');
    }

    public function test_alphabetic_price_is_rejected(): void
    {
        $this->expectException(InvalidPriceException::class);
        $this->calc->normalizePriceToCents('abc');
    }

    public function test_null_price_is_rejected(): void
    {
        $this->expectException(InvalidPriceException::class);
        $this->calc->normalizePriceToCents(null);
    }

    public function test_empty_string_price_is_rejected(): void
    {
        $this->expectException(InvalidPriceException::class);
        $this->calc->normalizePriceToCents('');
    }

    public function test_unreasonably_large_price_is_rejected(): void
    {
        $this->expectException(InvalidPriceException::class);
        $this->calc->normalizePriceToCents('2000000');
    }

    public function test_price_with_too_many_decimals_is_rejected(): void
    {
        // Enforce cent precision at the boundary; callers must send 2dp max.
        $this->expectException(InvalidPriceException::class);
        $this->calc->normalizePriceToCents('9.999');
    }

    public function test_whitespace_around_price_is_trimmed(): void
    {
        $this->assertSame(20000, $this->calc->normalizePriceToCents('  200  '));
    }

    /* ---- custom price (override) normalization: zero allowed ---- */

    public function test_custom_price_allows_explicit_zero(): void
    {
        // A deliberate comp/free line is permitted for a custom price only.
        $this->assertSame(0, $this->calc->normalizeCustomPriceToCents('0'));
        $this->assertSame(0, $this->calc->normalizeCustomPriceToCents(0));
    }

    public function test_custom_price_still_rejects_negative(): void
    {
        $this->expectException(InvalidPriceException::class);
        $this->calc->normalizeCustomPriceToCents('-1');
    }

    public function test_custom_price_still_rejects_non_numeric(): void
    {
        $this->expectException(InvalidPriceException::class);
        $this->calc->normalizeCustomPriceToCents('abc');
    }

    public function test_custom_price_still_rejects_too_large(): void
    {
        $this->expectException(InvalidPriceException::class);
        $this->calc->normalizeCustomPriceToCents('2000000');
    }

    public function test_custom_price_accepts_normal_value(): void
    {
        $this->assertSame(17550, $this->calc->normalizeCustomPriceToCents('175.50'));
    }

    /* ------------------------------------------------------------------ *
     *  Quantity normalization
     * ------------------------------------------------------------------ */

    public function test_valid_quantity(): void
    {
        $this->assertSame(3, $this->calc->normalizeQuantity('3'));
        $this->assertSame(1, $this->calc->normalizeQuantity(1));
    }

    public function test_zero_quantity_is_rejected(): void
    {
        $this->expectException(InvalidPriceException::class);
        $this->calc->normalizeQuantity(0);
    }

    public function test_negative_quantity_is_rejected(): void
    {
        $this->expectException(InvalidPriceException::class);
        $this->calc->normalizeQuantity(-2);
    }

    public function test_fractional_quantity_is_rejected(): void
    {
        $this->expectException(InvalidPriceException::class);
        $this->calc->normalizeQuantity('1.5');
    }

    /* ------------------------------------------------------------------ *
     *  Line + cart totals
     * ------------------------------------------------------------------ */

    public function test_line_total_multiplies_price_by_quantity(): void
    {
        // 9.99 * 3 = 29.97, exact via integer cents (no float drift).
        $this->assertSame(2997, $this->calc->lineTotalCents(999, 3));
    }

    public function test_custom_price_is_used_over_original(): void
    {
        // Single line where the cashier overrode the price to 175.50.
        $result = $this->calc->calculate([
            ['service_id' => 1, 'unit_price' => '175.50', 'quantity' => 2, 'original_price' => '200.00'],
        ]);

        $this->assertSame('175.50', $result['items'][0]['unit_price']);
        $this->assertSame('351.00', $result['items'][0]['line_total']);
        $this->assertSame('351.00', $result['total']);
        // original_price snapshot is passed through untouched.
        $this->assertSame('200.00', $result['items'][0]['original_price']);
    }

    public function test_subtotal_and_total_sum_all_lines(): void
    {
        $result = $this->calc->calculate([
            ['unit_price' => '200', 'quantity' => 1],   // 200.00
            ['unit_price' => '9.99', 'quantity' => 3],  // 29.97
            ['unit_price' => '50', 'quantity' => 2],    // 100.00
        ]);

        $this->assertSame('329.97', $result['subtotal']);
        $this->assertSame('329.97', $result['total']);
        $this->assertSame(3, count($result['items']));
    }

    public function test_totals_are_exact_with_tricky_decimals(): void
    {
        // 0.10 * 3 = 0.30 exactly. Classic float trap (0.1+0.1+0.1 != 0.3).
        $result = $this->calc->calculate([
            ['unit_price' => '0.10', 'quantity' => 3],
        ]);
        $this->assertSame('0.30', $result['total']);

        // Mixed cart.
        $result = $this->calc->calculate([
            ['unit_price' => '200', 'quantity' => 1],   // 200.00
            ['unit_price' => '9.99', 'quantity' => 3],  // 29.97
            ['unit_price' => '0.05', 'quantity' => 2],  // 0.10
        ]);
        $this->assertSame('230.07', $result['subtotal']);
        $this->assertSame('230.07', $result['total']);
    }

    public function test_custom_zero_line_is_allowed_via_allow_zero_flag(): void
    {
        // A comp line ($0.00) is only valid when flagged as a custom price.
        $result = $this->calc->calculate([
            ['unit_price' => '200', 'quantity' => 1],                        // 200.00
            ['unit_price' => '0', 'quantity' => 1, 'allow_zero' => true],    // 0.00 comp
        ]);
        $this->assertSame('0.00', $result['items'][1]['line_total']);
        $this->assertSame('200.00', $result['total']);
    }

    public function test_zero_line_without_flag_is_rejected(): void
    {
        // Without the custom-price flag, a 0 unit price is invalid.
        $this->expectException(InvalidPriceException::class);
        $this->calc->calculate([
            ['unit_price' => '0', 'quantity' => 1],
        ]);
    }

    public function test_empty_cart_is_rejected(): void
    {
        $this->expectException(InvalidPriceException::class);
        $this->calc->calculate([]);
    }

    public function test_cart_with_invalid_line_price_is_rejected(): void
    {
        $this->expectException(InvalidPriceException::class);
        $this->calc->calculate([
            ['unit_price' => '10-20', 'quantity' => 1],
        ]);
    }

    /* ------------------------------------------------------------------ *
     *  NULL-priced service handling (business rule)
     * ------------------------------------------------------------------ */

    public function test_null_priced_service_cannot_enter_the_cart(): void
    {
        // A service with price = NULL must never be sellable: attempting to add
        // it (unit_price null) is rejected by the calculator.
        $this->expectException(InvalidPriceException::class);
        $this->calc->calculate([
            ['service_id' => 22, 'unit_price' => null, 'quantity' => 1],
        ]);
    }

    /* ------------------------------------------------------------------ *
     *  Formatting helper
     * ------------------------------------------------------------------ */

    public function test_cents_to_decimal_string(): void
    {
        $this->assertSame('0.00', $this->calc->centsToDecimalString(0));
        $this->assertSame('0.05', $this->calc->centsToDecimalString(5));
        $this->assertSame('1.00', $this->calc->centsToDecimalString(100));
        $this->assertSame('123.45', $this->calc->centsToDecimalString(12345));
    }
}
