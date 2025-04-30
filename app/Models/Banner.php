<?php

namespace App\Models;

use App\Http\Resources\Admin\BannerResource;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    public $resource = BannerResource::class;

    protected $fillable = [
        'title',
        'sub_title',
        'description',
        'image',
        'video_url',
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