<?php

namespace App\Models;

use App\Http\Resources\Admin\EventResource;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    public $resource = EventResource::class;

    protected $fillable = [
        'title',
        'description',
        'date',
        'time',
        'location',
        'image',
        'is_active',
    ];
}
