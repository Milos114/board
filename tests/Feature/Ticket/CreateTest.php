<?php

namespace Tests\Feature\Ticket;

use App\Http\Requests\TicketRequest;
use App\Models\Priority;
use App\Models\Lane;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_can_be_created(): void
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = User::factory()->create());
        $lane = Lane::factory()->create();
        $priority = Priority::factory()->create();

        $response = $this->post("api/{$this->getApiVersion()}/tickets", [
            'user_id' => $user->id,
            'title' => 'My ticket',
            'description' => 'Content of ticket',
            'lane_id' => $lane->id,
            'priority_id' => $priority->id,
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('tickets', [
            'user_id' => $user->id,
            'lane_id' => $lane->id,
            'priority_id' => $priority->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_create_ticket(): void
    {
        $response = $this->post("api/{$this->getApiVersion()}/tickets", [
            'title' => 'My ticket',
            'description' => 'Content of ticket',
            'lane_id' => 1,
            'priority_id' => 1,
        ], ['Accept' => 'application/json']);

        $response->assertUnauthorized()
            ->assertJson(['message' => 'Unauthenticated.']);

        $this->assertGuest()
            ->assertDatabaseMissing('tickets', [
                'title' => 'My ticket',
                'description' => 'Content of ticket',
            ]);
    }

    public function test_ticket_creation_requires_title_and_description(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->post("api/{$this->getApiVersion()}/tickets", [
            'description' => '',
            'title' => '',
        ]);

        $response->assertSessionHasErrors(['title', 'description']);
        $this->assertDatabaseCount('tickets', 0);
    }

    public function test_request_passes_with_nullable_fields(): void
    {
        $data = [
            'user_id' => null,
            'title' => 'Sample Title',
            'description' => 'Sample Description',
            'lane_id' => null,
            'priority_id' => null,
        ];

        $validator = Validator::make($data, (new TicketRequest())->rules());

        $this->assertTrue($validator->passes());
    }
}
