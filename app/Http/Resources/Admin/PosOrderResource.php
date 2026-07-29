<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PosOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $operations = view('admin.pos_orders.sub.operations', ['instance' => $this])->render();

        $methodBadge = match ($this->payment_method) {
            'cash'  => '<span class="badge badge-light-success text-uppercase">cash</span>',
            'zelle' => '<span class="badge badge-light-warning text-uppercase">zelle</span>',
            default => '<span class="badge badge-light-info text-uppercase">card</span>',
        };

        $tip = (float) $this->tip;
        $tipCell = $tip > 0
            ? '$' . number_format($tip, 2)
            : '<span class="text-muted">—</span>';

        // Surcharge + any cents skimmed off a card tip.
        $fee = (float) $this->shop_fees;
        $feeCell = $fee > 0
            ? '<span class="text-primary">+$' . number_format($fee, 2) . '</span>'
            : '<span class="text-muted">—</span>';

        return [
            'order_number' => e($this->order_number),
            'employee'     => e($this->employee->name ?? '—'),
            'cashier'      => e($this->admin->name ?? $this->admin->email ?? '—'),
            'subtotal'     => '$' . number_format((float) $this->subtotal, 2),
            'card_fee'     => $feeCell,
            'tip'          => $tipCell,
            'total'        => '$' . number_format((float) $this->total, 2),
            'method'       => $methodBadge,
            'date'         => $this->created_at?->format('Y-m-d H:i'),
            'operations'   => $operations,
        ];
    }
}
