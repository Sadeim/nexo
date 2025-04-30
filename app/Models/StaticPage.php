<?php

namespace App\Models;

use App\Http\Resources\Admin\AttributeResource;
use App\Http\Resources\Admin\StaticPageResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelTrait;

class StaticPage extends Model
{
    use HasFactory, ModelTrait;
    public $resource = StaticPageResource::class;

    protected $fillable = [
        'title', 
        'content',
        'slug', 
        'status', 
    ];
}
