<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $operations = view('admin.employees.sub.operations', ['instance' => $this])->render();
        $status     = view('admin.employees.sub.status', ['instance' => $this])->render();

        $avatar = $this->avatar
            ? '<img src="' . asset($this->avatar) . '" class="rounded-circle" style="width:36px;height:36px;object-fit:cover;">'
            : '<span class="badge badge-light-primary" style="width:36px;height:36px;line-height:28px;font-size:14px;">'
                . e(mb_strtoupper(mb_substr((string) $this->name, 0, 1))) . '</span>';

        return [
            'id'         => $this->id,
            'avatar'     => $avatar,
            'name'       => e($this->name),
            'sort_order' => (int) $this->sort_order,
            'status'     => $status,
            'operations' => $operations,
        ];
    }
}
