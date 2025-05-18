<?php

namespace App\Models;

use App\Http\Resources\Admin\AporchResource;
use Illuminate\Database\Eloquent\Model;

class Aporch extends Model
{
    //
    public $resource = AporchResource::class;
    protected $guarded = [];
}
