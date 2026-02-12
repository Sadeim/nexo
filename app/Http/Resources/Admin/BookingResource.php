<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $operations = view('admin.bookings.sub.operations', ['instance' => $this])->render();

        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'email'         => $this->email,
            'service'       => $this->service ? $this->service->name : 'N/A',
            'date'          => $this->date?->format('Y-m-d'),
            'time'          => $this->time ? \Carbon\Carbon::parse($this->time)->format('H:i') : '',
            'created_at'    => $this->created_at->format('d-m-Y'),
            'operations'    => $operations,
        ];
    }
}
