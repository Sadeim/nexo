<?php

namespace App\Models;

use App\Http\Resources\Admin\InstagramResource;
use Illuminate\Database\Eloquent\Model;

class Instagram extends Model
{
    public $resource = InstagramResource::class;

    protected $fillable = [
        'image',
        'link',
        'is_active',
    ];
}
