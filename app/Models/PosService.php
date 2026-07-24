<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosService extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeSearch($query, $request)
    {
        if (!empty($request->search['value'])) {
            $search = '%' . $request->search['value'] . '%';
            return $query->where('name', 'LIKE', $search);
        }
        return $query;
    }
}
