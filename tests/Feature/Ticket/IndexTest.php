<?php

namespace Tests\Feature\Ticket;

use App\Models\Lane;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_tickets_can_be_listed(): void
    {
        $this->actingAs($user = User::factory()->create());
        $tickets = Ticket::factory()->count(3)->create([
            'user_id' => $user->id,
            'lane_id' => Lane::factory()->create()->id,
            'priority_id' => null,
        ]);

        $response = $this->get("api/{$this->getApiVersion()}/tickets");

        $firstTicket = $tickets->first();
        $response->assertOk()
            ->assertJsonFragment([
                'id' => $firstTicket->id,
                'title' => $firstTicket->title,
                'description' => $firstTicket->description,
            ]);
    }

    public function test_unauthenticated_user_cannot_list_tickets(): void
    {
        $this->get("api/{$this->getApiVersion()}/tickets", ['Accept' => 'application/json'])
            ->assertUnauthorized()
            ->assertJson(['message' => 'Unauthenticated.']);
    }
}
