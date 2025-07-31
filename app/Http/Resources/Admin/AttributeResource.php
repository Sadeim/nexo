<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttributeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $operations = view('admin.attributes.sub.operations', ['instance' => $this])->render();
        $status = view('admin.attributes.sub.status', ['instance' => $this])->render();

        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'status'        => $status,
            'created_at'    => $this->created_at->format('H:i:s Y-m-d'),
            'operations'    => $operations,
        ];
    }
}
