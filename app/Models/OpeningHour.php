<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpeningHour extends Model
{
    protected $fillable = ['about_id', 'day', 'from', 'to', 'is_closed'];

    public function about()
    {
        return $this->belongsTo(About::class);
    }
}
