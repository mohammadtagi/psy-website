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

        <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
            <div>
                <a
                    href="{{ route('psychologist.clients.index', ['q' => $client->mobile]) }}"
                    class="text-sm text-indigo-700 hover:underline"
                >
                    بازگشت به فهرست مراجع
                </a>

                <h1 class="mt-4 text-2xl font-bold text-gray-900">
                    پرونده بالینی
                </h1>

                <p class="mt-2 text-sm text-gray-600">
                    {{ $clientName ?: 'نام ثبت نشده' }}
                    -
                    <span dir="ltr">{{ $client->mobile }}</span>
                </p>
            </div>

            <a
                href="{{ route('psychologist.clinical-records.edit', $record) }}"
                class="border border-indigo-700 px-4 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-50"
            >
                ویرایش پرونده
            </a>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            <section class="border border-gray-200 bg-white p-5 md:col-span-2">
                <h2 class="text-lg font-semibold text-gray-900">مسئله اصلی</h2>

                <p class="mt-4 whitespace-pre-line text-sm leading-7 text-gray-700">
                    {{ $record->presenting_problem ?: 'ثبت نشده است.' }}
                </p>
            </section>

            <section class="border border-gray-200 bg-white p-5">
                <h2 class="text-lg font-semibold text-gray-900">سطح خطر</h2>

                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-500">خودکشی</dt>
                        <dd>{{ $record->suicide_risk ?: 'ثبت نشده' }}</dd>
                    </div>

                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-500">خودآسیب‌رسانی</dt>
                        <dd>{{ $record->self_harm_risk ?: 'ثبت نشده' }}</dd>
                    </div>

                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-500">آسیب به دیگران</dt>
                        <dd>{{ $record->harm_to_others_risk ?: 'ثبت نشده' }}</dd>
                    </div>

                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-500">خطر کلی</dt>
                        <dd>{{ $record->overall_risk_level ?: 'ثبت نشده' }}</dd>
                    </div>
                </dl>
            </section>

            <section class="border border-gray-200 bg-white p-5">
                <h2 class="text-lg font-semibold text-gray-900">برنامه درمان</h2>

                <p class="mt-4 whitespace-pre-line text-sm leading-7 text-gray-700">
                    {{ $record->treatment_plan_and_schedule ?: 'ثبت نشده است.' }}
                </p>
            </section>

            <section class="border border-gray-200 bg-white p-5 md:col-span-2">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            یادداشت‌های جلسات
                        </h2>

                        <p class="mt-2 text-sm text-gray-600">
                            تعداد یادداشت‌ها:
                            {{ $record->notes->count() }}
                        </p>
                    </div>

                    <a
                        href="{{ route('psychologist.clinical-notes.create', ['clinicalRecord' => $record->id]) }}"
                        class="border border-indigo-700 bg-indigo-700 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-800"
                    >
                        ثبت یادداشت
                    </a>
                </div>

                @if ($record->notes->isEmpty())
                    <p class="mt-5 text-sm text-gray-500">
                        هنوز یادداشتی برای این پرونده ثبت نشده است.
                    </p>
                @else
                    <div class="mt-5 space-y-4">
                        @foreach ($record->notes as $note)
                            <article class="border border-gray-200 p-4">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $note->session_at
                                                ? \App\Support\PersianDate::format($note->session_at, 'yyyy/MM/dd HH:mm')
                                                : 'تاریخ ثبت نشده' }}
                                        </p>

                                        @if ($note->appointment)
                                            <p class="mt-1 text-xs text-gray-500">
                                                نوبت مرتبط:
                                                {{ $note->appointment->starts_at
                                                    ? \App\Support\PersianDate::format($note->appointment->starts_at, 'yyyy/MM/dd HH:mm')
                                                    : 'بدون تاریخ' }}
                                            </p>
                                        @else
                                            <p class="mt-1 text-xs text-gray-500">
                                                یادداشت مستقل از نوبت
                                            </p>
                                        @endif
                                    </div>

                                    <div class="flex flex-wrap gap-3">
                                        <a
                                            href="{{ route('psychologist.clinical-notes.edit', ['clinicalNote' => $note->id]) }}"
                                            class="text-sm text-indigo-700 hover:underline"
                                        >
                                            ویرایش
                                        </a>

                                        <form
                                            method="POST"
                                            action="{{ route('psychologist.clinical-notes.destroy', ['clinicalNote' => $note->id]) }}"
                                            onsubmit="return confirm('آیا از حذف این یادداشت مطمئن هستید؟')"
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

                                @if ($note->summary)
                                    <p class="mt-4 whitespace-pre-line text-sm leading-7 text-gray-700">
                                        {{ $note->summary }}
                                    </p>
                                @endif
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>


            <section class="border border-gray-200 bg-white p-5 md:col-span-2">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            مدارک و ضمائم
                        </h2>

                        <p class="mt-2 text-sm text-gray-600">
                            تعداد ضمائم:
                            {{ $record->attachments->count() }}
                        </p>
                    </div>

                    <a
                        href="{{ route('psychologist.clinical-attachments.index', [
                'clinicalRecord' => $record,
            ]) }}"
                        class="border border-indigo-700 bg-indigo-700 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-800"
                    >
                        مدیریت ضمائم
                    </a>
                </div>

                @if ($record->attachments->isEmpty())
                    <p class="mt-5 text-sm text-gray-500">
                        هنوز ضمیمه‌ای برای این پرونده ثبت نشده است.
                    </p>
                @else
                    <div class="mt-5 space-y-3">
                        @foreach ($record->attachments->take(3) as $attachment)
                            <div class="flex flex-wrap items-center justify-between gap-3 border border-gray-200 p-3">
                                <div class="min-w-0">
                                    <p class="break-all text-sm font-medium text-gray-900">
                                        {{ $attachment->original_name }}
                                    </p>

                                    @if ($attachment->document_date)
                                        <p class="mt-1 text-xs text-gray-500">
                                            تاریخ مدرک:
                                            {{ \App\Support\PersianDate::format($attachment->document_date, 'yyyy/MM/dd') }}
                                        </p>
                                    @endif
                                </div>

                                <a
                                    href="{{ route('psychologist.clinical-attachments.download', [
                            'clinicalAttachment' => $attachment,
                        ]) }}"
                                    class="text-sm text-indigo-700 hover:underline"
                                >
                                    دریافت فایل
                                </a>
                            </div>
                        @endforeach
                    </div>

                    @if ($record->attachments->count() > 3)
                        <p class="mt-4 text-sm text-gray-500">
                            {{ $record->attachments->count() - 3 }}
                            ضمیمهٔ دیگر نیز وجود دارد.
                        </p>
                    @endif
                @endif
            </section>

        </div>
    </div>
</x-layouts.app>
