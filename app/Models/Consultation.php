<?php

namespace App\Models;

use App\Http\Resources\Admin\ConsultationResource;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    public $resource = ConsultationResource::class;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'service',
        'message',
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
