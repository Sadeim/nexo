<?php

namespace App\Models;

use App\Http\Resources\Admin\WorkResource;
use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    public $resource = WorkResource::class;

    protected $fillable = [
        'title',
        'slug',
        'image',
        'category',
        'description',
    ];
}
