<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $operations = view('admin.categories.sub.operations', ['instance' => $this])->render();
        $status = view('admin.categories.sub.status', ['instance' => $this])->render();

        return [
            'id'            => $this->id,
            'status'        => $status,
            'name'          => $this->name,
            'description'   => $this->description ?? '-',
            'operations'    => $operations,
        ];
    }
}
