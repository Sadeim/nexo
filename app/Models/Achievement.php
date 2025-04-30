<?php

namespace App\Models;

use App\Http\Resources\Admin\AchievementResource;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    public $resource = AchievementResource::class;

    protected $fillable = [
        'title',
        'description',
        'year',
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
