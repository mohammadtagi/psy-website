<x-layouts.app>
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a
                href="{{ route('psychologist.clinical-records.show', $client) }}"
                class="text-sm text-indigo-700 hover:underline"
            >
                بازگشت به پرونده
            </a>

            <h1 class="mt-4 text-2xl font-bold text-gray-900">
                ویرایش یادداشت بالینی
            </h1>
        </div>

        @include('psychologist.clinical-notes.form')
    </div>
</x-layouts.app>
