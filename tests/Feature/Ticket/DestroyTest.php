<?php

namespace Tests\Feature\Ticket;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_can_be_deleted(): void
    {
        $this->actingAs($user = User::factory()->create());
        $ticket = $user->tickets()->create([
            'title' => 'My ticket',
            'description' => 'Content of ticket',
        ]);

        $response = $this->delete("api/{$this->getApiVersion()}/tickets/$ticket->id");

        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('tickets', [
            'id' => $ticket->id,
            'title' => $ticket->title,
            'description' => $ticket->description,
        ]);
    }

    public function test_unauthenticated_user_cannot_delete_ticket(): void
    {
        $ticket = User::factory()->create()->tickets()->create([
            'title' => 'My ticket',
            'description' => 'Content of ticket',
        ]);

        $response = $this->delete("api/{$this->getApiVersion()}/tickets/$ticket->id", headers: ['Accept' => 'application/json']);

        $response->assertUnauthorized();
    }

    public function test_ticket_will_have_null_user_id_after_user_deletion(): void
    {
        $this->actingAs($user = User::factory()->create());
        $ticket = $user->tickets()->create([
            'title' => 'My ticket',
            'description' => 'Content of ticket',
        ]);

        $user->delete();

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'user_id' => null,
        ]);
    }
}
