<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TemplateSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Classic Header Template
        DB::table('header_footer_templates')->updateOrInsert(
            ['name' => 'Classic Header', 'type' => 'header'],
            [
                'type' => 'header',
                'name' => 'Classic Header',
                'content' => json_encode([
                    'type' => 'header',
                    'settings' => ['backgroundColor' => '#ffffff'],
                    'children' => []
                ]),
                'is_default' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        // 2. Mega Menu Header Template
        DB::table('header_footer_templates')->updateOrInsert(
            ['name' => 'Mega Menu Header', 'type' => 'header'],
            [
                'type' => 'header',
                'name' => 'Mega Menu Header',
                'content' => json_encode([
                    'type' => 'header',
                    'settings' => ['backgroundColor' => '#1a3a6b'],
                    'children' => []
                ]),
                'is_default' => 0,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        // 3. Institutional Footer Template
        DB::table('header_footer_templates')->updateOrInsert(
            ['name' => 'Institutional Footer', 'type' => 'footer'],
            [
                'type' => 'footer',
                'name' => 'Institutional Footer',
                'content' => json_encode([
                    'type' => 'footer',
                    'settings' => ['backgroundColor' => '#1f2937'],
                    'children' => []
                ]),
                'is_default' => 1,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        $this->command->info('Templates seeded successfully!');
    }
}
