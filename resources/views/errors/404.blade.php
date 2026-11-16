@extends('website.layouts.app')

@section('title', 'صفحه پیدا نشد | کریم محمدزاده')
@section('robots', 'noindex, follow')

@push('styles')
    <link rel="stylesheet" href="{{ asset('website/style/care-error-404.css') }}?v=3">
@endpush

@section('content')
    <div class="care404-header-wrap">
        <div class="layout">
            @include('website.partials.header')
        </div>
    </div>
    <main id="main-content" class="care404-content" aria-labelledby="care404-title">
        <div class="care404-inner">
            <div class="care404-illustration" aria-hidden="true">
                <svg viewBox="0 0 240 190" fill="none" focusable="false">
                    <circle cx="120" cy="91" r="76" fill="#edf3ee"/>
                    <path d="M64 161h112" stroke="#cbd8ce" stroke-width="2" stroke-linecap="round"/>
                    <path d="M84 159V82a36 36 0 0 1 72 0v77" fill="#fff" stroke="#315c45" stroke-width="3" stroke-linecap="round"/>
                    <path d="M98 159V84a22 22 0 0 1 44 0v75" fill="#e1ece3" stroke="#315c45" stroke-width="2"/>
                    <path d="m99 68 30 12v79H99V68Z" fill="#fffdf6" stroke="#315c45" stroke-width="2.5" stroke-linejoin="round"/>
                    <path d="M119 114v9" stroke="#ad8b3e" stroke-width="3" stroke-linecap="round"/>
                    <path d="M173 160v-34m0 19c-15 0-21-11-21-20 14 0 21 8 21 20Zm0-10c0-17 10-26 24-26 0 16-9 26-24 26Z" fill="#d6e4d7" stroke="#52745c" stroke-width="2" stroke-linejoin="round"/>
                    <path d="M55 83v10m-5-5h10" stroke="#b7994c" stroke-width="2" stroke-linecap="round"/>
                    <circle cx="179" cy="61" r="3" fill="#b7994c"/>
                </svg>
            </div>
            <p class="care404-label">خطای ۴۰۴</p>
            <h1 id="care404-title">صفحه پیدا نشد</h1>
            <p class="care404-description">آدرس این صفحه درست نیست یا صفحه جابه‌جا شده است.</p>
            <nav class="care404-actions" aria-label="ادامهٔ مسیر">
                <a class="care404-booking" href="{{ route('booking.index') }}">رزرو نوبت <span aria-hidden="true">←</span></a>
                <a class="care404-home" href="{{ route('home') }}">صفحهٔ اصلی</a>
            </nav>
        </div>
    </main>
@endsection
{{-- The website layout includes the actual site footer once. --}}
