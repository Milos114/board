<?php

namespace App\Http\Resources\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
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
            'user' => UserResource::make($this->whenLoaded('user')),
            'lane' => LaneResource::make($this->whenLoaded('lane')),
            'priority' => PriorityResource::make($this->whenLoaded('priority')),
            'title' => $this->title,
            'description' => $this->description,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
        ];
    }
}