<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTest extends TestCase
{

    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $employeeRole = Role::factory()->employee()->create();

        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'country' => 'Brazil',
            'currency' => 'BRL',
            'role_id' => $employeeRole->id,
        ]);

        $response
            ->assertCreated()
            ->assertJsonStructure([
                'token',
                'user',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);
    }

    public function test_user_can_login(): void
    {
        $employeeRole = Role::factory()->employee()->create();

        $user = User::factory()
            ->for($employeeRole)
            ->create([
                'email' => 'john@example.com',
                'password' => bcrypt('password'),
            ]);

        $response = $this->postJson('/api/login', [
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'token',
                'user',
            ]);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'invalid@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $employeeRole = Role::factory()->employee()->create();

        $user = User::factory()
            ->for($employeeRole)
            ->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/logout');

        $response->assertOk();
    }
}
