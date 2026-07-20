<?php

namespace App\Services\Pos;

use App\Models\Service;
use App\Services\Pos\Exceptions\InvalidPriceException;

/**
 * Turns raw browser cart items into an authoritative, server-priced cart.
 *
 * The server NEVER trusts a price/total from the browser: every unit price is
 * re-resolved from the DB (or a deliberate custom price is re-validated) and
 * the totals are recomputed by CartCalculator. Shared by the cash and card
 * checkout paths so both compute money identically.
 */
class CartResolver
{
    public function __construct(private CartCalculator $calculator)
    {
    }

    /**
     * @param  array<int,array{service_id:int,quantity:mixed,custom_price?:mixed}>  $inputItems
     * @return array{items:array<int,array<string,mixed>>,subtotal_cents:int,total_cents:int,subtotal:string,total:string}
     *
     * @throws InvalidPriceException  with a user-safe message on any bad input.
     */
    public function resolve(array $inputItems): array
    {
        $serviceIds = collect($inputItems)->pluck('service_id')->unique()->all();
        $services = Service::whereIn('id', $serviceIds)->get()->keyBy('id');

        $lines = [];
        foreach ($inputItems as $item) {
            $service = $services->get($item['service_id']);

            if (!$service) {
                throw new InvalidPriceException('One of the selected services no longer exists.');
            }
            if (!$service->isSellable()) {
                throw new InvalidPriceException("\"{$service->name}\" has no price and cannot be sold.");
            }

            $customPrice = $item['custom_price'] ?? null;
            $hasCustom = $customPrice !== null && $customPrice !== '';

            $lines[] = [
                'service_id'     => $service->id,
                'service_name'   => $service->name,
                'original_price' => (string) $service->price,
                'unit_price'     => $hasCustom ? $customPrice : (string) $service->price,
                'quantity'       => $item['quantity'],
                'allow_zero'     => $hasCustom,
            ];
        }

        return $this->calculator->calculate($lines);
    }
}
