<x-layouts.app>
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        <a
            href="{{ route('psychologist.clinical-records.show', $client) }}"
            class="text-sm text-indigo-700 hover:underline"
        >
            بازگشت به پرونده
        </a>

        <h1 class="mt-4 text-2xl font-bold text-gray-900">
            ویرایش نقشه درمان
        </h1>

        @if ($plan->isArchived())
            <p class="mt-4 border border-amber-300 bg-amber-50 p-4 text-sm text-amber-800">
                این نقشه درمان آرشیو شده و فقط‌خواندنی است.
            </p>
        @endif

        <form
            method="POST"
            action="{{ route('psychologist.treatment-plans.update', $plan) }}"
            class="mt-8 space-y-6"
        >
            @csrf
            @method('PUT')

            <section class="border border-gray-200 bg-white p-5">
                <label for="title" class="block text-sm font-medium text-gray-700">
                    عنوان نقشه درمان
                </label>

                <input
                    id="title"
                    name="title"
                    value="{{ old('title', $plan->title) }}"
                    required
                    @disabled($plan->isArchived())
                    class="mt-2 w-full border border-gray-300 px-3 py-2"
                >

                <label for="main_problem" class="mt-5 block text-sm font-medium text-gray-700">
                    مسئله اصلی
                </label>

                <textarea
                    id="main_problem"
                    name="main_problem"
                    rows="5"
                    @disabled($plan->isArchived())
                    class="mt-2 w-full border border-gray-300 px-3 py-2"
                >{{ old('main_problem', $plan->main_problem) }}</textarea>

                <label for="current_stage" class="mt-5 block text-sm font-medium text-gray-700">
                    مرحله فعلی
                </label>

                <select
                    id="current_stage"
                    name="current_stage"
                    @disabled($plan->isArchived())
                    class="mt-2 w-full border border-gray-300 px-3 py-2"
                >
                    @foreach ([1 => 'شناخت و ریشه‌یابی', 2 => 'کسب مهارت و اقدام', 3 => 'تثبیت و استقلال'] as $number => $title)
                        <option
                            value="{{ $number }}"
                            @selected((int) old('current_stage', $plan->current_stage) === $number)
                        >
                            {{ $number }}. {{ $title }}
                        </option>
                    @endforeach
                </select>
            </section>

            @foreach ($plan->stages as $stage)
                <section class="border border-gray-200 bg-white p-5">
                    <h2 class="font-semibold text-gray-900">
                        مرحله {{ $stage->stage_number }}: {{ $stage->title }}
                    </h2>

                    <label
                        for="stage_goal_{{ $stage->stage_number }}"
                        class="mt-4 block text-sm font-medium text-gray-700"
                    >
                        هدف مرحله
                    </label>

                    <textarea
                        id="stage_goal_{{ $stage->stage_number }}"
                        name="stages[{{ $stage->stage_number }}][goal]"
                        rows="4"
                        @disabled($plan->isArchived())
                        class="mt-2 w-full border border-gray-300 px-3 py-2"
                    >{{ old("stages.{$stage->stage_number}.goal", $stage->goal) }}</textarea>

                    <label
                        for="stage_sessions_{{ $stage->stage_number }}"
                        class="mt-4 block text-sm font-medium text-gray-700"
                    >
                        تعداد جلسات تخمینی
                    </label>

                    <input
                        id="stage_sessions_{{ $stage->stage_number }}"
                        name="stages[{{ $stage->stage_number }}][estimated_sessions]"
                        type="number"
                        min="0"
                        max="65535"
                        value="{{ old("stages.{$stage->stage_number}.estimated_sessions", $stage->estimated_sessions) }}"
                        @disabled($plan->isArchived())
                        class="mt-2 w-full border border-gray-300 px-3 py-2"
                    >
                </section>
            @endforeach

            @if ($plan->isActive())
                <button
                    type="submit"
                    class="border border-indigo-700 bg-indigo-700 px-5 py-2 font-medium text-white"
                >
                    ذخیره تغییرات
                </button>
            @endif
        </form>

        @if ($plan->isActive())
            <form
                method="POST"
                action="{{ route('psychologist.treatment-plans.archive', $plan) }}"
                class="mt-8 border-t border-gray-200 pt-6"
            >
                @csrf
                @method('PATCH')

                <label for="status" class="block text-sm font-medium text-gray-700">
                    آرشیو نقشه درمان
                </label>

                <select
                    id="status"
                    name="status"
                    required
                    class="mt-2 w-full border border-gray-300 px-3 py-2"
                >
                    <option value="completed">تکمیل‌شده</option>
                    <option value="dropped_out">انصراف مراجع</option>
                    <option value="referred">ارجاع‌شده</option>
                </select>

                <textarea
                    name="status_reason"
                    rows="3"
                    placeholder="توضیح وضعیت پایانی"
                    class="mt-3 w-full border border-gray-300 px-3 py-2"
                ></textarea>

                <button
                    type="submit"
                    class="mt-3 border border-red-700 px-5 py-2 font-medium text-red-700"
                >
                    آرشیو نقشه
                </button>
            </form>
        @endif
    </div>
</x-layouts.app>
