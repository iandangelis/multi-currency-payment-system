<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'employee',
                'finance',
            ]),
        ];
    }

    public function employee(): static
    {
        return $this->state(fn () => [
            'name' => 'employee',
        ]);
    }

    public function finance(): static
    {
        return $this->state(fn () => [
            'name' => 'finance',
        ]);
    }
}
