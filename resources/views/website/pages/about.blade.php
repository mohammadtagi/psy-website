@extends('website.layouts.app')

@section('title', 'درباره کریم محمدزاده | روان‌شناس و روان‌درمانگر')
@section('description', 'با مسیر حرفه‌ای، رویکرد درمانی و تجربه‌های کریم محمدزاده در زمینه روان‌درمانی و مشاوره فردی آشنا شوید.')
@section('canonical', route('website.about'))
@section('og_type', 'profile')
@section('social_image_alt', 'کریم محمدزاده، روان‌شناس و روان‌درمانگر')

@push('styles')
    <link rel="stylesheet" href="{{ asset('website/style/about.css') }}">
@endpush

@section('content')
    <div class="website-about">
        <!-- 1. Header and About -->
        <section class="min-h-screen hero">
            <div class="layout">
                @include('website.partials.header')
            </div>

            <div class="hero-content">
                <nav aria-label="مسیر صفحه" class="mb-6 text-sm text-white/80">
                    <ol class="flex items-center justify-center gap-3">
                        <li><a href="{{ route('home') }}" class="hover:text-white">خانه</a></li>
                        <li aria-hidden="true">/</li>
                        <li aria-current="page">درباره ما</li>
                    </ol>
                </nav>
                <h1 class="text-5xl font-bold tracking-tight">
                    درباره من
                </h1>

                <p class="mt-6 max-w-xl text-sm leading-8 text-white/90">
                    در این صفحه می‌توانید با مسیر کاری، رویکرد درمانی و تجربه‌های کاری من
                    در زمینه روان‌درمانی و مشاوره بیشتر آشنا شوید.
                </p>
            </div>
        </section>
        <section id="service" class="section bg-white">
            <div class="page-grid grid grid-cols-1 items-start text-end lg:grid-cols-12" dir="ltr">

                <!--
                  لیست:
                  موبایل و تبلت: بعد از بخش تغییر پایدار
                  دسکتاپ: ستون سمت چپ
                -->
                <div class="about-list order-2 mt-12 col-span-1 lg:order-1 lg:col-span-5 lg:col-start-1 lg:mt-0">

                    <div class="mb-10 rounded-xl bg-[#f4eddf] px-8 py-4 text-center text-lg font-medium">
                        چراپویش و سیرهای درمان
                    </div>

                    <div class="space-y-10">
                        <article>
                            <h3 class="text-lg font-semibold">
                                <span class="ml-3 text-[#006b4f]">۱</span>
                                کمک به بهبود و تثبیت سلامت روان
                            </h3>

                            <p class="body-text mt-3">
                                در جلسات درمان تلاش می‌کنیم شناخت دقیق‌تری از شرایط ایجاد شود و
                                مسیر بهبود با توجه و همراهی دنبال شود.
                            </p>
                        </article>

                        <article>
                            <h3 class="text-lg font-semibold">
                                <span class="ml-3 text-[#006b4f]">۲</span>
                                قدرتمندتر شدن، نه تسلیم‌شدن از بیرون
                            </h3>

                            <p class="body-text mt-3">
                                هدف درمان رسیدن به درک بهتر از خود، احساسات و انتخاب‌هایی است که
                                به زندگی معنادارتر و آرام‌تر کمک می‌کنند.
                            </p>
                        </article>

                        <article>
                            <h3 class="text-lg font-semibold">
                                <span class="ml-3 text-[#006b4f]">۳</span>
                                حمایت از رشد فردی، فراتر از یک مشکل
                            </h3>

                            <p class="body-text mt-3">
                                درمان فقط برای حل یک مشکل نیست؛ فرصتی است برای شناخت توانمندی‌ها،
                                ساختن رابطه‌ای سالم‌تر با خود و حرکت در مسیر رشد.
                            </p>
                        </article>
                    </div>
                </div>

                <!--
                  تغییر پایدار:
                  موبایل و تبلت: اولین بخش
                  دسکتاپ: ستون سمت راست
                -->
                <div class="about-content order-1 col-span-1 lg:order-2 lg:col-span-6 lg:col-start-7">
                    <h2 class="section-title">
                        تغییر پایدار، با مراقبت و توجه در روند درمان
                    </h2>

                    <!-- در تمام سایزها: متن و تصویر کنار هم -->
                    <div class="about-card mt-10 grid grid-cols-5 overflow-hidden rounded-2xl border border-[#e7e7e7] lg:mt-20">

                        <!-- متن: ۳ ستون از ۵ ستون -->
                        <div class="about-card-content col-span-3 p-5 sm:p-8">
                            <h3 class="text-base font-medium sm:text-lg">
                                چند خط برای شروع آشنایی
                            </h3>

                            <p class="body-text mt-4 sm:mt-5">
                                مسیر درمان با شناخت و گفت‌وگو آغاز می‌شود. در این مسیر تلاش می‌کنیم
                                تجربه‌های شما با دقت شنیده و بررسی شود.
                            </p>

                            <p class="mt-4 text-sm font-medium text-[#006b4f] sm:mt-5">
                                کریم محمدزاده<br>
                                روان‌شناس و روان‌درمانگر
                            </p>
                        </div>

                        <!-- عکس: ۲ ستون از ۵ ستون، هرگز تمام‌عرض نیست -->
                        <div class="about-card-image placeholder col-span-2 min-h-[220px] sm:min-h-[270px]">
                            <img
                                src="https://picsum.photos/seed/therapy-room/600/700"
                                alt=""
                                class="h-full w-full object-cover opacity-0"
                            >
                        </div>
                    </div>
                </div>

                <!-- لوگوها -->
                <div class="order-3 col-span-1 mt-12 lg:col-span-12 lg:mt-20">
                    <div class="grid grid-cols-2 items-center gap-8 text-center text-2xl text-[#999] sm:grid-cols-3 lg:grid-cols-5 lg:gap-12 lg:text-3xl">
                        <span>ULTRA<br><small class="text-xs">QUALITY</small></span>
                        <span>QC<br><small class="text-xs">POWER XRC MODULE</small></span>
                        <span>◌<br><small class="text-xs">CERTIFIED</small></span>
                        <span>◎<br><small class="text-xs">STANDARD</small></span>
                        <span class="text-2xl">▰▰▰<br><small class="text-xs">loqisum</small></span>
                    </div>
                </div>

            </div>
        </section>

        <!-- 3. Professional Journey -->
        <section class="section bg-white pt-10">
            <div class="page-grid">
                <div class="col-span-6 col-start-7">
                    <h2 class="section-title">
                        روایت مسیر حرفه‌ای
                    </h2>
                </div>

                <div class="timeline-card col-span-12 mt-12 rounded-[28px] bg-[#f4eddf] px-12 py-8">
                    <div class="space-y-0">

                        <article class="timeline-row grid grid-cols-[100px_1fr_55px] items-start gap-8 border-b border-[#e1d9ca] py-8">
                            <div class="text-2xl font-medium">۱۳۹۶</div>
                            <div>
                                <h3 class="text-2xl font-medium">شروع مسیر حرفه‌ای</h3>
                                <p class="body-text mt-4">
                                    از سال ۱۳۹۶ مسیر حرفه‌ای من در حوزه سلامت روان شروع شد و با
                                    یادگیری، تجربه و همراهی مراجعان ادامه پیدا کرد.
                                </p>
                            </div>
                            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-[#006b4f] text-white">
                                ▶
                            </div>
                        </article>

                        <article class="timeline-row grid grid-cols-[100px_1fr_55px] items-start gap-8 border-b border-[#e1d9ca] py-8">
                            <div class="text-2xl font-medium">۱۴۰۱</div>
                            <div>
                                <h3 class="text-2xl font-medium">گسترش حوزه‌های کاری و عمق جلسات</h3>
                                <p class="body-text mt-4">
                                    با افزایش تجربه و دانش، تمرکز بیشتری بر فرآیند درمان و ایجاد
                                    رابطه‌ای امن، روشن و قابل اعتماد شکل گرفت.
                                </p>
                            </div>
                            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-[#006b4f] text-white">
                                ▶
                            </div>
                        </article>

                        <article class="timeline-row grid grid-cols-[100px_1fr_55px] items-start gap-8 py-8">
                            <div class="text-2xl font-medium">۱۴۰۳</div>
                            <div>
                                <h3 class="text-2xl font-medium">افزودن امکان جلسات آنلاین</h3>
                                <p class="body-text mt-4">
                                    با فراهم شدن امکان برگزاری جلسات آنلاین، دسترسی به فرآیند درمان
                                    برای افراد بیشتری امکان‌پذیر شد.
                                </p>
                            </div>
                            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-[#006b4f] text-white">
                                ▶
                            </div>
                        </article>

                    </div>
                </div>
            </div>
        </section>

        <!-- 4. Trust Indicators -->
        <!-- اعتمادسنجی در نوار نشان‌های بخش دوم پیاده‌سازی شده است -->

        <!-- 5. Statistics -->
        <section class="section bg-[#183f30] text-white">
            <div class="page-grid items-center">

                <div class="stats-content col-span-5 col-start-1">

                    <h2 class="text-center text-4xl font-medium leading-[1.7]">
                        مرور آماری بر اساس<br>
                        اعداد و ارقام
                    </h2>

                    <p class="mt-8 text-center text-sm leading-8 text-white/70">
                        این اعداد، بخشی از تجربه و همراهی با مراجعان در مسیر درمان را نشان
                        می‌دهند؛ اما هر مسیر، داستان و ویژگی‌های منحصر به خود را دارد.
                    </p>

                    <div class="stats-grid mt-16 grid grid-cols-2 gap-x-16 gap-y-12 text-center">
                        <div>
                            <div class="text-4xl font-light">+۱۲۰۰</div>
                            <div class="mt-2 text-sm text-white/70">جلسات برگزارشده</div>
                        </div>

                        <div>
                            <div class="text-4xl font-light">+۱۵۰۰</div>
                            <div class="mt-2 text-sm text-white/70">مراجعان همراه</div>
                        </div>

                        <div>
                            <div class="text-4xl font-light">+۱۰</div>
                            <div class="mt-2 text-sm text-white/70">سال‌های فعالیت حرفه‌ای</div>
                        </div>

                        <div>
                            <div class="text-4xl font-light">+۲۰۰</div>
                            <div class="mt-2 text-sm text-white/70">کارگاه‌ها و نشست‌ها</div>
                        </div>
                    </div>
                </div>

                <div class="stats-image col-span-5 col-start-8">
                    <div class="relative aspect-square overflow-hidden rounded-[24px] bg-[#999]">

                        <img
                            src="https://picsum.photos/seed/abstract-green/900/900"
                            alt=""
                            class="h-full w-full object-cover opacity-0"
                        >

                        <div class="absolute -bottom-20 -left-20 h-72 w-72 rounded-full bg-[#c0d49a]/70"></div>
                        <div class="absolute -bottom-10 -left-10 h-56 w-56 rounded-full bg-[#b7d184]/75"></div>
                    </div>
                </div>

            </div>
        </section>

        <!-- 6. Session Process -->
        <section class="section bg-white">
            <div class="page-grid items-start">

                <div class="process-intro col-span-5 col-start-1">
                    <h2 class="text-center text-4xl font-medium leading-[1.7]">
                        روال اجرایی در جلسات<br>
                        مرحله به مرحله
                    </h2>


                    <p class="body-text mt-7 text-center">
                        جلسات با آشنایی اولیه آغاز می‌شوند و با توجه به مسئله، اهداف و نیازها
                        پیش می‌روند. نظم، پیوستگی و همکاری در مسیر درمان اهمیت زیادی دارد.
                    </p>
                </div>

                <div class="process-image col-span-5 col-start-8">
                    <div class="placeholder aspect-[1.7] overflow-hidden rounded-2xl">

                        <img
                            src="https://picsum.photos/seed/consultation/1000/600"
                            alt=""
                            class="h-full w-full object-cover opacity-0"
                        >
                    </div>
                </div>

                <div class="process-grid col-span-12 mt-24 grid grid-cols-[1fr_70px_1fr_70px_1fr_70px_1fr] items-start gap-4 text-center">

                    <div>
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-[#006b4f] text-white">
                            ✦
                        </div>
                        <div class="mt-5 text-sm font-medium">مرحله ۱</div>
                        <div class="mt-2 text-sm">گفت‌وگوی اولیه و ارزیابی</div>
                    </div>

                    <div class="process-arrow pt-3">←</div>

                    <div>
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-[#006b4f] text-white">
                            ✦
                        </div>
                        <div class="mt-5 text-sm font-medium">مرحله ۲</div>
                        <div class="mt-2 text-sm">طراحی مسیر درمان</div>
                    </div>

                    <div class="process-arrow pt-3">←</div>

                    <div>
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-[#006b4f] text-white">
                            ✦
                        </div>
                        <div class="mt-5 text-sm font-medium">مرحله ۳</div>
                        <div class="mt-2 text-sm">کار درمانی در جلسات</div>
                    </div>

                    <div class="process-arrow pt-3">←</div>

                    <div>
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-[#006b4f] text-white">
                            ✦
                        </div>
                        <div class="mt-5 text-sm font-medium">مرحله ۴</div>
                        <div class="mt-2 text-sm">پیگیری و سنجش پیشرفت</div>
                    </div>

                </div>
            </div>
        </section>
    </div>
@endsection
