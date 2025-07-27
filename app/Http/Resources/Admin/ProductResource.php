<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $operations = view('admin.products.sub.operations', ['instance' => $this])->render();

        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'price'       => number_format($this->price, 2), // format like "19.99"
            'description' => $this->description ?? '-',
            'operations'  => $operations,
        ];
    }
}
