<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosApiWebhookEvent extends Model
{
    protected $fillable = [
        'delivery_id',
        'event_type',
        'payment_intent_id',
        'payload',
        'processed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'processed_at' => 'datetime',
    ];
}
