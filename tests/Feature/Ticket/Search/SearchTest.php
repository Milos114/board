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

    public function test_ticket_can_be_filtered_by_assigned_user(): void
    {
        $this->actingAs($user = User::factory()->create());
        $assignedUsers = User::factory()->count(2)->create();
        $lane = Lane::factory()->create()->id;
        $priority = Priority::factory()->create()->id;

        $tickets = [
            [
                'title' => 'First ticket',
                'description' => 'Content of first ticket',
                'user_id' => $user->id,
                'assigned_user_id' => $assignedUsers[0]->id
            ],
            [
                'title' => 'Second ticket',
                'description' => 'Content of second ticket',
                'user_id' => $user->id,
                'assigned_user_id' => $assignedUsers[1]->id
            ],
            [
                'title' => 'Third ticket',
                'description' => 'Content of third ticket',
                'user_id' => $user->id,
                'assigned_user_id' => null
            ],
        ];

        foreach ($tickets as $ticket) {
            Ticket::factory()->create(array_merge($ticket, [
                'lane_id' => $lane,
                'priority_id' => $priority,
            ]));
        }

        $response = $this->get("api/{$this->getApiVersion()}/tickets?filter[assigned_user]=" . $assignedUsers[0]->id);

        $response->assertOk()
            ->assertJsonFragment([
                'title' => 'First ticket',
                'description' => 'Content of first ticket',
            ])->assertJsonMissing([
                'title' => 'Second ticket',
                'description' => 'Content of second ticket',
            ])->assertJsonMissing([
                'title' => 'Third ticket',
                'description' => 'Content of third ticket',
            ]);
    }

    public function test_ticket_can_be_filtered_by_priority(): void
    {
        $this->actingAs($user = User::factory()->create());
        $priorities = Priority::factory()->count(3)->create();
        $lane = Lane::factory()->create()->id;

        $tickets = [
            [
                'title' => 'High priority ticket',
                'description' => 'Content of high priority ticket',
                'user_id' => $user->id,
                'priority_id' => $priorities[0]->id
            ],
            [
                'title' => 'Medium priority ticket', 
                'description' => 'Content of medium priority ticket',
                'user_id' => $user->id,
                'priority_id' => $priorities[1]->id
            ],
            [
                'title' => 'Low priority ticket',
                'description' => 'Content of low priority ticket', 
                'user_id' => $user->id,
                'priority_id' => $priorities[2]->id
            ],
        ];

        foreach ($tickets as $ticket) {
            Ticket::factory()->create(array_merge($ticket, [
                'lane_id' => $lane,
            ]));
        }

        $response = $this->get("api/{$this->getApiVersion()}/tickets?filter[priority]=" . $priorities[0]->id);

        $response->assertOk()
            ->assertJsonFragment([
                'title' => 'High priority ticket',
                'description' => 'Content of high priority ticket',
            ])->assertJsonMissing([
                'title' => 'Medium priority ticket',
                'description' => 'Content of medium priority ticket',
            ])->assertJsonMissing([
                'title' => 'Low priority ticket',
                'description' => 'Content of low priority ticket',
            ]);
    }

    public function test_filters_can_be_combined(): void
    {
        $this->actingAs($user = User::factory()->create());
        $assignedUser = User::factory()->create();
        $priorities = Priority::factory()->count(2)->create();
        $lanes = Lane::factory()->count(2)->create();

        $tickets = [
            [
                'title' => 'Target ticket',
                'description' => 'Target ticket description',
                'user_id' => $user->id,
                'assigned_user_id' => $assignedUser->id,
                'priority_id' => $priorities[0]->id,
                'lane_id' => $lanes[0]->id
            ],
            [
                'title' => 'Wrong priority ticket',
                'description' => 'Wrong priority description',
                'user_id' => $user->id,
                'assigned_user_id' => $assignedUser->id,
                'priority_id' => $priorities[1]->id,
                'lane_id' => $lanes[0]->id
            ],
            [
                'title' => 'Wrong lane ticket',
                'description' => 'Wrong lane description',
                'user_id' => $user->id,
                'assigned_user_id' => $assignedUser->id,
                'priority_id' => $priorities[0]->id,
                'lane_id' => $lanes[1]->id
            ],
        ];

        foreach ($tickets as $ticket) {
            Ticket::factory()->create($ticket);
        }

        $response = $this->get(
            "api/{$this->getApiVersion()}/tickets" .
            "?filter[assigned_user]={$assignedUser->id}" .
            "&filter[priority]={$priorities[0]->id}" .
            "&filter[state]={$lanes[0]->id}"
        );

        $response->assertOk()
            ->assertJsonFragment([
                'title' => 'Target ticket',
                'description' => 'Target ticket description',
            ])->assertJsonMissing([
                'title' => 'Wrong priority ticket',
                'description' => 'Wrong priority description',
            ])->assertJsonMissing([
                'title' => 'Wrong lane ticket',
                'description' => 'Wrong lane description',
            ]);
    }

    public function test_filters_can_contain_only_valid_params(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->get("api/{$this->getApiVersion()}/tickets?filter[invalid_key]=awesome");

        $response->assertSessionHasErrors('filter');
    }

    public function test_valid_filter_params_are_accepted(): void
    {
        $this->actingAs($user = User::factory()->create());
        $assignedUser = User::factory()->create();
        $priority = Priority::factory()->create();
        $lane = Lane::factory()->create();

        Ticket::factory()->create([
            'user_id' => $user->id,
            'assigned_user_id' => $assignedUser->id,
            'priority_id' => $priority->id,
            'lane_id' => $lane->id,
            'title' => 'Test ticket',
            'description' => 'Test description'
        ]);

        $response = $this->get(
            "api/{$this->getApiVersion()}/tickets" .
            "?filter[search]=Test" .
            "&filter[user]={$user->id}" .
            "&filter[assigned_user]={$assignedUser->id}" .
            "&filter[priority]={$priority->id}" .
            "&filter[state]={$lane->id}"
        );

        $response->assertOk()
            ->assertJsonFragment([
                'title' => 'Test ticket',
                'description' => 'Test description',
            ]);
    }
}
