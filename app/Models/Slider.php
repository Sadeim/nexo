<?php

namespace App\Models;

use App\Http\Resources\Admin\SliderResource;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    public $resource = SliderResource::class;

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'image',
        'button_text',
        'button_link',
        'order',
        'is_active',
    ];
}
