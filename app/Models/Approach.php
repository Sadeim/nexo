<?php

namespace App\Models;

use App\Http\Resources\Admin\ApproachResource;
use Illuminate\Database\Eloquent\Model;

class Approach extends Model
{
    public $resource = ApproachResource::class;
    protected $fillable = [
        'title',
        'subtitle',
        'image_1',
        'image_2',
        'mission_description',
        'mission_points',
        'vision_description',
        'vision_points',
        'value_description',
        'value_points',
    ];

    protected $casts = [
        'mission_points' => 'array',
        'vision_points' => 'array',
        'value_points' => 'array',
    ];
}
