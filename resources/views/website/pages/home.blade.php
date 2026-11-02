@extends('website.layouts.app')

@section('title', 'روان‌شناس در تبریز؛ مشاوره حضوری و آنلاین | کریم محمدزاده')
@section('description', 'مشاوره فردی و مشاوره زوجین با کریم محمدزاده در تبریز به‌صورت حضوری یا در سراسر ایران به صورت آنلاین. آشنایی با روند جلسات، حوزه‌های مشاوره و ثبت درخواست نوبت.')
@section('canonical', route('home'))
@section('social_image_alt', 'کریم محمدزاده، روان‌شناس بالینی')

@section('content')
    <main>
        <section id="home" class="relative overflow-hidden bg-forest text-white">
            <div class="hero-panel pointer-events-none absolute inset-y-0 left-0 z-0 hidden w-[45.5%] bg-cream lg:block"
                 aria-hidden="true"></div>

            <div class="layout relative z-10">
                @include('website.partials.header')

                <div class="hero-content relative grid min-h-0 grid-cols-1 md:max-lg:grid-cols-[42%_58%] md:max-lg:items-end md:max-lg:gap-x-5 md:max-lg:pb-6 lg:min-h-[650px] lg:grid-cols-[46%_54%]">

                    <div class="relative order-3 mt-6 flex justify-center overflow-visible md:max-lg:order-1 md:max-lg:mt-4 md:max-lg:items-end lg:order-1 lg:mt-0 lg:block lg:min-h-[650px]">
                        <div class="relative h-[300px] w-[min(280px,84vw)] sm:h-[420px] sm:w-[360px] md:max-lg:h-[440px] md:max-lg:w-full md:max-lg:max-w-[320px] lg:absolute lg:bottom-0 lg:left-[47%] lg:h-[645px] lg:w-[520px] lg:-translate-x-1/2">
                            <div class="pointer-events-none absolute left-[46%] top-[56%] z-0 h-[365px] w-[365px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-cream-2 sm:h-[450px] sm:w-[450px] md:max-lg:h-[340px] md:max-lg:w-[340px] lg:hidden"
                                 aria-hidden="true"></div>
                            <img   src="{{ asset('website/images/DSC_720.webp') }}"
                                   srcset="
    {{ asset('website/images/DSC_480.webp') }} 480w,
    {{ asset('website/images/DSC_720.webp') }} 720w,
    {{ asset('website/images/DSC_1280.webp') }} 1280w
  "
                                   sizes="(max-width: 768px) 84vw, 550px"
                                   width="4755"
                                   height="5902"
                                   fetchpriority="high"
                                   decoding="async"
                                   alt="کریم محمدزاده، روان‌شناس بالینی در تبریز"
                                   class="relative z-10 h-full w-full object-cover object-top [clip-path:polygon(0%_0,88%_0,100%_100%,0_100%)] lg:[clip-path:polygon(8%_0,82%_0,100%_100%,0_100%)]"/>
                        </div>
                    </div>

                    <div class="hero-copy order-1 flex items-center justify-end pb-5 pt-8 text-right sm:pb-8 sm:pt-12 md:max-lg:order-2 md:max-lg:pb-4 md:max-lg:pt-8 lg:order-2 lg:pb-[112px] lg:pt-0">
                        <div class="w-full max-w-[700px] md:max-lg:max-w-none">
                            <h1 class="max-w-[700px] text-[28px] font-bold leading-[1.7] sm:text-[40px] md:max-lg:text-[32px] md:max-lg:leading-[1.7] lg:text-[45px] lg:leading-[1.65]">

                                اگر مدتی است حالتان خوب نیست، می‌شود از     <span class="block text-gold">همین‌جا</span> شروع کرد

                            </h1>
                            <p class="mt-5 max-w-[620px] text-[14px] leading-[2] text-white/85 sm:mt-7 sm:text-[16px] sm:leading-[2.25] lg:text-[16px]">
                                گاهی فشارها آن‌قدر آرام و تدریجی زیاد می‌شوند که متوجه نمی‌شویم چه زمانی از خودمان فاصله گرفته‌ایم. مشاوره می‌تواند فرصتی باشد برای مکث کردن، فهمیدن آنچه تجربه می‌کنید و پیدا کردن راهی که با شرایط زندگی‌تان هماهنگ باشد.
                            </p>
                            <div class="mt-6 flex w-full flex-col gap-3 sm:mt-9 sm:w-auto sm:flex-row sm:flex-wrap sm:justify-start">
                                <a href="#process"
                                   class="inline-flex h-12 w-full items-center justify-center rounded-full bg-cream px-7 text-[14px] font-medium text-forest-3 transition hover:bg-white sm:h-[45px] sm:w-auto sm:min-w-[200px] sm:text-[13px]">
                                    چطور جلسات برگزار می‌شود
                                </a>
                                <a href="{{ url('/about-us') }}"
                                   class="inline-flex h-12 w-full items-center justify-center rounded-full border border-white/45 px-7 text-[14px] font-medium text-white transition hover:bg-white hover:text-forest-3 sm:h-[45px] sm:w-auto sm:min-w-[125px] sm:text-[13px]">
                                    درباره ما
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="hero-copy order-2 border-y border-white/15 py-4 md:max-lg:order-3 md:max-lg:col-span-2 md:max-lg:py-5 lg:hidden">
                        <div class="grid grid-cols-3 gap-2 sm:gap-4 md:max-lg:gap-5" dir="rtl">
                            <div class="benefit flex flex-col items-center gap-2 text-center md:max-lg:flex-row md:max-lg:justify-start md:max-lg:gap-3 md:max-lg:text-right">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-full border border-white/50 sm:h-10 sm:w-10">
                                    <img src="{{ asset('website/images/person.png') }}" alt="" class="h-5 w-5 object-contain"/>
                                </span>
                                <p class="text-[11px] leading-[1.7] sm:text-[13px] sm:leading-[1.8]">رویکرد علمی متناسب با شما</p>
                            </div>
                            <div class="benefit flex flex-col items-center gap-2 text-center">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-full border border-white/50 sm:h-10 sm:w-10">
                                    <img src="{{ asset('website/images/f5e7b65c-6a4a-48ca-b819-953817773698.png') }}" alt=""
                                         class="h-5 w-5 object-contain"/>
                                </span>
                                <p class="text-[11px] leading-[1.7] sm:text-[13px] sm:leading-[1.8]">رابطه محترمانه و
                                    انسانی</p>
                            </div>
                            <div class="benefit flex flex-col items-center gap-2 text-center">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-full border border-white/50 sm:h-10 sm:w-10">
                                    <img src="{{ asset('website/images/confidential.png') }}" alt="" class="h-5 w-5 object-contain"/>
                                </span>
                                <p class="text-[11px] leading-[1.7] sm:text-[13px] sm:leading-[1.8]">رازداری و حریم شخصی</p>
                            </div>
                        </div>
                    </div>

                    <div class="hero-copy absolute bottom-[38px] left-0 right-0 hidden lg:block">
                        <div class="grid grid-cols-3 gap-6 pl-[46%]" dir="rtl">
                            <div class="benefit flex items-center justify-start gap-3">
                                <div class="flex h-[46px] w-[46px] items-center justify-center rounded-full">
                                    <span class="text-[21px]"><img src="{{ asset('website/images/person.png') }}" alt="" width="32"/></span>
                                </div>
                                <div class="text-right">
                                    <p class="text-[12px] leading-[1.9]">
                                        رویکرد علمی متناسب <br> با شما
                                    </p>
                                </div>
                            </div>
                            <div class="benefit flex items-center justify-start gap-3">
                                <div class="flex h-[46px] w-[46px] items-center justify-center rounded-full">
                                <span class="text-[21px]"><img src="{{ asset('website/images/f5e7b65c-6a4a-48ca-b819-953817773698.png') }}"
                                                               alt="" width="50" /></span>
                                </div>
                                <div class="text-right">
                                    <p class="text-[12px] leading-[1.9]">رابطه محترمانه و<br/>انسانی</p>
                                </div>
                            </div>
                            <div class="benefit flex items-center justify-start gap-3">
                                <div class="flex h-[46px] w-[46px] items-center justify-center rounded-full">
                                    <span class="text-[21px]"><img src="{{ asset('website/images/confidential.png') }}" alt="" width="42" /></span>
                                </div>
                                <div class="text-right">
                                    <p class="text-[12px] leading-[1.9]">رازداری و حریم<br/>شخصی</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="py-20" aria-labelledby="regular-therapy-title">
            <div class="layout">
                <div class="mb-12 flex flex-col items-end justify-between md:flex-row">
                    <div class="text-right">
                        <h2 id="regular-therapy-title" class="mb-4 text-3xl font-bold text-forest-2">
                            به‌جای عادت‌کردن به حال بد، روی
                            <span class="decoration-gold">تراپی منظم</span>
                            حساب کنید
                        </h2>
                        <p class="text-muted">
                            لازم نیست از قبل بدانید دقیقاً چه چیزی باید بگویید. می‌توانید از همان چیزی شروع کنید که این روزها <br> ذهن و زندگی‌تان را درگیر کرده است؛ از استرس و اضطراب گرفته تا افسردگی، خستگی یا رابطه‌ای که دیگر مثل قبل نیست.
                        </p>
                    </div>
                    <a href="{{ url('/about-us') }}"  class="mt-4 rounded-lg bg-forest-2 px-6 py-2 text-sm text-white md:mt-0">
                        با من آشنا شوید
                    </a>
                </div>

                <div class="overflow-hidden" dir="ltr">
                    <div class="overflow-hidden">
                        <div class="gallery-marquee flex w-max flex-nowrap pb-8">
                            <!-- مجموعه اصلی -->
                            <div class="flex flex-nowrap items-start gap-4 pe-4 pt-0">
                                <div class="h-64 w-40 shrink-0 overflow-hidden rounded-2xl bg-stone-200
                        sm:h-72 sm:w-48
                        md:h-80 md:w-60
                        lg:w-64">
                                    <img
                                        src="{{ asset('website/images/therapi1.png') }}"
                                        class="block h-full w-full object-cover"
                                        alt="فضای جلسه مشاوره فردی"
                                        width="576"
                                        height="884"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                </div>

                                <div class="h-64 w-40 shrink-0  overflow-hidden rounded-2xl bg-stone-200
                        sm:h-72 sm:w-48
                        md:h-80 md:w-60
                        lg:w-64">
                                    <img
                                        src="{{ asset('website/images/therapi2.png') }}"
                                        class="block h-full w-full object-cover"
                                        alt="فضای برگزاری جلسه مشاوره فردی"
                                        width="576"
                                        height="744"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                </div>

                                <div class="h-64 w-40 shrink-0 overflow-hidden rounded-2xl bg-stone-200
                        sm:h-72 sm:w-48
                        md:h-80 md:w-60
                        lg:w-64">
                                    <img
                                        src="{{ asset('website/images/therapi3.png') }}"
                                        class="block h-full w-full object-cover"
                                        alt="مشخص کردن مسیر درمان فردی"
                                        width="576"
                                        height="604"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                </div>

                                <div class="h-64 w-40 shrink-0  overflow-hidden rounded-2xl bg-stone-200
                        sm:h-72 sm:w-48
                        md:h-80 md:w-60
                        lg:w-64">
                                    <img
                                        src="{{ asset('website/images/therapi4.png') }}"
                                        class="block h-full w-full object-cover"
                                        alt="همراهی مشاور در جلسات تراپی"
                                        width="576"
                                        height="744"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                </div>

                                <div class="h-64 w-40 shrink-0 overflow-hidden rounded-2xl bg-stone-200
                        sm:h-72 sm:w-48
                        md:h-80 md:w-60
                        lg:w-64">
                                    <img
                                        src="{{ asset('website/images/therapi5.png') }}"
                                        class="block h-full w-full object-cover"
                                        alt="مشاوره اضطراب و استرس"
                                        width="576"
                                        height="884"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                </div>
                            </div>

                            <!-- کپی کاملاً مشابه مجموعهٔ اصلی -->
                            <div class="flex flex-nowrap items-start gap-4 pe-4 pt-0" aria-hidden="true">
                                <div class="h-64 w-40 shrink-0 overflow-hidden rounded-2xl bg-stone-200 sm:h-72 sm:w-48 md:h-80 md:w-60 lg:w-64">
                                    <img src="{{ asset('website/images/therapi1.png') }}" class="block h-full w-full object-cover" alt="">
                                </div>

                                <div class="h-64 w-40 shrink-0  overflow-hidden rounded-2xl bg-stone-200 sm:h-72 sm:w-48 md:h-80 md:w-60 lg:w-64">
                                    <img src="{{ asset('website/images/therapi2.png') }}" class="block h-full w-full object-cover" alt="">
                                </div>

                                <div class="h-64 w-40 shrink-0 overflow-hidden rounded-2xl bg-stone-200 sm:h-72 sm:w-48 md:h-80 md:w-60 lg:w-64">
                                    <img src="{{ asset('website/images/therapi3.png') }}" class="block h-full w-full object-cover" alt="">
                                </div>

                                <div class="h-64 w-40 shrink-0  overflow-hidden rounded-2xl bg-stone-200 sm:h-72 sm:w-48 md:h-80 md:w-60 lg:w-64">
                                    <img src="{{ asset('website/images/therapi4.png') }}" class="block h-full w-full object-cover" alt="">
                                </div>

                                <div class="h-64 w-40 shrink-0 overflow-hidden rounded-2xl bg-stone-200 sm:h-72 sm:w-48 md:h-80 md:w-60 lg:w-64">
                                    <img src="{{ asset('website/images/therapi5.png') }}" class="block h-full w-full object-cover" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <section id="services" dir="rtl" class="relative overflow-hidden bg-white py-16 lg:py-[90px]">
            <div class="layout relative xl:min-h-[980px]">

                <!-- Decorative circles -->
                <div
                    class="pointer-events-none absolute left-1/2 top-1/2 z-0 hidden h-[920px] w-[920px] -translate-x-1/2 -translate-y-1/2 rounded-full border circle_border  xl:block">
                </div>

                <div
                    class="pointer-events-none absolute left-1/2 top-1/2 z-0 hidden h-[800px] w-[800px] -translate-x-1/2 -translate-y-1/2 rounded-full border circle_border  xl:block">
                </div>

                <div
                    class="pointer-events-none absolute left-1/2 top-1/2 z-0 hidden h-[680px] w-[680px] -translate-x-1/2 -translate-y-1/2 rounded-full border circle_border  xl:block">
                </div>

                <!-- Center heading -->
                <div
                    class="relative z-10 mx-auto max-w-[720px] text-center xl:absolute xl:left-1/2 xl:top-1/2 xl:w-full xl:-translate-x-1/2 xl:-translate-y-1/2">

                    <h2 class="text-[24px] font-bold leading-[1.8] text-ink-1 sm:text-[26px] xl:text-[30px]">
                        <span class="text-gold-2">سلامت روان</span>، بخشی از زندگی روزمره شماست
                    </h2>

                    <p
                        class="mx-auto mt-5 max-w-[620px] text-[16px] leading-[2.1] text-muted-2 xl:mt-8 xl:text-[19px] xl:leading-[2.2]">
                        با جلسات آنلاین، امکان پیگیری حال روان‌تان را از هر جایی
                        <br class="hidden md:block" />
                        دارید خانه، محل کار یا در مسیر
                    </p>
                </div>

                <!-- Desktop graphic cards -->
                <div class="relative z-20 hidden xl:block">

                    <!-- Individual sessions -->
                    <article
                        class="absolute right-[10%] top-0 h-[269px] w-[435px] rounded-[28px] bg-cream p-[15px] shadow-[0_14px_34px_rgba(0,0,0,0.10)]">

                        <div class="mb-[45px] flex justify-start text-forest-8">
                            <svg width="54" height="54" viewBox="0 0 64 64" fill="none" aria-hidden="true">
                                <path
                                    d="M17 15h21c7 0 11 4 11 10s-4 10-11 10H22c-5 0-8 3-8 7s3 7 8 7h25"
                                    stroke="currentColor"
                                    stroke-width="5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M45 35l9 9-9 9"
                                    stroke="currentColor"
                                    stroke-width="5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </div>

                        <h3 class="mb-2 text-right text-[24px] font-bold leading-[1.6] text-ink-2">
                            جلسات مشاوره فردی
                        </h3>

                        <p class="text-right text-[14px] leading-[1.85] text-muted-3">
                            اگر با اضطراب، خستگی، بی‌انگیزگی یا افکار تکراری درگیر هستید، جلسات فردی به شما کمک می‌کند شرایطتان را بهتر بشناسید و راهکارهای مؤثری برای بهبود حال و کیفیت زندگی پیدا کنید.
                        </p>
                    </article>

                    <!-- Family relationships -->
                    <article
                        class="absolute left-[10%] top-[22px] h-[269px] w-[435px] rounded-[28px] bg-cream p-[15px] shadow-[0_14px_34px_rgba(0,0,0,0.10)]">

                        <div class="mb-[45px] flex justify-start text-forest-8">
                            <svg width="54" height="54" viewBox="0 0 64 64" fill="none" aria-hidden="true">
                                <path
                                    d="M24 30l8 8c3 3 7 3 10 0l9-9"
                                    stroke="currentColor"
                                    stroke-width="5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M13 27l9-9 10 10"
                                    stroke="currentColor"
                                    stroke-width="5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M51 27l-9-9-8 8"
                                    stroke="currentColor"
                                    stroke-width="5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M18 35l9 9M28 40l7 7M39 40l-7 7"
                                    stroke="currentColor"
                                    stroke-width="4"
                                    stroke-linecap="round" />
                            </svg>
                        </div>

                        <h3 class="mb-2 text-right text-[24px] font-bold leading-[1.6] text-ink-2">
                            مشاوره روابط و زوجین
                        </h3>

                        <p class="text-right text-[14px] leading-[1.85] text-muted-3">
                            سکوت‌ها، فاصله‌ها و حرف‌های ناگفته گاهی بیشتر از دعوا به رابطه آسیب می‌زنند. در مشاوره زوجین، با شناخت تعارض‌ها و نیازهای عاطفی، راه‌های بهتری برای درک متقابل پیدا می‌کنیم.                    </p>
                    </article>

                    <!-- Depression support -->
                    <article
                        class="absolute right-[10%] top-[685px] h-[269px] w-[435px] rounded-[28px] bg-cream p-[15px] shadow-[0_14px_34px_rgba(0,0,0,0.10)]">

                        <div class="mb-[45px] flex justify-start text-forest-8">
                            <svg width="54" height="54" viewBox="0 0 64 64" fill="none" aria-hidden="true">
                                <path
                                    d="M14 41h12l13 6c5 2 10 0 13-4l3-4"
                                    stroke="currentColor"
                                    stroke-width="5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M26 41V29c0-3 2-5 5-5h5"
                                    stroke="currentColor"
                                    stroke-width="5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M45 15c5-6 15 0 10 8-2 4-6 7-10 10-4-3-8-6-10-10-5-8 5-14 10-8z"
                                    fill="currentColor" />
                            </svg>
                        </div>

                        <h3 class="mb-2 text-right text-[24px] font-bold leading-[1.6] text-ink-2">
                            حمایت در مواجهه با افسردگی
                        </h3>

                        <p class="text-right text-[14px] leading-[1.85] text-muted-3">
                            وقتی خستگی و ناامیدی طولانی می‌شود، تراپی راهکاری
                            برای صحبت کردن و یافتن راه هایی که روزها را قابل‌تحمل‌تر و
                            رابطه شما با خودتان را آرام‌تر کند
                        </p>
                    </article>

                    <!-- Trauma support -->
                    <article
                        class="absolute left-[10%] top-[700px] h-[269px] w-[435px] rounded-[28px] bg-cream p-[15px] shadow-[0_14px_34px_rgba(0,0,0,0.10)]">

                        <div class="mb-[45px] flex justify-start text-forest-8">
                            <svg width="54" height="54" viewBox="0 0 64 64" fill="none" aria-hidden="true">
                                <path
                                    d="M32 11l22 12v18L32 53 10 41V23l22-12z"
                                    stroke="currentColor"
                                    stroke-width="5"
                                    stroke-linejoin="round" />
                                <path
                                    d="M24 32h16M32 24v16"
                                    stroke="currentColor"
                                    stroke-width="5"
                                    stroke-linecap="round" />
                                <circle
                                    cx="32"
                                    cy="32"
                                    r="11"
                                    stroke="currentColor"
                                    stroke-width="5" />
                            </svg>
                        </div>

                        <h3 class="mb-2 text-right text-[24px] font-bold leading-[1.6] text-ink-2">
                            تروما و تجربه‌های آسیب‌زا
                        </h3>

                        <p class="text-right text-[14px] leading-[1.85] text-muted-3">
                            اگر تجربه‌ای سخت هنوز آرامش، احساس امنیت یا روابط شما را تحت‌تأثیر قرار داده است، در جلسات مشاوره می‌توانید با سرعت و آمادگی خودتان درباره آن صحبت کنید و مسیر بهبود را آغاز کنید.                    </p>
                    </article>
                </div>

                <!-- Tablet and mobile grid cards -->
                <div
                    class="relative z-20 mt-10 grid grid-cols-1 gap-5 sm:gap-6 md:mt-12 md:grid-cols-2 xl:hidden">

                    <!-- Individual sessions -->
                    <article
                        class="flex h-full flex-col rounded-[24px] bg-cream p-6 shadow-[0_12px_28px_rgba(0,0,0,0.09)] sm:p-8">

                        <div class="mb-5 text-forest-8" aria-hidden="true">
                            <svg width="44" height="44" viewBox="0 0 64 64" fill="none">
                                <path
                                    d="M17 15h21c7 0 11 4 11 10s-4 10-11 10H22c-5 0-8 3-8 7s3 7 8 7h25"
                                    stroke="currentColor"
                                    stroke-width="5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M45 35l9 9-9 9"
                                    stroke="currentColor"
                                    stroke-width="5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </div>

                        <h3 class="mb-3 text-[20px] font-bold leading-[1.6] text-ink-2 sm:text-[23px]">
                            جلسات مشاوره فردی
                        </h3>

                        <p class="text-[15px] leading-[2] text-muted-3 sm:text-[16px]">
                            در جلسات فردی، فرصتی دارید احساسات‌تان را بررسی کنید، درباره نگرانی‌ها و فشارهای روزمره صحبت کنید و به تدریج راههایی سالم‌تر برای مواجهه با آن‌ها پیدا کنید.
                        </p>
                    </article>

                    <!-- Family relationships -->
                    <article
                        class="flex h-full flex-col rounded-[24px] bg-cream p-6 shadow-[0_12px_28px_rgba(0,0,0,0.09)] sm:p-8">

                        <div class="mb-5 text-forest-8" aria-hidden="true">
                            <svg width="44" height="44" viewBox="0 0 64 64" fill="none">
                                <path
                                    d="M24 30l8 8c3 3 7 3 10 0l9-9"
                                    stroke="currentColor"
                                    stroke-width="5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M13 27l9-9 10 10"
                                    stroke="currentColor"
                                    stroke-width="5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M51 27l-9-9-8 8"
                                    stroke="currentColor"
                                    stroke-width="5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M18 35l9 9M28 40l7 7M39 40l-7 7"
                                    stroke="currentColor"
                                    stroke-width="4"
                                    stroke-linecap="round" />
                            </svg>
                        </div>

                        <h3 class="mb-3 text-[20px] font-bold leading-[1.6] text-ink-2 sm:text-[23px]">
                            مشاوره روابط و زوجین
                        </h3>

                        <p class="text-[15px] leading-[2] text-muted-3 sm:text-[16px]">
                            سکوت‌ها، فاصله‌ها و حرف‌های ناگفته گاهی بیشتر از دعوا به رابطه آسیب می‌زنند. در مشاوره زوجین، با شناخت تعارض‌ها و نیازهای عاطفی، راه‌های بهتری برای درک متقابل پیدا می‌کنیم.
                        </p>
                    </article>

                    <!-- Depression support -->
                    <article
                        class="flex h-full flex-col rounded-[24px] bg-cream p-6 shadow-[0_12px_28px_rgba(0,0,0,0.09)] sm:p-8">

                        <div class="mb-5 text-forest-8" aria-hidden="true">
                            <svg width="44" height="44" viewBox="0 0 64 64" fill="none">
                                <path
                                    d="M14 41h12l13 6c5 2 10 0 13-4l3-4"
                                    stroke="currentColor"
                                    stroke-width="5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M26 41V29c0-3 2-5 5-5h5"
                                    stroke="currentColor"
                                    stroke-width="5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M45 15c5-6 15 0 10 8-2 4-6 7-10 10-4-3-8-6-10-10-5-8 5-14 10-8z"
                                    fill="currentColor" />
                            </svg>
                        </div>

                        <h3 class="mb-3 text-[20px] font-bold leading-[1.6] text-ink-2 sm:text-[23px]">
                            حمایت در مواجهه با افسردگی
                        </h3>

                        <p class="text-[15px] leading-[2] text-muted-3 sm:text-[16px]">
                            وقتی خستگی و ناامیدی طولانی می‌شود، جلسات فرصتی است برای صحبت کردن و یافتن راههایی که روزها را قابل‌تحمل‌تر و رابطه شما با خودتان را آرام‌تر کند.
                        </p>
                    </article>

                    <!-- Trauma support -->
                    <article
                        class="flex h-full flex-col rounded-[24px] bg-cream p-6 shadow-[0_12px_28px_rgba(0,0,0,0.09)] sm:p-8">

                        <div class="mb-5 text-forest-8" aria-hidden="true">
                            <svg width="44" height="44" viewBox="0 0 64 64" fill="none">
                                <path
                                    d="M32 11l22 12v18L32 53 10 41V23l22-12z"
                                    stroke="currentColor"
                                    stroke-width="5"
                                    stroke-linejoin="round" />
                                <path
                                    d="M24 32h16M32 24v16"
                                    stroke="currentColor"
                                    stroke-width="5"
                                    stroke-linecap="round" />
                                <circle
                                    cx="32"
                                    cy="32"
                                    r="11"
                                    stroke="currentColor"
                                    stroke-width="5" />
                            </svg>
                        </div>

                        <h3 class="mb-3 text-[20px] font-bold leading-[1.6] text-ink-2 sm:text-[23px]">
                            تروما و تجربه‌های آسیب‌زا
                        </h3>

                        <p class="text-[15px] leading-[2] text-muted-3 sm:text-[16px]">
                            تجربه‌های سخت و ناگهانی می‌توانند مدت‌ها احساس امنیت و آرامش شما را به هم بزنند. در جلسات، قدم‌به‌قدم روی این تجربه‌ها کار می‌کنیم تا زندگی روزمره قابل‌تحمل‌تر و امن‌تر شود.
                        </p>
                    </article>
                </div>
            </div>
        </section>

        <section class="py-10 mb-28" aria-label="آمار تجربه و جلسات">
            <div class="layout">
                <div class="rounded-4xl bg-forest-2 py-8 lg:py-12">
                    <ul class="grid grid-cols-2 text-center text-white lg:grid-cols-4">
                        <li class="border-b border-e border-white/20 px-4 py-6 lg:border-b-0 lg:py-2">
                            <div class="text-3xl font-extrabold text-gold sm:text-4xl">+۱۰</div>
                            <p class="mt-2 text-sm text-white/70">سال تجربه در حوزه سلامت روان</p>
                        </li>
                        <li class="border-b border-white/20 px-4 py-6 lg:border-b-0 lg:border-e lg:py-2">
                            <div class="text-3xl font-extrabold text-gold sm:text-4xl">+۱۵۰۰</div>
                            <p class="mt-2 text-sm text-white/70">جلسه مشاوره برگزار شده</p>
                        </li>
                        <li class="border-e border-white/20 px-4 py-6 lg:py-2">
                            <div class="text-3xl font-extrabold text-gold sm:text-4xl">+۷</div>
                            <p class="mt-2 text-sm text-white/70">حوزه تخصصی درمان</p>
                        </li>
                        <li class="px-4 py-6 lg:py-2">
                            <div class="text-3xl font-extrabold text-gold sm:text-4xl">۹۵٪</div>
                            <p class="mt-2 text-sm text-white/70">رضایت مراجعان از روند درمان</p>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
        <section class="bg-cream pt-10" aria-labelledby="process-title" id="process">
            <div class="layout">
                <header class="mb-8 text-right">
                    <h2 id="process-title" class="mb-4 text-[42px] font-bold text-forest-2 font-[IRANSansX]">فرایند شروع جلسات</h2>
                    <div class="mb-6 mr-0 ml-auto h-1.5 w-16 rounded-full bg-forest-2"></div>
                    <p class="max-w-2xl text-[15px] leading-relaxed text-muted font-[IRANSansX]">
                        شروع مسیر درمان می‌تواند ساده و آرام باشد<br>
                        ما با شما هستیم تا با اطمینان و در فضایی امن، اولین قدم را بردارید
                    </p>
                </header>

                <div class="relative">
                    <div class="absolute top-[1.15rem] right-[12.5%] left-[12.5%] z-0 hidden h-[2px] bg-gold xl:block" aria-hidden="true"></div>

                    <ol class="relative z-10 grid grid-cols-1 gap-6 md:grid-cols-2 md:gap-8 xl:grid-cols-4">
                        <li class="flex h-full flex-col items-center">
                            <div class="relative z-10 mb-[-1.25rem] flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gold font-bold text-white shadow-sm">۱</div>
                            <article class="flex h-full w-full flex-col items-center rounded-[30px] border border-white/50 bg-white px-6 pb-8 pt-12 text-center shadow-sm">
                                <div class="mb-6 flex h-[120px] w-[120px] shrink-0 items-center justify-center" aria-hidden="true">
                                    <img src="{{ asset('website/images/28e44207-019c-4fc5-8b9f-.png') }}" alt="" width="120" height="120" class="h-full w-full object-contain">
                                </div>
                                <h3 class="mb-4 text-[18px] font-bold text-ink">نوع مشاوره را انتخاب کنید</h3>
                                <p class="text-[14px] leading-7 text-muted">مشاوره حضوری در تبریز یا برگزاری جلسات به صورت آنلاین، بر اساس نیاز و شرایط خود انتخاب کنید.</p>
                            </article>
                        </li>

                        <li class="flex h-full flex-col items-center">
                            <div class="relative z-10 mb-[-1.25rem] flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gold font-bold text-white shadow-sm">۲</div>
                            <article class="flex h-full w-full flex-col items-center rounded-[30px] border border-white/50 bg-white px-6 pb-8 pt-12 text-center shadow-sm">
                                <div class="mb-6 flex h-[120px] w-[120px] shrink-0 items-center justify-center" aria-hidden="true">
                                    <img src="{{ asset('website/images/10a303d7-0f72-49b9-bfbf-.png') }}" alt="" width="120" height="120" class="h-full w-full object-contain">
                                </div>
                                <h3 class="mb-4 text-[18px] font-bold text-ink">درخواست اولیه را ثبت کنید</h3>
                                <p class="text-[14px] leading-7 text-muted">فرم کوتاه درخواست نوبت را تکمیل کنید تا اطلاعات اولیه برای هماهنگی جلسه ثبت شود.</p>
                            </article>
                        </li>

                        <li class="flex h-full flex-col items-center">
                            <div class="relative z-10 mb-[-1.25rem] flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gold font-bold text-white shadow-sm">۳</div>
                            <article class="flex h-full w-full flex-col items-center rounded-[30px] border border-white/50 bg-white px-6 pb-8 pt-12 text-center shadow-sm">
                                <div class="mb-6 flex h-[120px] w-[120px] shrink-0 items-center justify-center" aria-hidden="true">
                                    <img src="{{ asset('website/images/45584d31-1882-4fe3-9fb.png') }}" alt="" width="120" height="120" class="h-full w-full object-contain">
                                </div>
                                <h3 class="mb-4 text-[18px] font-bold text-ink">زمان مناسب را هماهنگ کنید</h3>
                                <p class="text-[14px] leading-7 text-muted">پس از بررسی درخواست شما، با کمترین تأخیر درباره زمان مناسب جلسه با شما تماس گرفته می‌شود.</p>
                            </article>
                        </li>

                        <li class="flex h-full flex-col items-center">
                            <div class="relative z-10 mb-[-1.25rem] flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gold font-bold text-white shadow-sm">۴</div>
                            <article class="flex h-full w-full flex-col items-center rounded-[30px] border border-white/50 bg-white px-6 pb-8 pt-12 text-center shadow-sm">
                                <div class="mb-6 flex h-[120px] w-[120px] shrink-0 items-center justify-center" aria-hidden="true">
                                    <img src="{{ asset('website/images/f82b6797-59b5-4de6-ba8a-05da0-Recovered.png') }}" alt="" width="130" height="120" class="h-full w-full object-contain">
                                </div>
                                <h3 class="mb-4 text-[18px] font-bold text-ink">جلسه را شروع کنید</h3>
                                <p class="text-[14px] leading-7 text-muted">در فضایی امن و محترمانه، از همان جایی شروع می‌کنیم که اکنون قرار دارید؛ بدون عجله و با توجه به شرایط شما.</p>
                            </article>
                        </li>
                    </ol>
                </div>

                <div class="mt-10 flex flex-col items-center">
                    <div class="mb-8 flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-forest-2/10 text-forest-2" aria-hidden="true">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </div>
                        <p class="text-[16px] text-muted">اطلاعات شما کاملا محرمانه باقی می‌ماند و فقط برای هماهنگی و شروع روند مشاوره استفاده می‌شود.</p>
                    </div>

                    <div class="mb-8 flex w-full items-center justify-end gap-4">
                        <a href="#contact" class="flex items-center gap-3 rounded-xl bg-forest-2 px-10 py-4 font-bold text-white transition-all hover:bg-forest-1">
                            درخواست نوبت
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="rotate-180" aria-hidden="true">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>


        <section id="client-testimonials" dir="rtl" class="overflow-hidden bg-white py-16 md:py-20 lg:py-[96px]" aria-labelledby="testimonials-title">
            <div class="layout">
                <div class="grid grid-cols-1 items-center gap-8
                    md:grid-cols-[minmax(0,1fr)_auto] md:items-end md:gap-x-8 md:gap-y-10
                    lg:grid-cols-[minmax(280px,0.9fr)_minmax(0,1.58fr)] lg:grid-rows-[auto_auto] lg:items-center lg:gap-x-16 lg:gap-y-14">

                    <div class="w-full md:col-start-1 md:row-start-1 lg:col-start-1 lg:row-start-1">
                        <div class="max-w-[36rem] md:max-w-none lg:max-w-[450px] lg:mr-auto">
                            <h2 id="testimonials-title" class="font-bold font-[IRANSansX] text-[30px] leading-[1.45] text-forest-2 sm:text-[36px] lg:leading-[1.35]">
                                تجربه مراجعان از                          <br class="hidden sm:block">
                                جلسات مشاوره
                            </h2>

                            <p class="mt-6 max-w-[42rem] text-[16px] font-[IRANSansX] leading-[2] text-forest-2/90 sm:mt-8 sm:text-[14px] md:mt-7 md:max-w-[34rem] md:text-[14px] md:leading-[2] lg:mt-9 lg:max-w-none">
                                هر فرد با مسئله و شرایط متفاوتی وارد جلسات می‌شود. روایت‌های زیر، نمونه‌هایی کوتاه از تجربه مراجعانی است که با حفظ حریم خصوصی و رضایت آن‌ها منتشر شده‌اند.
                            </p>
                        </div>
                    </div>

                    <div class="min-w-0 w-full md:col-span-2 md:row-start-2 lg:col-span-1 lg:col-start-2 lg:row-span-2 lg:row-start-1">
                        <div id="testimonials-viewport" class="touch-pan-y cursor-grab overflow-hidden active:cursor-grabbing" aria-live="polite">
                            <div id="testimonials-track" class="flex items-stretch gap-0 transition-transform duration-700 ease-[cubic-bezier(.22,.61,.36,1)] lg:gap-8">
                                <article class="testimonial-card flex min-h-[300px] w-full min-w-0 shrink-0 flex-col rounded-[28px] bg-forest-2 px-6 py-8 text-white shadow-[0_18px_40px_rgba(0,0,0,0.10)] sm:min-h-[320px] sm:rounded-[36px] sm:px-8 sm:py-10 lg:min-h-0 lg:w-[430px] lg:rounded-[42px] lg:px-9 lg:py-11">
                                    <p class="text-[15px] leading-[2.1] text-white/95 sm:text-[17px] sm:leading-[2.15]">
                                        مدتی بود خسته و بی‌حوصله بودم، اما نمی‌فهمیدم دلیلش چیست. زود عصبی می‌شدم، از دیگران فاصله می‌گرفتم و شب‌ها سخت می‌خوابیدم. در جلسات متوجه شدم چقدر احساساتم را نادیده می‌گیرم. حالا زودتر متوجه فشارها می‌شوم و بهتر به خودم رسیدگی می‌کنم.
                                    </p>
                                    <p class="mt-8 text-[15px] text-white/95 sm:mt-14 sm:text-[16px]">
                                        <span>-</span>
                                        <span class="mr-2">م. محمدی، ۳۸ سال</span>
                                    </p>
                                </article>

                                <article class="testimonial-card flex min-h-[300px] w-full min-w-0 shrink-0 flex-col rounded-[28px] bg-gold px-6 py-8 text-forest-2 shadow-[0_18px_40px_rgba(0,0,0,0.08)] sm:min-h-[320px] sm:rounded-[36px] sm:px-8 sm:py-10 lg:min-h-0 lg:w-[430px] lg:rounded-[42px] lg:px-9 lg:py-11">
                                    <p class="text-[15px] leading-[2.1] sm:text-[17px] sm:leading-[2.15]">
                                        مدتی بود خسته بودم و احساس می‌کردم کنترل خیلی از
                                        موقعیت‌های روزمره از دستم خارج شده است. در جلسات یاد
                                        گرفتم با آرامش بیشتری به احساساتم نگاه کنم و قدم‌به‌قدم
                                        شرایط بهتری برای خودم بسازم.
                                    </p>
                                    <p class="mt-8 text-[15px] sm:mt-14 sm:text-[16px]">
                                        <span>-</span>
                                        <span class="mr-2">س. رستگار، ۳۱ سال</span>
                                    </p>
                                </article>

                                <article class="testimonial-card flex min-h-[300px] w-full min-w-0 shrink-0 flex-col rounded-[28px] bg-cream px-6 py-8 text-forest-2 shadow-[0_18px_40px_rgba(0,0,0,0.08)] sm:min-h-[320px] sm:rounded-[36px] sm:px-8 sm:py-10 lg:min-h-0 lg:w-[430px] lg:rounded-[42px] lg:px-9 lg:py-11">
                                    <p class="text-[15px] leading-[2.1] sm:text-[17px] sm:leading-[2.15]">
                                        اضطراب و استرس شغلی باعث شده بود بیشتر روزها احساس کنم از پس کارها برنمی‌آیم. جلسات کمک کرد فشارهایی را که تحمل می‌کردم بهتر بشناسم و راه‌های مؤثرتری برای مدیریتشان پیدا کنم. حالا سلامت روانم را جدی‌تر دنبال می‌کنم.
                                    </p>
                                    <p class="mt-8 text-[15px] sm:mt-14 sm:text-[16px]">
                                        <span>-</span>
                                        <span class="mr-2">ن. کریمی، ۲۹ سال</span>
                                    </p>
                                </article>

                                <article class="testimonial-card flex min-h-[300px] w-full min-w-0 shrink-0 flex-col rounded-[28px] bg-forest-2 px-6 py-8 text-white shadow-[0_18px_40px_rgba(0,0,0,0.10)] sm:min-h-[320px] sm:rounded-[36px] sm:px-8 sm:py-10 lg:min-h-0 lg:w-[430px] lg:rounded-[42px] lg:px-9 lg:py-11">
                                    <p class="text-[15px] leading-[2.1] text-white/95 sm:text-[17px] sm:leading-[2.15]">
                                        مدت زیادی بود خواب منظمی نداشتم و ذهنم همیشه درگیر بود.
                                        صحبت کردن درباره این موضوع‌ها کمک کرد ریشه‌ی نگرانی‌هایم
                                        را بهتر ببینم و برای روزها و شب‌هایم نظم بیشتری ایجاد کنم.
                                    </p>
                                    <p class="mt-8 text-[15px] text-white/95 sm:mt-14 sm:text-[16px]">
                                        <span>-</span>
                                        <span class="mr-2">الف. احمدی، ۳۵ سال</span>
                                    </p>
                                </article>

                                <article class="testimonial-card flex min-h-[300px] w-full min-w-0 shrink-0 flex-col rounded-[28px] bg-gold px-6 py-8 text-forest-2 shadow-[0_18px_40px_rgba(0,0,0,0.08)] sm:min-h-[320px] sm:rounded-[36px] sm:px-8 sm:py-10 lg:min-h-0 lg:w-[430px] lg:rounded-[42px] lg:px-9 lg:py-11">
                                    <p class="text-[15px] leading-[2.1] sm:text-[17px] sm:leading-[2.15]">
                                        همیشه در گفت‌وگو با آدم‌های نزدیکم از بیان خواسته‌هایم
                                        فرار می‌کردم. حالا بهتر می‌توانم احساساتم را توضیح بدهم
                                        و بدون دعوا و درگیری درباره چیزهایی که برایم مهم هستند صحبت کنم.
                                    </p>
                                    <p class="mt-8 text-[15px] sm:mt-14 sm:text-[16px]">
                                        <span>-</span>
                                        <span class="mr-2">ر. موسوی، ۴۱ سال</span>
                                    </p>
                                </article>

                                <article class="testimonial-card flex min-h-[300px] w-full min-w-0 shrink-0 flex-col rounded-[28px] bg-cream px-6 py-8 text-forest-2 shadow-[0_18px_40px_rgba(0,0,0,0.08)] sm:min-h-[320px] sm:rounded-[36px] sm:px-8 sm:py-10 lg:min-h-0 lg:w-[430px] lg:rounded-[42px] lg:px-9 lg:py-11">
                                    <p class="text-[15px] leading-[2.1] sm:text-[17px] sm:leading-[2.15]">
                                        جلسات مشاوره با کریم محمدزاده برایم فضایی بود که می‌توانستم بدون ترس از قضاوت
                                        درباره تجربه‌هایم صحبت کنم. همین احساس امنیت باعث شد
                                        چیزهایی را ببینم که مدت‌ها از آن‌ها دوری می‌کردم.
                                    </p>
                                    <p class="mt-8 text-[15px] sm:mt-14 sm:text-[16px]">
                                        <span>-</span>
                                        <span class="mr-2">م. نادری، ۲۷ سال</span>
                                    </p>
                                </article>

                                <article class="testimonial-card flex min-h-[300px] w-full min-w-0 shrink-0 flex-col rounded-[28px] bg-forest-2 px-6 py-8 text-white shadow-[0_18px_40px_rgba(0,0,0,0.10)] sm:min-h-[320px] sm:rounded-[36px] sm:px-8 sm:py-10 lg:min-h-0 lg:w-[430px] lg:rounded-[42px] lg:px-9 lg:py-11">
                                    <p class="text-[15px] leading-[2.1] text-white/95 sm:text-[17px] sm:leading-[2.15]">
                                        چیزی که برایم ارزشمند بود، این بود که در روند درمان
                                        احساس عجله یا فشار نداشتم. با سرعت خودم پیش رفتم و
                                        کم‌کم توانستم ارتباط آرام‌تری با خودم داشته باشم.
                                    </p>
                                    <p class="mt-8 text-[15px] text-white/95 sm:mt-14 sm:text-[16px]">
                                        <span>-</span>
                                        <span class="mr-2">ک. یوسفی، ۳۳ سال</span>
                                    </p>
                                </article>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-row-reverse items-center justify-center gap-5
                        md:col-start-2 md:row-start-1 md:justify-end md:pb-1
                        lg:col-start-1 lg:row-start-2 lg:justify-end lg:pb-0">
                        <button
                            id="testimonials-prev"
                            type="button"
                            class="flex h-11 w-11 items-center justify-center text-forest-2 transition hover:opacity-60 disabled:cursor-not-allowed disabled:opacity-30"
                            aria-label="نمایش نظر قبلی"
                        >
                            <svg viewBox="0 0 24 24" fill="none" class="h-8 w-8" aria-hidden="true">
                                <path d="M15 6 9 12l6 6" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>

                        <button
                            id="testimonials-next"
                            type="button"
                            class="flex h-14 w-14 items-center justify-center rounded-full bg-forest-2 text-white shadow-[0_10px_24px_rgba(34,75,56,0.25)] transition hover:bg-forest-1 disabled:cursor-not-allowed disabled:opacity-40 lg:h-[60px] lg:w-[60px]"
                            aria-label="نمایش نظر بعدی"
                        >
                            <svg viewBox="0 0 24 24" fill="none" class="h-8 w-8" aria-hidden="true">
                                <path d="m9 6 6 6-6 6" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-10" aria-labelledby="faq-title">
            <div class="layout">
                <div class="flex flex-col gap-10 lg:flex-row lg:items-center">
                    <div class="lg:w-[52%]">
                        <h2 id="faq-title" class="max-w-md text-[22px] font-medium leading-9 text-ink lg:text-[26px]">
                            گاهی آرامش از تغییر شرایط شروع نمی‌شود؛ از فهمیدن خودمان شروع می‌شود.
                        </h2>
                    </div>
                    <div class="space-y-4 lg:w-[48%]">
                        <article class="faq-item is-open rounded-2xl border border-gold/40 bg-cream">
                            <button type="button" class="faq-toggle flex w-full items-start gap-3 px-4 py-3 text-right" aria-expanded="true">
                                <span class="faq-icon mt-0.5 text-lg leading-none text-ink" aria-hidden="true">−</span>
                                <span class="text-[14px] font-medium leading-6 text-ink">
                چرا با وجود اینکه می‌دانم چه کاری درست است، باز به الگوهای قبلی برمی‌گردم؟
            </span>
                            </button>
                            <div class="faq-answer">
                                <div>
                                    <p class="px-4 pb-3 pr-10 text-[13px] leading-6 text-muted">
                                        چون تغییر فقط به دانستن وابسته نیست. تجربه‌های گذشته، ترس‌ها، نیازهای برآورده‌نشده و عادت‌های ذهنی می‌توانند ما را به سمت واکنش‌های آشنا برگردانند. در جلسات، این الگوها را با دقت بررسی می‌کنیم تا امکان انتخاب‌های تازه‌تر و آگاهانه‌تر فراهم شود.
                                    </p>
                                </div>
                            </div>
                        </article>

                        <article class="faq-item rounded-2xl border border-gold/40 bg-cream">
                            <button type="button" class="faq-toggle flex w-full items-start gap-3 px-4 py-3 text-right" aria-expanded="false">
                                <span class="faq-icon mt-0.5 text-lg leading-none text-ink" aria-hidden="true">+</span>
                                <span class="text-[14px] font-medium leading-6 text-ink">
                وقتی ذهنم آرام نمی‌گیرد، تراپی چطور می‌تواند کمکم کند؟
            </span>
                            </button>
                            <div class="faq-answer">
                                <div>
                                    <p class="px-4 pb-3 pr-10 text-[13px] leading-6 text-muted">
                                        جلسات تراپی فرصتی امن برای مکث‌کردن و مرتب‌کردن افکار و احساسات فراهم می‌کنند. با شناختن ریشه‌های نگرانی، فشار روانی یا بی‌قراری، می‌توان به‌تدریج راه‌های مؤثرتری برای مواجهه با آن‌ها پیدا کرد.
                                    </p>
                                </div>
                            </div>
                        </article>

                        <article class="faq-item rounded-2xl border border-gold/40 bg-cream">
                            <button type="button" class="faq-toggle flex w-full items-start gap-3 px-4 py-3 text-right" aria-expanded="false">
                                <span class="faq-icon mt-0.5 text-lg leading-none text-ink" aria-hidden="true">+</span>
                                <span class="text-[14px] font-medium leading-6 text-ink">
                آیا جلسات مشاوره قرار است مرا تغییر دهد؟
            </span>
                            </button>
                            <div class="faq-answer">
                                <div>
                                    <p class="px-4 pb-3 pr-10 text-[13px] leading-6 text-muted">
                                        روان‌درمانی قرار نیست شما را به فرد دیگری تبدیل کند یا تصمیم‌هایتان را به جای شما بگیرد. هدف، شناخت عمیق‌تر خود، احساسات و الگوهای رفتاری‌تان است. این شناخت می‌تواند به شما کمک کند با آزادی و آگاهی بیشتری مسیر تغییرات موردنظرتان را انتخاب کنید.
                                    </p>
                                </div>
                            </div>
                        </article>

                        <article class="faq-item rounded-2xl border border-gold/40 bg-cream">
                            <button type="button" class="faq-toggle flex w-full items-start gap-3 px-4 py-3 text-right" aria-expanded="false">
                                <span class="faq-icon mt-0.5 text-lg leading-none text-ink" aria-hidden="true">+</span>
                                <span class="text-[14px] font-medium leading-6 text-ink">
                آیا برای شروع تراپی باید حالم خیلی بد باشد؟
            </span>
                            </button>
                            <div class="faq-answer">
                                <div>
                                    <p class="px-4 pb-3 pr-10 text-[13px] leading-6 text-muted">
                                        خیر. لازم نیست حتماً در بحران باشید تا از روان‌درمانی کمک بگیرید. گاهی فرد برای شناخت بهتر خود، بهبود رابطه‌ها، مدیریت اضطراب یا روبه‌روشدن با یک تصمیم مهم به درمان مراجعه می‌کند. شروع درمان می‌تواند فرصتی برای رسیدگی به خود، پیش از شدیدترشدن فشارها باشد.
                                    </p>
                                </div>
                            </div>
                        </article>

                        <div class="pt-4 text-left">
                            <a href="#" class="inline-flex items-center gap-2 rounded-xl bg-forest-2 px-5 py-4 text-[13px] font-medium text-white transition-all hover:bg-forest-1">
                                مشاهده پرسش‌های بیشتر
                                <span aria-hidden="true">←</span>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <section class="py-20" aria-label="Contact call to action" id="contact">
            <div class="layout">
                <div class="cta-panel">
                    <div class="rings" aria-hidden="true">
                    </div>

                    <div class="content-grid">
                        <div class="content">
                            <h2 class="m-0 text-center font-bold text-white">
                                می‌توانید درباره‌اش صحبت کنید!
                            </h2>

                            <p class="text-center font-light text-white/80">
                                برای شروع مشاوره، اطلاعاتتان را وارد کنید تا درباره زمان مناسب جلسه با شما تماس بگیریم.
                            </p>

                            <div class="appointment-form">
                                <p class="text-white">برای انتخاب زمان جلسه، وارد بخش رزرو نوبت شوید.</p>
                                <a href="{{ route('booking.index') }}" class="cta-appointment">
                                    <span class="cta-appointment__text">مشاهده زمان‌ها و رزرو نوبت</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection

@push('scripts')
    <script src="{{ asset('website/js/home.js') }}" defer></script>
@endpush
