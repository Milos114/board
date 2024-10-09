<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_update_their_profile(): void
    {
        $this->actingAs($user = User::factory()->create());

        $this->put("api/{$this->getApiVersion()}/users/$user->id", [
            'name' => 'updated name',
            'email' => 'test@email.com',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'updated name',
            'email' => 'test@email.com',
        ]);
    }

    public function test_user_can_not_update_other_users(): void
    {
        $this->actingAs(User::factory()->create());
        $user2 = User::factory()->create();

        $this->put("api/{$this->getApiVersion()}/users/". $user2->id, [
            'name' => 'updated name',
            'email' => 'test@email.com'
        ])->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
