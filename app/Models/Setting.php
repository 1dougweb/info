<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'is_encrypted'];

    public static function get(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        if (!$setting) return $default;

        if ($setting->is_encrypted && $setting->value) {
            try {
                return Crypt::decryptString($setting->value);
            } catch (\Exception $e) {
                return $setting->value;
            }
        }

        return $setting->value;
    }

    public static function set(string $key, $value, bool $encrypt = false)
    {
        $val = ($encrypt && $value) ? Crypt::encryptString($value) : $value;
        return self::updateOrCreate(['key' => $key], ['value' => $val, 'is_encrypted' => $encrypt]);
    }
}
