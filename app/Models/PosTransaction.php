<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosTransaction extends Model
{
    protected $fillable = [
        'admin_id',
        'subtotal',
        'total',
        'payment_method',
        'status',
        'reference',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'total'    => 'decimal:2',
    ];

    /** The cashier (admin) who made the sale. */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PosTransactionItem::class);
    }
}
