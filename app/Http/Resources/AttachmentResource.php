<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uploaded_by' => $this->uploaded_by,
            'file_name' => $this->file_name,
            'file_path' => asset('storage/' . $this->file_path),
            'mime_type' => $this->mime_type,
            'file_hash' => $this->file_hash,
            'file_size' => $this->file_size,
            'file_extension' => $this->file_extension,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
