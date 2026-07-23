<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $operations = view('admin.accounts.sub.operations', ['instance' => $this])->render();
        $status     = view('admin.accounts.sub.status', ['instance' => $this])->render();

        $role = $this->roles->first();
        $roleName = $role ? $role->name : '—';
        $badgeClass = $roleName === 'admin' ? 'badge-light-primary' : 'badge-light-success';

        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'role'       => '<span class="badge ' . $badgeClass . ' text-capitalize">' . e($roleName) . '</span>',
            'status'     => $status,
            'operations' => $operations,
        ];
    }
}
