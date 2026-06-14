<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employeeRole = Role::firstOrCreate([
            'name' => 'employee'
        ]);

        $financeRole = Role::firstOrCreate([
            'name' => 'finance'
        ]);

        User::factory()->for($employeeRole)->create([
            'name' => 'Test User Employee',
            'email' => 'employee@example.com',
        ]);

        User::factory()->for($financeRole)->create([
            'name' => 'Test User Finance',
            'email' => 'finance@example.com',
        ]);

        User::create([
            'name' => 'John Smith',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'role_id' => $employeeRole->id,
            'country' => 'United States',
            'currency' => 'USD',
        ]);

        User::create([
            'name' => 'Maria Silva',
            'email' => 'maria@example.com',
            'password' => bcrypt('password'),
            'role_id' => $employeeRole->id,
            'country' => 'Brazil',
            'currency' => 'BRL',
        ]);

        User::create([
            'name' => 'James Wilson',
            'email' => 'james@example.com',
            'password' => bcrypt('password'),
            'role_id' => $employeeRole->id,
            'country' => 'United Kingdom',
            'currency' => 'GBP',
        ]);

        User::create([
            'name' => 'Sophie Martin',
            'email' => 'sophie@example.com',
            'password' => bcrypt('password'),
            'role_id' => $employeeRole->id,
            'country' => 'Canada',
            'currency' => 'CAD',
        ]);

        User::create([
            'name' => 'Takashi Yamamoto',
            'email' => 'takashi@example.com',
            'password' => bcrypt('password'),
            'role_id' => $employeeRole->id,
            'country' => 'Japan',
            'currency' => 'JPY',
        ]);
    }
}
