@if ($errors->any())
    <div role="alert" class="mb-5 border border-red-200 bg-red-50 p-4">
        <ul class="list-inside list-disc">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<section class="space-y-4 border border-gray-200 bg-white p-5">
    <p class="text-sm text-gray-600">
        این یادداشت محرمانه است و برای مراجع یا مشاور دیگر نمایش داده نمی‌شود.
        اطلاعات عمومی جلسه را در صفحه مدیریت جلسه ثبت کنید.
    </p>

    @if ($note->exists)
        <div class="space-y-1 text-xs text-gray-500">
            <p>
                زمان ثبت:
                {{ \App\Support\PersianDate::format(
                    $note->created_at->copy()->setTimezone('Asia/Tehran'),
                    'yyyy/MM/dd HH:mm'
                ) }}
            </p>

            <p>
                آخرین ویرایش:
                {{ \App\Support\PersianDate::format(
                    $note->updated_at->copy()->setTimezone('Asia/Tehran'),
                    'yyyy/MM/dd HH:mm'
                ) }}
            </p>
        </div>
    @endif

    <div>
        <label for="private_note" class="mb-2 block font-medium">
            متن یادداشت محرمانه
        </label>

        <textarea
            id="private_note"
            name="private_note"
            rows="12"
            maxlength="20000"
            required
            class="w-full border border-gray-300 p-3"
        >{{ old('private_note', $note->private_note) }}</textarea>
    </div>

    <button type="submit" class="bg-indigo-700 px-5 py-2 text-white">
        {{ $note->exists ? 'ذخیره تغییرات' : 'ثبت یادداشت' }}
    </button>
</section>
