{{-- resources/views/partials/header.blade.php --}}
<header class="absolute inset-x-0 top-0 z-50">
    <div class="mx-auto flex w-full max-w-site items-center justify-between px-6 py-5 lg:px-[75px]">
        {{-- لوگو + نام (سمت راست در RTL) --}}
        <a href="{{ url('/') }}" class="flex items-center gap-3" aria-label="صفحه اصلی دکتر کریم محمدزاده">
            <img
                src="https://placehold.co/48x48/0A5C3A/F5EDE0?text=Logo"
                width="48"
                height="48"
                alt="لوگوی دکتر کریم محمدزاده"
                class="h-10 w-10 rounded-full object-cover lg:h-12 lg:w-12"
            >
            <span class="text-sm font-medium text-white lg:text-base">
                دکتر کریم محمدزاده
            </span>
        </a>

        {{-- منو --}}
        <nav class="hidden items-center gap-8 md:flex" aria-label="منوی اصلی">
            <a href="{{ url('/') }}" class="text-sm text-white/95 transition hover:text-gold-soft">خانه</a>
            <a href="#services" class="text-sm text-white/95 transition hover:text-gold-soft">خدمات</a>
            <a href="#contact" class="text-sm text-white/95 transition hover:text-gold-soft">تماس با ما</a>
            <a href="#about" class="text-sm text-white/95 transition hover:text-gold-soft">درباره ما</a>
        </nav>

        {{-- CTA --}}
        <a
            href="#contact"
            class="inline-flex items-center justify-center rounded-pill bg-primary px-5 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-primary/90 focus:outline-none focus-visible:ring-2 focus-visible:ring-gold-soft focus-visible:ring-offset-2"
        >
            تماس بگیرید
        </a>
    </div>
</header>
