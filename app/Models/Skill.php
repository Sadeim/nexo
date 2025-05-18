<?php

namespace App\Models;

use App\Http\Resources\Admin\SkillResource;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    public $resource = SkillResource::class;

    protected $guarded = [];
          
    protected $fillable = [
        'image',
        'image2',
        'image3',
        'title',
        'description',
        'percent1',
        'text1',
        'percent2',
        'text2',
        'percent3',
        'text3',
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
