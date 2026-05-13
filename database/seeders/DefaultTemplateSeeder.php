<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HeaderTemplate;
use App\Models\FooterTemplate;
use Illuminate\Support\Facades\Schema;

class DefaultTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if is_default column exists before using it
        if (!Schema::hasColumn('header_templates', 'is_default')) {
            $this->command->warn('is_default column missing. Run WS-1 migration first.');
            return;
        }

        // Create default header if none exists with is_default = true
        if (!HeaderTemplate::where('is_default', true)->exists()) {
            HeaderTemplate::create([
                'name' => 'Default Header',
                'html' => '<header class="bg-white shadow-sm"><div class="container mx-auto px-4 py-4">{!! menu_slot("primary") !!}</div></header>',
                'css' => '',
                'js' => '',
                'menu_slots' => json_encode(['primary' => null]),
                'is_default' => true,
                'is_active' => true,
            ]);
            $this->command->info('Created default header template.');
        }

        // Create default footer if none exists with is_default = true
        if (!FooterTemplate::where('is_default', true)->exists()) {
            FooterTemplate::create([
                'name' => 'Default Footer',
                'html' => '<footer class="bg-gray-100 mt-auto"><div class="container mx-auto px-4 py-6"><p class="text-center text-gray-600">&copy; {{ date("Y") }} CMS Portal. All rights reserved.</p></div></footer>',
                'css' => '',
                'js' => '',
                'menu_slots' => json_encode([]),
                'is_default' => true,
                'is_active' => true,
            ]);
            $this->command->info('Created default footer template.');
        }
    }
}