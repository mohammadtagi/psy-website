@extends('website.layouts.app')

@section('title', 'صفحه پیدا نشد | کریم محمدزاده')
@section('robots', 'noindex, follow')

@push('styles')
    <link rel="stylesheet" href="{{ asset('website/style/care-error-404.css') }}?v=2">
@endpush

@section('content')
    <div class="site-not-found">
        <div class="layout">
            @include('website.partials.header')
        </div>

        <main id="main-content" class="site-not-found-content layout" aria-labelledby="not-found-title">
            <div class="site-not-found-copy">
                <p class="site-not-found-label">خطای ۴۰۴</p>
                <h1 id="not-found-title">صفحه‌ای که دنبال آن هستید<br>پیدا نشد.</h1>
                <p class="site-not-found-description">ممکن است این صفحه حذف شده باشد یا آدرس آن تغییر کرده باشد.</p>
                <a class="site-not-found-home" href="{{ route('home') }}">بازگشت به صفحهٔ اصلی <span aria-hidden="true">←</span></a>
            </div>
        </main>
    </div>
@endsection
{{-- The shared website layout renders the real site footer. Do not duplicate it here. --}}
