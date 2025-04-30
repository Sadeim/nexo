<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class NewsletterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $operations = view('admin.newsletters.sub.operations', ['instance' => $this])->render();

        return [
            'id'            => $this->id,
            'email'          => $this->email,
            'operations'    => $operations,
        ];
    }
}