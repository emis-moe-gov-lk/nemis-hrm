<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SystemSetting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'value',
    ];

    public static function bool(string $key, bool $default = false): bool
    {
        $value = Cache::remember("system_setting:{$key}", 86400, function () use ($key) {
            try {
                $setting = static::query()->whereKey($key)->first();
                return $setting ? $setting->value : '__NULL__';
            } catch (\Illuminate\Database\QueryException $e) {
                return '__NULL__';
            }
        });

        if ($value === '__NULL__') {
            return $default;
        }

        return filter_var($value, FILTER_VALIDATE_BOOL);
    }

    public static function setBool(string $key, bool $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value ? '1' : '0'],
        );

        Cache::forget("system_setting:{$key}");
    }
}
