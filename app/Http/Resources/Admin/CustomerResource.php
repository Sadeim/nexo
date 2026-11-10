<?php

namespace App\Http\Resources\Admin;

use App\Support\Phone;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $consent = $this->sms_marketing_consent
            ? '<span class="badge badge-light-success fs-7" title="'
                .e(optional($this->sms_consent_at)->format('j M Y, g:i A') ?? '').'">&check;</span>'
            : '<span class="badge badge-light-secondary fs-7">&times;</span>';

        return [
            'id' => $this->id,
            'name' => e($this->name),
            // Linkable, because a number is for calling and texting.
            'phone' => '<a href="tel:'.e(Phone::e164($this->phone)).'">'.e(Phone::pretty($this->phone)).'</a>',
            'email' => e($this->email ?: '—'),
            'bookings_count' => (int) $this->bookings_count,
            'last_booked_at' => $this->last_booked_at
                ? $this->last_booked_at->format('j M Y')
                : '—',
            'sms_marketing_consent' => $consent,
            'created_at' => optional($this->created_at)->format('j M Y') ?? '—',
            'operations' => '<a class="btn btn-sm btn-light-primary" href="'
                .route('admin.customers.show', $this->id).'">View</a>',
        ];
    }
}
