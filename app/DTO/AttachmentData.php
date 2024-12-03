<?php

namespace App\DTO;

use Illuminate\Http\UploadedFile;

class AttachmentData
{
    public function __construct(
        public string $file_name,
        public int $file_size,
        public string $mime_type,
        public string $file_hash,
        public int $uploaded_by,
        public string $file_extension
    ) {
    }

    public static function fromUploadedFile(UploadedFile $file): AttachmentData
    {
        return new AttachmentData(
            file_name: $file->getClientOriginalName(),
            file_size: $file->getSize(),
            mime_type: $file->getMimeType(),
            file_hash: $file->hashName(),
            uploaded_by: auth()->id(),
            file_extension: $file->extension()
        );
    }
}
