<?php

namespace App\Models;

use App\Http\Resources\Admin\ProductResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasImages;



class Product extends Model
{
    use HasFactory , HasImages;
    public $resource = ProductResource::class;


    protected $fillable = [
        'name',
        'image',
        'price',
        'description',
    ];


    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'product_attributes')->withPivot('attribute_value_id')->with('attributeValues');
    }

    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class, 'product_attributes', 'product_id', 'attribute_value_id');
    }

        public function variants()
    {
        return $this->hasMany(Variant::class);
    }

      public function inventory()
    {
        return $this->morphOne(Inventory::class, 'inventoryable');
    }

     public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('pos', 'ASC');
    }
}
