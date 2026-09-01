{{-- resources/views/landing.blade.php --}}
@extends('layouts.app')

@section('title', 'دکتر کریم محمدزاده | روان‌شناس بالینی')
@section('meta_description', 'وقتی نمی‌دانید با حال‌تان چه کار کنید تنها نیستید. جلسات روان‌درمانی آنلاین با دکتر کریم محمدزاده.')

@section('content')
    {{-- ================= HERO ================= --}}
    <section
        class="relative overflow-hidden bg-primary-dark"
        aria-labelledby="hero-heading"
    >
        {{-- پس‌زمینه دو تکه (کرم / سبز تیره) با برش مورب --}}
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute inset-y-0 right-0 w-full bg-primary-dark lg:w-[58%]"></div>
            <div
                class="absolute inset-y-0 left-0 hidden w-[52%] bg-cream lg:block"
                style="clip-path: polygon(0 0, 78% 0, 100% 100%, 0 100%);"
            ></div>
            {{-- نسخه موبایل: گرادیان ساده‌تر --}}
            <div class="absolute inset-0 bg-gradient-to-b from-cream via-cream to-primary-dark lg:hidden"></div>
        </div>

        <div class="relative mx-auto grid min-h-[720px] w-full max-w-site grid-cols-1 items-end gap-10 px-6 pb-16 pt-28 lg:grid-cols-12 lg:items-center lg:gap-[30px] lg:px-[75px] lg:pb-20 lg:pt-32">
            {{-- تصویر دکتر (چپ بصری / در RTL ستون‌های آخر) --}}
            <div class="order-1 flex justify-center lg:col-span-5 lg:order-2 lg:justify-start">
                <div class="relative w-full max-w-md lg:max-w-none">
                    <img
                        src="https://placehold.co/560x680/1F3D2E/F5EDE0?text=Doctor+Portrait"
                        width="560"
                        height="680"
                        alt="پرتره دکتر کریم محمدزاده، روان‌شناس بالینی"
                        class="mx-auto h-auto w-[min(100%,420px)] object-contain object-bottom drop-shadow-xl lg:w-full"
                        fetchpriority="high"
                    >
                </div>
            </div>

            {{-- متن هیرو --}}
            <div class="order-2 z-10 flex flex-col lg:col-span-7 lg:order-1 lg:col-start-1">
                <h1
                    id="hero-heading"
                    class="text-[28px] font-bold leading-relaxed text-white sm:text-4xl lg:text-[44px] lg:leading-[1.7]"
                >
                    وقتی
                    <span class="text-white"> نمی‌دانید با این حال‌تان</span>
                    <br class="hidden sm:block">
                    چه کار کنید،
                    <span class="text-gold">تنها نیستید</span>
                </h1>

                <div class="mt-6 max-w-xl space-y-3 font-secondary text-sm leading-8 text-white/90 lg:text-[15px]">
                    <p>من کریم محمدزاده هستم؛</p>
                    <p>
                        روان‌شناس بالینی. اینجا جایی‌ست که می‌توانید بدون خجالت و ترس، از حال این روزهای‌تان
                        حرف بزنید و کم‌کم راهی پیدا کنیم که زندگی دوباره کمی قابل‌تحمل‌تر شود.
                    </p>
                </div>

                <div class="mt-8 flex flex-flex flex-wrap items-center gap-3">
                    <a
                        href="#about"
                        class="inline-flex items-center justify-center rounded-pill border border-white/80 bg-transparent px-6 py-2.5 text-sm font-medium text-white transition hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-gold-soft"
                    >
                        درباره من
                    </a>
                    <a
                        href="#sessions"
                        class="inline-flex items-center justify-center rounded-pill bg-cream px-6 py-2.5 text-sm font-medium text-primary-dark transition hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-gold-soft"
                    >
                        چطور جلسات برگزار می‌شود
                    </a>
                </div>

                {{-- سه ویژگی --}}
                <ul class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-3 sm:gap-4" role="list">
                    <li class="flex items-center gap-3 text-white">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/10" aria-hidden="true">
                        {{-- user icon --}}
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 1115 0v.75H4.5v-.75z" />
                        </svg>
                    </span>
                        <span class="text-xs leading-6 lg:text-sm">رویکرد علمی و<br class="hidden sm:block"> تخصصی</span>
                    </li>
                    <li class="flex items-center gap-3 text-white">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/10" aria-hidden="true">
                        {{-- handshake --}}
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v3m0 12v3M3 12h3m12 0h3M6.5 6.5l2 2m7 7l2 2m0-11l-2 2m-7 7l-2 2" />
                        </svg>
                    </span>
                        <span class="text-xs leading-6 lg:text-sm">رابطه محترمانه و<br class="hidden sm:block"> انسانی</span>
                    </li>
                    <li class="flex items-center gap-3 text-white">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/10" aria-hidden="true">
                        {{-- lock/doc --}}
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V7.5a4.5 4.5 0 10-9 0v3m-.75 0h10.5A1.75 1.75 0 0119 12.25v6A1.75 1.75 0 0117.25 20H6.75A1.75 1.75 0 015 18.25v-6a1.75 1.75 0 011.75-1.75z" />
                        </svg>
                    </span>
                        <span class="text-xs leading-6 lg:text-sm">رازداری و حریم<br class="hidden sm:block"> شخصی</span>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    {{-- ================= THERAPY SECTION ================= --}}
    <section
        id="sessions"
        class="bg-white py-16 lg:py-24"
        aria-labelledby="therapy-heading"
    >
        <div class="mx-auto w-full max-w-site px-6 lg:px-[75px]">
            <div class="grid grid-cols-1 items-start gap-8 lg:grid-cols-12 lg:gap-[30px]">
                {{-- دکمه سمت راست در طرح --}}
                <div class="lg:col-span-3 lg:pt-2">
                    <a
                        href="#about"
                        class="inline-flex items-center gap-2 rounded-pill border border-primary/20 bg-cream px-5 py-2.5 text-sm font-medium text-primary-dark transition hover:border-primary/40 hover:bg-cream/80 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                    >
                        با من آشنا شوید
                        <span aria-hidden="true" class="text-base">←</span>
                    </a>
                </div>

                <div class="lg:col-span-9">
                    <h2
                        id="therapy-heading"
                        class="text-2xl font-bold leading-relaxed text-primary-dark sm:text-3xl lg:text-[32px]"
                    >
                        به‌جای عادت‌کردن به حال بد، روی
                        <span class="text-gold">تراپی منظم</span>
                        حساب کنید
                    </h2>

                    <p class="mt-4 max-w-3xl font-secondary text-sm leading-8 text-primary-dark/80 lg:text-[15px]">
                        با جلسات آنلاین فردی، می‌توانید بدون رفت‌وآمد
                        روی اضطراب، حال بد، تروماها و استرس‌های روزمره کار کنید.
                    </p>
                </div>
            </div>

            {{-- گالری ۴ تایی --}}
            <ul class="mt-12 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 lg:gap-[30px]" role="list">
                @php
                    $therapyImages = [
                        ['src' => 'https://placehold.co/400x480/4A7C83/F5EDE0?text=Session+1', 'alt' => 'جلسه مشاوره؛ مراجع روی مبل و درمانگر در حال یادداشت'],
                        ['src' => 'https://placehold.co/400x480/0A5C3A/F5EDE0?text=Session+2', 'alt' => 'گفت‌وگوی درمانی رودررو در اتاق مشاوره'],
                        ['src' => 'https://placehold.co/400x480/1F3D2E/F5EDE0?text=Session+3', 'alt' => 'جلسه دونفره درمانی در فضای آرام منزل‌مانند'],
                        ['src' => 'https://placehold.co/400x480/E8C670/1F3D2E?text=Session+4', 'alt' => 'مشاوره فردی با تمرکز و فضای حرفه‌ای'],
                    ];
                @endphp

                @foreach ($therapyImages as $image)
                    <li class="overflow-hidden rounded-card shadow-card">
                        <img
                            src="{{ $image['src'] }}"
                            width="400"
                            height="480"
                            alt="{{ $image['alt'] }}"
                            class="h-64 w-full object-cover transition duration-500 hover:scale-[1.03] sm:h-72 lg:h-80"
                            loading="lazy"
                            decoding="async"
                        >
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
@endsection
