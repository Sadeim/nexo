<?php

namespace App\Models;

use App\Http\Resources\Admin\UserResource;
use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\App;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasApiTokens, ModelTrait;
    
    // protected $guarded = [];
    // protected $guard_name = 'user';
    public $resource = UserResource::class;
    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phone_code',
        'phone',
        'email',
        'email_verified_at',
        'username',
        'password',
        'remember_token',
        'status',
        'image',
        'otp',
        'otp_expired_at',
        'provider',
        'provider_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function scopeSearch($query, $request)
    {
        if (!empty($request->search['value'])) {
            $search = '%' . $request->search['value'] . '%';
            return $query->where(function($r) use ($search){
                    $r->where('first_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $search . '%');
            });
            // return $query->where('first_name', 'LIKE' , '%' . $search . '%');
        }
        return $query;
    }


    public function products()
    {
        return $this->belongsToMany(Product::class, 'user_products', 'user_id', 'product_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id', 'id');
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(Product::class, 'user_favorites', 'user_id', 'product_id')->withTimestamps();
    }

    public function addToFavorites($productId)
    {
        $this->favorites()->attach($productId);
    }

    public function removeFromFavorites($productId)
    {
        $this->favorites()->detach($productId);
    }

    public function isFavorite($productId)
    {
        return $this->favorites()->where('product_id', $productId)->exists();
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }
}
