<?php

namespace App\Models;

use App\Http\Resources\Admin\CategoryResource;
use App\Traits\Model\ScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelTrait;

class Category extends Model
{
    use HasFactory, ModelTrait;
    public $resource = CategoryResource::class;

    protected $fillable = [
        'name', 
        'description',
        'parent_id',
        'slug',
        'image',
        'status',
    ];
 
    public function scopeSearch($query, $request)
    { 
        if (!empty($request->search['value'])) {
            $search = '%' . $request->search['value'] . '%';
            return $query->where('name', 'like', $search);
        }
        return $query;
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }
}
