<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $operations = view('admin.roles.sub.operations', ['instance' => $this])->render();

        return [
            'operations'    => $operations,
            'created_at'    => $this->created_at->format('d-m-Y'),,
            'name'          => $this->name,
            'guard_name'    => $this->guard_name,
        ];
    }
}
