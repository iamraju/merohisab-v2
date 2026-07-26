<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'MeroHisab Admin',
            'email' => 'admin@merohisab.test',
            'password' => bcrypt('Test@123'),
            'role' => UserRole::SuperAdmin,
            'status' => 'active',
        ]);

        User::factory()->create([
            'name' => 'Test Customer',
            'email' => 'customer@merohisab.test',
            'password' => bcrypt('Test@123'),
            'role' => UserRole::Customer,
            'status' => 'active',
        ]);
    }
}
