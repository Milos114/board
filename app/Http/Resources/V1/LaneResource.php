<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LaneResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'tickets_count' => $this->tickets_count,
//            'tickets_count' => $this->whenLoaded('tickets', fn () => $this->tickets_count),
            'tickets' => TicketResource::collection($this->whenLoaded('tickets')),
        ];
    }
}
