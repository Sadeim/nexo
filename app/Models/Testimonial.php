<?php

namespace App\Models;

use App\Http\Resources\Admin\TestimonialResource;
use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory, ModelTrait;
    public $resource = TestimonialResource::class;
    protected $fillable = [
        'name',
        'position', 
        'message', 
        'rating', 
        'photo',
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
