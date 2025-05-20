<?php

namespace App\Models;

use App\Http\Resources\Admin\HowResource;
use App\Http\Resources\Admin\WorkResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class How extends Model
{
    //
    use HasFactory;
    public $resource = HowResource::class;

    protected $guarded = [];
}
