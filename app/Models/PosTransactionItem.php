<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosTransactionItem extends Model
{
    protected $fillable = [
        'pos_transaction_id',
        'service_id',
        'service_name',
        'original_price',
        'unit_price',
        'quantity',
        'line_total',
    ];

    protected $casts = [
        'original_price' => 'decimal:2',
        'unit_price'     => 'decimal:2',
        'line_total'     => 'decimal:2',
        'quantity'       => 'integer',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(PosTransaction::class, 'pos_transaction_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
