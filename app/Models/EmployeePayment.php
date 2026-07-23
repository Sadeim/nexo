<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeePayment extends Model
{
    protected $fillable = [
        'employee_id',
        'admin_id',
        'amount',
        'period_from',
        'period_to',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'period_from' => 'date',
        'period_to'   => 'date',
        'paid_at'     => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
