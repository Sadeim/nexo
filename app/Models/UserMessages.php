<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMessages extends Model
{
    use HasFactory;
    protected $table = 'user_messages';
    protected $fillable = [
        'name', 
        'email', 
        'subject', 
        'message',
        'number',
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
