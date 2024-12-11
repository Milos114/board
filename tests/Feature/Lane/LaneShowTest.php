<?php

namespace Tests\Feature\Lane;

use App\Models\Lane;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaneShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_lane_can_be_shown(): void
    {
        $this->actingAs(User::factory()->create());
        $lane = Lane::factory()->create();

        $this->get("api/{$this->getApiVersion()}/lanes/$lane->id")
            ->assertOk()
            ->assertJsonFragment([
                'id' => $lane->id,
                'name' => $lane->name,
            ]);
    }

    public function test_unauthenticated_user_cannot_show_lane(): void
    {
        $lane = Lane::factory()->create();

        $response = $this->get("api/{$this->getApiVersion()}/lanes/$lane->id", ['Accept' => 'application/json']);

        $response->assertUnauthorized();
    }
}
