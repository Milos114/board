<?php

namespace Tests\Feature\Ticket\Attachment;

use App\Models\Ticket;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AttachmentCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_upload_ticket_files(): void
    {
        $this->actingAs($user = UserFactory::new()->create());
        Storage::fake('public');

        $attachments = [
            UploadedFile::fake()->image('photo1.jpg'),
            UploadedFile::fake()->image('photo2.jpg')
        ];

        $this->postJson('/api/' . $this->getApiVersion() . '/tickets', [
            'uploaded_by' => $user->id,
            'title' => 'Test Ticket',
            'description' => 'This is a test ticket',
            'attachments' => $attachments
        ]);

        foreach ($attachments as $file) {
            Storage::disk('public')->assertExists('attachments/' . $file->hashName());
        }
    }

    public function test_attachment_can_be_associated_to_ticket(): void
    {
        $this->actingAs($user = UserFactory::new()->create());
        Storage::fake('public');

        $this->postJson('/api/' . $this->getApiVersion() . '/tickets', [
            'uploaded_by' => $user->id,
            'title' => 'Test Ticket',
            'description' => 'This is a test ticket',
            'attachments' => [
                UploadedFile::fake()->image('photo1.jpg'),
                UploadedFile::fake()->image('photo2.jpg')
            ]
        ]);
        $ticket = Ticket::first();

        $this->assertCount(2, $ticket->attachments);
    }

    public function test_file_hash_is_stored_in_database(): void
    {
        $this->actingAs($user = UserFactory::new()->create());
        Storage::fake('public');
        $attachments = [
            UploadedFile::fake()->image('photo1.jpg'),
            UploadedFile::fake()->image('photo2.jpg')
        ];

        $this->postJson('/api/' . $this->getApiVersion() . '/tickets', [
            'uploaded_by' => $user->id,
            'title' => 'Test Ticket',
            'description' => 'This is a test ticket',
            'attachments' => $attachments
        ]);

        $ticket = Ticket::first();
        foreach ($attachments as $attachment) {
            $this->assertDatabaseHas('attachments', [
                'ticket_id' => $ticket->id,
                'file_hash' => $attachment->hashName()
            ]);
        }
    }

    public function test_file_path_is_stored_in_database(): void
    {
        $this->actingAs($user = UserFactory::new()->create());
        Storage::fake('public');
        $attachments = [
            UploadedFile::fake()->image('photo1.jpg'),
            UploadedFile::fake()->image('photo2.jpg')
        ];

        $this->postJson('/api/' . $this->getApiVersion() . '/tickets', [
            'uploaded_by' => $user->id,
            'title' => 'Test Ticket',
            'description' => 'This is a test ticket',
            'attachments' => $attachments
        ]);

        $ticket = Ticket::first();
        foreach ($attachments as $file) {
            $this->assertDatabaseHas('attachments', [
                'ticket_id' => $ticket->id,
                'file_path' => 'attachments/' . $file->hashName()
            ]);
        }
    }

    public function test_file_size_is_stored_in_database(): void
    {
        $this->actingAs($user = UserFactory::new()->create());
        Storage::fake('public');
        $attachments = [
            UploadedFile::fake()->image('photo1.jpg'),
            UploadedFile::fake()->image('photo2.jpg')
        ];

        $this->postJson('/api/' . $this->getApiVersion() . '/tickets', [
            'uploaded_by' => $user->id,
            'title' => 'Test Ticket',
            'description' => 'This is a test ticket',
            'attachments' => $attachments
        ]);

        $ticket = Ticket::first();
        foreach ($attachments as $file) {
            $this->assertDatabaseHas('attachments', [
                'ticket_id' => $ticket->id,
                'file_size' => $file->getSize()
            ]);
        }
    }

    public function test_mime_type_is_stored_in_database(): void
    {
        $this->actingAs($user = UserFactory::new()->create());
        Storage::fake('public');
        $attachments = [
            UploadedFile::fake()->image('photo1.jpg'),
            UploadedFile::fake()->create('document1.pdf')
        ];

        $this->postJson('/api/' . $this->getApiVersion() . '/tickets', [
            'uploaded_by' => $user->id,
            'title' => 'Test Ticket',
            'description' => 'This is a test ticket',
            'attachments' => $attachments
        ]);

        $ticket = Ticket::first();
        $this->assertDatabaseHas('attachments', [
            'ticket_id' => $ticket->id,
            'file_name' => 'photo1.jpg',
            'mime_type' => 'image/jpeg'
        ]);
        $this->assertDatabaseHas('attachments', [
            'ticket_id' => $ticket->id,
            'file_name' => 'document1.pdf',
            'mime_type' => 'application/pdf'
        ]);
    }

    public function test_attachment_has_uploaded_by_in_database(): void
    {
        $this->actingAs($user = UserFactory::new()->create());
        Storage::fake('public');
        $attachments = [
            UploadedFile::fake()->image('photo1.jpg'),
            UploadedFile::fake()->create('document1.pdf')
        ];

        $this->postJson('/api/' . $this->getApiVersion() . '/tickets', [
            'uploaded_by' => $user->id,
            'title' => 'Test Ticket',
            'description' => 'This is a test ticket',
            'attachments' => $attachments
        ]);

        $ticket = Ticket::first();
        $this->assertDatabaseHas('attachments', [
            'ticket_id' => $ticket->id,
            'uploaded_by' => $user->id
        ]);
    }

    public function test_file_extension_is_stored_in_database(): void
    {
        $this->actingAs($user = UserFactory::new()->create());
        Storage::fake('public');
        $attachments = [
            UploadedFile::fake()->image('photo1.jpg'),
            UploadedFile::fake()->create('document1.pdf')
        ];

        $this->postJson('/api/' . $this->getApiVersion() . '/tickets', [
            'uploaded_by' => $user->id,
            'title' => 'Test Ticket',
            'description' => 'This is a test ticket',
            'attachments' => $attachments
        ]);

        $ticket = Ticket::first();
        $this->assertDatabaseHas('attachments', [
            'ticket_id' => $ticket->id,
            'file_name' => 'photo1.jpg',
            'file_extension' => 'jpg'
        ]);
        $this->assertDatabaseHas('attachments', [
            'ticket_id' => $ticket->id,
            'file_name' => 'document1.pdf',
            'file_extension' => 'pdf'
        ]);
    }
}
