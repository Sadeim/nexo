<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $fillable = ['key', 'value'];

    public static function setSetting($data)
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        foreach ($data as $key => $value) {
            if (!is_null($value)) {
                if (request()->hasFile($key)) {
                    $value = uploadImage($value);
                }
                self::updateOrCreate(['key' => $key], ['value' => $value]);
            }
        }
        $diff = array_diff_key($settings, $data);
        foreach ($diff as $index => $item) {
            $setting = Setting::where(['key' => $index])->first();
            if ($setting) {
                $setting->delete();
            }
        }
        return true;
    }

    public function key($key)
    {
        return $this->where(['key' => $key])->first();
    }

    public function valueOf($type, $default = null)
    {
        if($this->first()) {
            return $this->key($type) ? $this->key($type)->value : $default;
        }
        return '';
    }
}
