<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // User seeder (your existing code)
        User::updateOrCreate(
            ['email' => 'admin@cms.com'],
            [
                'name' => 'Admin User',
                'password' => 'Admin@123',
                'role' => User::ROLE_ADMIN,
            ]
        );

        // Add the Default Template Seeder here
        $this->call(DefaultTemplateSeeder::class);
    }
}