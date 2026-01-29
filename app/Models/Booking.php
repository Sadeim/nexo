<?php

namespace App\Models;

use App\Http\Resources\Admin\BookingResource;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
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
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
