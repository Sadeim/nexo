<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;

trait ModelTrait
{
    public function scopeActive($q) 
    {
        $q->where('status',1);
    }
}
