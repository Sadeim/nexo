<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReasonTab extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'description',
        'features',
        'order',
    ];

    protected $casts = [
        'features' => 'array',
    ];
}
