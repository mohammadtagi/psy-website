@extends('components.layouts.app')
@section('title', 'جلسه و یادداشت‌های من')
@push('styles')
<link rel="stylesheet" href="{{ asset('website/style/care-client-session.css') }}">
@endpush
@section('content')
<main class="ccs" dir="rtl">
    <header class="ccs-header">
        <div><h1>جلسه و یادداشت‌های من</h1><p class="ccs-muted">مشاور: {{ $appointment->psychologist?->first_name }} {{ $appointment->psychologist?->last_name }}</p></div>
        <nav aria-label="بازگشت"><a href="{{ route('client.dashboard') }}">داشبورد</a><a href="{{ route('client.appointments.index') }}">نوبت‌های من</a></nav>
    </header>
    @include('client.appointments.partials.session-summary', ['appointment' => $appointment])
    @include('client.appointments.partials.private-notes', ['appointment' => $appointment, 'notes' => $notes])
</main>
<script src="{{ asset('website/js/care-client-session.js') }}" defer></script>
@endsection
