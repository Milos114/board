<?php

namespace Tests\Unit\Rules;

use App\Enums\LaneEnum;
use App\Models\Lane;
use App\Models\Ticket;
use App\Rules\LaneTransitionRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaneTransitionRuleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_rule_allows_valid_transition_from_backlog_to_todo(): void
    {
        $backlogLane = Lane::factory()->create(['name' => LaneEnum::BACK_LOG->value]);
        $todoLane = Lane::factory()->create(['name' => LaneEnum::TO_DO->value]);
        
        $ticket = Ticket::factory()->create(['lane_id' => $backlogLane->id]);
        
        $rule = new LaneTransitionRule($ticket);
        
        $passes = true;
        $rule->validate('lane_id', $todoLane->id, function($message) use (&$passes) {
            $passes = false;
        });
        
        $this->assertTrue($passes);
    }

    /** @test */
    public function test_rule_allows_valid_transition_from_done_to_todo(): void
    {
        $doneLane = Lane::factory()->create(['name' => LaneEnum::DONE->value]);
        $todoLane = Lane::factory()->create(['name' => LaneEnum::TO_DO->value]);
        
        $ticket = Ticket::factory()->create(['lane_id' => $doneLane->id]);
        
        $rule = new LaneTransitionRule($ticket);
        
        $passes = true;
        $rule->validate('lane_id', $todoLane->id, function($message) use (&$passes) {
            $passes = false;
        });
        
        $this->assertTrue($passes);
    }

    /** @test */
    public function test_rule_allows_valid_transition_from_todo_to_in_progress(): void
    {
        $todoLane = Lane::factory()->create(['name' => LaneEnum::TO_DO->value]);
        $inProgressLane = Lane::factory()->create(['name' => LaneEnum::IN_PROGRESS->value]);
        
        $ticket = Ticket::factory()->create(['lane_id' => $todoLane->id]);
        
        $rule = new LaneTransitionRule($ticket);
        
        $passes = true;
        $rule->validate('lane_id', $inProgressLane->id, function($message) use (&$passes) {
            $passes = false;
        });
        
        $this->assertTrue($passes);
    }

    /** @test */
    public function test_rule_allows_valid_transition_from_in_progress_to_done(): void
    {
        $inProgressLane = Lane::factory()->create(['name' => LaneEnum::IN_PROGRESS->value]);
        $doneLane = Lane::factory()->create(['name' => LaneEnum::DONE->value]);
        
        $ticket = Ticket::factory()->create(['lane_id' => $inProgressLane->id]);
        
        $rule = new LaneTransitionRule($ticket);
        
        $passes = true;
        $rule->validate('lane_id', $doneLane->id, function($message) use (&$passes) {
            $passes = false;
        });
        
        $this->assertTrue($passes);
    }

    /** @test */
    public function test_rule_blocks_invalid_transition_from_backlog_to_in_progress(): void
    {
        $backlogLane = Lane::factory()->create(['name' => LaneEnum::BACK_LOG->value]);
        $inProgressLane = Lane::factory()->create(['name' => LaneEnum::IN_PROGRESS->value]);
        
        $ticket = Ticket::factory()->create(['lane_id' => $backlogLane->id]);
        
        $rule = new LaneTransitionRule($ticket);
        
        $passes = true;
        $errorMessage = '';
        $rule->validate('lane_id', $inProgressLane->id, function($message) use (&$passes, &$errorMessage) {
            $passes = false;
            $errorMessage = $message;
        });
        
        $this->assertFalse($passes);
        $this->assertStringContains('Cannot move ticket from back_log to in_progress', $errorMessage);
    }

    /** @test */
    public function test_rule_blocks_invalid_transition_from_backlog_to_done(): void
    {
        $backlogLane = Lane::factory()->create(['name' => LaneEnum::BACK_LOG->value]);
        $doneLane = Lane::factory()->create(['name' => LaneEnum::DONE->value]);
        
        $ticket = Ticket::factory()->create(['lane_id' => $backlogLane->id]);
        
        $rule = new LaneTransitionRule($ticket);
        
        $passes = true;
        $errorMessage = '';
        $rule->validate('lane_id', $doneLane->id, function($message) use (&$passes, &$errorMessage) {
            $passes = false;
            $errorMessage = $message;
        });
        
        $this->assertFalse($passes);
        $this->assertStringContains('Cannot move ticket from back_log to done', $errorMessage);
    }

    /** @test */
    public function test_rule_blocks_invalid_transition_from_todo_to_backlog(): void
    {
        $todoLane = Lane::factory()->create(['name' => LaneEnum::TO_DO->value]);
        $backlogLane = Lane::factory()->create(['name' => LaneEnum::BACK_LOG->value]);
        
        $ticket = Ticket::factory()->create(['lane_id' => $todoLane->id]);
        
        $rule = new LaneTransitionRule($ticket);
        
        $passes = true;
        $errorMessage = '';
        $rule->validate('lane_id', $backlogLane->id, function($message) use (&$passes, &$errorMessage) {
            $passes = false;
            $errorMessage = $message;
        });
        
        $this->assertFalse($passes);
        $this->assertStringContains('Cannot move ticket from to_do to back_log', $errorMessage);
    }

    /** @test */
    public function test_rule_blocks_invalid_transition_from_todo_to_done(): void
    {
        $todoLane = Lane::factory()->create(['name' => LaneEnum::TO_DO->value]);
        $doneLane = Lane::factory()->create(['name' => LaneEnum::DONE->value]);
        
        $ticket = Ticket::factory()->create(['lane_id' => $todoLane->id]);
        
        $rule = new LaneTransitionRule($ticket);
        
        $passes = true;
        $errorMessage = '';
        $rule->validate('lane_id', $doneLane->id, function($message) use (&$passes, &$errorMessage) {
            $passes = false;
            $errorMessage = $message;
        });
        
        $this->assertFalse($passes);
        $this->assertStringContains('Cannot move ticket from to_do to done', $errorMessage);
    }

    /** @test */
    public function test_rule_blocks_invalid_transition_from_in_progress_to_backlog(): void
    {
        $inProgressLane = Lane::factory()->create(['name' => LaneEnum::IN_PROGRESS->value]);
        $backlogLane = Lane::factory()->create(['name' => LaneEnum::BACK_LOG->value]);
        
        $ticket = Ticket::factory()->create(['lane_id' => $inProgressLane->id]);
        
        $rule = new LaneTransitionRule($ticket);
        
        $passes = true;
        $errorMessage = '';
        $rule->validate('lane_id', $backlogLane->id, function($message) use (&$passes, &$errorMessage) {
            $passes = false;
            $errorMessage = $message;
        });
        
        $this->assertFalse($passes);
        $this->assertStringContains('Cannot move ticket from in_progress to back_log', $errorMessage);
    }

    /** @test */
    public function test_rule_blocks_invalid_transition_from_in_progress_to_todo(): void
    {
        $inProgressLane = Lane::factory()->create(['name' => LaneEnum::IN_PROGRESS->value]);
        $todoLane = Lane::factory()->create(['name' => LaneEnum::TO_DO->value]);
        
        $ticket = Ticket::factory()->create(['lane_id' => $inProgressLane->id]);
        
        $rule = new LaneTransitionRule($ticket);
        
        $passes = true;
        $errorMessage = '';
        $rule->validate('lane_id', $todoLane->id, function($message) use (&$passes, &$errorMessage) {
            $passes = false;
            $errorMessage = $message;
        });
        
        $this->assertFalse($passes);
        $this->assertStringContains('Cannot move ticket from in_progress to to_do', $errorMessage);
    }

    /** @test */
    public function test_rule_blocks_invalid_transition_from_done_to_backlog(): void
    {
        $doneLane = Lane::factory()->create(['name' => LaneEnum::DONE->value]);
        $backlogLane = Lane::factory()->create(['name' => LaneEnum::BACK_LOG->value]);
        
        $ticket = Ticket::factory()->create(['lane_id' => $doneLane->id]);
        
        $rule = new LaneTransitionRule($ticket);
        
        $passes = true;
        $errorMessage = '';
        $rule->validate('lane_id', $backlogLane->id, function($message) use (&$passes, &$errorMessage) {
            $passes = false;
            $errorMessage = $message;
        });
        
        $this->assertFalse($passes);
        $this->assertStringContains('Cannot move ticket from done to back_log', $errorMessage);
    }

    /** @test */
    public function test_rule_blocks_invalid_transition_from_done_to_in_progress(): void
    {
        $doneLane = Lane::factory()->create(['name' => LaneEnum::DONE->value]);
        $inProgressLane = Lane::factory()->create(['name' => LaneEnum::IN_PROGRESS->value]);
        
        $ticket = Ticket::factory()->create(['lane_id' => $doneLane->id]);
        
        $rule = new LaneTransitionRule($ticket);
        
        $passes = true;
        $errorMessage = '';
        $rule->validate('lane_id', $inProgressLane->id, function($message) use (&$passes, &$errorMessage) {
            $passes = false;
            $errorMessage = $message;
        });
        
        $this->assertFalse($passes);
        $this->assertStringContains('Cannot move ticket from done to in_progress', $errorMessage);
    }

    /** @test */
    public function test_rule_allows_staying_in_same_lane(): void
    {
        $todoLane = Lane::factory()->create(['name' => LaneEnum::TO_DO->value]);
        
        $ticket = Ticket::factory()->create(['lane_id' => $todoLane->id]);
        
        $rule = new LaneTransitionRule($ticket);
        
        $passes = true;
        $rule->validate('lane_id', $todoLane->id, function($message) use (&$passes) {
            $passes = false;
        });
        
        $this->assertTrue($passes);
    }

    /** @test */
    public function test_rule_handles_invalid_lane_id(): void
    {
        $todoLane = Lane::factory()->create(['name' => LaneEnum::TO_DO->value]);
        
        $ticket = Ticket::factory()->create(['lane_id' => $todoLane->id]);
        
        $rule = new LaneTransitionRule($ticket);
        
        $passes = true;
        $errorMessage = '';
        $rule->validate('lane_id', 99999, function($message) use (&$passes, &$errorMessage) {
            $passes = false;
            $errorMessage = $message;
        });
        
        $this->assertFalse($passes);
        $this->assertStringContains('Invalid lane ID: 99999', $errorMessage);
    }

    /** @test */
    public function test_rule_handles_null_ticket(): void
    {
        $todoLane = Lane::factory()->create(['name' => LaneEnum::TO_DO->value]);
        
        $rule = new LaneTransitionRule(null);
        
        $passes = true;
        $rule->validate('lane_id', $todoLane->id, function($message) use (&$passes) {
            $passes = false;
        });
        
        // Should pass when ticket is null (for new tickets)
        $this->assertTrue($passes);
    }
}