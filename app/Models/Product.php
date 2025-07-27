<?php

namespace App\Models;

use App\Http\Resources\Admin\ProductResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Product extends Model
{
    use HasFactory;
    public $resource = ProductResource::class;


    protected $fillable = [
        'name',
        'image',
        'price',
        'description',
    ];
}
