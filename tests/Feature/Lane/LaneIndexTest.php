<?php

namespace Tests\Feature\Lane;

use App\Models\Lane;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaneIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_lane_index_can_be_listed(): void
    {
        $this->actingAs(User::factory()->create());
        $lane = Lane::factory()->create([
            'name' => 'to_do',
        ]);
        $lane2 = Lane::factory()->create([
            'name' => 'in_progress',
        ]);
        $lane3 = Lane::factory()->create([
            'name' => 'done',
        ]);
        $lane->tickets()->createMany(
            Ticket::factory()->count(3)->make()->toArray()
        );
        $lane2->tickets()->createMany(
            Ticket::factory()->count(2)->make()->toArray()
        );
        $lane3->tickets()->createMany(
            Ticket::factory()->count(1)->make()->toArray()
        );

        $response = $this->get("api/{$this->getApiVersion()}/lanes");
        $response->assertJsonStructure([
            'data' => [
                '0' => [
                    'id',
                    'name',
                    'tickets_count',
                    'tickets' => [
                        '0' => [
                            'id',
                            'user' => [
                                'id',
                                'name',
                                'email',
                            ],
                            'priority' => [
                                'id',
                                'name',
                            ],
                            'title',
                            'description',
                            'created_at',
                        ],
                    ],
                ],
            ],
        ])->assertJsonFragment([
            'id' => $lane->id,
            'name' => 'to_do',
            'tickets_count' => 3,
        ])->assertJsonFragment([
            'id' => $lane2->id,
            'name' => 'in_progress',
            'tickets_count' => 2,
        ])->assertJsonFragment([
            'id' => $lane3->id,
            'name' => 'done',
            'tickets_count' => 1,
        ]);
    }

    public function test_unauthenticated_user_cannot_list_lanes(): void
    {
        $this->get("api/{$this->getApiVersion()}/lanes", ['Accept' => 'application/json'])
            ->assertUnauthorized()
            ->assertJson(['message' => 'Unauthenticated.']);
    }
}
