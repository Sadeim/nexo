<?php

namespace App\Models;

use App\Http\Resources\Admin\ServiceResource;
use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory, ModelTrait;
    public $resource = ServiceResource::class;
    protected $fillable = [
        'name',
        'description',
        'price',
        'status',
        'icon',
        'image',
        'is_featured'
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * A service can be sold in the POS only when it has a trusted price.
     */
    public function scopeSellable($query)
    {
        return $query->whereNotNull('price');
    }

    public function isSellable(): bool
    {
        return $this->price !== null;
    }

    public function scopeSearch($query, $request)
    {
        if (!empty($request->search['value'])) {
            $search = '%' . $request->search['value'] . '%';
            return $query->where('name', 'LIKE', $search);
        }
        return $query;
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
