<?php

namespace Tests\Feature\Priority;

use App\Models\Priority;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriorityIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_priority_index_can_be_listed(): void
    {
        $this->withoutExceptionHandling();
        $this->actingAs(User::factory()->create());
        Priority::factory()->count(3)->create();

        $response = $this->get("api/{$this->getApiVersion()}/priorities");

        $response->assertJsonStructure([
            'data' => [
                '0' => [
                    'id',
                    'name'
                ]
            ]
        ]);
    }
}
