<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_be_listed(): void
    {
        $this->actingAs(User::factory()->create());
        User::factory()->create();

        $response = $this->get("api/{$this->getApiVersion()}/users")->assertOk();

        $response->assertJsonStructure([
            'data' => [
                '0' => [
                    'id',
                    'name',
                    'email',
                ],
            ],
        ]);
    }
}
