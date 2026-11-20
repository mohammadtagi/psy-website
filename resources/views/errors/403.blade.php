@extends('website.layouts.app')

@section('title', 'دسترسی مجاز نیست | کریم محمدزاده')
@section('robots', 'noindex, follow')

@push('styles')
    <link rel="stylesheet" href="{{ asset('website/style/care-error-403.css') }}?v=1">
@endpush

@section('content')
    <div class="care403-header-wrap">
        <div class="layout">
            @include('website.partials.header')
        </div>
    </div>
    <main id="main-content" class="care403-content" aria-labelledby="care403-title">
        <div class="care403-inner">
            <div class="care403-illustration" aria-hidden="true">
                <svg viewBox="0 0 240 190" fill="none" focusable="false">
                    <circle cx="120" cy="91" r="76" fill="#edf3ee"/>
                    <path d="M64 161h112" stroke="#cbd8ce" stroke-width="2" stroke-linecap="round"/>
                    <path d="M96 91V73a24 24 0 0 1 48 0v18" stroke="#315c45" stroke-width="5" stroke-linecap="round"/>
                    <rect x="84" y="88" width="72" height="68" rx="14" fill="#fffdf6" stroke="#315c45" stroke-width="3"/>
                    <circle cx="120" cy="115" r="7" fill="#ad8b3e"/>
                    <path d="M120 121v12" stroke="#ad8b3e" stroke-width="5" stroke-linecap="round"/>
                    <path d="M173 160v-34m0 19c-15 0-21-11-21-20 14 0 21 8 21 20Zm0-10c0-17 10-26 24-26 0 16-9 26-24 26Z" fill="#d6e4d7" stroke="#52745c" stroke-width="2" stroke-linejoin="round"/>
                    <path d="M55 83v10m-5-5h10" stroke="#b7994c" stroke-width="2" stroke-linecap="round"/>
                    <circle cx="179" cy="61" r="3" fill="#b7994c"/>
                </svg>
            </div>
            <p class="care403-label">خطای ۴۰۳</p>
            <h1 id="care403-title">دسترسی مجاز نیست</h1>
            <p class="care403-description">شما اجازهٔ دسترسی به این صفحه را ندارید.</p>
            <nav class="care403-actions" aria-label="ادامهٔ مسیر">
                <a class="care403-booking" href="{{ route('booking.index') }}">رزرو نوبت <span aria-hidden="true">←</span></a>
                <a class="care403-home" href="{{ route('home') }}">صفحهٔ اصلی</a>
            </nav>
        </div>
    </main>
@endsection
{{-- The website layout includes the actual site footer once. --}}
