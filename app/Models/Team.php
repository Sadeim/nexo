<?php

namespace App\Models;

use App\Http\Resources\Admin\TeamResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;
    public $resource = TeamResource::class;

    protected $fillable = [
        'name',
        'position',
        'image',
        'social_links',
    ];

    protected $casts = [
        'social_links' => 'array', // تحويل JSON إلى Array
    ];

    public function scopeSearch($query, $request)
    { 
        if (!empty($request->search['value'])) {
            $search = '%' . $request->search['value'] . '%';
            return $query->where('name', 'like', $search);
        }
        return $query;
    }
}
