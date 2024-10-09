<?php

namespace Tests\Feature\User;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_be_created_by_admins(): void
    {
        $this->actingAs(
            $user = User::factory()->create()
        );

        $user->assignRole(
            Role::create(['name' => 'admin'])
        );

        $this->post("api/{$this->getApiVersion()}/users", [
            'name' => 'new user',
            'email' => 'test@test.com',
            'password' => 'password',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'new user',
            'email' => 'test@test.com',
        ]);
    }

    public function test_users_cannot_be_created_by_regular_users(): void
    {
        $this->actingAs(
            User::factory()->create()
        );

        $this->post("api/{$this->getApiVersion()}/users", [
            'name' => 'new user',
            'email' => 'test@test.com',
            'password' => 'password',
        ])->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_create_user_validation_errors_are_returned(): void
    {
        $this->actingAs(
            $user = User::factory()->create()
        );

        $user->assignRole(
            Role::create(['name' => 'admin'])
        );

        $this->post("api/{$this->getApiVersion()}/users", [
            'name' => '',
            'email' => '',
            'password' => '',
        ])->assertSessionHasErrors([
            'name',
            'email',
            'password',
        ]);
    }
}
