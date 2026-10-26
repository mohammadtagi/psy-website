@extends('components.layouts.app')

@section('title', 'جزئیات جلسه')

@section('content')
    <main class="mx-auto max-w-4xl space-y-5 px-4 py-8">
        <header>
            <h1 class="text-2xl font-bold">جزئیات جلسه</h1>

            <p class="mt-2 text-gray-600">
                مشاور:
                {{ $appointment->psychologist->first_name }}
                {{ $appointment->psychologist->last_name }}
            </p>

            <a
                href="{{ route('client.appointments.index') }}"
                class="mt-3 inline-block text-indigo-700"
            >
                بازگشت به نوبت‌ها
            </a>
        </header>

        @include('appointments.partials.session-details', [
            'appointment' => $appointment,
        ])
        @include('client.appointments.partials.private-notes', [
    'appointment' => $appointment,
    'notes' => $notes,
])

    </main>
@endsection
