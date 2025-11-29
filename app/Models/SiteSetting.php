<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'description'];

    /**
     * Get a setting value by key (Secure)
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        // Sanitize key to prevent cache poisoning
        $sanitizedKey = preg_replace('/[^a-zA-Z0-9_]/', '', $key);
        
        return Cache::remember("setting_{$sanitizedKey}", 3600, function () use ($sanitizedKey, $default) {
            // Use parameterized query (Laravel does this automatically)
            $setting = self::where('key', $sanitizedKey)->first();
            
            if (!$setting) {
                return $default;
            }

            return self::castValue($setting->value, $setting->type);
        });
    }

    /**
     * Set a setting value (Secure)
     */
    public static function set(string $key, mixed $value, string $type = 'string'): void
    {
        // Validate key format (alphanumeric and underscore only)
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $key)) {
            throw new \InvalidArgumentException('Invalid setting key format');
        }

        // Validate type
        $allowedTypes = ['string', 'boolean', 'integer', 'json'];
        if (!in_array($type, $allowedTypes, true)) {
            throw new \InvalidArgumentException('Invalid setting type');
        }

        // Convert value based on type for consistency and safety
        $stringValue = match($type) {
            'boolean' => ($value ? 'true' : 'false'),
            'integer' => (string)(int)$value,
            'json' => is_array($value) ? json_encode($value) : (string)$value,
            default => (string)$value,
        };
        
        // Use updateOrCreate (safe from SQL injection via Eloquent)
        self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $stringValue,
                'type' => $type,
                'updated_at' => now(),
            ]
        );

        // Clear both specific and general cache
        Cache::forget("setting_{$key}");
        Cache::forget('site_settings');
    }

    /**
     * Cast value based on type
     */
    private static function castValue(string $value, string $type): mixed
    {
        return match ($type) {
            'boolean' => $value === 'true',
            'integer' => (int) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }
}
