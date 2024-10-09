<?php

namespace Tests\Feature\User;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDestroyTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->admin->assignRole(Role::create(['name' => 'admin']));

        $this->regularUser = User::factory()->create();
    }

    public function test_admin_can_remove_other_user(): void
    {
        $this->actingAs($this->admin)
            ->delete("api/{$this->getApiVersion()}/users/{$this->regularUser->id}")
            ->assertSuccessful();

        $this->assertModelMissing($this->regularUser);
    }

    public function test_regular_user_cannot_remove_other_user(): void
    {
        $this->actingAs($this->regularUser)
            ->delete("api/{$this->getApiVersion()}/users/{$this->admin->id}")
            ->assertForbidden();

        $this->assertModelExists($this->admin);
    }
}
