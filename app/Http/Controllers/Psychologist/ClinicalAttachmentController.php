<?php

namespace App\Http\Controllers\Psychologist;

use App\Http\Controllers\Controller;
use App\Models\ClinicalAttachment;
use App\Models\ClinicalRecord;
use App\Support\PersianDate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;
use Illuminate\View\View;

class ClinicalAttachmentController extends Controller
{
    private const DISK = 'private';

    public const CATEGORY_LABELS = [
        'lab_report' => 'گزارش آزمایش',
        'prescription' => 'نسخه',
        'medical_report' => 'گزارش پزشکی',
        'image' => 'تصویر',
        'other' => 'سایر',
    ];
    public function index(ClinicalRecord $clinicalRecord): View
    {
        $clinicalRecord->load([
            'client',
            'attachments' => fn ($query) => $query->latest('id'),
        ]);

        $this->ensureRecordAccessible($clinicalRecord);

        return view('psychologist.clinical-attachments.index', [
            'record' => $clinicalRecord,
            'client' => $clinicalRecord->client,
            'attachments' => $clinicalRecord->attachments,
            'attachmentCategories' => self::CATEGORY_LABELS,
        ]);
    }

    public function store(
        Request $request,
        ClinicalRecord $clinicalRecord,
    ): RedirectResponse {
        $clinicalRecord->load('client');
        $this->ensureRecordAccessible($clinicalRecord);

        $validated = $this->validatedData($request);
        $file = $validated['file'];

        unset($validated['file']);

        $disk = self::DISK;
        $storedPath = null;

        try {
            $extension = strtolower((string) $file->extension());

            if ($extension === '') {
                throw new RuntimeException('پسوند فایل قابل تشخیص نیست.');
            }

            $directory = 'clinical-records/'.$clinicalRecord->id;
            $filename = Str::uuid()->toString().'.'.$extension;

            $storedPath = Storage::disk($disk)->putFileAs(
                $directory,
                $file,
                $filename,
            );

            if (! is_string($storedPath)) {
                throw new RuntimeException('ذخیره فایل انجام نشد.');
            }

            DB::transaction(function () use (
                $clinicalRecord,
                $validated,
                $file,
                $disk,
                $storedPath,
            ): void {
                ClinicalAttachment::query()->create([
                    'clinical_record_id' => $clinicalRecord->id,
                    'description' => $validated['description'] ?? null,
                    'category' => $validated['category'] ?? null,
                    'disk' => $disk,
                    'path' => $storedPath,
                    'original_name' => Str::limit(
                        $file->getClientOriginalName(),
                        255,
                        '',
                    ),
                    'mime_type' => $file->getMimeType() ?: null,
                    'size' => $file->getSize() ?: null,
                    'document_date' => $validated['document_date'] ?? null,
                ]);
            });
        } catch (Throwable $exception) {
            if ($storedPath !== null) {
                Storage::disk($disk)->delete($storedPath);
            }

            report($exception);

            return back()
                ->withInput()
                ->withErrors([
                    'file' => 'ذخیره ضمیمه انجام نشد. دوباره تلاش کنید.',
                ]);
        }

        return redirect()
            ->route('psychologist.clinical-attachments.index', [
                'clinicalRecord' => $clinicalRecord,
            ])
            ->with('status', 'ضمیمه با موفقیت ثبت شد.');

    }

    public function download(
        ClinicalAttachment $clinicalAttachment,
    ): StreamedResponse {
        $this->ensureAttachmentAccessible($clinicalAttachment);

        if ($clinicalAttachment->disk !== self::DISK) {
            abort(404);
        }

        $path = $clinicalAttachment->path;

        if (
            $path === ''
            || str_contains($path, '..')
            || str_starts_with($path, '/')
            || str_contains($path, '\\')
        ) {
            abort(404);
        }

        $storage = Storage::disk(self::DISK);

        if (! $storage->exists($path)) {
            abort(404);
        }

        $downloadName = str_replace(
            ["\r", "\n"],
            '',
            (string) $clinicalAttachment->original_name,
        );

        return $storage->download(
            $path,
            $downloadName !== '' ? $downloadName : 'attachment',
            [
                'Content-Type' => $clinicalAttachment->mime_type
                    ?: 'application/octet-stream',
            ],
        );
    }

    public function destroy(
        ClinicalAttachment $clinicalAttachment,
    ): RedirectResponse {
        $this->ensureAttachmentAccessible($clinicalAttachment);

        $record = $clinicalAttachment->clinicalRecord;
        $client = $record->client;

        if ($clinicalAttachment->disk !== self::DISK) {
            abort(404);
        }

        $storage = Storage::disk(self::DISK);

        if (
            $storage->exists($clinicalAttachment->path)
            && ! $storage->delete($clinicalAttachment->path)
        ) {
            return back()->withErrors([
                'attachment' => 'حذف فایل انجام نشد. دوباره تلاش کنید.',
            ]);
        }

        $clinicalAttachment->delete();

        return redirect()
            ->route('psychologist.clinical-attachments.index', [
                'clinicalRecord' => $record,
            ])
            ->with('status', 'ضمیمه حذف شد.');

    }

    private function validatedData(Request $request): array
    {
        $documentDate = trim((string) $request->input('document_date'));

        if ($documentDate === '') {
            $request->merge([
                'document_date' => null,
            ]);
        } else {
            try {
                $request->merge([
                    'document_date' => PersianDate::toGregorian($documentDate),
                ]);
            } catch (InvalidArgumentException $exception) {
                throw ValidationException::withMessages([
                    'document_date' => $exception->getMessage(),
                ]);
            }
        }

        return $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png,doc,docx',
                'max:10240',
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'category' => [
                'nullable',
                'string',
                Rule::in(array_keys(self::CATEGORY_LABELS)),
            ],
            'document_date' => [
                'nullable',
                'date_format:Y-m-d',
            ],
        ]);
    }

    private function ensureRecordAccessible(
        ClinicalRecord $clinicalRecord,
    ): void {
        abort_unless(
            auth()->check()
            && auth()->user()->isPsychologist(),
            403,
        );

        if (! $clinicalRecord->client) {
            abort(404);
        }
    }

    private function ensureAttachmentAccessible(
        ClinicalAttachment $clinicalAttachment,
    ): void {
        $record = $clinicalAttachment->clinicalRecord;

        if (! $record) {
            abort(404);
        }

        $this->ensureRecordAccessible($record);
    }
}
