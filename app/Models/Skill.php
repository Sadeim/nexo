<?php

namespace App\Models;

use App\Http\Resources\Admin\SkillResource;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    public $resource = SkillResource::class;

        protected $guarded = [];
          
        
    public function scopeSearch($query, $request)
    { 
        if (!empty($request->search['value'])) {
            $search = '%' . $request->search['value'] . '%';
            return $query->where('text', 'like', $search);
        }
        return $query;
    }
}
