<?php

namespace App\Support;

use App\Models\Setting;

/**
 * Typed accessors for the POS knobs stored in the generic `settings` table.
 */
class PosSettings
{
    public const CARD_FEE_KEY = 'pos_card_fee';

    /**
     * Flat surcharge (in currency) ADDED to each card sale. The customer pays
     * subtotal + this, so the processor is charged the higher amount too.
     * 0 disables the surcharge. Cash/Zelle never carry it.
     */
    public static function cardFee(): float
    {
        $raw = Setting::where('key', self::CARD_FEE_KEY)->value('value');

        return max(0, round((float) $raw, 2));
    }
}
