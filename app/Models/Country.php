<?php

namespace App\Models;

use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory, ModelTrait;

    public $table = 'countries';

    protected $fillable = [
        'name',
        'code',
    ]; 
}
