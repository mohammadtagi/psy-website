<x-layouts.app>
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        @php
            $clientName = trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? ''));
        @endphp

        @if (session('status'))
            <div class="mb-6 border-r-4 border-green-600 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="mb-8">
            <a
                href="{{ route('psychologist.clinical-records.show', ['client' => $client]) }}"
                class="text-sm text-indigo-700 hover:underline"
            >
                بازگشت به پرونده بالینی
            </a>

            <h1 class="mt-4 text-2xl font-bold text-gray-900">
                مدارک و ضمائم پرونده
            </h1>

            <p class="mt-2 text-sm text-gray-600">
                {{ $clientName ?: 'نام ثبت نشده' }}
                -
                <span dir="ltr">{{ $client->mobile }}</span>
            </p>
        </div>

        <section class="border border-gray-200 bg-white p-5">
            <h2 class="text-lg font-semibold text-gray-900">
                افزودن ضمیمه
            </h2>

            <form
                method="POST"
                action="{{ route('psychologist.clinical-attachments.store', [
                    'clinicalRecord' => $record,
                ]) }}"
                enctype="multipart/form-data"
                class="mt-6"
            >
                @csrf

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label
                            for="attachment-file"
                            class="block text-sm font-medium text-gray-700"
                        >
                            فایل
                        </label>

                        <input
                            id="attachment-file"
                            name="file"
                            type="file"
                            required
                            accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                            class="mt-2 block w-full border border-gray-300 px-3 py-2 text-sm"
                        >

                        <p class="mt-1 text-xs text-gray-500">
                            فرمت‌های مجاز: PDF، JPG، PNG، DOC و DOCX؛ حداکثر ۱۰ مگابایت
                        </p>

                        @error('file')
                        <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="attachment-category"
                            class="block text-sm font-medium text-gray-700"
                        >
                            دسته‌بندی
                        </label>

                        <select
                            id="attachment-category"
                            name="category"
                            class="mt-2 block w-full border border-gray-300 px-3 py-2 text-sm"
                        >
                            <option value="">بدون دسته‌بندی</option>

                            @foreach ($attachmentCategories as $value => $label)
                                <option
                                    value="{{ $value }}"
                                    @selected(old('category') === $value)
                                >
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>

                        @error('category')
                        <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="attachment-document-date"
                            class="block text-sm font-medium text-gray-700"
                        >
                            تاریخ مدرک
                        </label>

                        <input
                            id="attachment-document-date"
                            name="document_date"
                            type="text"
                            dir="ltr"
                            value="{{ old('document_date') }}"
                            placeholder="۱۴۰۵/۰۷/۰۲"
                            class="mt-2 block w-full border border-gray-300 px-3 py-2 text-sm"
                        >

                        @error('document_date')
                        <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="attachment-description"
                            class="block text-sm font-medium text-gray-700"
                        >
                            توضیح
                        </label>

                        <textarea
                            id="attachment-description"
                            name="description"
                            rows="3"
                            maxlength="1000"
                            class="mt-2 block w-full border border-gray-300 px-3 py-2 text-sm"
                        >{{ old('description') }}</textarea>

                        @error('description')
                        <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <button
                    type="submit"
                    class="mt-4 border border-indigo-700 bg-indigo-700 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-800"
                >
                    آپلود ضمیمه
                </button>
            </form>

            @error('attachment')
            <p class="mt-4 text-sm text-red-700">{{ $message }}</p>
            @enderror
        </section>

        <section class="mt-6 border border-gray-200 bg-white p-5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">
                        فهرست ضمائم
                    </h2>

                    <p class="mt-2 text-sm text-gray-600">
                        تعداد ضمائم: {{ $attachments->count() }}
                    </p>
                </div>
            </div>

            @if ($attachments->isEmpty())
                <p class="mt-6 text-sm text-gray-500">
                    هنوز ضمیمه‌ای برای این پرونده ثبت نشده است.
                </p>
            @else
                <div class="mt-6 space-y-4">
                    @foreach ($attachments as $attachment)
                        <article class="border border-gray-200 p-4">
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="break-all text-sm font-medium text-gray-900">
                                        {{ $attachment->original_name }}
                                    </p>

                                    <div class="mt-2 space-y-1 text-xs text-gray-500">
                                        <p>
                                            دسته‌بندی:
                                            {{ $attachmentCategories[$attachment->category] ?? 'بدون دسته‌بندی' }}
                                        </p>

                                        @if ($attachment->document_date)
                                            <p>
                                                تاریخ مدرک:
                                                {{ \App\Support\PersianDate::format($attachment->document_date, 'yyyy/MM/dd') }}
                                            </p>
                                        @endif

                                        @if ($attachment->size)
                                            <p>
                                                حجم:
                                                {{ number_format($attachment->size / 1024, 0) }}
                                                کیلوبایت
                                            </p>
                                        @endif

                                        @if ($attachment->description)
                                            <p class="mt-2 whitespace-pre-wrap text-gray-700">
                                                {{ $attachment->description }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center gap-3">
                                    <a
                                        href="{{ route('psychologist.clinical-attachments.download', [
                                            'clinicalAttachment' => $attachment,
                                        ]) }}"
                                        class="text-sm text-indigo-700 hover:underline"
                                    >
                                        دریافت فایل
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('psychologist.clinical-attachments.destroy', [
                                            'clinicalAttachment' => $attachment,
                                        ]) }}"
                                        onsubmit="return confirm('آیا از حذف این ضمیمه مطمئن هستید؟')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="text-sm text-red-700 hover:underline"
                                        >
                                            حذف
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</x-layouts.app>
