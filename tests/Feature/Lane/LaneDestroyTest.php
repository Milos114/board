<?php

namespace Tests\Feature\Lane;

use App\Models\Lane;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaneDestroyTest extends TestCase
{
    use RefreshDatabase;

    public function test_lane_can_be_destroyed(): void
    {
        $this->actingAs(User::factory()->create());

        $lane = Lane::factory()->create();

        $this->delete("api/{$this->getApiVersion()}/lanes/$lane->id")
            ->assertNoContent();

        $this->assertDatabaseMissing('lanes', $lane->toArray());
    }

    public function test_lane_tickets_has_null_lane_id_after_lane_is_destroyed(): void
    {
        $this->actingAs(User::factory()->create());
        $lane = Lane::factory()->create();
        $lane->tickets()->create(
            ['title' => 'ticket', 'description' => 'description'],
        );

        $this->delete("api/{$this->getApiVersion()}/lanes/$lane->id")
            ->assertNoContent();

        $this->assertDatabaseHas('tickets', [
            'title' => 'ticket',
            'description' => 'description',
            'lane_id' => null,
        ]);
    }

    public function test_unauthenticated_user_cannot_destroy_lane(): void
    {
        $lane = Lane::factory()->create();

        $this->delete("api/{$this->getApiVersion()}/lanes/$lane->id");

        $this->assertDatabaseHas('lanes', [
            'id' => $lane->id,
        ]);
    }
}
