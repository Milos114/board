<?php

namespace Tests\Feature\Ticket;

use App\Models\Lane;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_single_ticket_can_be_retrieved(): void
    {
        $this->actingAs($user = User::factory()->create());
        $ticket = $user->tickets()->create([
            'title' => 'My ticket',
            'description' => 'Content of ticket',
        ]);

        $response = $this->get("api/{$this->getApiVersion()}/tickets/$ticket->id");

        $response->assertOk()
            ->assertJsonFragment([
                'id' => $ticket->id,
                'title' => $ticket->title,
                'description' => $ticket->description,
            ]);
    }

    public function test_unauthenticated_user_cannot_retrieve_ticket(): void
    {
        $ticket = User::factory()->create()->tickets()->create([
            'title' => 'My ticket',
            'description' => 'Content of ticket',
        ]);

        $response = $this->get("api/{$this->getApiVersion()}/tickets/$ticket->id", ['Accept' => 'application/json']);

        $response->assertUnauthorized();
    }
}
