<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class SkillResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $operations = view('admin.skills.sub.operations', ['instance' => $this])->render();

        return [
            'id'            => $this->id,
            'percent'          => $this->percent,
            'text'          => $this->text,
            'created_at'          => $this->created_at->format('d-m-Y'),
            'operations'    => $operations,
        ];
    }
}