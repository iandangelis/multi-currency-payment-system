<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
        ]);

        User::factory()->employee()->create([
            'name' => 'Test User Employee',
            'email' => 'employee@example.com',
        ]);

        User::factory()->finance()->create([
            'name' => 'Test User Finance',
            'email' => 'finance@example.com',
        ]);
    }
}
