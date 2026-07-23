<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Order placed via the Flutter POS terminal.
 * Distinct from `PosTransaction` (used by the browser-based Web POS).
 */
class PosOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'employee_id',
        'admin_id',
        'subtotal',
        'tip',
        'total',
        'payment_method',
        'customer_email',
        'receipt_sent_at',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tip' => 'decimal:2',
        'total' => 'decimal:2',
        'receipt_sent_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(PosOrderItem::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public static function generateOrderNumber(): string
    {
        do {
            $number = 'N' . now()->format('ymd') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
        } while (self::where('order_number', $number)->exists());

        return $number;
    }
}
