<?php

namespace App\Models;

use App\Http\Resources\Admin\ReasonResource;
use Illuminate\Database\Eloquent\Model;

class Reason extends Model
{
    public $resource = ReasonResource::class;

    protected $guarded = [];
       
    protected $fillable = [
        'title',
        'text',
        'icon',
    ];

    public function scopeSearch($query, $request)
    { 
        if (!empty($request->search['value'])) {
            $search = '%' . $request->search['value'] . '%';
            return $query->where('title', 'like', $search);
        }
        return $query;
    }
}