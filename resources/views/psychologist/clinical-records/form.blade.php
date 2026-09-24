@php
    $isEdit = $record->exists;
    $formAction = $isEdit
        ? route('psychologist.clinical-records.update', $record)
        : route('psychologist.clinical-records.store', $client);
@endphp

<form method="POST" action="{{ $formAction }}" class="space-y-8">
    @csrf

    @if ($isEdit)
        @method('PUT')
    @endif

    <section class="border border-gray-200 bg-white p-5">
        <h2 class="text-lg font-semibold text-gray-900">مشخصات و وضعیت کلی</h2>

        <div class="mt-5 grid gap-5 md:grid-cols-2">
            <div>
                <label for="gender" class="mb-2 block text-sm font-medium text-gray-700">
                    جنسیت
                </label>

                <input
                    id="gender"
                    name="gender"
                    value="{{ old('gender', $record->gender) }}"
                    class="w-full border border-gray-300 px-3 py-2"
                >
            </div>

            <div>
                <label for="occupation" class="mb-2 block text-sm font-medium text-gray-700">
                    شغل
                </label>

                <input
                    id="occupation"
                    name="occupation"
                    value="{{ old('occupation', $record->occupation) }}"
                    class="w-full border border-gray-300 px-3 py-2"
                >
            </div>

            <div>
                <label for="employment_status" class="mb-2 block text-sm font-medium text-gray-700">
                    وضعیت اشتغال
                </label>

                <input
                    id="employment_status"
                    name="employment_status"
                    value="{{ old('employment_status', $record->employment_status) }}"
                    class="w-full border border-gray-300 px-3 py-2"
                >
            </div>

            <div>
                <label for="status" class="mb-2 block text-sm font-medium text-gray-700">
                    وضعیت پرونده
                </label>

                <select
                    id="status"
                    name="status"
                    class="w-full border border-gray-300 px-3 py-2"
                >
                    <option
                        value="active"
                        @selected(old('status', $record->status) === 'active')
                    >
                        فعال
                    </option>

                    <option
                        value="closed"
                        @selected(old('status', $record->status) === 'closed')
                    >
                        بسته‌شده
                    </option>
                </select>
            </div>
        </div>

        <div class="mt-5">
            <label for="family_status_and_living_conditions" class="mb-2 block text-sm font-medium text-gray-700">
                وضعیت خانوادگی و شرایط زندگی
            </label>

            <textarea
                id="family_status_and_living_conditions"
                name="family_status_and_living_conditions"
                rows="4"
                class="w-full border border-gray-300 px-3 py-2"
            >{{ old('family_status_and_living_conditions', $record->family_status_and_living_conditions) }}</textarea>
        </div>
    </section>

    <section class="border border-gray-200 bg-white p-5">
        <h2 class="text-lg font-semibold text-gray-900">مسئله و وضعیت فعلی</h2>

        @foreach ([
            'presenting_problem' => 'مسئله اصلی مراجع',
            'problem_onset' => 'زمان و نحوه شروع مسئله',
            'previous_treatment' => 'سوابق درمانی',
            'current_medications' => 'داروهای فعلی',
            'important_medical_history' => 'سوابق پزشکی مهم',
        ] as $field => $label)
            <div class="mt-5">
                <label for="{{ $field }}" class="mb-2 block text-sm font-medium text-gray-700">
                    {{ $label }}
                </label>

                <textarea
                    id="{{ $field }}"
                    name="{{ $field }}"
                    rows="4"
                    class="w-full border border-gray-300 px-3 py-2"
                >{{ old($field, $record->{$field}) }}</textarea>
            </div>
        @endforeach
    </section>

    <section class="border border-gray-200 bg-white p-5">
        <h2 class="text-lg font-semibold text-gray-900">ایمنی و خطر</h2>

        <div class="grid gap-5 md:grid-cols-2">
            @foreach ([
                'suicide_risk' => 'خطر خودکشی',
                'self_harm_risk' => 'خطر خودآسیب‌رسانی',
                'harm_to_others_risk' => 'خطر آسیب به دیگران',
                'overall_risk_level' => 'سطح خطر کلی',
            ] as $field => $label)
                <div>
                    <label for="{{ $field }}" class="mb-2 block text-sm font-medium text-gray-700">
                        {{ $label }}
                    </label>

                    <select
                        id="{{ $field }}"
                        name="{{ $field }}"
                        class="w-full border border-gray-300 px-3 py-2"
                    >
                        <option value="">ثبت نشده</option>
                        @foreach ([
                            'none' => 'بدون خطر',
                            'low' => 'کم',
                            'medium' => 'متوسط',
                            'high' => 'زیاد',
                        ] as $value => $option)
                            <option
                                value="{{ $value }}"
                                @selected(old($field, $record->{$field}) === $value)
                            >
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endforeach
        </div>

        <div class="mt-5">
            <label for="safety_actions" class="mb-2 block text-sm font-medium text-gray-700">
                اقدامات ایمنی و برنامه مدیریت خطر
            </label>

            <textarea
                id="safety_actions"
                name="safety_actions"
                rows="5"
                class="w-full border border-gray-300 px-3 py-2"
            >{{ old('safety_actions', $record->safety_actions) }}</textarea>
        </div>
    </section>

    <section class="border border-gray-200 bg-white p-5">
        <h2 class="text-lg font-semibold text-gray-900">ارزیابی و برنامه درمان</h2>

        @foreach ([
            'important_life_history' => 'سوابق مهم زندگی',
            'important_relationships_and_support' => 'روابط مهم و شبکه حمایت',
            'values_and_personal_resources' => 'ارزش‌ها و منابع شخصی',
            'clinical_observations' => 'مشاهدات بالینی',
            'clinical_formulation' => 'فرمول‌بندی بالینی',
            'treatment_approach' => 'رویکرد درمانی',
            'treatment_goals' => 'اهداف درمان',
            'treatment_plan_and_schedule' => 'برنامه و زمان‌بندی درمان',
        ] as $field => $label)
            <div class="mt-5">
                <label for="{{ $field }}" class="mb-2 block text-sm font-medium text-gray-700">
                    {{ $label }}
                </label>

                <textarea
                    id="{{ $field }}"
                    name="{{ $field }}"
                    rows="4"
                    class="w-full border border-gray-300 px-3 py-2"
                >{{ old($field, $record->{$field}) }}</textarea>
            </div>
        @endforeach

        <div class="mt-5">
            <label for="treatment_stage" class="mb-2 block text-sm font-medium text-gray-700">
                مرحله درمان
            </label>

            <input
                id="treatment_stage"
                name="treatment_stage"
                value="{{ old('treatment_stage', $record->treatment_stage) }}"
                class="w-full border border-gray-300 px-3 py-2"
            >
        </div>
    </section>

    <button
        type="submit"
        class="border border-indigo-700 bg-indigo-700 px-5 py-2 font-medium text-white hover:bg-indigo-800"
    >
        {{ $isEdit ? 'ذخیره تغییرات' : 'ایجاد پرونده' }}
    </button>
</form>
