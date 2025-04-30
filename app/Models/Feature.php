<?php

namespace App\Models;

use App\Http\Resources\Admin\FeatureResource;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    public $resource = FeatureResource::class;

    protected $fillable = [
        'title',
        'icon',
        'image',
        'link',
    ];
}
