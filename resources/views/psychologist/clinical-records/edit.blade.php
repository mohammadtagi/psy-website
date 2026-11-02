<x-layouts.app>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('website/style/care-clinical-record.css') }}?v=phase5-ux2">
    @endpush
    <script src="{{ asset('website/js/care-record-editor.js') }}" defer></script>
    <div class="care-record care-record-edit" dir="rtl">
        <div class="mb-8">
            <a
                href="{{ route('psychologist.clinical-records.show', $client) }}"
                class="text-sm text-indigo-700 hover:underline"
            >
                بازگشت به پرونده
            </a>

            <h1 class="mt-4 text-2xl font-bold text-gray-900">
                ویرایش پرونده بالینی
            </h1>
        </div>

        @if ($errors->any())
            <div class="cr-errors" role="alert">
                <p>اطلاعات ذخیره نشد. موارد زیر را بررسی کنید:</p>
                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        <div class="cr-editor-actions" role="region" aria-label="عملیات ویرایش پرونده">
            <div><strong>ویرایش پرونده</strong><p>تغییرات با دکمه ذخیره ثبت می‌شوند.</p></div>
            <div class="cr-editor-buttons">
                <button type="submit" form="clinical-record-form" class="cr-save">ذخیره تغییرات</button>
                <a class="cr-cancel" href="{{ route('psychologist.clinical-records.show', $client) }}">انصراف و بازگشت</a>
            </div>
        </div>
        @include('psychologist.clinical-records.form')
    </div>
</x-layouts.app>
