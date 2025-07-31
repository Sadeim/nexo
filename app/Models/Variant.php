<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
       use HasFactory;

    protected $fillable = [
        'product_id',
        'price',
        'sku',
        'barcode',
    ];

    public function product() 
    {
        return $this->belongsTo(Product::class);
    }
    
    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class, 'variant_attribute_values', 'variant_id', 'attribute_value_id') ;
    }

    public function inventory()
    {
        return $this->morphOne(Inventory::class, 'inventoryable');
    }
}
