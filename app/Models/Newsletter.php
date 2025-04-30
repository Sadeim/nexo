<?php

namespace App\Models;

use App\Http\Resources\Admin\NewsletterResource;
use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    use HasFactory, ModelTrait;
    public $resource = NewsletterResource::class;
    protected $table = 'newsletters';
    protected $fillable = [
        'email',
    ];

    public function scopeSearch($query, $request)
    {
        if (!empty($request->search['value'])) {
            $search = '%' . $request->search['value'] . '%';
            return $query->where('email', 'LIKE' , $search);
        }
        return $query;
    }
}
