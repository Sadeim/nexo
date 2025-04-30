<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class StaticPageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $operations = view('admin.static_pages.sub.operations', ['instance' => $this])->render();
        $status = view('admin.static_pages.sub.status', ['instance' => $this])->render();

        return [
            'id'                => $this->id,
            'title'             => $this->title,
            'status'            => $status,
            'created_at'        => $this->created_at->format('H:i:s Y-m-d'),
            'operations'        => $operations,
        ];
    }
}