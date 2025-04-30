<?php

namespace App\Models;

use App\Http\Resources\Admin\ServiceResource;
use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory, ModelTrait;
    public $resource = ServiceResource::class;
    protected $fillable = [
        'name',
        'description',
        'status',
        'icon', 
        'image', 
        'is_featured',
    ];

    public function scopeSearch($query, $request)
    {
        if (!empty($request->search['value'])) {
            $search = '%' . $request->search['value'] . '%';
            return $query->where('name', 'LIKE' , $search);
        }
        return $query;
    }
}
