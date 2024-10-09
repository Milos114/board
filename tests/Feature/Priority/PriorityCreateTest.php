<?php

namespace Tests\Feature\Priority;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriorityCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_priority_can_be_created(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->post("api/{$this->getApiVersion()}/priorities", [
            'name' => 'Low',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('priorities', [
            'name' => 'Low',
        ]);
    }

    public function test_unauthorized_user_cannot_create_priority(): void
    {
        $response = $this->post("api/{$this->getApiVersion()}/priorities", [
            'name' => 'Low',
        ], ['Accept' => 'application/json']);

        $response->assertUnauthorized();
    }
}
