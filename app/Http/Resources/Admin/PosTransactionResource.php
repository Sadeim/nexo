<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PosTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $operations = view('admin.pos_transactions.sub.operations', ['instance' => $this])->render();

        $methodBadge = $this->payment_method === 'cash'
            ? '<span class="badge badge-light-success text-uppercase">cash</span>'
            : '<span class="badge badge-light-info text-uppercase">card</span>';

        return [
            'id'         => '#' . $this->id,
            'employee'   => e($this->admin->name ?? $this->admin->email ?? '—'),
            'total'      => '$' . number_format((float) $this->total, 2),
            'method'     => $methodBadge,
            'date'       => $this->created_at?->format('Y-m-d H:i'),
            'operations' => $operations,
        ];
    }
}
