<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $operations = view('admin.services.sub.operations', ['instance' => $this])->render();

        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'description'   => $this->description,
            'operations'    => $operations,
        ];
    }
}