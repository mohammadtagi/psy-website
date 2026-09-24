<x-layouts.app>
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        @php
            $clientName = trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? ''));
        @endphp

        <div class="mb-8">
            <a
                href="{{ route('psychologist.clients.index', ['q' => $client->mobile]) }}"
                class="text-sm text-indigo-700 hover:underline"
            >
                بازگشت به فهرست مراجع
            </a>

            <h1 class="mt-4 text-2xl font-bold text-gray-900">
                ایجاد پرونده بالینی
            </h1>

            <p class="mt-2 text-sm text-gray-600">
                {{ $clientName ?: 'نام ثبت نشده' }}
                -
                <span dir="ltr">{{ $client->mobile }}</span>
            </p>
        </div>

        @include('psychologist.clinical-records.form')
    </div>
</x-layouts.app>
