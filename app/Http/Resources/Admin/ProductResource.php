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

        
        if($this->variants->count()){
            $price = $this->variants()->first()->price;
        }else{
            $price = $this->price;
        }
        

        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'price'       => $price, 
            'description' => $this->description ?? '-',
            'operations'  => $operations,
        ];
    }
}
