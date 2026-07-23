<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'avatar',
        'is_active',
        'sort_order',
        'commission_rate',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'commission_rate' => 'decimal:2',
    ];

    public function posOrders()
    {
        return $this->hasMany(PosOrder::class);
    }

    public function payments()
    {
        return $this->hasMany(EmployeePayment::class);
    }

    /** Commission earned on a subtotal amount (in currency, not %). */
    public function commissionOn(float $subtotal): float
    {
        return round($subtotal * (float) $this->commission_rate / 100, 2);
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeSearch($query, $request)
    {
        if (!empty($request->search['value'])) {
            $search = '%' . $request->search['value'] . '%';
            return $query->where('name', 'LIKE', $search);
        }
        return $query;
    }
}
