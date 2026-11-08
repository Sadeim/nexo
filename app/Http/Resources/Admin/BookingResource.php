<?php

namespace App\Http\Resources\Admin;

use App\Models\Employee;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Published barbers, fetched once for the whole page rather than once per
     * row — the dropdown is identical in every row.
     */
    private static $barbers = null;

    private static function barbers()
    {
        return self::$barbers ??= Employee::publicBarbers()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $operations = view('admin.bookings.sub.operations', ['instance' => $this])->render();

        $barberCell = view('admin.bookings.sub.barber_select', [
            'booking' => $this,
            'barbers' => self::barbers(),
        ])->render();

        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'email'         => $this->email,
            'service'       => $this->service ? $this->service->name : 'N/A',
            'barber'        => $barberCell,
            'date'          => $this->date?->format('Y-m-d'),
            'time'          => $this->time ? \Carbon\Carbon::parse($this->time)->format('H:i') : '',
            'created_at'    => $this->created_at->format('d-m-Y'),
            'operations'    => $operations,
        ];
    }
}
