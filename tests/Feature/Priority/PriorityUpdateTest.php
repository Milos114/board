<?php

namespace Tests\Feature\Priority;

use App\Models\Priority;
use App\Models\User;
use Tests\TestCase;

class PriorityUpdateTest extends TestCase
{
    public function test_priority_can_be_updated(): void
    {
        $this->actingAs(User::factory()->create());

        $priority = Priority::factory()->create([
            'name' => 'Low',
        ]);

        $this->put("api/{$this->getApiVersion()}/priorities/" . $priority->id, [
            'name' => 'High',
        ])->assertOk();

        $this->assertDatabaseHas('priorities', [
            'id' => $priority->id,
            'name' => 'High',
        ]);
    }

    public function test_unauthenticated_user_cannot_update_priority(): void
    {
        $priority = Priority::factory()->create([
            'name' => 'Low',
        ]);

        $response = $this->put("api/{$this->getApiVersion()}/priorities/" . $priority->id, [
            'name' => 'High',
        ], ['Accept' => 'application/json']);

        $response->assertUnauthorized();
    }

    public function test_priority_name_is_required(): void
    {
        $this->actingAs(User::factory()->create());

        $priority = Priority::factory()->create([
            'name' => 'Low',
        ]);

        $response = $this->put("api/{$this->getApiVersion()}/priorities/" . $priority->id, [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }
}
