<?php

namespace App\Rules;

use App\Enums\LaneEnum;
use App\Models\Lane;
use App\Models\Ticket;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

readonly class LaneTransitionRule implements ValidationRule
{

    public function __construct(private ?Ticket $ticket)
    {
    }

    /**
     * Run the validation rule.
     *
     * @param Closure(string, ?string=): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $validTransitions = match ($this->ticket?->lane->name) {
            LaneEnum::BACK_LOG->value, LaneEnum::DONE->value => [LaneEnum::TO_DO->value],
            LaneEnum::TO_DO->value => [LaneEnum::IN_PROGRESS->value],
            LaneEnum::IN_PROGRESS->value => [LaneEnum::DONE->value],
            default => [],
        };

        $newLane = Lane::find($value);
        if (!$newLane) {
            $fail("Invalid lane ID: $value");
            return;
        }
        
        $newLaneName = $newLane->name;

        if ($newLaneName === $this->ticket?->lane->name) {
            return;
        }

        if (!in_array($newLaneName, $validTransitions, true)) {
            $fail("Cannot move ticket from {$this->ticket->lane->name} to $newLaneName");
        }
    }
}
