<?php

namespace Tests\Feature\Priority;

use App\Models\Priority;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriorityShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_priority_can_be_shown(): void
    {
        $this->actingAs(User::factory()->create());
        $priority = Priority::factory()->create([
            'name' => 'Low',
        ]);

        $this->get("api/{$this->getApiVersion()}/priorities/$priority->id")
            ->assertJsonFragment([
                'id' => $priority->id,
                'name' => 'Low',
            ]);
    }

    public function test_unauthorized_user_cannot_show_priority(): void
    {
        $priority = Priority::factory()->create([
            'name' => 'Low',
        ]);

        $this->get("api/{$this->getApiVersion()}/priorities/$priority->id", ['Accept' => 'application/json'])
            ->assertUnauthorized();
    }
}
