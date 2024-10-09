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
        $state = Lane::factory()->create();
        $ticket = $user->tickets()->create([
            'title' => 'My ticket',
            'description' => 'Content of ticket',
            'state_id' => $state->id,
        ]);

        $this->put("api/{$this->getApiVersion()}/tickets/$ticket->id", [
            'user_id' => $user->id,
            'state_id' => $state->id,
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
