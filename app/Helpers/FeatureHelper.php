<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Schema;

class FeatureHelper
{
    public static function isAvailable(string $feature): bool
    {
        if (!config("features.{$feature}", false)) {
            return false;
        }

        return match($feature) {
            'header_templates' => Schema::hasTable('header_footer_templates'),
            'footer_templates' => Schema::hasTable('header_footer_templates'),
            'menus' => Schema::hasTable('menus'),
            'versioning' => Schema::hasTable('page_versions'),
            default => true,
        };
    }
}