<?php

namespace Tests\Feature\Ticket;

use App\Models\Lane;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_can_be_updated(): void
    {
        $this->actingAs($user = User::factory()->create());
        $lane = Lane::factory()->create();
        $ticket = $user->tickets()->create([
            'title' => 'My ticket',
            'description' => 'Content of ticket',
            'lane_id' => $lane->id,
        ]);

        $this->put("api/{$this->getApiVersion()}/tickets/$ticket->id", [
            'user_id' => $user->id,
            'lane_id' => $lane->id,
            'title' => 'Updated ticket',
            'description' => 'Updated content of ticket',
        ])->assertOk();

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'title' => 'Updated ticket',
            'description' => 'Updated content of ticket',
        ]);
    }

    public function test_unauthenticated_user_cannot_update_ticket(): void
    {
        $ticket = User::factory()->create()->tickets()->create([
            'title' => 'My ticket',
            'description' => 'Content of ticket',
        ]);

        $response = $this->put("api/{$this->getApiVersion()}/tickets/$ticket->id", [
            'title' => 'Updated ticket',
            'description' => 'Updated content of ticket',
        ], ['Accept' => 'application/json']);

        $response->assertUnauthorized();
    }

    public function test_ticket_can_change_status_from_backlog_to_to_do(): void
    {
        $this->actingAs($user = User::factory()->create());
        $backlog = Lane::factory()->create(['name' => 'back_log']);
        $toDo = Lane::factory()->create(['name' => 'to_do']);
        $ticket = $user->tickets()->create([
            'title' => 'My ticket',
            'description' => 'Content of ticket',
            'lane_id' => $backlog->id,
        ]);

        $this->put("api/{$this->getApiVersion()}/tickets/$ticket->id", [
            'lane_id' => $toDo->id,
            'title' => 'Updated ticket',
            'description' => 'Updated content of ticket',
        ])->assertOk();
    }

    public function test_ticket_can_not_change_status_from_done_to_backlog(): void
    {
        $this->actingAs($user = User::factory()->create());
        $backlog = Lane::factory()->create(['name' => 'back_log']);
        $done = Lane::factory()->create(['name' => 'done']);
        $ticket = $user->tickets()->create([
            'title' => 'My ticket',
            'description' => 'Content of ticket',
            'lane_id' => $done->id,
        ]);

        $this->put("api/{$this->getApiVersion()}/tickets/$ticket->id", [
            'lane_id' => $backlog->id,
            'title' => 'Updated ticket',
            'description' => 'Updated content of ticket',
        ])->assertSessionHasErrors('lane_id');
    }

    public function test_ticket_update_requires_title_and_description(): void
    {
        $this->actingAs($user = User::factory()->create());
        $ticket = $user->tickets()->create([
            'title' => 'My ticket',
            'description' => 'Content of ticket',
        ]);

        $response = $this->put("api/{$this->getApiVersion()}/tickets/$ticket->id", [
            'title' => '',
            'description' => '',
        ]);

        $response->assertSessionHasErrors(['title', 'description']);
    }
}
