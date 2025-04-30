<?php

namespace App\Models;

use App\Http\Resources\Admin\ClientResource;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    public $resource = ClientResource::class;

    protected $fillable = [
        'name',
        'logo',
        'link',
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
