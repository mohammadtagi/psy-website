@php
    $isEdit = $note->exists;

    $formAction = $isEdit
        ? route('psychologist.clinical-notes.update', $note)
        : route('psychologist.clinical-notes.store', $record);

    $selectedSessionType = old(
        'session_type',
        $note->session_type ?: \App\Models\ClinicalNote::SESSION_TYPE_FREE
    );

    $selectedPlanId = old('treatment_plan_id', $note->treatment_plan_id);
    $selectedStageId = old('treatment_plan_stage_id', $note->treatment_plan_stage_id);
@endphp

<form method="POST" action="{{ $formAction }}" class="space-y-6">
    @csrf

    @if ($isEdit)
        @method('PUT')
    @endif

    <section class="border border-gray-200 bg-white p-5">
        <h2 class="text-lg font-semibold text-gray-900">مشخصات جلسه</h2>

        <div class="mt-5 grid gap-5 md:grid-cols-2">
            <div>
                <label for="session_type" class="mb-2 block text-sm font-medium text-gray-700">
                    نوع جلسه
                </label>

                <select
                    id="session_type"
                    name="session_type"
                    class="w-full border border-gray-300 px-3 py-2"
                >
                    <option
                        value="{{ \App\Models\ClinicalNote::SESSION_TYPE_PLAN }}"
                        @selected($selectedSessionType === \App\Models\ClinicalNote::SESSION_TYPE_PLAN)
                    >
                        جلسه مسیر درمان
                    </option>
                    <option
                        value="{{ \App\Models\ClinicalNote::SESSION_TYPE_FREE }}"
                        @selected($selectedSessionType === \App\Models\ClinicalNote::SESSION_TYPE_FREE)
                    >
                        جلسه آزاد
                    </option>
                </select>

                @error('session_type')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="session_at" class="mb-2 block text-sm font-medium text-gray-700">
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

                @error('session_at')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="treatment_plan_id" class="mb-2 block text-sm font-medium text-gray-700">
                    نقشه درمان
                </label>

                <select
                    id="treatment_plan_id"
                    name="treatment_plan_id"
                    class="w-full border border-gray-300 px-3 py-2"
                >
                    <option value="">بدون نقشه درمان</option>

                    @foreach ($plans as $plan)
                        <option
                            value="{{ $plan->id }}"
                            @selected((string) $selectedPlanId === (string) $plan->id)
                        >
                            {{ $plan->title }}
                            @if (!$plan->isActive())
                                - آرشیو شده
                            @endif
                        </option>
                    @endforeach
                </select>

                @error('treatment_plan_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="treatment_plan_stage_id" class="mb-2 block text-sm font-medium text-gray-700">
                    مرحله مسیر درمان
                </label>

                <select
                    id="treatment_plan_stage_id"
                    name="treatment_plan_stage_id"
                    class="w-full border border-gray-300 px-3 py-2"
                >
                    <option value="">بدون مرحله</option>

                    @foreach ($plans as $plan)
                        @foreach ($plan->stages as $stage)
                            <option
                                value="{{ $stage->id }}"
                                data-plan-id="{{ $plan->id }}"
                                @selected((string) $selectedStageId === (string) $stage->id)
                            >
                                مرحله {{ $stage->stage_number }}:
                                {{ $stage->title }}
                            </option>
                        @endforeach
                    @endforeach
                </select>

                @error('treatment_plan_stage_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="appointment_id" class="mb-2 block text-sm font-medium text-gray-700">
                    نوبت مرتبط
                </label>

                <select
                    id="appointment_id"
                    name="appointment_id"
                    class="w-full border border-gray-300 px-3 py-2"
                >
                    <option value="">بدون نوبت مرتبط</option>

                    @foreach ($appointments as $appointment)
                        <option
                            value="{{ $appointment->id }}"
                            @selected((string) old('appointment_id', $note->appointment_id) === (string) $appointment->id)
                        >
                            {{ \App\Support\PersianDate::format($appointment->starts_at, 'yyyy/MM/dd HH:mm') }}
                        </option>
                    @endforeach
                </select>

                @error('appointment_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </section>

    @foreach ([
        'summary' => 'موضوع و خلاصه جلسه',
        'client_condition' => 'وضعیت و شرایط مراجع',
        'interventions' => 'دستور جلسه و مداخلات انجام‌شده',
        'homework_and_next_plan' => 'تمرین‌ها و برنامه جلسه بعد',
        'private_note' => 'یادداشت خصوصی مشاور',
    ] as $field => $label)
        <section class="border border-gray-200 bg-white p-5">
            <label for="{{ $field }}" class="block text-sm font-medium text-gray-700">
                {{ $label }}
            </label>

            <textarea
                id="{{ $field }}"
                name="{{ $field }}"
                rows="6"
                class="mt-2 w-full border border-gray-300 px-3 py-2"
            >{{ old($field, $note->{$field}) }}</textarea>

            @error($field)
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const planSelect = document.getElementById('treatment_plan_id');
        const stageSelect = document.getElementById('treatment_plan_stage_id');
        const typeSelect = document.getElementById('session_type');

        function syncStages() {
            const planId = planSelect.value;

            [...stageSelect.options].forEach(function (option) {
                if (!option.dataset.planId) {
                    option.hidden = false;
                    return;
                }

                option.hidden = option.dataset.planId !== planId;
            });

            if (!planId || typeSelect.value === 'free') {
                stageSelect.value = '';
                stageSelect.disabled = true;
            } else {
                stageSelect.disabled = false;
            }
        }

        function syncPlan() {
            if (typeSelect.value === 'free') {
                planSelect.removeAttribute('required');
                stageSelect.value = '';
            }

            syncStages();
        }

        planSelect.addEventListener('change', syncStages);
        typeSelect.addEventListener('change', syncPlan);

        syncPlan();
    });
</script>
