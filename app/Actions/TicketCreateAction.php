<?php

namespace App\Actions;

use App\DTO\AttachmentData;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class TicketCreateAction
{
    /**
     * @throws \Exception
     */
    public function execute(array $data): Ticket
    {
        DB::beginTransaction();

        try {
            $ticket = Ticket::create($data);

            foreach ($data['attachments'] ?? [] as $file) {
                $filePath = $file->store('attachments', 'public');
                $fileDto = AttachmentData::fromUploadedFile($file);

                $ticket->attachments()->create([
                    'uploaded_by' => auth()->id(),
                    'file_name' => $fileDto->file_name,
                    'file_path' => $filePath,
                    'file_size' => $fileDto->file_size,
                    'mime_type' => $fileDto->mime_type,
                    'file_hash' => $fileDto->file_hash,
                    'file_extension' => $fileDto->file_extension,
                ]);
            }

            DB::commit();
            return $ticket;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

}
