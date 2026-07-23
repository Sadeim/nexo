<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PosApiToken extends Model
{
    protected $fillable = [
        'admin_id',
        'name',
        'token',
        'last_used_at',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public static function issue(Admin $admin, string $name = 'POS Terminal'): self
    {
        return self::create([
            'admin_id' => $admin->id,
            'name' => $name,
            'token' => Str::random(60),
        ]);
    }
}
