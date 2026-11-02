@extends('website.layouts.app')

@section('title', 'خدمات مشاوره و حمایت از سلامت روان | کریم محمدزاده')
@section('description', 'آشنایی با خدمات مشاوره فردی، خانواده، زوج و جلسات آنلاین کریم محمدزاده؛ همراه با توضیح روند جلسات و زمینه‌های تخصصی.')
@section('canonical', route('website.services'))
@section('social_image', asset('website/images/hero-section-bg.webp'))
@section('social_image_alt', 'خدمات مشاوره کریم محمدزاده')

@push('styles')
    <link rel="stylesheet" href="{{ asset('website/style/services.css') }}">
@endpush

@push('head')
    @php
        $servicesSchema = ['@context' => 'https://schema.org',
        '@graph' => [['@type' => 'WebSite',
        '@id' => route('home') . '#website',
        'url' => route('home'),
        'name' => 'کریم محمدزاده',
        'inLanguage' => 'fa-IR'],
        ['@type' => 'WebPage',
        '@id' => route('website.services') . '#webpage',
        'url' => route('website.services'),
        'name' => 'خدمات مشاوره و حمایت از سلامت روان | کریم محمدزاده',
        'description' => 'آشنایی با خدمات مشاوره فردی، خانواده، زوج و جلسات آنلاین کریم محمدزاده؛ همراه با توضیح روند جلسات و زمینه‌های تخصصی.',
        'inLanguage' => 'fa-IR',
        'isPartOf' => ['@id' => route('home') . '#website'],
        'about' => ['@id' => route('website.services')],
        'mainEntity' => ['@id' => route('website.services')],
        'image' => ['@type' => 'ImageObject',
        'url' => asset('website/images/hero-section-bg.webp')]],
        ['@type' => 'Person',
        '@id' => route('home') . '#person',
        'name' => 'کریم محمدزاده',
        'email' => 'mailto:contact@karimmohammadzadeh.ir',
        'telephone' => '+984134244933',
        'jobTitle' => 'مشاور',
        'address' => ['@type' => 'PostalAddress',
        'addressLocality' => 'تبریز',
        'streetAddress' => 'آبرسان، طبقه اول ساختمان بانک سپه'],
        'hasCredential' => ['@type' => 'EducationalOccupationalCredential',
        'credentialCategory' => 'پروانه اشتغال سازمان نظام روان‌شناسی و مشاوره',
        'identifier' => '51308',
        'url' => 'https://my.pcoiran.ir/member/51308/54d371a1-92cb-4157-b65f-efc840f56fcd/']],
        ['@type' => 'ProfessionalService',
        '@id' => route('website.services'),
        'name' => 'خدمات مشاوره و حمایت از سلامت روان کریم محمدزاده',
        'url' => route('website.services'),
        'description' => 'ارائه خدمات مشاوره فردی، مشاوره روابط و زوجین، مشاوره خانواده و جلسات آنلاین.',
        'provider' => ['@id' => route('home') . '#person'],
        'telephone' => '+984134244933',
        'email' => 'mailto:contact@karimmohammadzadeh.ir',
        'address' => ['@type' => 'PostalAddress',
        'addressLocality' => 'تبریز',
        'streetAddress' => 'آبرسان، طبقه اول ساختمان بانک سپه'],
        'openingHoursSpecification' => ['@type' => 'OpeningHoursSpecification',
        'dayOfWeek' => ['Saturday',
        'Sunday',
        'Monday',
        'Tuesday',
        'Wednesday'],
        'opens' => '08:00',
        'closes' => '16:00'],
        'hasOfferCatalog' => ['@type' => 'OfferCatalog',
        'name' => 'خدمات مشاوره',
        'itemListElement' => [['@type' => 'Offer',
        'name' => 'تراپی فردی',
        'url' => route('booking.index'),
        'itemOffered' => ['@type' => 'Service',
        '@id' => route('home') . '#individual-therapy',
        'name' => 'تراپی فردی',
        'serviceType' => 'مشاوره فردی',
        'description' => 'تمرکز بر خودشناسی، تنظیم هیجان و حل الگوهای درونی',
        'provider' => ['@id' => route('home') . '#person']],
        'priceSpecification' => [['@type' => 'UnitPriceSpecification',
        'price' => '700000',
        'unitText' => 'تومان',
        'billingDuration' => 'PT45M',
        'description' => 'جلسه ۴۵ دقیقه‌ای'],
        ['@type' => 'UnitPriceSpecification',
        'price' => '1000000',
        'unitText' => 'تومان',
        'billingDuration' => 'PT60M',
        'description' => 'جلسه ۶۰ دقیقه‌ای']]],
        ['@type' => 'Offer',
        'name' => 'مشاوره روابط و زوج',
        'url' => route('booking.index'),
        'itemOffered' => ['@type' => 'Service',
        '@id' => route('home') . '#couples-counseling',
        'name' => 'مشاوره روابط و زوج',
        'serviceType' => 'مشاوره روابط و زوجین',
        'description' => 'خروج از چرخه‌های تعارض، ترمیم آسیب و بازسازی صمیمیت',
        'provider' => ['@id' => route('home') . '#person']],
        'priceSpecification' => [['@type' => 'UnitPriceSpecification',
        'price' => '700000',
        'unitText' => 'تومان',
        'billingDuration' => 'PT45M',
        'description' => 'جلسه ۴۵ دقیقه‌ای'],
        ['@type' => 'UnitPriceSpecification',
        'price' => '1000000',
        'unitText' => 'تومان',
        'billingDuration' => 'PT60M',
        'description' => 'جلسه ۶۰ دقیقه‌ای']]],
        ['@type' => 'Offer',
        'name' => 'مشاوره خانواده',
        'url' => route('booking.index'),
        'itemOffered' => ['@type' => 'Service',
        '@id' => route('home') . '#family-counseling',
        'name' => 'مشاوره خانواده',
        'serviceType' => 'مشاوره خانواده',
        'description' => 'شفاف‌سازی مرزها، بهبود الگوهای تعاملی و کاهش تنش',
        'provider' => ['@id' => route('home') . '#person']],
        'priceSpecification' => [['@type' => 'UnitPriceSpecification',
        'price' => '700000',
        'unitText' => 'تومان',
        'billingDuration' => 'PT45M',
        'description' => 'جلسه ۴۵ دقیقه‌ای'],
        ['@type' => 'UnitPriceSpecification',
        'price' => '1000000',
        'unitText' => 'تومان',
        'billingDuration' => 'PT60M',
        'description' => 'جلسه ۶۰ دقیقه‌ای']]],
        ['@type' => 'Offer',
        'name' => 'جلسات آنلاین',
        'itemOffered' => ['@type' => 'Service',
        '@id' => route('home') . '#online-sessions',
        'name' => 'جلسات آنلاین',
        'serviceType' => 'جلسات آنلاین',
        'description' => 'امکان برگزاری جلسات درمانی به صورت آنلاین و در محیطی امن و آرام.',
        'provider' => ['@id' => route('home') . '#person']]]]]]]];
    @endphp
    <script type="application/ld+json">{!! json_encode($servicesSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
@endpush

@section('content')
    <div class="website-services">
        <div class="hero-bg min-h-screen text-white">
            <div class="layout">

                @include('website.partials.header')

                <section
                    class="container-layout flex min-h-[555px] items-center justify-center text-center"
                    aria-labelledby="page-title"
                >
                    <div class="max-w-[580px]">
                        <nav aria-label="مسیر صفحه" class="mb-6 text-sm text-white/80">
                            <ol class="flex justify-center items-center gap-3">
                                <li><a href="{{ route('home') }}">خانه</a></li>
                                <li aria-hidden="true">/</li>
                                <li aria-current="page">خدمات</li>
                            </ol>
                        </nav>
                        <h1
                            id="page-title"
                            class="text-4xl leading-[1.4!important] font-bold tracking-tight md:text-5xl"
                        >
                            خدمات مشاوره و حمایت از سلامت روان
                        </h1>

                        <p class="mt-6 text-sm leading-8 text-white/90">
                            در این بخش، چند حوزه‌ی اصلی کاری بنده رو می‌بینید؛ یعنی
                            موضوعاتی که معمولاً در جلسات درمان روی آن‌ها کار می‌کنیم.
                            انتخاب مسیر دقیق، در جلسه‌ی ارزیابی و با توجه به وضعیت هر فرد
                            انجام می‌شود.
                        </p>
                    </div>
                </section>

            </div>
        </div>

        <main>
            <!-- ۲. خدمات جامع برای حمایت از سلامت روان -->
            <section class="bg-white py-16 sm:py-24">
                <div class="container-layout">
                    <div class="mb-12 flex flex-col items-start justify-between gap-6 sm:mb-16 sm:flex-row sm:items-start sm:gap-8">
                        <h2 class="max-w-full text-2xl font-normal leading-[1.8] sm:max-w-[530px] sm:text-3xl md:text-4xl">
                            خدمات جامع برای حمایت از
                            <br class="hidden sm:block"/>
                            سلامت روان
                        </h2>

                        <a
                            href="{{ route('booking.index') }}"
                            class="mt-0 inline-flex shrink-0 whitespace-nowrap rounded-full bg-forest-4 px-6 py-3 text-xs text-white transition-colors duration-300 hover:bg-forest sm:mt-4"
                        >
                            رزرو جلسه آنلاین
                        </a>
                    </div>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        <article
                            class="group min-h-[170px] rounded-[10px] bg-[#F8F8F8] p-7 text-[#171717] transition-all duration-300 ease-out hover:-translate-y-1 hover:bg-forest-4 hover:text-white hover:shadow-[0_10px_24px_rgba(23,79,60,0.16)] focus-within:-translate-y-1 focus-within:bg-forest-4 focus-within:text-white"
                        >
                            <div class="mb-5 flex h-9 w-9 items-center justify-center rounded-full bg-forest text-white transition-colors duration-300 group-hover:bg-[#174F3C] group-focus-within:bg-[#174F3C]">
                                ✦
                            </div>
                            <h3 class="text-lg font-medium">جلسات آنلاین</h3>
                            <p class="mt-4 text-xs leading-7 text-gray-500 transition-colors duration-300 group-hover:text-white/80 group-focus-within:text-white/80">
                                امکان برگزاری جلسات درمانی به صورت آنلاین و در محیطی امن و آرام.
                            </p>
                        </article>

                        <article
                            class="group min-h-[170px] rounded-[10px] bg-[#F8F8F8] p-7 text-[#171717] transition-all duration-300 ease-out hover:-translate-y-1 hover:bg-forest-4 hover:text-white hover:shadow-[0_10px_24px_rgba(23,79,60,0.16)] focus-within:-translate-y-1 focus-within:bg-forest-4 focus-within:text-white"
                        >
                            <div
                                class="mb-5 flex h-9 w-9 items-center justify-center rounded-full bg-forest text-white transition-colors duration-300 group-hover:bg-[#174F3C] group-focus-within:bg-[#174F3C]"
                            >
                                ✦
                            </div>

                            <h3 class="text-lg font-medium">مشاوره خانواده</h3>

                            <p class="mt-4 text-xs leading-7 text-gray-500 transition-colors duration-300 group-hover:text-white/80 group-focus-within:text-white/80">
                                کمک به بهبود ارتباطات خانوادگی و ایجاد رابطه‌ای سالم‌تر.
                            </p>
                        </article>


                        <article
                            class="group min-h-[170px] rounded-[10px] bg-[#F8F8F8] p-7 text-[#171717] transition-all duration-300 ease-out hover:-translate-y-1 hover:bg-forest-4 hover:text-white hover:shadow-[0_10px_24px_rgba(23,79,60,0.16)] focus-within:-translate-y-1 focus-within:bg-forest-4 focus-within:text-white"
                        >
                            <div class="mb-5 flex h-9 w-9 items-center justify-center rounded-full bg-forest text-white transition-colors duration-300 group-hover:bg-[#174F3C] group-focus-within:bg-[#174F3C]">
                                ✦
                            </div>
                            <h3 class="text-lg font-medium">کار روی روابط مهم و زوج</h3>
                            <p class="mt-4 text-xs leading-7 text-gray-500 transition-colors duration-300 group-hover:text-white/80 group-focus-within:text-white/80">
                                شناخت الگوهای ارتباطی و ساختن رابطه‌ای امن، آگاهانه و پایدار.
                            </p>
                        </article>

                        <article
                            class="group min-h-[170px] rounded-[10px] bg-[#F8F8F8] p-7 text-[#171717] transition-all duration-300 ease-out hover:-translate-y-1 hover:bg-forest-4 hover:text-white hover:shadow-[0_10px_24px_rgba(23,79,60,0.16)] focus-within:-translate-y-1 focus-within:bg-forest-4 focus-within:text-white"
                        >
                            <div class="mb-5 flex h-9 w-9 items-center justify-center rounded-full bg-forest text-white transition-colors duration-300 group-hover:bg-[#174F3C] group-focus-within:bg-[#174F3C]">
                                ✦
                            </div>
                            <h3 class="text-lg font-medium">ترمیم فردی</h3>
                            <p class="mt-4 text-xs leading-7 text-gray-500 transition-colors duration-300 group-hover:text-white/80 group-focus-within:text-white/80">
                                همراهی برای شناخت خود، ترمیم زخم‌های گذشته و حرکت به سوی رشد فردی.
                            </p>
                        </article>
                    </div>
                </div>
            </section>
            <!-- ۳. زمینه‌های اصلی کار درمانی -->
            <section class="bg-cream py-24">
                <div class="container-layout">
                    <div class="mb-14 flex items-center justify-between">
                        <h2 class="text-3xl font-normal md:text-4xl">
                            زمینه‌های اصلی کار درمانی
                        </h2>

                        <a
                            href="{{ route('website.contact') }}"
                            class="rounded-full bg-forest px-6 py-3 text-xs text-white"
                        >
                            اطلاعات بیشتر
                        </a>
                    </div>

                    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                        <article class="rounded-[10px] bg-white p-7">
                            <h3 class="text-base font-medium">
                                اضطراب و نگرانی‌های مداوم
                            </h3>
                            <p class="mt-4 text-xs leading-7 text-gray-500">
                                شناخت ریشه‌های اضطراب، مدیریت نگرانی‌ها و پیدا کردن راه‌هایی برای
                                تجربه آرامش و حضور بیشتر در زندگی.
                            </p>
                        </article>

                        <article class="rounded-[10px] bg-white p-7">
                            <h3 class="text-base font-medium">
                                تجربه‌های سخت و ماندگار (تروما)
                            </h3>
                            <p class="mt-4 text-xs leading-7 text-gray-500">
                                بررسی تجربه‌های دشوار گذشته و کمک به بازسازی احساس امنیت و ارتباط
                                دوباره با خود.
                            </p>
                        </article>

                        <article class="rounded-[10px] bg-white p-7">
                            <h3 class="text-base font-medium">
                                افسردگی، بی‌حوصلگی و افت انگیزه
                            </h3>
                            <p class="mt-4 text-xs leading-7 text-gray-500">
                                همراهی در شناخت الگوهای افسردگی و ایجاد گام‌های کوچک اما مؤثر برای
                                بازگشت انرژی و معنا.
                            </p>
                        </article>

                        <article class="rounded-[10px] bg-white p-7">
                            <h3 class="text-base font-medium">
                                اعتمادبه‌نفس، فاصله عاطفی و سردی رابطه
                            </h3>
                            <p class="mt-4 text-xs leading-7 text-gray-500">
                                کار روی تصویر ذهنی از خود، نیازهای عاطفی و الگوهای فاصله گرفتن در
                                روابط.
                            </p>
                        </article>

                        <article class="rounded-[10px] bg-white p-7">
                            <h3 class="text-base font-medium">
                                اختلاف نسل‌ها و تعارض در خانواده
                            </h3>
                            <p class="mt-4 text-xs leading-7 text-gray-500">
                                کمک به گفت‌وگوی سازنده، درک تفاوت‌ها و کاهش تنش‌های تکرارشونده در
                                خانواده.
                            </p>
                        </article>

                        <article class="rounded-[10px] bg-white p-7">
                            <h3 class="text-base font-medium">
                                بحث‌ها و دعواهای تکراری در رابطه عاطفی
                            </h3>
                            <p class="mt-4 text-xs leading-7 text-gray-500">
                                شناسایی چرخه‌های تعارض و تمرین روش‌های سالم‌تر برای گفت‌وگو و حل
                                اختلاف.
                            </p>
                        </article>
                    </div>
                </div>
            </section>

            <!-- ۴. چرا می‌توانید روی این فرایند درمان حساب کنید؟ -->
            <section class="bg-white py-16 md:py-20 lg:py-24">
                <div class="container-layout grid items-center gap-10 md:gap-12 lg:grid-cols-2 lg:gap-16">

                    <!-- Image -->
                    <div class="order-1 overflow-hidden rounded-[22px]">
                        <img
                            src="{{ asset('website/images/therapi2.png') }}"
                            alt="جلسه مشاوره و گفت‌وگوی درمانی"
                            width="800"
                            height="600"
                            loading="lazy"
                            decoding="async"
                            class="h-[280px] w-full object-cover sm:h-[340px] md:h-[400px] lg:h-[430px]"
                        />
                    </div>

                    <!-- Content -->
                    <div class="order-2 text-right">
                        <h2 class="max-w-[560px] text-2xl font-normal leading-[1.8] sm:text-3xl md:text-4xl">
                            چرا می‌توانید روی این
                            <br class="hidden sm:block"/>
                            فرایند درمان حساب کنید؟
                        </h2>

                        <p class="mt-5 max-w-[520px] text-sm leading-8 text-gray-500 md:mt-6">
                            در این مسیر، با تمرکز بر نیازهای منحصربه‌فرد شما و با استفاده از
                            رویکردهای علمی و تجربه‌شده، تلاش می‌کنیم فضایی امن برای شناخت و تغییر
                            فراهم کنیم.
                        </p>

                        <ul class="mt-6 space-y-3 text-sm leading-7 text-gray-700 md:mt-8 md:space-y-4">
                            <li>✓ برنامه درمان متناسب با فرد</li>
                            <li>✓ رویکردی همدلانه و آموزشی</li>
                            <li>✓ تمرکز بر رشد و تغییر درونی</li>
                            <li>✓ حفظ امنیت و رازداری مراجع</li>
                        </ul>

                        <a
                            href="{{ route('booking.index') }}"
                            class="mt-7 inline-block rounded-full bg-forest px-6 py-3.5 text-xs text-white transition hover:opacity-90 md:mt-8 md:px-7 md:py-4"
                        >
                            درخواست جلسه مشاوره
                        </a>
                    </div>

                </div>
            </section>

            <!-- ۵. ساختار جلسات و حدود هزینه‌ها -->
            <section class="bg-forest py-24 text-white">
                <div class="container-layout">

                    <!-- Header Section -->
                    <div class="mx-auto mb-8 max-w-2xl text-center">

                        <h2 class="mb-4 text-3xl font-bold tracking-tight text-[#F6F1E7] md:text-4xl">
                            سرمایه‌گذاری روی سلامت روان
                        </h2>

                        <p class="text-sm leading-relaxed text-[#DFE9DF] md:text-base">
                            تعرفه تمامی جلسات
                            <span class="text-[#E2C15F]">فردی، زوج و خانواده</span>
                            کاملاً یکسان بوده و بر اساس مدت‌زمان تخصیص‌یافته تعیین می‌شود.
                        </p>
                    </div>

                    <!-- Interactive Duration Switcher -->
                    <div class="mx-auto mb-12 w-fit rounded-2xl border border-[#285B43] bg-[#123A28] p-1.5 shadow-inner">

                        <div class="flex flex-col items-stretch justify-center gap-2 sm:flex-row">

                            <button
                                id="btn-45"
                                type="button"
                                onclick="setDuration(45, '۷۰۰,۰۰۰', this)"
                                class="duration-btn rounded-xl bg-[#E4BB54] px-6 py-3 text-sm font-semibold text-[#123A28] shadow-md transition-all duration-300"
                            >
                                <span>⏱️ جلسات ۴۵ دقیقه‌ای</span>
                                <span class="mt-1 text-[11px] opacity-75">
                (استاندارد)
            </span>
                            </button>

                            <button
                                id="btn-60"
                                type="button"
                                onclick="setDuration(60, '۱,۰۰۰,۰۰۰', this)"
                                class="duration-btn rounded-xl px-6 py-3 text-sm font-semibold text-[#B7C6B7] transition-all duration-300 hover:bg-[#285B43] hover:text-[#F6F1E7]"
                            >
                                <span>⏳ جلسات ۶۰ دقیقه‌ای</span>
                                <span class="mt-1 text-[11px] opacity-75">
                (جامع و عمیق)
            </span>
                            </button>

                        </div>
                    </div>


                    <!-- Pricing Cards Grid -->
                    <div class="grid w-full max-w-[1290px] grid-cols-1 gap-6 md:grid-cols-3">

                        <div class="group flex flex-col justify-between rounded-3xl border border3xl border border-[#E9DDC7] bg-[#FBF9F4] p-8 text-[#1D1D1B] shadow-2xl transition-transform duration-300 hover:-translate-y-1.5">

                            <div>
                                <div class="mb-5 text-center">
                    <span class="mb-3 inline-flex rounded-full bg-[#DFE9DF] px-3 py-1 text-[11px] font-bold text-[#1F4F39]">
                        برای خودت
                    </span>

                                    <h3 class="text-xl font-bold text-[#1D1D1B]">
                                        تراپی فردی
                                    </h3>
                                </div>

                                <p class="mb-6 text-center text-xs leading-relaxed text-[#6F6A60]">
                                    تمرکز بر خودشناسی، تنظیم هیجان و حل الگوهای درونی
                                </p>

                                <div class="mb-6 rounded-2xl border border-[#DFE9DF] bg-[#F4ECDF] p-4 text-center">
                        <span class="duration-label mb-1 block text-xs font-medium text-[#1F4F39]">
                        مدت جلسه: ۴۵ دقیقه
                    </span>

                                    <div class="flex items-baseline justify-center gap-1">
                        <span class="price-value text-3xl font-extrabold text-[#123A28]">
                            ۷۰۰,۰۰۰
                        </span>

                                        <span class="text-xs text-[#6F6A60]">
                            تومان / جلسه
                        </span>
                                    </div>
                                </div>

                                <ul class="space-y-3.5 border-t border-[#E9DDC7] pt-6 text-xs text-[#6F6A60]">
                                    <li>
                                        <span class="text-base font-bold text-[#086B47]">✓</span>
                                        <span>برگزاری به‌صورت آنلاین و حضوری</span>
                                    </li>

                                    <li class="flex items-center gap-2.5">
                                        <span class="text-base font-bold text-[#086B47]">✓</span>
                                        <span>تمرکز: اضطراب، تروما، افسردگی</span>
                                    </li>

                                    <li class="flex items-center gap-2.5">
                                        <span class="text-base font-bold text-[#086B47]">✓</span>
                                        <span>امکان بازبینی فواصل جلسات در طول مسیر</span>
                                    </li>
                                </ul>
                            </div>

                            <a
                                href="{{ route('booking.index') }}"
                                class="mt-8 block w-full rounded-xl bg-[#123A28] px-4 py-3.5 text-center text-sm font-semibold text-[#F6F1E7]"
                            >
                                رزرو وقت فردی
                            </a>
                        </div>


                        <!-- Card 2: زوج درمانی - Featured -->
                        <div class="relative flex flex-col justify-between rounded-3xl border-2 border-[#D7BF67] bg-[#FBF9F4] p-8 text-[#1D1D1B] shadow-2xl transition-transform duration-300 hover:-translate-y-1.5">

                            <div class="absolute -top-3.5 right-1/2 translate-x-1/2 rounded-full bg-[#E4BB54] px-3.5 py-1 text-[11px] font-bold text-[#123A28] shadow-md">
                                بیشترین تقاضا
                            </div>

                            <div>
                                <div class="mb-5 text-center">
                        <span class="mb-3 inline-flex rounded-full bg-[#F3EBDD] px-3 py-1 text-[11px] font-bold text-[#1F4F39]">
                            برای رابطه
                        </span>

                                    <h3 class="text-xl font-bold text-[#1D1D1B]">
                                        مشاوره روابط و زوج
                                    </h3>
                                </div>

                                <p class="mb-6 text-center text-xs leading-relaxed text-[#6F6A60]">
                                    خروج از چرخه‌های تعارض، ترمیم آسیب و بازسازی صمیمیت
                                </p>

                                <div class="mb-6 rounded-2xl border border-[#D7BF67]/60 bg-[#F4ECDF] p-4 text-center">
                        <span class="duration-label block text-xs font-medium text-[#1F4F39]">
                            مدت جلسه: ۴۵ دقیقه
                        </span>

                                    <div class="flex items-baseline justify-center gap-1">
                            <span class="price-value text-3xl font-extrabold text-[#123A28]">
                                ۷۰۰,۰۰۰
                            </span>

                                        <span class="text-xs text-[#6F6A60]">
                                تومان / جلسه
                            </span>
                                    </div>
                                </div>

                                <ul class="space-y-3.5 border-t border-[#E9DDC7] pt-6 text-xs text-[#6F6A60]">
                                    <li class="flex items-center gap-2.5">
                                        <span class="text-base font-bold text-[#086B47]">✓</span>
                                        <span>جلسات مشترک یا تفکیکی زوجین</span>
                                    </li>

                                    <li class="flex items-center gap-2.5">
                                        <span class="text-base font-bold text-[#086B47]">✓</span>
                                        <span>تمرکز: بی‌اعتمادی، خیانت، تعارضات مزمن</span>
                                    </li>

                                    <li class="flex items-center gap-2.5">
                                        <span class="text-base font-bold text-[#086B47]">✓</span>
                                        <span>پروتکل گاتمن و درمان متمرکز بر هیجان (EFT)</span>
                                    </li>
                                </ul>
                            </div>

                            <a
                                href="{{ route('booking.index') }}"
                                class="mt-8 block w-full rounded-xl bg-[#123A28] px-4 py-3.5 text-center text-sm font-semibold text-[#F6F1E7]"
                            >
                                رزرو وقت زوج درمانی
                            </a>
                        </div>


                        <!-- Card 3: خانواده -->
                        <div class="group flex flex-col justify-between rounded-3xl border border-[#E9DDC7] bg-[#FBF9F4] p-8 text-[#1D1D1B] shadow-2xl transition-transform duration-300 hover:-translate-y-1.5">

                            <div>
                                <div class="mb-5 text-center">
                        <span class="mb-3 inline-flex rounded-full bg-[#DFE9DF] px-3 py-1 text-[11px] font-bold text-[#1F4F39]">
                            برای خانواده
                        </span>

                                    <h3 class="text-xl font-bold text-[#1D1D1B]">
                                        مشاوره خانواده
                                    </h3>
                                </div>

                                <p class="mb-6 text-center text-xs leading-relaxed text-[#6F6A60]">
                                    شفاف‌سازی مرزها، بهبود الگوهای تعاملی و کاهش تنش
                                </p>

                                <div class="mb-6 rounded-2xl border border-[#DFE9DF] bg-[#F4ECDF] p-4 text-center">
                        <span class="duration-label mb-1 block text-xs font-medium text-[#1F4F39]">
                            مدت جلسه: ۴۵ دقیقه
                        </span>

                                    <div class="flex items-baseline justify-center gap-1">
                            <span class="price-value text-3xl font-extrabold text-[#123A28]">
                                ۷۰۰,۰۰۰
                            </span>

                                        <span class="text-xs text-[#6F6A60]">
                                تومان / جلسه
                            </span>
                                    </div>
                                </div>

                                <ul class="space-y-3.5 border-t border-[#E9DDC7] pt-6 text-xs text-[#6F6A60]">
                                    <li class="flex items-center gap-2.5">
                                        <span class="text-base font-bold text-[#086B47]">✓</span>
                                        <span>حضور والد-فرزند یا چند عضو خانواده</span>
                                    </li>

                                    <li class="flex items-center gap-2.5">
                                        <span class="text-base font-bold text-[#086B47]">✓</span>
                                        <span>تمرکز: شکاف نسلی، تعارضات والدگری</span>
                                    </li>

                                    <li class="flex items-center gap-2.5">
                                        <span class="text-base font-bold text-[#086B47]">✓</span>
                                        <span>رویکرد ساختاری و سیستمی به خانواده</span>
                                    </li>
                                </ul>
                            </div>

                            <a
                                href="{{ route('booking.index') }}"
                                class="mt-8 block w-full rounded-xl bg-[#123A28] px-4 py-3.5 text-center text-sm font-semibold text-[#F6F1E7]"
                            >
                                رزرو وقت خانواده
                            </a>
                        </div>

                    </div>
            </section>

            <!-- ۶. آماده‌اید فرایند درمان را شروع کنید؟ -->
            <section id="contact" class="cta-bg flex min-h-[700px] items-center justify-center text-center text-white">
                <div class="container-layout">
                    <h2 class="text-3xl font-normal md:text-5xl">
                        آماده‌اید فرایند درمان را شروع کنید؟
                    </h2>

                    <p class="mx-auto mt-7 max-w-[620px] text-sm leading-8 text-white/85">
                        اگر آماده‌اید قدمی برای سلامت روان و کیفیت بهتر زندگی خود بردارید،
                        می‌توانید همین حالا با ما در ارتباط باشید.
                    </p>

                    <div class="mt-9 flex flex-col items-center gap-4">
                        <a href="" class="rounded-full bg-forest px-8 py-4 text-xs text-white">
                            درخواست وقت مشاوره
                        </a>

                        <a href="{{ url('/contact-us') }}" class="rounded-full bg-forest px-8 py-4 text-xs text-white">
                            تماس با ما
                        </a>
                    </div>
                </div>
            </section>
        </main>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('website/js/services.js') }}" defer></script>
@endpush
