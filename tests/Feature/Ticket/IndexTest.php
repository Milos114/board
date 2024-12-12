<?php

namespace Tests\Feature\Ticket;

use App\Models\Lane;
use App\Models\Ticket;
use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
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

    public function test_ticket_files_data_is_included(): void
    {
        $this->actingAs($user = UserFactory::new()->create());
        Storage::fake('public');

        $ticket = Ticket::factory()->create([
            'user_id' => $user->id,
            'lane_id' => Lane::factory()->create()->id,
            'priority_id' => null,
        ]);
        $ticket->attachments()->create([
            'uploaded_by' => $user->id,
            'file_name' => 'photo1.jpg',
            'file_path' => 'attachments/photo1.jpg',
            'mime_type' => 'image/jpeg',
            'file_hash' => 'hash',
            'file_size' => 1024,
            'file_extension' => 'jpg',
        ]);

        $response = $this->get("api/{$this->getApiVersion()}/tickets");

        $this->assertArrayHasKey('attachments', $response->json('data.0'));
        $this->assertCount(1, $response->json('data.0.attachments'));
    }

    public function test_ticket_attachments_is_not_included_when_ticket_has_no_attachments(): void
    {
        $this->actingAs($user = UserFactory::new()->create());
        Ticket::factory(1)->create([
            'user_id' => $user->id,
            'lane_id' => Lane::factory()->create()->id,
            'priority_id' => null,
        ]);

        $response = $this->get("api/{$this->getApiVersion()}/tickets");

        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('attachments', $response->json('data.0'));
        $this->assertCount(0, $response->json('data.0.attachments'));
    }

    public function test_assigned_user_data_is_included(): void
    {
        $this->actingAs($user = UserFactory::new()->create());
        $ticket = Ticket::factory()->create([
            'user_id' => $user->id,
            'assigned_user_id' => UserFactory::new()->create()->id,
            'lane_id' => Lane::factory()->create()->id,
            'priority_id' => null,
        ]);

        $response = $this->get("api/{$this->getApiVersion()}/tickets");

        $this->assertArrayHasKey('user', $response->json('data.0'));
        $this->assertArrayHasKey('id', $response->json('data.0.user'));
        $this->assertArrayHasKey('name', $response->json('data.0.user'));
        $this->assertArrayHasKey('assigned_user', $response->json('data.0'));
    }
}
