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
        'status',
        'payment_intent_id',
        'provider_payment_id',
        'reference',
        'amount_cents',
        'currency',
        'failure_reason',
        'idempotency_key',
        'customer_email',
        'receipt_sent_at',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tip' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_cents' => 'integer',
        'receipt_sent_at' => 'datetime',
    ];

    /** Card status flow: awaiting_payment → processing → completed | failed | canceled. */
    public const TERMINAL_STATUSES = ['completed', 'failed', 'canceled'];

    public function isTerminal(): bool
    {
        return in_array($this->status, self::TERMINAL_STATUSES, true);
    }

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

    /**
     * Completed orders whose created_at falls inside [from-startOfDay,
     * to-endOfDay] in the CALLER's timezone. The boundaries are converted to
     * UTC before the query is issued so a Chicago "today" doesn't leak into
     * the previous UTC day on the DB side.
     */
    public function scopeCompletedIn($query, \Carbon\Carbon $from, \Carbon\Carbon $to)
    {
        return $query
            ->where('status', 'completed')
            ->whereBetween('created_at', [
                $from->copy()->startOfDay()->utc(),
                $to->copy()->endOfDay()->utc(),
            ]);
    }
}
