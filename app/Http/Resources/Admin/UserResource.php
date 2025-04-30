<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray(Request $request)
    {
        $operations = view('admin.users.sub.operations', ['instance' => $this])->render();
        $status = view('admin.users.sub.status', ['instance' => $this])->render();

        return [
            'id'            => $this->id,
            'name'          => $this->full_name,
            'email'         => $this->email,
            'status'        => $status,
            'operations'    => $operations,
        ];
    }
}
