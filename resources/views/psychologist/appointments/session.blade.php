@extends('components.layouts.app')

@section('title', 'مدیریت جلسه')

@section('content')
    @php
        $editable = $appointment->status
            === \App\Models\Appointment::STATUS_CONFIRMED;

        $selectedType = old(
            'therapy_session_type',
            $appointment->therapy_session_type
        );

        $selectedPlan = old(
            'treatment_plan_id',
            $appointment->treatment_plan_id
        );

        $selectedStage = old(
            'treatment_plan_stage_id',
            $appointment->treatment_plan_stage_id
        );
    @endphp

    <main class="mx-auto max-w-4xl space-y-5 px-4 py-8">
        <header>
            <h1 class="text-2xl font-bold">مدیریت جلسه</h1>

            <p class="mt-2 text-gray-600">
                مراجع:
                {{ $appointment->client->first_name }}
                {{ $appointment->client->last_name }}
            </p>

            <a
                href="{{ route('psychologist.appointments.index') }}"
                class="mt-3 inline-block text-indigo-700"
            >
                بازگشت به نوبت‌ها
            </a>
        </header>

        @if (session('status'))
            <div role="status" class="border border-green-200 bg-green-50 p-4">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div role="alert" class="border border-red-200 bg-red-50 p-4">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @include('appointments.partials.session-details', [
            'appointment' => $appointment,
        ])

        @if (! $editable)
            <p class="border border-amber-200 bg-amber-50 p-4">
                فقط اطلاعات نوبت تأییدشده قابل ویرایش است.
            </p>
        @else
            <form
                method="POST"
                action="{{ route('psychologist.appointments.session.update', $appointment) }}"
                class="space-y-5 border border-gray-200 bg-white p-5"
            >
                @csrf
                @method('PUT')

                <h2 class="text-lg font-semibold">ویرایش اطلاعات جلسه</h2>

                <p class="text-sm text-amber-800">
                    موضوع، موارد مطرح‌شده، توصیه‌ها و تمرین‌های این فرم برای مراجع قابل مشاهده‌اند.
                    یادداشت محرمانه را اینجا وارد نکنید.
                </p>

                <div>
                    <label for="attendance_status" class="mb-2 block">
                        وضعیت برگزاری
                    </label>

                    <select
                        id="attendance_status"
                        name="attendance_status"
                        required
                        class="w-full border border-gray-300 p-2"
                    >
                        @foreach ([
                            'pending' => 'در انتظار برگزاری',
                            'held' => 'برگزارشده',
                            'absent' => 'عدم حضور مراجع',
                        ] as $value => $label)
                            <option
                                value="{{ $value }}"
                                @selected(old('attendance_status', $appointment->attendance_status) === $value)
                            >
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="therapy_session_type" class="mb-2 block">
                        ارتباط با مسیر درمان
                    </label>

                    <select
                        id="therapy_session_type"
                        name="therapy_session_type"
                        required
                        class="w-full border border-gray-300 p-2"
                    >
                        <option value="free" @selected($selectedType === 'free')>
                            جلسه آزاد
                        </option>
                        <option value="plan" @selected($selectedType === 'plan')>
                            جلسه مسیر درمان
                        </option>
                    </select>
                </div>

                <div>
                    <label for="treatment_plan_id" class="mb-2 block">
                        مسیر درمان
                    </label>

                    <select
                        id="treatment_plan_id"
                        name="treatment_plan_id"
                        class="w-full border border-gray-300 p-2"
                    >
                        <option value="">انتخاب مسیر</option>

                        @foreach ($plans as $plan)
                            <option
                                value="{{ $plan->id }}"
                                @selected((string) $selectedPlan === (string) $plan->id)
                            >
                                {{ $plan->title }}
                                {{ $plan->isActive() ? '' : ' — آرشیوشده' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="treatment_plan_stage_id" class="mb-2 block">
                        مرحله مسیر
                    </label>

                    <select
                        id="treatment_plan_stage_id"
                        name="treatment_plan_stage_id"
                        class="w-full border border-gray-300 p-2"
                    >
                        <option value="">انتخاب مرحله</option>

                        @foreach ($plans as $plan)
                            @foreach ($plan->stages as $stage)
                                <option
                                    value="{{ $stage->id }}"
                                    data-plan-id="{{ $plan->id }}"
                                    @selected((string) $selectedStage === (string) $stage->id)
                                >
                                    {{ $plan->title }} —
                                    مرحله {{ $stage->stage_number }}:
                                    {{ $stage->title }}
                                </option>
                            @endforeach
                        @endforeach
                    </select>
                </div>

                @if ($appointment->session_type === \App\Models\Appointment::SESSION_TYPE_ONLINE)
                    <div>
                        <label for="meeting_url" class="mb-2 block">
                            لینک جلسه آنلاین
                        </label>

                        <input
                            id="meeting_url"
                            name="meeting_url"
                            type="url"
                            dir="ltr"
                            maxlength="2048"
                            value="{{ old('meeting_url', $appointment->meeting_url) }}"
                            class="w-full border border-gray-300 p-2"
                        >
                    </div>
                @endif

                <div>
                    <label for="session_subject" class="mb-2 block">
                        موضوع جلسه
                    </label>

                    <input
                        id="session_subject"
                        name="session_subject"
                        type="text"
                        maxlength="255"
                        value="{{ old('session_subject', $appointment->session_subject) }}"
                        class="w-full border border-gray-300 p-2"
                    >
                </div>

                @foreach ([
                    'session_discussion' => 'موارد مطرح‌شده',
                    'session_recommendations' => 'توصیه‌ها',
                    'session_exercises' => 'تمرین‌ها',
                ] as $field => $label)
                    <div>
                        <label for="{{ $field }}" class="mb-2 block">
                            {{ $label }}
                        </label>

                        <textarea
                            id="{{ $field }}"
                            name="{{ $field }}"
                            rows="5"
                            maxlength="10000"
                            class="w-full border border-gray-300 p-2"
                        >{{ old($field, $appointment->{$field}) }}</textarea>
                    </div>
                @endforeach

                <button
                    type="submit"
                    class="bg-indigo-700 px-5 py-2 text-white"
                >
                    ذخیره اطلاعات جلسه
                </button>
            </form>

            <script>
                (() => {
                    const type = document.getElementById('therapy_session_type');
                    const plan = document.getElementById('treatment_plan_id');
                    const stage = document.getElementById('treatment_plan_stage_id');

                    function sync() {
                        const isPlan = type.value === 'plan';

                        plan.disabled = !isPlan;
                        plan.required = isPlan;

                        stage.disabled = !isPlan || !plan.value;
                        stage.required = isPlan;

                        for (const option of stage.options) {
                            if (!option.dataset.planId) continue;

                            const allowed = isPlan
                                && option.dataset.planId === plan.value;

                            option.hidden = !allowed;
                            option.disabled = !allowed;
                        }

                        const selected = stage.options[stage.selectedIndex];

                        if (!isPlan || !plan.value || selected?.disabled) {
                            stage.value = '';
                        }

                        if (!isPlan) {
                            plan.value = '';
                        }
                    }

                    type.addEventListener('change', sync);
                    plan.addEventListener('change', sync);
                    sync();
                })();
            </script>
        @endif
    </main>
@endsection
