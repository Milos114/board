<?php

namespace Tests\Feature\Lane;

use App\Models\Lane;
use App\Models\User;
use Tests\TestCase;

class LaneUpdateTest extends TestCase
{
    public function test_lane_can_be_updated(): void
    {
        $this->actingAs(User::factory()->create());

        $lane = Lane::factory()->create();

        $this->put("api/{$this->getApiVersion()}/lanes/$lane->id", ['name' => 'updated'])
            ->assertOk();
    }

    public function test_unauthenticated_user_cannot_update_lane(): void
    {
        $lane = Lane::factory()->create();

        $response = $this->put("api/{$this->getApiVersion()}/lanes/$lane->id", ['name' => 'updated'], ['Accept' => 'application/json']);

        $response->assertUnauthorized();
    }
}
