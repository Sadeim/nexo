<?php

namespace App\Models;

use App\Http\Resources\Admin\AttributeResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\ModelTrait;


class Attribute extends Model
{
    use HasFactory, ModelTrait;
    public $resource = AttributeResource::class;

    protected $fillable = [
        'name', 
        'description',
        'slug',
        'status',
    ];

     public function attributeValues()
    {
        return $this->hasMany(AttributeValue::class);
    }
}
