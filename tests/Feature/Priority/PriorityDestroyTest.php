<?php

namespace Tests\Feature\Priority;

use App\Models\Priority;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriorityDestroyTest extends TestCase
{
    use RefreshDatabase;

    public function test_priority_can_be_destroyed(): void
    {
        $this->actingAs(User::factory()->create());
        $priority = Priority::factory()->create();

        $this->delete("api/{$this->getApiVersion()}/priorities/$priority->id")
            ->assertNoContent();

        $this->assertDatabaseMissing('priorities', [
            'id' => $priority->id,
        ]);
    }

    public function test_tickets_has_null_priority_id_after_priority_is_destroyed(): void
    {
        $this->actingAs(User::factory()->create());
        $priority = Priority::factory()->create();
        $priority->tickets()->create(
            ['title' => 'ticket', 'description' => 'description'],
        );

        $this->delete("api/{$this->getApiVersion()}/priorities/$priority->id")
            ->assertNoContent();

        $this->assertDatabaseHas('tickets', [
            'title' => 'ticket',
            'description' => 'description',
            'priority_id' => null,
        ]);
    }

    public function test_priority_can_not_be_destroyed_by_an_unauthenticated_user(): void
    {
        $priority = Priority::factory()->create();

        $this->delete("api/{$this->getApiVersion()}/priorities/$priority->id", headers: ['Accept' => 'application/json'])
            ->assertUnauthorized();
    }
}
