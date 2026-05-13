<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $table = 'themes';

    protected $fillable = [
        'name',
        'slug',
        'version',
        'author',
        'description',
        'screenshot',
        'settings',
        'is_active',
        'is_builtin',
        'theme_path',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'is_builtin' => 'boolean',
    ];

    public function setAsActive()
    {
        // Deactivate all other themes
        self::where('is_active', true)->update(['is_active' => false]);
        
        // Activate this theme
        $this->is_active = true;
        $this->save();
    }

    public static function getActive()
    {
        return self::where('is_active', true)->first();
    }

    public function getSetting(string $key, $default = null)
    {
        $settings = $this->settings ?? [];
        $keys = explode('.', $key);
        
        foreach ($keys as $segment) {
            if (!is_array($settings) || !array_key_exists($segment, $settings)) {
                return $default;
            }
            $settings = $settings[$segment];
        }
        
        return $settings;
    }
}