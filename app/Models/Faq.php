<?php

namespace App\Models;

use App\Http\Resources\Admin\FaqResource;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    public $resource = FaqResource::class;

    protected $fillable = [
        'question',
        'answer',
    ];

    public function scopeSearch($query, $request)
    { 
        if (!empty($request->search['value'])) {
            $search = '%' . $request->search['value'] . '%';
            return $query->where('question', 'like', $search);
        }
        return $query;
    }
}
