<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $operations = view('admin.banners.sub.operations', ['instance' => $this])->render();

        return [
            'id'            => $this->id,
            'title'          => $this->title,
            'sub_title'          => $this->sub_title,
            'description'          => $this->description,
            'created_at'          => $this->created_at->format('d-m-Y'),
            'operations'    => $operations,
        ];
    }
}