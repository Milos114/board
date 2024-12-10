<?php

namespace Tests\Feature\Lane;

use App\Models\Lane;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaneUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_lane_can_be_updated(): void
    {
        $this->actingAs(User::factory()->create());

        $lane = Lane::factory()->create([
            'name' => 'to_do',
        ]);

        $this->put("api/{$this->getApiVersion()}/lanes/$lane->id", ['name' => 'in_progress'])
            ->assertOk();
    }

    public function test_lane_name_is_required(): void
    {
        $this->actingAs(User::factory()->create());

        $lane = Lane::factory()->create();

        $this->put("api/{$this->getApiVersion()}/lanes/$lane->id", ['name' => ''])
            ->assertSessionHasErrors('name');
    }

    public function test_lane_name_must_be_valid(): void
    {
        $this->actingAs(User::factory()->create());

        $lane = Lane::factory()->create();

        $this->put("api/{$this->getApiVersion()}/lanes/$lane->id", ['name' => 'random_name'])
            ->assertSessionHasErrors('name');
    }

    public function test_lane_name_must_be_unique(): void
    {
        $this->actingAs(User::factory()->create());

        $lane = Lane::factory()->create();
        $lane2 = Lane::factory()->create();

        $this->put("api/{$this->getApiVersion()}/lanes/$lane->id", ['name' => $lane2->name])
            ->assertSessionHasErrors('name');
    }

    public function test_unauthenticated_user_cannot_update_lane(): void
    {
        $lane = Lane::factory()->create();

        $response = $this->put(
            "api/{$this->getApiVersion()}/lanes/$lane->id",
            ['name' => 'updated'],
            ['Accept' => 'application/json']
        );

        $response->assertUnauthorized();
    }
}
