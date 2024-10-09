<?php

namespace Tests\Feature\Ticket\Search;

use App\Models\Priority;
use App\Models\Lane;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_tickets_can_be_searched_by_description(): void
    {
        $this->actingAs($user = User::factory()->create());
        $state = Lane::factory()->create();
        $user->tickets()->createMany([
            [
                'title' => 'My ticket',
                'description' => 'Awesome ticket',
                'user_id' => $user->id,
                'state_id' => $state->id
            ],
            [
                'title' => 'Another ticket',
                'description' => 'Content of another ticket',
                'user_id' => $user->id,
                'state_id' => $state->id
            ],
            [
                'title' => 'Third ticket',
                'description' => 'Content of third ticket',
                'user_id' => $user->id,
                'state_id' => $state->id
            ],
        ]);

        $response = $this->get("api/{$this->getApiVersion()}/tickets?filter[search]=Awesome");

        $response->assertOk()
            ->assertJsonFragment([
                'title' => 'My ticket',
                'description' => 'Awesome ticket',
            ]);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_tickets_can_be_searched_by_title(): void
    {
        $this->actingAs($user = User::factory()->create());
        $state = Lane::factory()->create();
        $user->tickets()->createMany([
            [
                'title' => 'My ticket',
                'description' => 'Content of awesome ticket',
                'user_id' => $user->id,
                'state_id' => $state->id
            ],
            [
                'title' => 'Another ticket',
                'description' => 'Content of another ticket',
                'user_id' => $user->id,
                'state_id' => $state->id
            ],
            [
                'title' => 'Third ticket',
                'description' => 'Content of third ticket',
                'user_id' => $user->id,
                'state_id' => $state->id
            ],
        ]);

        $response = $this->get("api/{$this->getApiVersion()}/tickets?filter[search]=My ticket");

        $response->assertOk()
            ->assertJsonFragment([
                'title' => 'My ticket',
                'description' => 'Content of awesome ticket',
            ])->assertJsonMissing([
                'title' => 'Another ticket',
                'description' => 'Content of another ticket',
            ])->assertJsonMissing([
                'title' => 'Third ticket',
                'description' => 'Content of third ticket',
            ]);
    }

    public function test_ticket_can_be_filtered_by_lane(): void
    {
        $this->actingAs($user = User::factory()->create());
        $lane = Lane::factory()->create(['name' => 'to_do']);
        $lane2 = Lane::factory()->create(['name' => 'in_progress']);
        $lane3 = Lane::factory()->create(['name' => 'done']);
        $user->tickets()->createMany([
            [
                'title' => 'My ticket',
                'description' => 'Content of first ticket',
                'user_id' => $user->id,
                'lane_id' => $lane->id
            ],
            [
                'title' => 'Another ticket',
                'description' => 'Content of another ticket',
                'user_id' => $user->id,
                'lane_id' => $lane2->id
            ],
            [
                'title' => 'Third ticket',
                'description' => 'Content of third ticket',
                'user_id' => $user->id,
                'lane_id' => $lane3->id
            ],
        ]);

        $response = $this->get("api/{$this->getApiVersion()}/tickets?filter[state]=" . $lane->id);

        $response->assertOk()
            ->assertJsonFragment([
                'title' => 'My ticket',
                'description' => 'Content of first ticket',
            ])->assertJsonMissing([
                'title' => 'Another ticket',
                'description' => 'Content of another ticket',
            ])->assertJsonMissing([
                'title' => 'Third ticket',
                'description' => 'Content of third ticket',
            ]);
    }

    public function test_ticket_can_be_filtered_by_user(): void
    {
        $this->actingAs($user = User::factory()->create());
        $users = User::factory()->count(2)->create();
        $lane = Lane::factory()->create()->id;
        $priority = Priority::factory()->create()->id;

        $tickets = [
            [
                'title' => 'My ticket',
                'description' => 'Content of first ticket',
                'user_id' => $user->id
            ],
            [
                'title' => 'Another ticket',
                'description' => 'Content of another ticket',
                'user_id' => $users[0]->id
            ],
            [
                'title' => 'Third ticket',
                'description' => 'Content of third ticket',
                'user_id' => $users[1]->id
            ],
        ];

        foreach ($tickets as $ticket) {
            Ticket::factory()->create(array_merge($ticket, [
                'lane_id' => $lane,
                'priority_id' => $priority,
            ]));
        }

        $response = $this->get("api/{$this->getApiVersion()}/tickets?filter[user]=" . $user->id);

        $response->assertOk()
            ->assertJsonFragment([
                'title' => 'My ticket',
                'description' => 'Content of first ticket',
            ])->assertJsonMissing([
                'title' => 'Another ticket',
                'description' => 'Content of another ticket',
            ])->assertJsonMissing([
                'title' => 'Third ticket',
                'description' => 'Content of third ticket',
            ]);
    }

    public function test_filters_can_contain_only_valid_params(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->get("api/{$this->getApiVersion()}/tickets?filter[invalid_key]=awesome");

        $response->assertSessionHasErrors('filter');
    }
}
