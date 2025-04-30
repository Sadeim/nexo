<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class InstagramResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $operations = view('admin.instagrams.sub.operations', ['instance' => $this])->render();

        return [
            'id'            => $this->id,
            'link'          => $this->link,
            'created_at'    => $this->created_at->format('d-m-Y'),
            'operations'    => $operations,
        ];
    }
}