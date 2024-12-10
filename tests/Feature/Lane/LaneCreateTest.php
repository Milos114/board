<?php

namespace Tests\Feature\Lane;

use App\Models\Lane;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaneCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_lane_can_be_created(): void
    {
        $this->actingAs(User::factory()->create());
        $lane = Lane::factory()->make();

        $this->post("api/{$this->getApiVersion()}/lanes", $lane->toArray())
            ->assertCreated();

        $this->assertDatabaseHas('lanes', $lane->toArray());
    }

    public function test_unauthenticated_user_cannot_list_lanes(): void
    {
        $response = $this->post("api/{$this->getApiVersion()}/lanes", Lane::factory()->make()->toArray(), ['Accept' => 'application/json']);

        $response->assertUnauthorized();
    }

    public function test_lane_creation_requires_name(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->post("api/{$this->getApiVersion()}/lanes", ['name' => '']);

        $response->assertSessionHasErrors('name');
    }

    public function test_lane_name_must_be_unique(): void
    {
        $this->actingAs(User::factory()->create());
        $lane = Lane::factory()->create();

        $response = $this->post("api/{$this->getApiVersion()}/lanes", $lane->toArray());

        $response->assertSessionHasErrors('name');
    }

    public function test_lane_name_must_be_valid(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->post("api/{$this->getApiVersion()}/lanes", ['name' => 'invalid']);

        $response->assertSessionHasErrors('name');
    }
}
