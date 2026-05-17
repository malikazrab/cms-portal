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
        if (!Schema::hasTable('header_footer_templates')) {
            $this->command?->warn('header_footer_templates table is missing. Run the template migrations first.');
            return;
        }

        if (!Schema::hasColumns('header_footer_templates', ['content', 'is_default', 'created_by', 'type'])) {
            $this->command?->warn('header_footer_templates is missing required columns. Run the latest migrations first.');
            return;
        }

        $systemUserId = \App\Models\User::query()->value('id');

        if (!$systemUserId) {
            $this->command?->warn('No users exist yet. Seed a user before creating default templates.');
            return;
        }

        if (!HeaderTemplate::where('is_default', true)->exists()) {
            HeaderTemplate::create([
                'name' => 'Default Header',
                'content' => [
                    'widgets' => [],
                    'settings' => [
                        'backgroundColor' => '#ffffff',
                        'containerWidth' => 'full',
                        'paddingTop' => 10,
                        'paddingBottom' => 10,
                        'paddingLeft' => 20,
                        'paddingRight' => 20,
                    ],
                    'globalStyles' => [],
                ],
                'is_default' => true,
                'created_by' => $systemUserId,
            ]);
            $this->command?->info('Created default header template.');
        }

        if (!FooterTemplate::where('is_default', true)->exists()) {
            FooterTemplate::create([
                'name' => 'Default Footer',
                'content' => [
                    'widgets' => [],
                    'settings' => [
                        'backgroundColor' => '#f3f4f6',
                        'containerWidth' => 'full',
                        'paddingTop' => 24,
                        'paddingBottom' => 24,
                        'paddingLeft' => 20,
                        'paddingRight' => 20,
                    ],
                    'globalStyles' => [],
                ],
                'is_default' => true,
                'created_by' => $systemUserId,
            ]);
            $this->command?->info('Created default footer template.');
        }
    }
}
