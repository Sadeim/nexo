<?php

namespace App\Models;
 use App\Http\Resources\Admin\AttributeValueResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\ModelTrait;

class AttributeValue extends Model
{
    use HasFactory, ModelTrait;
    public $resource = AttributeValueResource::class;
   
    protected $fillable = [
        'name',
        'description',
        'status',
        'slug',
        'attribute_id',
    ];

      public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }
}
