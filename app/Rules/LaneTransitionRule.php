<?php

namespace App\Rules;

use App\Models\Lane;
use App\Models\Ticket;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class LaneTransitionRule implements ValidationRule
{

    public function __construct(private readonly ?Ticket $ticket)
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
            'back_log', 'done' => ['to_do'],
            'to_do' => ['in_progress'],
            'in_progress' => ['done'],
            default => [],
        };

        $newLane = Lane::find($value)->name;

        if ($newLane === $this->ticket?->lane->name) {
            return;
        }

        if (!in_array($newLane, $validTransitions, true)) {
            $fail("Cannot move ticket from {$this->ticket->lane->name} to $newLane");
        }
    }
}
