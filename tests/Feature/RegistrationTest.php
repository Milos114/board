<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password',
            'c_password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
            ]);
    }

    public function test_user_can_login(): void
    {
        $user = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password',
            'c_password' => 'password',
        ];

        $this->postJson('/api/register', $user);

        $response = $this->postJson('/api/login', [
            'email' => $user['email'],
            'password' => $user['password'],
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
            ]);
    }
}
