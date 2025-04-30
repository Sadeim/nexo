<?php

namespace App\Models;

use App\Http\Resources\Admin\MenuItemResource;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    public $resource = MenuItemResource::class;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'image',
        'is_featured',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
