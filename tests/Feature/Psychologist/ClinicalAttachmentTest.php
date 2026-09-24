<?php

namespace Tests\Feature\Psychologist;

use App\Models\ClinicalAttachment;
use App\Models\ClinicalRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ClinicalAttachmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('private');
    }

    public function test_guest_is_redirected_to_login_from_attachment_page(): void
    {
        $record = $this->createRecord();

        $response = $this->get(
            route('psychologist.clinical-attachments.index', [
                'clinicalRecord' => $record,
            ]),
        );

        $response->assertRedirect(route('auth.login'));
    }

    public function test_client_cannot_view_attachment_page(): void
    {
        $client = User::factory()->client()->create();
        $record = $this->createRecord($client);

        $response = $this->actingAs($client)->get(
            route('psychologist.clinical-attachments.index', [
                'clinicalRecord' => $record,
            ]),
        );

        $response->assertForbidden();
    }

    public function test_psychologist_can_view_attachment_page(): void
    {
        $psychologist = User::factory()->psychologist()->create();
        $client = User::factory()->client()->create();
        $record = $this->createRecord($client);

        $response = $this->actingAs($psychologist)->get(
            route('psychologist.clinical-attachments.index', [
                'clinicalRecord' => $record,
            ]),
        );

        $response
            ->assertOk()
            ->assertViewIs('psychologist.clinical-attachments.index')
            ->assertViewHas('record', function ($value) use ($record): bool {
                return $value->is($record);
            })
            ->assertViewHas('client', function ($value) use ($client): bool {
                return $value->is($client);
            })
            ->assertViewHas('attachments')
            ->assertViewHas('attachmentCategories');
    }

    public function test_psychologist_can_upload_valid_attachment(): void
    {
        $psychologist = User::factory()->psychologist()->create();
        $client = User::factory()->client()->create();
        $record = $this->createRecord($client);

        $file = UploadedFile::fake()->create(
            'report.pdf',
            100,
            'application/pdf',
        );

        $response = $this->actingAs($psychologist)->post(
            route('psychologist.clinical-attachments.store', [
                'clinicalRecord' => $record,
            ]),
            [
                'file' => $file,
                'description' => 'گزارش آزمایش اولیه',
                'category' => 'lab_report',
                'document_date' => '1405/07/02',
            ],
        );

        $response
            ->assertRedirect(route(
                'psychologist.clinical-attachments.index',
                ['clinicalRecord' => $record],
            ))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status');

        $attachment = ClinicalAttachment::query()->firstOrFail();

        $this->assertSame($record->id, $attachment->clinical_record_id);
        $this->assertSame('گزارش آزمایش اولیه', $attachment->description);
        $this->assertSame('lab_report', $attachment->category);
        $this->assertSame('private', $attachment->disk);
        $this->assertSame('report.pdf', $attachment->original_name);
        $this->assertSame('2026-09-24', $attachment->document_date->format('Y-m-d'));
        $this->assertNotNull($attachment->mime_type);
        $this->assertGreaterThan(0, $attachment->size);

        Storage::disk('private')->assertExists($attachment->path);
    }

    public function test_client_cannot_upload_attachment(): void
    {
        $client = User::factory()->client()->create();
        $record = $this->createRecord($client);

        $response = $this->actingAs($client)->post(
            route('psychologist.clinical-attachments.store', [
                'clinicalRecord' => $record,
            ]),
            $this->validUploadData(),
        );

        $response->assertForbidden();

        $this->assertDatabaseCount('clinical_attachments', 0);
    }

    public function test_invalid_file_extension_is_rejected(): void
    {
        $psychologist = User::factory()->psychologist()->create();
        $record = $this->createRecord();

        $response = $this->actingAs($psychologist)->post(
            route('psychologist.clinical-attachments.store', [
                'clinicalRecord' => $record,
            ]),
            $this->validUploadData([
                'file' => UploadedFile::fake()->create(
                    'archive.zip',
                    100,
                    'application/zip',
                ),
            ]),
        );

        $response
            ->assertSessionHasErrors('file')
            ->assertRedirect();

        $this->assertDatabaseCount('clinical_attachments', 0);
    }

    public function test_file_larger_than_10_megabytes_is_rejected(): void
    {
        $psychologist = User::factory()->psychologist()->create();
        $record = $this->createRecord();

        $response = $this->actingAs($psychologist)->post(
            route('psychologist.clinical-attachments.store', [
                'clinicalRecord' => $record,
            ]),
            $this->validUploadData([
                'file' => UploadedFile::fake()->create(
                    'large-report.pdf',
                    10241,
                    'application/pdf',
                ),
            ]),
        );

        $response
            ->assertSessionHasErrors('file')
            ->assertRedirect();

        $this->assertDatabaseCount('clinical_attachments', 0);
    }

    public function test_invalid_document_date_is_rejected(): void
    {
        $psychologist = User::factory()->psychologist()->create();
        $record = $this->createRecord();

        $response = $this->actingAs($psychologist)->post(
            route('psychologist.clinical-attachments.store', [
                'clinicalRecord' => $record,
            ]),
            $this->validUploadData([
                'document_date' => '1405/13/01',
            ]),
        );

        $response
            ->assertSessionHasErrors('document_date')
            ->assertRedirect();

        $this->assertDatabaseCount('clinical_attachments', 0);
    }

    public function test_invalid_category_is_rejected(): void
    {
        $psychologist = User::factory()->psychologist()->create();
        $record = $this->createRecord();

        $response = $this->actingAs($psychologist)->post(
            route('psychologist.clinical-attachments.store', [
                'clinicalRecord' => $record,
            ]),
            $this->validUploadData([
                'category' => 'unknown_category',
            ]),
        );

        $response
            ->assertSessionHasErrors('category')
            ->assertRedirect();

        $this->assertDatabaseCount('clinical_attachments', 0);
    }

    public function test_description_longer_than_1000_characters_is_rejected(): void
    {
        $psychologist = User::factory()->psychologist()->create();
        $record = $this->createRecord();

        $response = $this->actingAs($psychologist)->post(
            route('psychologist.clinical-attachments.store', [
                'clinicalRecord' => $record,
            ]),
            $this->validUploadData([
                'description' => str_repeat('x', 1001),
            ]),
        );

        $response
            ->assertSessionHasErrors('description')
            ->assertRedirect();

        $this->assertDatabaseCount('clinical_attachments', 0);
    }

    public function test_description_with_1000_characters_can_be_stored(): void
    {
        $psychologist = User::factory()->psychologist()->create();
        $record = $this->createRecord();
        $description = str_repeat('x', 1000);

        $response = $this->actingAs($psychologist)->post(
            route('psychologist.clinical-attachments.store', [
                'clinicalRecord' => $record,
            ]),
            $this->validUploadData([
                'description' => $description,
            ]),
        );

        $response
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('clinical_attachments', [
            'clinical_record_id' => $record->id,
            'description' => $description,
        ]);
    }

    public function test_psychologist_can_download_existing_attachment(): void
    {
        $psychologist = User::factory()->psychologist()->create();
        $record = $this->createRecord();
        $path = "clinical-records/{$record->id}/report.pdf";
        $contents = 'clinical attachment content';

        Storage::disk('private')->put($path, $contents);

        $attachment = $this->createAttachment($record, [
            'path' => $path,
            'original_name' => 'report.pdf',
            'mime_type' => 'application/pdf',
            'size' => strlen($contents),
        ]);

        $response = $this->actingAs($psychologist)->get(
            route('psychologist.clinical-attachments.download', [
                'clinicalAttachment' => $attachment,
            ]),
        );

        $response
            ->assertOk()
            ->assertDownload('report.pdf')
            ->assertHeader('Content-Type', 'application/pdf');

        $this->assertSame($contents, $response->streamedContent());
    }

    public function test_client_cannot_download_attachment(): void
    {
        $client = User::factory()->client()->create();
        $record = $this->createRecord($client);
        $attachment = $this->createAttachment($record);

        $response = $this->actingAs($client)->get(
            route('psychologist.clinical-attachments.download', [
                'clinicalAttachment' => $attachment,
            ]),
        );

        $response->assertForbidden();
    }

    public function test_download_returns_not_found_when_file_is_missing(): void
    {
        $psychologist = User::factory()->psychologist()->create();
        $record = $this->createRecord();

        $attachment = $this->createAttachment($record, [
            'path' => "clinical-records/{$record->id}/missing.pdf",
        ]);

        $response = $this->actingAs($psychologist)->get(
            route('psychologist.clinical-attachments.download', [
                'clinicalAttachment' => $attachment,
            ]),
        );

        $response->assertNotFound();
    }

    public function test_download_rejects_unsafe_paths(): void
    {
        $psychologist = User::factory()->psychologist()->create();
        $record = $this->createRecord();

        foreach ([
                     '../outside.pdf',
                     '/absolute/path.pdf',
                     'clinical-records/invalid/path.pdf',
                 ] as $path) {
            $attachment = $this->createAttachment($record, [
                'path' => $path,
            ]);

            $response = $this->actingAs($psychologist)->get(
                route('psychologist.clinical-attachments.download', [
                    'clinicalAttachment' => $attachment,
                ]),
            );

            $response->assertNotFound();
        }
    }

    public function test_download_rejects_non_private_disk(): void
    {
        $psychologist = User::factory()->psychologist()->create();
        $record = $this->createRecord();

        $attachment = $this->createAttachment($record, [
            'disk' => 'public',
        ]);

        $response = $this->actingAs($psychologist)->get(
            route('psychologist.clinical-attachments.download', [
                'clinicalAttachment' => $attachment,
            ]),
        );

        $response->assertNotFound();
    }

    public function test_psychologist_can_delete_attachment_and_file(): void
    {
        $psychologist = User::factory()->psychologist()->create();
        $record = $this->createRecord();
        $path = "clinical-records/{$record->id}/report.pdf";

        Storage::disk('private')->put($path, 'attachment');

        $attachment = $this->createAttachment($record, [
            'path' => $path,
        ]);

        $response = $this->actingAs($psychologist)->delete(
            route('psychologist.clinical-attachments.destroy', [
                'clinicalAttachment' => $attachment,
            ]),
        );

        $response
            ->assertRedirect(route(
                'psychologist.clinical-attachments.index',
                ['clinicalRecord' => $record],
            ))
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('clinical_attachments', [
            'id' => $attachment->id,
        ]);

        Storage::disk('private')->assertMissing($path);
    }

    public function test_client_cannot_delete_attachment(): void
    {
        $client = User::factory()->client()->create();
        $record = $this->createRecord($client);
        $attachment = $this->createAttachment($record);

        $response = $this->actingAs($client)->delete(
            route('psychologist.clinical-attachments.destroy', [
                'clinicalAttachment' => $attachment,
            ]),
        );

        $response->assertForbidden();

        $this->assertDatabaseHas('clinical_attachments', [
            'id' => $attachment->id,
        ]);
    }

    private function createRecord(?User $client = null): ClinicalRecord
    {
        $client ??= User::factory()->client()->create();

        return ClinicalRecord::query()->create([
            'client_id' => $client->id,
            'status' => ClinicalRecord::STATUS_ACTIVE,
        ]);
    }

    private function createAttachment(
        ClinicalRecord $record,
        array $attributes = [],
    ): ClinicalAttachment {
        return ClinicalAttachment::query()->create(array_merge([
            'clinical_record_id' => $record->id,
            'description' => null,
            'category' => 'other',
            'disk' => 'private',
            'path' => "clinical-records/{$record->id}/attachment.pdf",
            'original_name' => 'attachment.pdf',
            'mime_type' => 'application/pdf',
            'size' => 10,
            'document_date' => null,
        ], $attributes));
    }

    private function validUploadData(array $overrides = []): array
    {
        return array_merge([
            'file' => UploadedFile::fake()->create(
                'report.pdf',
                100,
                'application/pdf',
            ),
            'description' => 'Test attachment',
            'category' => 'other',
            'document_date' => '1405/07/02',
        ], $overrides);
    }
}
