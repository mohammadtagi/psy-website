<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>مشاوره روان‌شناختی کریم محمدزاده | رزرو جلسه آنلاین و حضوری</title>

    <meta
        name="description"
        content="رزرو جلسه مشاوره روان‌شناختی آنلاین و حضوری با کریم محمدزاده. مشاهده زمان‌های آزاد و انتخاب جلسه ۴۵ یا ۶۰ دقیقه‌ای."
    >

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-stone-50 text-slate-800 antialiased">

<header class="border-b border-stone-200 bg-stone-50/95">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-5 lg:px-8">
        <a
            href="{{ route('home') }}"
            class="text-lg font-bold tracking-tight text-slate-900 sm:text-xl"
        >
            کریم محمدزاده
        </a>

        <nav class="hidden items-center gap-8 text-sm text-slate-600 md:flex">
            <a href="#about" class="transition hover:text-teal-700">
                درباره جلسات
            </a>

            <a href="#services" class="transition hover:text-teal-700">
                خدمات
            </a>

            <a href="#process" class="transition hover:text-teal-700">
                روند رزرو
            </a>

            <a href="#faq" class="transition hover:text-teal-700">
                پرسش‌های متداول
            </a>
        </nav>

        <div class="flex items-center gap-3">
            @auth
                @if (auth()->user()->role === 'psychologist')
                    <a
                        href="{{ route('psychologist.dashboard') }}"
                        class="hidden rounded-full px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-stone-200 sm:inline-flex"
                    >
                        پنل روان‌شناس
                    </a>
                @elseif (auth()->user()->role === 'admin')
                    <a
                        href="{{ route('admin.dashboard') }}"
                        class="hidden rounded-full px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-stone-200 sm:inline-flex"
                    >
                        پنل مدیریت
                    </a>
                @else
                    <a
                        href="{{ route('client.appointments.index') }}"
                        class="hidden rounded-full px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-stone-200 sm:inline-flex"
                    >
                        نوبت‌های من
                    </a>
                @endif
            @else
                <a
                    href="{{ route('auth.login') }}"
                    class="rounded-full px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-stone-200"
                >
                    ورود
                </a>
            @endauth

            <a
                href="{{ route('booking.index') }}"
                class="rounded-full bg-teal-700 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-teal-800"
            >
                رزرو جلسه
            </a>
        </div>
    </div>
</header>

<main>
    <section class="relative overflow-hidden">
        <div class="mx-auto grid max-w-7xl gap-12 px-5 py-20 lg:grid-cols-[1.05fr_0.95fr] lg:items-center lg:px-8 lg:py-28">
            <div class="max-w-2xl">
                <p class="mb-5 text-sm font-semibold tracking-wide text-teal-700">
                    مشاوره روان‌شناختی آنلاین و حضوری
                </p>

                <h1 class="text-4xl font-bold leading-[1.45] tracking-tight text-slate-900 sm:text-5xl lg:text-6xl">
                    فضایی برای شناخت بهتر خود و ساختن قدم بعدی
                </h1>

                <p class="mt-7 max-w-xl text-lg leading-9 text-slate-600">
                    جلسات مشاوره با کریم محمدزاده با تمرکز بر شنیدن تجربه شما،
                    شناخت مسئله و پیدا کردن مسیرهای قابل‌اجرا برای ادامه راه برگزار می‌شود.
                </p>

                <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                    <a
                        href="{{ route('booking.index') }}"
                        class="inline-flex items-center justify-center rounded-full bg-teal-700 px-7 py-3.5 text-base font-semibold text-white transition hover:bg-teal-800"
                    >
                        مشاهده زمان‌های آزاد
                    </a>

                    <a
                        href="#services"
                        class="inline-flex items-center justify-center rounded-full border border-stone-300 bg-white px-7 py-3.5 text-base font-semibold text-slate-700 transition hover:border-teal-700 hover:text-teal-700"
                    >
                        آشنایی با جلسات
                    </a>
                </div>

                <div class="mt-10 flex flex-wrap gap-x-8 gap-y-3 text-sm text-slate-500">
                    <span>جلسات ۴۵ و ۶۰ دقیقه‌ای</span>
                    <span>برگزاری آنلاین با Google Meet</span>
                    <span>امکان جلسه حضوری</span>
                </div>
            </div>

            <div class="relative">
                <div class="absolute -right-8 -top-8 h-40 w-40 rounded-full bg-teal-100/70 blur-2xl"></div>
                <div class="absolute -bottom-8 -left-8 h-48 w-48 rounded-full bg-amber-100/70 blur-2xl"></div>

                <div class="relative rounded-[2rem] border border-stone-200 bg-white p-6 shadow-xl shadow-slate-200/50 sm:p-8">
                    <div class="rounded-[1.5rem] bg-gradient-to-br from-teal-800 to-teal-600 p-8 text-white sm:p-10">
                        <div class="mb-16 flex items-start justify-between">
                                <span class="rounded-full bg-white/15 px-3 py-1 text-xs">
                                    جلسات مشاوره
                                </span>

                            <span class="text-3xl text-teal-100">◌</span>
                        </div>

                        <p class="text-sm text-teal-100">
                            با سرعت و زمان مناسب خودتان
                        </p>

                        <h2 class="mt-3 text-3xl font-bold leading-relaxed">
                            شروع گفت‌وگو می‌تواند اولین قدم باشد.
                        </h2>

                        <div class="mt-8 border-t border-white/20 pt-5 text-sm leading-7 text-teal-50">
                            زمان مناسب خود را انتخاب کنید و جلسه را آنلاین یا حضوری رزرو کنید.
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-4">
                        <div class="rounded-2xl bg-stone-100 p-4">
                            <p class="text-xs text-slate-500">مدت جلسه</p>
                            <p class="mt-2 font-bold text-slate-900">۴۵ دقیقه</p>
                        </div>

                        <div class="rounded-2xl bg-stone-100 p-4">
                            <p class="text-xs text-slate-500">مدت جلسه</p>
                            <p class="mt-2 font-bold text-slate-900">۶۰ دقیقه</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="about" class="border-y border-stone-200 bg-white">
        <div class="mx-auto grid max-w-7xl gap-10 px-5 py-16 lg:grid-cols-3 lg:px-8">
            <div>
                <p class="text-sm font-semibold text-teal-700">درباره جلسات</p>
                <h2 class="mt-3 text-3xl font-bold leading-relaxed text-slate-900">
                    گفت‌وگویی امن و ساختاریافته
                </h2>
            </div>

            <div class="lg:col-span-2">
                <p class="text-base leading-9 text-slate-600">
                    در جلسه مشاوره، تجربه‌ها و دغدغه‌های شما با دقت بررسی می‌شوند.
                    هدف جلسه، ایجاد فضایی برای گفت‌وگوی روشن، شناخت بهتر مسئله و
                    بررسی راه‌های مناسب برای ادامه مسیر است.
                </p>
            </div>
        </div>
    </section>

    <section id="services" class="mx-auto max-w-7xl px-5 py-20 lg:px-8">
        <div class="max-w-2xl">
            <p class="text-sm font-semibold text-teal-700">انتخاب نوع جلسه</p>

            <h2 class="mt-3 text-3xl font-bold leading-relaxed text-slate-900">
                جلسه مناسب خود را انتخاب کنید
            </h2>

            <p class="mt-4 leading-8 text-slate-600">
                هر دو نوع جلسه به‌صورت آنلاین و حضوری قابل رزرو هستند.
                زمان‌های در دسترس پس از انتخاب نوع جلسه نمایش داده می‌شوند.
            </p>
        </div>

        <div class="mt-10 grid gap-6 md:grid-cols-2">
            <article class="rounded-3xl border border-stone-200 bg-white p-7 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm text-slate-500">جلسه استاندارد</p>
                        <h3 class="mt-2 text-2xl font-bold text-slate-900">
                            مشاوره ۴۵ دقیقه‌ای
                        </h3>
                    </div>

                    <span class="rounded-full bg-teal-50 px-3 py-1 text-sm font-semibold text-teal-700">
                            ۴۵ دقیقه
                        </span>
                </div>

                <p class="mt-6 leading-8 text-slate-600">
                    مناسب برای گفت‌وگو و بررسی متمرکز یک موضوع یا مسئله مشخص.
                </p>

                <div class="mt-8 flex items-center justify-between border-t border-stone-100 pt-5">
                        <span class="font-bold text-slate-900">
                            ۷۶۰٬۰۰۰ تومان
                        </span>

                    <a
                        href="{{ route('booking.index') }}"
                        class="font-semibold text-teal-700 hover:text-teal-900"
                    >
                        مشاهده زمان‌ها
                    </a>
                </div>
            </article>

            <article class="rounded-3xl border border-teal-200 bg-teal-50/50 p-7 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm text-slate-500">جلسه گسترده‌تر</p>
                        <h3 class="mt-2 text-2xl font-bold text-slate-900">
                            مشاوره ۶۰ دقیقه‌ای
                        </h3>
                    </div>

                    <span class="rounded-full bg-white px-3 py-1 text-sm font-semibold text-teal-700">
                            ۶۰ دقیقه
                        </span>
                </div>

                <p class="mt-6 leading-8 text-slate-600">
                    مناسب برای موضوعاتی که به زمان بیشتر برای گفت‌وگو و بررسی نیاز دارند.
                </p>

                <div class="mt-8 flex items-center justify-between border-t border-teal-100 pt-5">
                        <span class="font-bold text-slate-900">
                            ۹۵۰٬۰۰۰ تومان
                        </span>

                    <a
                        href="{{ route('booking.index') }}"
                        class="font-semibold text-teal-700 hover:text-teal-900"
                    >
                        مشاهده زمان‌ها
                    </a>
                </div>
            </article>
        </div>
    </section>

    <section id="process" class="bg-slate-900 text-white">
        <div class="mx-auto max-w-7xl px-5 py-20 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-sm font-semibold text-teal-300">روند رزرو</p>

                <h2 class="mt-3 text-3xl font-bold leading-relaxed">
                    رزرو جلسه در چند قدم ساده
                </h2>
            </div>

            <div class="mt-12 grid gap-8 md:grid-cols-3">
                <div class="border-r border-slate-700 pr-5">
                    <span class="text-3xl font-bold text-teal-300">۱</span>
                    <h3 class="mt-5 text-xl font-bold">انتخاب زمان</h3>
                    <p class="mt-3 leading-8 text-slate-300">
                        نوع جلسه و یکی از زمان‌های آزاد را انتخاب کنید.
                    </p>
                </div>

                <div class="border-r border-slate-700 pr-5">
                    <span class="text-3xl font-bold text-teal-300">۲</span>
                    <h3 class="mt-5 text-xl font-bold">ورود یا ثبت اطلاعات</h3>
                    <p class="mt-3 leading-8 text-slate-300">
                        با شماره موبایل وارد شوید و اطلاعات لازم برای رزرو را ثبت کنید.
                    </p>
                </div>

                <div class="border-r border-slate-700 pr-5">
                    <span class="text-3xl font-bold text-teal-300">۳</span>
                    <h3 class="mt-5 text-xl font-bold">تأیید نوبت</h3>
                    <p class="mt-3 leading-8 text-slate-300">
                        پس از تکمیل مراحل رزرو، جزئیات جلسه را در حساب کاربری خود ببینید.
                    </p>
                </div>
            </div>

            <a
                href="{{ route('booking.index') }}"
                class="mt-12 inline-flex rounded-full bg-white px-7 py-3.5 font-semibold text-slate-900 transition hover:bg-teal-50"
            >
                شروع رزرو جلسه
            </a>
        </div>
    </section>

    <section id="faq" class="mx-auto max-w-4xl px-5 py-20 lg:px-8">
        <div class="text-center">
            <p class="text-sm font-semibold text-teal-700">پرسش‌های متداول</p>

            <h2 class="mt-3 text-3xl font-bold text-slate-900">
                قبل از رزرو بدانید
            </h2>
        </div>

        <div class="mt-10 divide-y divide-stone-200 rounded-3xl border border-stone-200 bg-white px-6">
            <details class="group py-5">
                <summary class="flex cursor-pointer list-none items-center justify-between gap-5 font-semibold text-slate-900">
                    جلسه آنلاین چگونه برگزار می‌شود؟
                    <span class="text-xl text-teal-700 transition group-open:rotate-45">+</span>
                </summary>

                <p class="mt-4 leading-8 text-slate-600">
                    جلسه آنلاین از طریق Google Meet برگزار می‌شود و اطلاعات جلسه پس از تکمیل فرآیند رزرو در اختیار شما قرار می‌گیرد.
                </p>
            </details>

            <details class="group py-5">
                <summary class="flex cursor-pointer list-none items-center justify-between gap-5 font-semibold text-slate-900">
                    آیا امکان لغو نوبت وجود دارد؟
                    <span class="text-xl text-teal-700 transition group-open:rotate-45">+</span>
                </summary>

                <p class="mt-4 leading-8 text-slate-600">
                    مراجع می‌تواند تا ۱۲ ساعت پیش از شروع جلسه نوبت را لغو کند. لغو نوبت‌های نزدیک‌تر از این بازه برای مراجع امکان‌پذیر نیست.
                </p>
            </details>

            <details class="group py-5">
                <summary class="flex cursor-pointer list-none items-center justify-between gap-5 font-semibold text-slate-900">
                    چه مدت زمانی برای جلسه قابل انتخاب است؟
                    <span class="text-xl text-teal-700 transition group-open:rotate-45">+</span>
                </summary>

                <p class="mt-4 leading-8 text-slate-600">
                    جلسه‌های ۴۵ دقیقه‌ای و ۶۰ دقیقه‌ای در زمان‌های آزاد قابل انتخاب هستند.
                </p>
            </details>
        </div>
    </section>
</main>

<footer class="border-t border-stone-200 bg-white">
    <div class="mx-auto flex max-w-7xl flex-col gap-5 px-5 py-8 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between lg:px-8">
        <div>
            <p class="font-semibold text-slate-800">
                کریم محمدزاده
            </p>

            <p class="mt-1">
                مشاوره روان‌شناختی آنلاین و حضوری
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-5">
            <a href="{{ route('booking.index') }}" class="hover:text-teal-700">
                رزرو جلسه
            </a>

            @auth
                <a href="{{ route('client.appointments.index') }}" class="hover:text-teal-700">
                    نوبت‌های من
                </a>
            @else
                <a href="{{ route('auth.login') }}" class="hover:text-teal-700">
                    ورود
                </a>
            @endauth
        </div>

        <p>
            © {{ now()->year }} تمامی حقوق محفوظ است.
        </p>
    </div>
</footer>

</body>
</html>
