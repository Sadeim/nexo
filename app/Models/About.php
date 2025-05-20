<?php

namespace App\Models;

use App\Http\Resources\Admin\AboutResource;
use Illuminate\Database\Eloquent\Model;

class About extends Model
{
    public $resource = AboutResource::class;

    protected $fillable = [
        'title',
        'description',
        'image1',
        'image2',
        'tab1_title',
        'tab1_content',
        'tab2_title',
        'tab2_content',
        'tab3_title',
        'tab3_content',
        'button_text',
        'button_link',
    ];

    public function openingHours()
    {
        return $this->hasMany(OpeningHour::class);
    }
}
