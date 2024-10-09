<?php

namespace Tests\Feature\Ticket\History;

use App\Models\ActivityLog;
use App\Models\Lane;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_ticket_history_is_saved(): void
    {
        $this->actingAs($user = User::factory()->create());

        $ticket = Ticket::factory()->create([
            'user_id' => User::factory(),
            'lane_id' => Lane::factory(),
            'title' => 'My ticket',
            'description' => 'Awesome ticket',
        ]);

        $ticket->update([
            'title' => 'Updated title',
            'description' => 'Updated description',
        ]);

        $activityLog = ActivityLog::latest('id')->first();

        $this->assertDatabaseHas('activity_logs', [
            'event' => 'updated',
            'model_type' => Ticket::class,
            'model_id' => $ticket->id,
            'user_id' => $user->id,
        ]);

        $this->assertEquals(
            ['title' => 'My ticket', 'description' => 'Awesome ticket'],
            $activityLog->old_values
        );

        $this->assertEquals(
            ['title' => 'Updated title', 'description' => 'Updated description'],
            $activityLog->new_values
        );
    }

    public function test_create_ticket_history_is_saved(): void
    {
        $this->actingAs($user = User::factory()->create());

        $ticket = Ticket::factory()->create([
            'user_id' => User::factory(),
            'lane_id' => Lane::factory(),
            'title' => 'My ticket',
            'description' => 'Awesome ticket',
        ]);

        $activityLog = ActivityLog::first();

        $this->assertDatabaseHas('activity_logs', [
            'event' => 'created',
            'model_type' => Ticket::class,
            'model_id' => $ticket->id,
            'user_id' => $user->id,
        ]);

        $this->assertEquals(
            [],
            $activityLog->old_values
        );

        $this->assertEquals($ticket->title, $activityLog->new_values['title']);
        $this->assertEquals($ticket->description, $activityLog->new_values['description']);
    }

    public function test_delete_ticket_history_is_saved(): void
    {
        $this->actingAs($user = User::factory()->create());

        $ticket = Ticket::factory()->create([
            'user_id' => User::factory(),
            'lane_id' => Lane::factory(),
            'title' => 'My ticket',
            'description' => 'Awesome ticket',
        ]);

        $ticket->delete();

        $activityLog = ActivityLog::latest('id')->first();

        $this->assertDatabaseHas('activity_logs', [
            'event' => 'deleted',
            'model_type' => Ticket::class,
            'model_id' => $ticket->id,
            'user_id' => $user->id,
        ]);

        $this->assertEquals($ticket->title, $activityLog->old_values['title']);
        $this->assertEquals($ticket->description, $activityLog->old_values['description']);

        $this->assertEquals(
            [],
            $activityLog->new_values
        );
    }
}
