<?php

namespace Tests\Feature\User\Search;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_be_searched_by_name(): void
    {
        $this->actingAs(User::factory()->create());

        $user2 = User::factory()->create([
            'name' => 'test name',
        ]);

        $user3 = User::factory()->create([
            'name' => 'random name',
        ]);

        $response = $this->get("api/{$this->getApiVersion()}/users?filter[name]=random");

        $response->assertJsonFragment([
            'id' => $user3->id,
            'name' => 'random name',
        ])->assertJsonMissing([
            'id' => $user2->id,
        ]);
    }

    public function test_users_can_be_searched_by_email(): void
    {
        $this->actingAs($user = User::factory([
            'email' => 'test@test.com'
        ])->create());

        $user2 = User::factory()->create([
            'email' => 'mile@google.com'
        ]);

        $response = $this->get("api/{$this->getApiVersion()}/users?filter[email]=mile@");

        $response->assertJsonFragment([
            'id' => $user2->id,
            'email' => 'mile@google.com'
        ])->assertJsonMissing([
            'id' => $user->id,
            'email' => 'test@test.com'
        ]);
    }
}
