<x-layouts.app>
    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        <a
            href="{{ route('psychologist.clinical-records.show', $client) }}"
            class="text-sm text-indigo-700 hover:underline"
        >
            بازگشت به پرونده
        </a>

        <h1 class="mt-4 text-2xl font-bold text-gray-900">
            ایجاد نقشه درمان
        </h1>

        <form
            method="POST"
            action="{{ route('psychologist.treatment-plans.store', $record) }}"
            class="mt-8 space-y-6"
        >
            @csrf

            <section class="border border-gray-200 bg-white p-5">
                <label for="title" class="block text-sm font-medium text-gray-700">
                    عنوان نقشه درمان
                </label>

                <input
                    id="title"
                    name="title"
                    value="{{ old('title', $plan->title) }}"
                    required
                    class="mt-2 w-full border border-gray-300 px-3 py-2"
                >

                @error('title')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <label for="main_problem" class="mt-5 block text-sm font-medium text-gray-700">
                    مسئله اصلی
                </label>

                <textarea
                    id="main_problem"
                    name="main_problem"
                    rows="6"
                    class="mt-2 w-full border border-gray-300 px-3 py-2"
                >{{ old('main_problem', $plan->main_problem) }}</textarea>

                @error('main_problem')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </section>

            <div class="flex gap-3">
                <button
                    type="submit"
                    class="border border-indigo-700 bg-indigo-700 px-5 py-2 font-medium text-white"
                >
                    ایجاد نقشه درمان
                </button>

                <a
                    href="{{ route('psychologist.clinical-records.show', $client) }}"
                    class="border border-gray-300 px-5 py-2 font-medium text-gray-700"
                >
                    انصراف
                </a>
            </div>
        </form>
    </div>
</x-layouts.app>
