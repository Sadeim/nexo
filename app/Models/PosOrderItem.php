<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'pos_order_id',
        'service_id',
        'name',
        'price',
        'quantity',
        'is_custom',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_custom' => 'boolean',
    ];

    public function posOrder()
    {
        return $this->belongsTo(PosOrder::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function getLineTotalAttribute(): float
    {
        return (float) $this->price * (int) $this->quantity;
    }
}
