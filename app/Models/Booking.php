<?php

namespace App\Models;

use App\Http\Resources\Admin\BookingResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    public $resource = BookingResource::class;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'persons',
        'date',
        'time',
        'message',
        'status',
        'service_id',
        'barber_id',
        'reminder_sent_at',
    ];

    protected $casts = [
        'date' => 'date',
        'reminder_sent_at' => 'datetime',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /** The requested barber, or null for "any available barber". */
    public function barber()
    {
        return $this->belongsTo(Employee::class, 'barber_id');
    }
}
