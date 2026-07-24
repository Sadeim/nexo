<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PosServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $operations = view('admin.pos_services.sub.operations', ['instance' => $this])->render();
        $status     = view('admin.pos_services.sub.status', ['instance' => $this])->render();

        return [
            'id'         => $this->id,
            'name'       => e($this->name),
            'price'      => '$' . number_format((float) $this->price, 2),
            'sort_order' => (int) $this->sort_order,
            'status'     => $status,
            'operations' => $operations,
        ];
    }
}
