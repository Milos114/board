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

    public function test_attached_files_are_included(): void
    {
        $this->actingAs($user = User::factory()->create());
        $lane = Lane::factory()->create();
        $ticket = $user->tickets()->create([
            'lane_id' => $lane->id,
            'title' => 'My ticket',
            'description' => 'Content of ticket',
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

        $response = $this->get("api/{$this->getApiVersion()}/tickets/$ticket->id");

        $this->assertArrayHasKey('attachments', $response->json());
        $this->assertIsArray($response->json()['attachments']);
        $this->assertNotEmpty($response->json()['attachments']);
        $this->assertCount(1, $response->json()['attachments']);
    }

    public function test_attached_files_are_not_included_when_ticket_has_no_attachments(): void
    {
        $this->actingAs($user = User::factory()->create());
        $ticket = $user->tickets()->create([
            'title' => 'My ticket',
            'description' => 'Content of ticket',
        ]);

        $response = $this->get("api/{$this->getApiVersion()}/tickets/$ticket->id");

        $this->assertCount(0, $response->json()['attachments']);
    }
}
