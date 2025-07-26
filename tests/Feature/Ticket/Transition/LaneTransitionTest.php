<?php

namespace Tests\Feature\Ticket\Transition;

use App\Enums\LaneEnum;
use App\Models\ActivityLog;
use App\Models\Lane;
use App\Models\Priority;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LaneTransitionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Lane $backlogLane;
    protected Lane $todoLane;
    protected Lane $inProgressLane;
    protected Lane $doneLane;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
        
        // Create lanes with specific names that match LaneEnum
        $this->backlogLane = Lane::factory()->create(['name' => LaneEnum::BACK_LOG->value]);
        $this->todoLane = Lane::factory()->create(['name' => LaneEnum::TO_DO->value]);
        $this->inProgressLane = Lane::factory()->create(['name' => LaneEnum::IN_PROGRESS->value]);
        $this->doneLane = Lane::factory()->create(['name' => LaneEnum::DONE->value]);
    }

    /** @test */
    public function test_ticket_can_transition_from_backlog_to_todo(): void
    {
        $ticket = $this->createTicketInLane($this->backlogLane);

        $response = $this->putJsonTicketUpdate($ticket, ['lane_id' => $this->todoLane->id]);

        $response->assertOk();
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'lane_id' => $this->todoLane->id,
        ]);
    }

    /** @test */
    public function test_ticket_can_transition_from_done_to_todo(): void
    {
        $ticket = $this->createTicketInLane($this->doneLane);

        $response = $this->putJsonTicketUpdate($ticket, ['lane_id' => $this->todoLane->id]);

        $response->assertOk();
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'lane_id' => $this->todoLane->id,
        ]);
    }

    /** @test */
    public function test_ticket_can_transition_from_todo_to_in_progress(): void
    {
        $ticket = $this->createTicketInLane($this->todoLane);

        $response = $this->putJsonTicketUpdate($ticket, ['lane_id' => $this->inProgressLane->id]);

        $response->assertOk();
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'lane_id' => $this->inProgressLane->id,
        ]);
    }

    /** @test */
    public function test_ticket_can_transition_from_in_progress_to_done(): void
    {
        $ticket = $this->createTicketInLane($this->inProgressLane);

        $response = $this->putJsonTicketUpdate($ticket, ['lane_id' => $this->doneLane->id]);

        $response->assertOk();
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'lane_id' => $this->doneLane->id,
        ]);
    }

    /** @test */
    public function test_ticket_cannot_transition_from_backlog_to_in_progress(): void
    {
        $ticket = $this->createTicketInLane($this->backlogLane);

        $response = $this->putJsonTicketUpdate($ticket, ['lane_id' => $this->inProgressLane->id]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['lane_id']);
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'lane_id' => $this->backlogLane->id, // Should remain unchanged
        ]);
    }

    /** @test */
    public function test_ticket_cannot_transition_from_backlog_to_done(): void
    {
        $ticket = $this->createTicketInLane($this->backlogLane);

        $response = $this->putJsonTicketUpdate($ticket, ['lane_id' => $this->doneLane->id]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['lane_id']);
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'lane_id' => $this->backlogLane->id,
        ]);
    }

    /** @test */
    public function test_ticket_cannot_transition_from_todo_to_backlog(): void
    {
        $ticket = $this->createTicketInLane($this->todoLane);

        $response = $this->putJsonTicketUpdate($ticket, ['lane_id' => $this->backlogLane->id]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['lane_id']);
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'lane_id' => $this->todoLane->id,
        ]);
    }

    /** @test */
    public function test_ticket_cannot_transition_from_todo_to_done(): void
    {
        $ticket = $this->createTicketInLane($this->todoLane);

        $response = $this->putJsonTicketUpdate($ticket, ['lane_id' => $this->doneLane->id]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['lane_id']);
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'lane_id' => $this->todoLane->id,
        ]);
    }

    /** @test */
    public function test_ticket_cannot_transition_from_in_progress_to_backlog(): void
    {
        $ticket = $this->createTicketInLane($this->inProgressLane);

        $response = $this->putJsonTicketUpdate($ticket, ['lane_id' => $this->backlogLane->id]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['lane_id']);
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'lane_id' => $this->inProgressLane->id,
        ]);
    }

    /** @test */
    public function test_ticket_cannot_transition_from_in_progress_to_todo(): void
    {
        $ticket = $this->createTicketInLane($this->inProgressLane);

        $response = $this->putJsonTicketUpdate($ticket, ['lane_id' => $this->todoLane->id]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['lane_id']);
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'lane_id' => $this->inProgressLane->id,
        ]);
    }

    /** @test */
    public function test_ticket_cannot_transition_from_done_to_backlog(): void
    {
        $ticket = $this->createTicketInLane($this->doneLane);

        $response = $this->putJsonTicketUpdate($ticket, ['lane_id' => $this->backlogLane->id]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['lane_id']);
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'lane_id' => $this->doneLane->id,
        ]);
    }

    /** @test */
    public function test_ticket_cannot_transition_from_done_to_in_progress(): void
    {
        $ticket = $this->createTicketInLane($this->doneLane);

        $response = $this->putJsonTicketUpdate($ticket, ['lane_id' => $this->inProgressLane->id]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['lane_id']);
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'lane_id' => $this->doneLane->id,
        ]);
    }

    /** @test */
    public function test_ticket_can_stay_in_same_lane(): void
    {
        $ticket = $this->createTicketInLane($this->todoLane);

        $response = $this->putJsonTicketUpdate($ticket, ['lane_id' => $this->todoLane->id]);

        $response->assertOk();
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'lane_id' => $this->todoLane->id,
        ]);
    }

    /** @test */
    public function test_ticket_transition_with_invalid_lane_id_fails(): void
    {
        $ticket = $this->createTicketInLane($this->todoLane);

        $response = $this->putJsonTicketUpdate($ticket, ['lane_id' => 99999]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['lane_id']);
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'lane_id' => $this->todoLane->id, // Should remain unchanged
        ]);
    }

    /** @test */
    public function test_lane_transition_logs_activity(): void
    {
        $ticket = $this->createTicketInLane($this->todoLane);
        
        // Clear any existing activity logs
        ActivityLog::query()->delete();

        $response = $this->putJsonTicketUpdate($ticket, [
            'lane_id' => $this->inProgressLane->id,
            'title' => $ticket->title, // Keep other fields to avoid validation errors
            'description' => $ticket->description,
        ]);

        $response->assertOk();

        // Check that activity was logged
        $this->assertDatabaseHas('activity_logs', [
            'event' => 'updated',
            'model_type' => Ticket::class,
            'model_id' => $ticket->id,
            'user_id' => $this->user->id,
        ]);

        $activityLog = ActivityLog::where('model_id', $ticket->id)
            ->where('event', 'updated')
            ->first();

        $this->assertNotNull($activityLog);
        $this->assertEquals($this->todoLane->id, $activityLog->old_values['lane_id']);
        $this->assertEquals($this->inProgressLane->id, $activityLog->new_values['lane_id']);
    }

    /** @test */
    public function test_unauthenticated_user_cannot_transition_ticket_lanes(): void
    {
        $ticket = $this->createTicketInLane($this->todoLane);

        // Reset authentication state properly for JWT
        $this->app['auth']->forgetGuards();
        
        $response = $this->putJson("api/{$this->getApiVersion()}/tickets/{$ticket->id}", [
            'title' => $ticket->title,
            'description' => $ticket->description,
            'lane_id' => $this->inProgressLane->id,
        ], [
            'Accept' => 'application/json',
            // Explicitly don't include authorization header
        ]);

        // Based on the JWT exception, check for the appropriate error response
        $response->assertStatus(401);
    }

    /**
     * Helper method to create a ticket in a specific lane
     */
    private function createTicketInLane(Lane $lane): Ticket
    {
        return Ticket::factory()->create([
            'user_id' => $this->user->id,
            'lane_id' => $lane->id,
            'priority_id' => Priority::factory()->create()->id,
            'title' => 'Test Ticket',
            'description' => 'Test Description',
        ]);
    }

    /**
     * Helper method to make PUT request to update ticket
     */
    private function putJsonTicketUpdate(Ticket $ticket, array $data): \Illuminate\Testing\TestResponse
    {
        return $this->putJson("api/{$this->getApiVersion()}/tickets/{$ticket->id}", array_merge([
            'title' => $ticket->title,
            'description' => $ticket->description,
        ], $data), [
            'Accept' => 'application/json',
        ]);
    }
}