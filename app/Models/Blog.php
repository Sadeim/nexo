<?php

namespace App\Models;

use App\Http\Resources\Admin\BlogResource;
use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    public $resource = BlogResource::class;

    protected $fillable = [
        'title',
        'slug',
        'image',
        'category',
        'author',
        'content',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeSearch($query, $request)
    { 
        if (!empty($request->search['value'])) {
            $search = '%' . $request->search['value'] . '%';
            return $query->where('title', 'like', $search);
        }
        return $query;
    }
}
