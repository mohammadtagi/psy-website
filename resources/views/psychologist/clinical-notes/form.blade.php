@php
    $isEdit = $note->exists;

    $formAction = $isEdit
        ? route('psychologist.clinical-notes.update', $note)
        : route('psychologist.clinical-notes.store', $record);
@endphp

<form method="POST" action="{{ $formAction }}" class="space-y-6">
    @csrf

    @if ($isEdit)
        @method('PUT')
    @endif

    <section class="border border-gray-200 bg-white p-5">
        <h2 class="text-lg font-semibold text-gray-900">
            مشخصات جلسه
        </h2>

        <div class="mt-5 grid gap-5 md:grid-cols-2">
            <div>
                <label
                    for="session_at"
                    class="mb-2 block text-sm font-medium text-gray-700"
                >
                    تاریخ و زمان جلسه
                </label>

                <input
                    id="session_at"
                    name="session_at"
                    type="text"
                    inputmode="numeric"
                    placeholder="۱۴۰۵/۰۷/۰۲ ۱۵:۳۰"
                    value="{{ old(
        'session_at',
        $note->session_at
            ? \App\Support\PersianDate::format($note->session_at, 'yyyy/MM/dd HH:mm')
            : ''
    ) }}"
                    class="w-full border border-gray-300 px-3 py-2"
                >
                <p class="mt-1 text-xs text-gray-500">
                    قالب: سال/ماه/روز ساعت:دقیقه
                </p>


                @error('session_at')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
                @enderror
            </div>

            <div>
                <label
                    for="appointment_id"
                    class="mb-2 block text-sm font-medium text-gray-700"
                >
                    نوبت مرتبط
                </label>

                <select
                    id="appointment_id"
                    name="appointment_id"
                    class="w-full border border-gray-300 px-3 py-2"
                >
                    <option value="">بدون نوبت مرتبط</option>

                    @foreach ($appointments as $appointment)
                        @php
                            $appointmentLabel = $appointment->starts_at
                                ? \App\Support\PersianDate::format(
                                    $appointment->starts_at,
                                    'yyyy/MM/dd HH:mm'
                                )
                                : 'بدون تاریخ';
                        @endphp

                        <option
                            value="{{ $appointment->id }}"
                            @selected(
                                (string) old(
                                    'appointment_id',
                                    $note->appointment_id
                                ) === (string) $appointment->id
                            )
                        >
                            {{ $appointmentLabel }}
                        </option>
                    @endforeach

                </select>

                @error('appointment_id')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
                @enderror
            </div>
        </div>
    </section>

    @foreach ([
        'summary' => 'خلاصه جلسه',
        'client_condition' => 'وضعیت و شرایط مراجع',
        'interventions' => 'مداخلات انجام‌شده',
        'homework_and_next_plan' => 'تکلیف و برنامه جلسه بعد',
        'private_note' => 'یادداشت خصوصی',
    ] as $field => $label)
        <section class="border border-gray-200 bg-white p-5">
            <label
                for="{{ $field }}"
                class="block text-sm font-medium text-gray-700"
            >
                {{ $label }}
            </label>

            <textarea
                id="{{ $field }}"
                name="{{ $field }}"
                rows="6"
                class="mt-2 w-full border border-gray-300 px-3 py-2"
            >{{ old($field, $note->{$field}) }}</textarea>

            @error($field)
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
            @enderror
        </section>
    @endforeach

    <div class="flex flex-wrap gap-3">
        <button
            type="submit"
            class="border border-indigo-700 bg-indigo-700 px-5 py-2 font-medium text-white hover:bg-indigo-800"
        >
            {{ $isEdit ? 'ذخیره تغییرات' : 'ثبت یادداشت' }}
        </button>

        <a
            href="{{ route('psychologist.clinical-records.show', $record->client) }}"
            class="border border-gray-300 px-5 py-2 font-medium text-gray-700 hover:bg-gray-50"
        >
            انصراف
        </a>
    </div>
</form>
