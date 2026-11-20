@extends('website.layouts.app')

@section('title', 'روان‌شناس در تبریز؛ مشاوره حضوری و آنلاین | کریم محمدزاده')
@section('description', 'مشاوره فردی و مشاوره زوجین با کریم محمدزاده در تبریز به‌صورت حضوری یا در سراسر ایران به صورت آنلاین. آشنایی با روند جلسات، حوزه‌های مشاوره و ثبت درخواست نوبت.')
@section('canonical', route('home'))
@section('social_image_alt', 'کریم محمدزاده، روان‌شناس بالینی')

@push('styles')
    <style>
        /* This stylesheet is emitted only by the landing view. */
        html body :focus-visible { outline: 2px solid #286052 !important; outline-offset: 2px !important; box-shadow: 0 0 0 2px #fff !important; }
        @media (forced-colors: active) { html body :focus-visible { outline-color: Highlight !important; } }
        @media (min-width: 768px) {
            .therapy-intro { align-items: baseline; column-gap: 1.5rem; }
            .therapy-intro > a { align-self: flex-start; margin-top: 0; }
        }
        @media (min-width: 1280px) {
            #services { padding-block: clamp(24px, 3vh, 40px); }
            #services > .layout { --scene: clamp(680px, 84vh, 780px); min-height: var(--scene); }
            #services .circle_border { width: calc(var(--scene) * .92); height: auto; aspect-ratio: 1; }
            #services .circle_border:nth-child(2) { width: calc(var(--scene) * .8); }
            #services .circle_border:nth-child(3) { width: calc(var(--scene) * .68); }
            #services .z-10 h2 { font-size: 26px; }
            #services .z-10 p { font-size: 17px; line-height: 1.95; margin-top: 1rem; }
            #services .z-20 article.absolute { width: min(35%, 390px); height: auto; min-height: 205px; }
            #services article.absolute > div { margin-bottom: 30px; }
            #services article.absolute h3 { font-size: 21px; }
            #services article.absolute:nth-of-type(3) { top: calc(var(--scene) - 230px); }
            #services article.absolute:nth-of-type(4) { top: calc(var(--scene) - 216px); }
        }
        #services .services-page-link { display:inline-flex; align-items:center; justify-content:center; min-height:44px; margin-top:1rem; font-size:.9375rem; }
        #services .services-page-link:hover {background-color: #1e4935; text-underline-offset:4px; }
        .insight {padding:clamp(3rem,6vw,5rem) 1.25rem;text-align:center;}
        .insight .insight-dialog {display:block;width:64px;height:64px;margin:0 auto 1rem;}
        .insight h2 {font-size:clamp(1.6rem,3vw,2.4rem);line-height:1.55;}
        .insight p {max-width:58ch;margin:1rem auto 1.2rem;line-height:2.15;text-wrap:pretty;}
        .insight a {display:inline-flex;align-items:center;gap:.75rem;min-height:46px;font-weight:700;}
        .insight a span {display:grid;place-items:center;width:2.25rem;height:2.25rem;border-radius:50%;background:#e8efe9;}
        .insight a:hover span,.insight a:focus-visible span {background:#dce9df;}
        @media (prefers-reduced-motion:no-preference) {
            .insight a span {transition:transform .22s ease-out;}
            .insight a:hover span,.insight a:focus-visible span {transform:translateX(-4px);}
        }
        .home-question-label {cursor:pointer;list-style:none;}
        .home-question-label::-webkit-details-marker {display:none;}
        .question-sign::before {content:"+";}
        .home-question[open] .question-sign::before {content:"−";}
        @media(max-width:540px){.insight p{text-align:start;}}
        @media(max-width:767px){
            #services {padding-bottom:5.5rem;}
            #services .services-list {margin-top:1.5rem;gap:.75rem;}
            #services .services-list article {display:grid;grid-template-columns:32px minmax(0,1fr);align-items:center;gap:.35rem .6rem;height:auto;min-height:0;padding:.85rem 1rem;border-radius:16px;box-shadow:0 3px 12px rgba(26,55,43,.07);}
            #services .services-list article > div {margin:0;}
            #services .services-list h3 {margin:0;font-size:1rem;line-height:1.65;}
            #services .services-list p {grid-column:1/-1;margin:0;font-size:.875rem;line-height:1.85;}
        }
        #services :is(.services-orbit,.services-list) article > div > svg {display:block;width:32px;height:32px;flex:none;animation:none;}
    </style>
@endpush

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

                                همراهی در مسیر     <span class="block text-gold"> شناخت خویشتن </span> و حل تعارضات درونی

                            </h1>
                            <p class="mt-5 max-w-[620px] text-[14px] leading-[2] text-white/85 sm:mt-7 sm:text-[16px] sm:leading-[2.25] lg:text-[16px]">
                                گاهی فشارها آن‌قدر آرام و تدریجی زیاد می‌شوند که متوجه نمی‌شویم چه زمانی از خودمان فاصله گرفته‌ایم. مشاوره می‌تواند فرصتی باشد برای مکث کردن و فهمیدن آنچه تجربه می‌کنید.
                            </p>
                            <div class="mt-6 flex w-full flex-col gap-3 sm:mt-9 sm:w-auto sm:flex-row sm:flex-wrap sm:justify-start">
                                <a href="#process"
                                   class="inline-flex h-12 w-full items-center justify-center rounded-full bg-cream px-7 text-[14px] font-medium text-forest-3 transition hover:bg-white sm:h-[45px] sm:w-auto sm:min-w-[200px] sm:text-[13px]">
                                    چطور جلسات برگزار می‌شود
                                </a>
                                <a href="{{ url('/about-us') }}"
                                   class="inline-flex h-12 w-full items-center justify-center rounded-full border border-white/45 px-7 text-[14px] font-medium text-white transition hover:bg-white hover:text-forest-3 sm:h-[45px] sm:w-auto sm:min-w-[125px] sm:text-[13px]">
                                    با من بیشتر آشنا شوید
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
                                <p class="text-[11px] leading-[1.7] sm:text-[13px] sm:leading-[1.8]">گفت‌وگو بدون
                                    قضاوت</p>
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
                                <div class="flex h-[46px] w-11.5 items-center justify-center rounded-full">
                                    <span class="text-[21px]"><img src="{{ asset('website/images/person.png') }}" alt="" width="32"/></span>
                                </div>
                                <div class="text-right">
                                    <p class="text-[12px] leading-[1.9]">
                                        رویکرد علمی متناسب <br> با شما
                                    </p>
                                </div>
                            </div>
                            <div class="benefit flex items-center justify-start gap-3">
                                <div class="flex h-[46px] w-11.5 items-center justify-center rounded-full">
                                <span class="text-[21px]"><img src="{{ asset('website/images/f5e7b65c-6a4a-48ca-b819-953817773698.png') }}"
                                                               alt="" width="50" /></span>
                                </div>
                                <div class="text-right">
                                    <p class="text-[12px] leading-[1.9]">گفت‌وگو بدون<br/>قضاوت</p>
                                </div>
                            </div>
                            <div class="benefit flex items-center justify-start gap-3">
                                <div class="flex h-[46px] w-11.5 items-center justify-center rounded-full">
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
                <div class="therapy-intro mb-12 flex flex-col items-end justify-between md:flex-row">
                    <div class="text-right">
                        <h2 id="regular-therapy-title" style="margin-bottom: 1.5rem" class="text-3xl font-bold text-forest-2">
                            به‌جای عادت‌کردن به حال بد، روی
                            <span class="decoration-gold">تراپی منظم</span>
                            حساب کنید
                        </h2>
                        <p class="text-muted" style="max-inline-size: 100ch; font-size: .9375rem; line-height: 2">
                            لازم نیست از قبل بدانید دقیقاً چه چیزی باید بگویید. می‌توانید از همان چیزی شروع کنید که این روزها ذهن و زندگی‌تان را درگیر کرده است؛ از استرس و اضطراب گرفته تا افسردگی، خستگی یا رابطه‌ای که دیگر مثل قبل نیست.
                        </p>
                    </div>
                    <a href="{{ route('booking.index') }}" class="mt-4 rounded-3xl bg-forest-2 px-6 py-2 text-white md:mt-0" style="display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; min-height: 44px; font-size: .8125rem">
                        رزرو جلسه ارزیابی
                    </a>
                </div>

                {{-- پس از آماده شدن عکس افقی اتاق، آن را با نام therapy-room.webp قرار دهید. --}}
                @php($hasTherapyRoomPhoto = is_file(public_path('website/images/therapy-room.webp')))
                <img src="{{ asset($hasTherapyRoomPhoto ? 'website/images/therapy-room.webp' : 'website/images/therapi1.png') }}"
                     alt="{{ $hasTherapyRoomPhoto ? 'نمای اتاق مشاوره' : '' }}"
                     class="rounded-2xl" style="display:block;width:100%;height:clamp(190px,32vw,380px);object-fit:cover"
                     loading="lazy" decoding="async">
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
                        <span class="text-gold-2">سلامت روان</span>، بخشی از زندگی روزمره ماست
                    </h2>

                    <a href="{{ route('website.services') }}" class="services-page-link rounded-3xl bg-forest-2 px-6 py-2 text-white">دیدن خدمات درمانی محمدزاده</a>
                </div>

                <!-- Desktop graphic cards -->
                <div class="services-orbit relative z-20 hidden xl:block">

                    <!-- Individual sessions -->
                    <article
                        class="absolute right-[10%] top-0 h-[269px] w-[435px] rounded-[28px] bg-cream p-[15px] shadow-[0_14px_34px_rgba(0,0,0,0.10)]">

                        <div class="mb-[45px] flex justify-start text-forest-8">
                            <svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_52_33)">
                                    <path d="M38.0517 20.4604C36.5772 20.2153 33.183 20.1883 30.3253 20.811C30.1423 20.6901 29.9587 20.5651 29.7778 20.4539C29.2254 20.1155 28.6836 19.8295 28.1455 19.6139C27.6238 19.4063 27.1036 19.2634 26.5594 19.2346V19.2323C25.7442 19.1661 24.9529 19.1367 24.2252 19.1367C23.1175 19.1383 22.1725 19.1996 21.4721 19.3164H21.475C21.3782 19.3312 21.272 19.3402 21.1567 19.3402C20.5515 19.3475 19.7139 19.0599 18.9539 18.6447C18.4416 18.3701 17.8066 18.5156 17.465 18.9863L9.12422 30.4887C8.84016 30.8801 8.82996 31.4105 9.09762 31.8142C9.30926 32.1314 9.48621 32.4605 9.60305 32.79C9.77098 33.255 10.0141 33.6774 10.2948 34.0885C10.5758 34.4978 10.8967 34.895 11.2316 35.2742L11.3629 35.4C11.3629 35.4 11.9634 35.8952 12.9204 36.6829C12.6707 37.0409 12.5334 37.4659 12.5285 37.9136C12.5224 38.5037 12.7459 39.0627 13.1595 39.4851C13.5795 39.9141 14.1409 40.1503 14.7411 40.1503C15.1616 40.1503 15.5636 40.0293 15.9137 39.8078C15.6797 40.1608 15.5497 40.5719 15.5448 41.0042C15.5382 41.5943 15.7625 42.1524 16.1756 42.5741C16.5948 43.0039 17.1567 43.24 17.7573 43.24C18.2291 43.24 18.6757 43.0864 19.0525 42.8118C18.3669 43.6715 18.4041 44.9258 19.1921 45.7324C19.6114 46.1606 20.1728 46.3967 20.7725 46.3967H20.773C21.3548 46.3967 21.9048 46.1721 22.3232 45.7626L22.7321 45.3564C22.5609 45.6703 22.4587 46.0183 22.4546 46.3853C22.4486 46.9761 22.672 47.5343 23.0855 47.9567C23.5051 48.3849 24.0669 48.621 24.6671 48.621C25.2485 48.621 25.798 48.3972 26.2201 47.9853L26.469 47.7328C26.5936 47.8309 26.7073 47.9191 26.8 47.991C26.8759 48.0482 26.9406 48.0973 26.9998 48.1399L27.0877 48.202L27.2122 48.2789C27.68 48.5354 28.1275 48.6466 28.4832 48.725C28.6605 48.7618 28.8125 48.7871 28.9253 48.8026L29.0626 48.8206L29.1055 48.8247L29.1218 48.8264L29.132 48.828L29.143 48.8288L29.1578 48.8296C29.1713 48.8296 29.1647 48.8313 29.2357 48.8329L29.3195 48.8296C30.0988 48.7725 30.8366 48.5043 31.4422 48.0419C31.6832 47.8572 31.8941 47.6341 32.0829 47.3899C32.4559 47.5108 32.8509 47.5827 33.262 47.5827C34.9294 47.5819 36.3326 46.5187 36.876 45.0397C36.9364 45.0356 36.9966 45.0331 37.0561 45.0177C37.2677 45.0561 37.4823 45.0839 37.7045 45.0839C39.3589 45.0822 40.7591 44.0396 41.3115 42.58C41.3153 42.5768 41.3189 42.5752 41.3225 42.5726L41.4524 42.585C43.5896 42.585 45.3197 40.8533 45.3205 38.7173C45.3218 38.124 45.2397 37.4678 45.0137 36.7855C46.4805 35.4895 48.6301 33.4873 49.0187 32.4005C49.2059 31.8775 50.258 30.6233 50.5636 30.1958L42.2877 18.7824C41.2322 19.3801 39.1839 20.65 38.0517 20.4604ZM42.9988 38.7168C42.9976 39.5708 42.3067 40.2621 41.4526 40.2637C41.241 40.2637 41.0433 40.2213 40.8591 40.1444C40.8407 40.1362 40.8214 40.1346 40.803 40.1288L39.3689 38.3841C39.0061 37.9641 38.3707 37.9168 37.9502 38.2795C37.5294 38.6423 37.4829 39.2782 37.8461 39.699L39.2332 41.3896C39.1445 42.1586 38.4982 42.7609 37.7046 42.7617C37.3279 42.7609 36.9937 42.6269 36.7204 42.4021L35.3439 40.7235C34.9806 40.3035 34.3452 40.257 33.9248 40.6198C33.5039 40.9826 33.4577 41.6185 33.8205 42.0384L34.7795 43.2332C34.7779 43.3075 34.7706 43.3819 34.7836 43.4564C34.8004 43.5503 34.8081 43.6352 34.8081 43.7137C34.8069 44.5685 34.1156 45.2589 33.262 45.2606C33.0389 45.2606 32.8335 45.2091 32.6439 45.1258L30.9662 43.3624C30.5613 42.9815 29.9248 43.0012 29.5439 43.4064C29.1627 43.811 29.1824 44.4484 29.5868 44.8283L30.3741 45.7517C30.2952 45.9209 30.1902 46.0737 30.0305 46.1978C29.8257 46.3548 29.5443 46.4626 29.224 46.4985C29.1305 46.4847 29.0033 46.4634 28.8521 46.4275C28.6646 46.3851 28.4668 46.3156 28.3643 46.2616C28.3288 46.2356 28.2381 46.1686 28.107 46.0654C28.0325 46.0082 27.948 45.943 27.8541 45.8677C28.024 45.1536 27.8409 44.3707 27.2898 43.8069C26.8702 43.3778 26.3084 43.1417 25.7081 43.1417C25.3706 43.1417 25.0471 43.225 24.7495 43.3698L24.7406 43.3624L24.7508 43.3509C25.1729 42.9375 25.4091 42.385 25.4155 41.7935C25.4216 41.2026 25.1973 40.6444 24.7839 40.2228C24.3646 39.7946 23.8025 39.5576 23.2027 39.5576C22.7336 39.5576 22.2891 39.7095 21.9139 39.9809C22.2175 39.5984 22.3937 39.1343 22.399 38.6383C22.4055 38.0475 22.1812 37.4893 21.768 37.0677C21.3485 36.6378 20.7867 36.4017 20.1865 36.4017C19.6051 36.4017 19.0555 36.6256 18.6371 37.0358L18.2641 37.4068C18.7242 36.5732 18.618 35.506 17.9193 34.7919C17.4993 34.3628 16.9375 34.1267 16.3372 34.1267C15.7559 34.1267 15.2062 34.3505 14.7871 34.7608L14.5403 35.0084C14.365 34.8637 14.1929 34.7224 14.0409 34.5973C13.4698 34.1282 13.0763 33.803 12.9182 33.6723C12.651 33.3675 12.4037 33.061 12.2105 32.7767C12.0029 32.476 11.8559 32.1973 11.7896 32.0085C11.6924 31.7356 11.5764 31.4791 11.4502 31.2356L18.7826 21.1248C19.5075 21.4279 20.3005 21.6559 21.1566 21.6617C21.3845 21.6617 21.6178 21.6453 21.8543 21.6062H21.8572C22.3454 21.5228 23.2157 21.4573 24.225 21.4591C24.8906 21.4575 25.6228 21.4852 26.3713 21.5457L26.4224 21.549C26.6062 21.5539 26.9123 21.6193 27.2841 21.7696L27.3682 21.8081C24.236 23.2225 19.7972 25.9439 18.3384 26.4308C16.6726 26.9857 16.5333 29.2075 20.2813 29.6234C24.03 30.041 27.3616 27.6794 28.1944 27.2643C28.7921 26.9653 32.5898 27.3305 34.6934 27.5609C35.6209 28.477 36.4339 29.3268 37.0268 29.9691L41.6467 35.6173L41.6671 35.6403C42.2591 36.3259 42.5803 36.8823 42.7625 37.3596C42.9429 37.8368 42.9976 38.2485 42.9988 38.7168Z" fill="#0A5C3A"/>
                                    <path d="M59.8407 26.9086L48.6766 11.5115C48.4065 11.138 47.886 11.0563 47.5133 11.326L42.5682 14.9118C42.1956 15.1815 42.1126 15.7028 42.3831 16.0755L53.5473 31.4726C53.8173 31.8453 54.3379 31.9286 54.7105 31.659L59.6564 28.0724C60.0284 27.8017 60.1117 27.2812 59.8407 26.9086ZM56.0421 28.292C55.4218 28.7415 54.5533 28.6026 54.1029 27.9823C53.653 27.3622 53.7916 26.4943 54.4126 26.044C55.0333 25.5938 55.9011 25.7327 56.3514 26.352C56.8013 26.9731 56.6627 27.8418 56.0421 28.292Z" fill="#0A5C3A"/>
                                    <path d="M17.4325 14.9118L12.4865 11.326C12.1146 11.0563 11.5937 11.138 11.3232 11.5115L0.158677 26.9088C-0.111791 27.2814 -0.0276507 27.802 0.344185 28.0724L5.28926 31.659C5.66192 31.9286 6.18235 31.8453 6.452 31.4726L17.6167 16.0755C17.8872 15.7028 17.8042 15.1815 17.4325 14.9118ZM14.1274 16.6312C13.6775 17.2515 12.8093 17.3903 12.1887 16.9401C11.5676 16.4907 11.4292 15.6212 11.8793 15.0018C12.3291 14.3807 13.1974 14.2419 13.8181 14.6922C14.4395 15.1423 14.5768 16.0101 14.1274 16.6312Z" fill="#0A5C3A"/>
                                </g>
                                <defs>
                                    <clipPath id="clip0_52_33">
                                        <rect width="60" height="60" fill="white"/>
                                    </clipPath>
                                </defs>
                            </svg>
                        </div>

                        <h3 class="mb-2 text-right text-[24px] font-bold leading-[1.6] text-ink-2">
                            جلسات مشاوره فردی
                        </h3>

                        <p class="text-right text-[14px] leading-[1.85] text-muted-3">در جلسات فردی، ریشه‌های اضطراب، خستگی و الگوهای تکراری در روابط‌تان را بررسی می‌کنیم.</p>
                    </article>

                    <!-- Family relationships -->
                    <article
                        class="absolute left-[10%] top-[22px] h-[269px] w-[435px] rounded-[28px] bg-cream p-[15px] shadow-[0_14px_34px_rgba(0,0,0,0.10)]">

                        <div class="mb-[45px] flex justify-start text-forest-8">
                            <svg xmlns="http://www.w3.org/2000/svg" width="54" height="54" viewBox="0 0 64 64" fill="#0A5C3A" aria-hidden="true"><path d="M32 13C22 6 26-1 32 4c6-5 10 2 0 9Z"/><g><circle cx="15" cy="22" r="5"/><path d="M5 44c1-9 3-15 9-15h3c4 0 9-8 13-14 1-2 4-1 3 2-3 7-8 14-11 18v11l4 13c1 4-4 5-5 2l-6-13-3 13c-1 3-6 3-5-1l3-16v-7l-2 8c-1 3-4 2-3-1Z"/></g><g transform="translate(64 0) scale(-1 1)"><circle cx="15" cy="22" r="5"/><path d="M5 44c1-9 3-15 9-15h3c4 0 9-8 13-14 1-2 4-1 3 2-3 7-8 14-11 18v11l4 13c1 4-4 5-5 2l-6-13-3 13c-1 3-6 3-5-1l3-16v-7l-2 8c-1 3-4 2-3-1Z"/></g></svg>
                        </div>

                        <h3 class="mb-2 text-right text-[24px] font-bold leading-[1.6] text-ink-2">
                            مشاوره زوجین و روابط
                        </h3>

                        <p class="text-right text-[14px] leading-[1.85] text-muted-3">از فاصله‌ها و حرف‌های ناگفته شروع می‌کنیم تا نیازهای عاطفی یکدیگر را بهتر بشناسید.</p>
                    </article>

                    <!-- Depression support -->
                    <article
                        class="absolute right-[10%] top-[685px] h-[269px] w-[435px] rounded-[28px] bg-cream p-[15px] shadow-[0_14px_34px_rgba(0,0,0,0.10)]">

                        <div class="mb-[45px] flex justify-start text-forest-8">
                            <svg width="50" height="50" viewBox="0 0 58 58" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M13.3941 30.187C11.4301 28.3262 3.5766 27.7236 1.21621 27.5838C0.915966 27.5815 0.592839 27.6778 0.363708 27.8931C0.132212 28.1109 0.00158691 28.4134 0.00158691 28.7296V49.3535C0.00158691 49.986 0.514958 50.4994 1.14746 50.4994H8.02249C8.51749 50.4994 8.95749 50.1809 9.11337 49.7088C9.28062 49.1954 13.2293 37.0778 13.7471 31.1197C13.7791 30.7714 13.6463 30.4276 13.3941 30.187Z" fill="#0A5C3A"/>
                                <path d="M33.5518 24.8592C33.7671 25.0815 34.0651 25.2098 34.3768 25.2098C34.6884 25.2098 34.9864 25.0815 35.2018 24.8592L45.9589 13.6811C47.3568 12.2328 48.1268 10.3056 48.1268 8.04609C48.1268 4.11841 45.4616 0.706223 41.9278 0.108129C38.7675 -0.43035 35.9007 1.10721 34.3768 3.5982C32.8528 1.10495 29.9836 -0.432605 26.8257 0.108129C23.292 0.706223 20.6267 4.11831 20.6267 8.25921C20.6267 10.3079 21.3967 12.2328 22.7923 13.6811L33.5518 24.8592Z" fill="#0A5C3A"/>
                                <path d="M54.8803 44.2573C53.3197 41.1384 49.7012 39.4702 46.3163 40.3181L41.1348 41.6128C41.2127 42.1375 41.2517 42.6875 41.2517 43.2627V43.6248C41.2517 45.2037 40.1791 46.5763 38.646 46.959L35.5614 47.729C34.7501 47.9329 33.9091 48.0361 33.0611 48.0361C32.3759 48.0361 31.6953 47.9696 31.0399 47.8367L21.0963 45.8477C20.4753 45.7239 20.0719 45.1212 20.1957 44.5002C20.3217 43.8769 20.9359 43.4805 21.5432 43.6019L31.4891 45.5909C32.6304 45.8201 33.8701 45.7903 35.0023 45.5084L38.0891 44.7385C38.6025 44.6079 38.96 44.1519 38.96 43.6248V43.2627C38.96 42.8433 38.9325 42.4515 38.8844 42.0756C38.8706 41.9679 38.8454 41.8716 38.8271 41.7663C38.7813 41.5004 38.7309 41.2392 38.6575 40.994C38.6254 40.8863 38.5865 40.7878 38.5498 40.6847C38.4695 40.4578 38.3801 40.2356 38.2725 40.027C38.2244 39.9354 38.1716 39.846 38.119 39.7566C37.9999 39.5526 37.8669 39.3579 37.718 39.1723C37.6584 39.0966 37.5965 39.021 37.5301 38.95C37.3651 38.7644 37.1817 38.5903 36.9847 38.4253C36.916 38.368 36.8495 38.3084 36.7762 38.2512C36.5562 38.0839 36.3133 37.9281 36.059 37.7814C35.9902 37.7424 35.9261 37.6967 35.8527 37.6599C35.5571 37.5018 35.2362 37.3621 34.8948 37.2291C34.8444 37.2107 34.8008 37.1879 34.7505 37.1672C34.3471 37.0182 33.914 36.8854 33.4488 36.7685L17.0244 33.1181C16.7172 33.0516 16.401 33.1112 16.1397 33.2853C15.8785 33.4594 15.702 33.7322 15.647 34.0393C14.822 38.7347 13.0322 44.8278 12.0399 48.0429C11.8565 48.6388 12.182 49.2712 12.7709 49.4682L25.9457 53.8611C27.7103 54.528 29.0807 55.0001 30.6986 55.0001C30.6986 55.0001 30.6986 55.0001 30.7008 55.0001C33.0521 55.0001 35.715 54.0033 41.7215 51.5421L54.3258 45.8132C54.6077 45.6848 54.8277 45.4466 54.9331 45.1532C55.0408 44.86 55.0202 44.5369 54.8803 44.2573Z" fill="#0A5C3A"/>
                            </svg>
                        </div>

                        <h3 class="mb-2 text-right text-[24px] font-bold leading-[1.6] text-ink-2">
                            مواجهه با افسردگی و خستگی مفرط
                        </h3>

                        <p class="text-right text-[14px] leading-[1.85] text-muted-3">با هم بررسی می‌کنیم پشت بی‌انگیزگی و خستگی چه احساس‌هایی مجال بیان پیدا نکرده‌اند.</p>
                    </article>

                    <!-- Trauma support -->
                    <article
                        class="absolute left-[10%] top-[700px] h-[269px] w-[435px] rounded-[28px] bg-cream p-[15px] shadow-[0_14px_34px_rgba(0,0,0,0.10)]">

                        <div class="mb-[45px] flex justify-start text-forest-8">
                            <svg width="65" height="65" viewBox="0 0 65 65" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.5029 36.9607L16.0971 31.9391L20.698 25.5156H16.0971V23.2342L32.551 13.7391L49.0049 23.2342V29.2627L52.4971 24.394L56.0147 29.3264V19.194L32.551 5.646L9.08725 19.194V25.5156H4.32059L9.08725 32.1813L12.5029 36.9607Z" fill="#0A5C3A"/>
                                <path d="M56.0147 33.0353L52.4971 28.1157L49.0049 32.9844L44.302 39.5608H49.0049V42.2373L32.551 51.7324L16.0971 42.2373V35.7118L12.5029 40.7334L9.08725 35.9667V46.2902L32.551 59.8255L56.0147 46.2902V39.5608H60.6794L56.0147 33.0353Z" fill="#0A5C3A"/>
                                <path d="M21.6157 32.5C21.6157 38.5412 26.5098 43.4353 32.551 43.4353C38.5922 43.4353 43.4863 38.5412 43.4863 32.5C43.4863 26.4588 38.5922 21.5647 32.551 21.5647C26.5098 21.5647 21.6157 26.4588 21.6157 32.5ZM38.1843 31.2255V33.7745H33.8255V38.1333H31.2765V33.7745H26.9177V31.2255H31.2765V26.8667H33.8255V31.2255H38.1843Z" fill="#0A5C3A"/>
                            </svg>
                        </div>

                        <h3 class="mb-2 text-right text-[24px] font-bold leading-[1.6] text-ink-2">
                            تجربه‌های آسیب‌زا (تروما)
                        </h3>

                        <p class="text-right text-[14px] leading-[1.85] text-muted-3">با سرعتی متناسب با آمادگی شما، بدون فشار برای بازگویی خاطرات، به اثر تجربه‌های گذشته می‌پردازیم.</p>
                    </article>
                </div>

                <!-- Tablet and mobile grid cards -->
                <div
                    class="services-list relative z-20 mt-10 grid grid-cols-1 gap-5 sm:gap-6 md:mt-12 md:grid-cols-2 xl:hidden">

                    <!-- Individual sessions -->
                    <article
                        class="flex h-full flex-col rounded-[24px] bg-cream p-6 shadow-[0_12px_28px_rgba(0,0,0,0.09)] sm:p-8">

                        <div class="mb-6 text-forest-8" aria-hidden="true">
                            <svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_52_33)">
                                    <path d="M38.0517 20.4604C36.5772 20.2153 33.183 20.1883 30.3253 20.811C30.1423 20.6901 29.9587 20.5651 29.7778 20.4539C29.2254 20.1155 28.6836 19.8295 28.1455 19.6139C27.6238 19.4063 27.1036 19.2634 26.5594 19.2346V19.2323C25.7442 19.1661 24.9529 19.1367 24.2252 19.1367C23.1175 19.1383 22.1725 19.1996 21.4721 19.3164H21.475C21.3782 19.3312 21.272 19.3402 21.1567 19.3402C20.5515 19.3475 19.7139 19.0599 18.9539 18.6447C18.4416 18.3701 17.8066 18.5156 17.465 18.9863L9.12422 30.4887C8.84016 30.8801 8.82996 31.4105 9.09762 31.8142C9.30926 32.1314 9.48621 32.4605 9.60305 32.79C9.77098 33.255 10.0141 33.6774 10.2948 34.0885C10.5758 34.4978 10.8967 34.895 11.2316 35.2742L11.3629 35.4C11.3629 35.4 11.9634 35.8952 12.9204 36.6829C12.6707 37.0409 12.5334 37.4659 12.5285 37.9136C12.5224 38.5037 12.7459 39.0627 13.1595 39.4851C13.5795 39.9141 14.1409 40.1503 14.7411 40.1503C15.1616 40.1503 15.5636 40.0293 15.9137 39.8078C15.6797 40.1608 15.5497 40.5719 15.5448 41.0042C15.5382 41.5943 15.7625 42.1524 16.1756 42.5741C16.5948 43.0039 17.1567 43.24 17.7573 43.24C18.2291 43.24 18.6757 43.0864 19.0525 42.8118C18.3669 43.6715 18.4041 44.9258 19.1921 45.7324C19.6114 46.1606 20.1728 46.3967 20.7725 46.3967H20.773C21.3548 46.3967 21.9048 46.1721 22.3232 45.7626L22.7321 45.3564C22.5609 45.6703 22.4587 46.0183 22.4546 46.3853C22.4486 46.9761 22.672 47.5343 23.0855 47.9567C23.5051 48.3849 24.0669 48.621 24.6671 48.621C25.2485 48.621 25.798 48.3972 26.2201 47.9853L26.469 47.7328C26.5936 47.8309 26.7073 47.9191 26.8 47.991C26.8759 48.0482 26.9406 48.0973 26.9998 48.1399L27.0877 48.202L27.2122 48.2789C27.68 48.5354 28.1275 48.6466 28.4832 48.725C28.6605 48.7618 28.8125 48.7871 28.9253 48.8026L29.0626 48.8206L29.1055 48.8247L29.1218 48.8264L29.132 48.828L29.143 48.8288L29.1578 48.8296C29.1713 48.8296 29.1647 48.8313 29.2357 48.8329L29.3195 48.8296C30.0988 48.7725 30.8366 48.5043 31.4422 48.0419C31.6832 47.8572 31.8941 47.6341 32.0829 47.3899C32.4559 47.5108 32.8509 47.5827 33.262 47.5827C34.9294 47.5819 36.3326 46.5187 36.876 45.0397C36.9364 45.0356 36.9966 45.0331 37.0561 45.0177C37.2677 45.0561 37.4823 45.0839 37.7045 45.0839C39.3589 45.0822 40.7591 44.0396 41.3115 42.58C41.3153 42.5768 41.3189 42.5752 41.3225 42.5726L41.4524 42.585C43.5896 42.585 45.3197 40.8533 45.3205 38.7173C45.3218 38.124 45.2397 37.4678 45.0137 36.7855C46.4805 35.4895 48.6301 33.4873 49.0187 32.4005C49.2059 31.8775 50.258 30.6233 50.5636 30.1958L42.2877 18.7824C41.2322 19.3801 39.1839 20.65 38.0517 20.4604ZM42.9988 38.7168C42.9976 39.5708 42.3067 40.2621 41.4526 40.2637C41.241 40.2637 41.0433 40.2213 40.8591 40.1444C40.8407 40.1362 40.8214 40.1346 40.803 40.1288L39.3689 38.3841C39.0061 37.9641 38.3707 37.9168 37.9502 38.2795C37.5294 38.6423 37.4829 39.2782 37.8461 39.699L39.2332 41.3896C39.1445 42.1586 38.4982 42.7609 37.7046 42.7617C37.3279 42.7609 36.9937 42.6269 36.7204 42.4021L35.3439 40.7235C34.9806 40.3035 34.3452 40.257 33.9248 40.6198C33.5039 40.9826 33.4577 41.6185 33.8205 42.0384L34.7795 43.2332C34.7779 43.3075 34.7706 43.3819 34.7836 43.4564C34.8004 43.5503 34.8081 43.6352 34.8081 43.7137C34.8069 44.5685 34.1156 45.2589 33.262 45.2606C33.0389 45.2606 32.8335 45.2091 32.6439 45.1258L30.9662 43.3624C30.5613 42.9815 29.9248 43.0012 29.5439 43.4064C29.1627 43.811 29.1824 44.4484 29.5868 44.8283L30.3741 45.7517C30.2952 45.9209 30.1902 46.0737 30.0305 46.1978C29.8257 46.3548 29.5443 46.4626 29.224 46.4985C29.1305 46.4847 29.0033 46.4634 28.8521 46.4275C28.6646 46.3851 28.4668 46.3156 28.3643 46.2616C28.3288 46.2356 28.2381 46.1686 28.107 46.0654C28.0325 46.0082 27.948 45.943 27.8541 45.8677C28.024 45.1536 27.8409 44.3707 27.2898 43.8069C26.8702 43.3778 26.3084 43.1417 25.7081 43.1417C25.3706 43.1417 25.0471 43.225 24.7495 43.3698L24.7406 43.3624L24.7508 43.3509C25.1729 42.9375 25.4091 42.385 25.4155 41.7935C25.4216 41.2026 25.1973 40.6444 24.7839 40.2228C24.3646 39.7946 23.8025 39.5576 23.2027 39.5576C22.7336 39.5576 22.2891 39.7095 21.9139 39.9809C22.2175 39.5984 22.3937 39.1343 22.399 38.6383C22.4055 38.0475 22.1812 37.4893 21.768 37.0677C21.3485 36.6378 20.7867 36.4017 20.1865 36.4017C19.6051 36.4017 19.0555 36.6256 18.6371 37.0358L18.2641 37.4068C18.7242 36.5732 18.618 35.506 17.9193 34.7919C17.4993 34.3628 16.9375 34.1267 16.3372 34.1267C15.7559 34.1267 15.2062 34.3505 14.7871 34.7608L14.5403 35.0084C14.365 34.8637 14.1929 34.7224 14.0409 34.5973C13.4698 34.1282 13.0763 33.803 12.9182 33.6723C12.651 33.3675 12.4037 33.061 12.2105 32.7767C12.0029 32.476 11.8559 32.1973 11.7896 32.0085C11.6924 31.7356 11.5764 31.4791 11.4502 31.2356L18.7826 21.1248C19.5075 21.4279 20.3005 21.6559 21.1566 21.6617C21.3845 21.6617 21.6178 21.6453 21.8543 21.6062H21.8572C22.3454 21.5228 23.2157 21.4573 24.225 21.4591C24.8906 21.4575 25.6228 21.4852 26.3713 21.5457L26.4224 21.549C26.6062 21.5539 26.9123 21.6193 27.2841 21.7696L27.3682 21.8081C24.236 23.2225 19.7972 25.9439 18.3384 26.4308C16.6726 26.9857 16.5333 29.2075 20.2813 29.6234C24.03 30.041 27.3616 27.6794 28.1944 27.2643C28.7921 26.9653 32.5898 27.3305 34.6934 27.5609C35.6209 28.477 36.4339 29.3268 37.0268 29.9691L41.6467 35.6173L41.6671 35.6403C42.2591 36.3259 42.5803 36.8823 42.7625 37.3596C42.9429 37.8368 42.9976 38.2485 42.9988 38.7168Z" fill="#0A5C3A"/>
                                    <path d="M59.8407 26.9086L48.6766 11.5115C48.4065 11.138 47.886 11.0563 47.5133 11.326L42.5682 14.9118C42.1956 15.1815 42.1126 15.7028 42.3831 16.0755L53.5473 31.4726C53.8173 31.8453 54.3379 31.9286 54.7105 31.659L59.6564 28.0724C60.0284 27.8017 60.1117 27.2812 59.8407 26.9086ZM56.0421 28.292C55.4218 28.7415 54.5533 28.6026 54.1029 27.9823C53.653 27.3622 53.7916 26.4943 54.4126 26.044C55.0333 25.5938 55.9011 25.7327 56.3514 26.352C56.8013 26.9731 56.6627 27.8418 56.0421 28.292Z" fill="#0A5C3A"/>
                                    <path d="M17.4325 14.9118L12.4865 11.326C12.1146 11.0563 11.5937 11.138 11.3232 11.5115L0.158677 26.9088C-0.111791 27.2814 -0.0276507 27.802 0.344185 28.0724L5.28926 31.659C5.66192 31.9286 6.18235 31.8453 6.452 31.4726L17.6167 16.0755C17.8872 15.7028 17.8042 15.1815 17.4325 14.9118ZM14.1274 16.6312C13.6775 17.2515 12.8093 17.3903 12.1887 16.9401C11.5676 16.4907 11.4292 15.6212 11.8793 15.0018C12.3291 14.3807 13.1974 14.2419 13.8181 14.6922C14.4395 15.1423 14.5768 16.0101 14.1274 16.6312Z" fill="#0A5C3A"/>
                                </g>
                                <defs>
                                    <clipPath id="clip0_52_33">
                                        <rect width="60" height="60" fill="white"/>
                                    </clipPath>
                                </defs>
                            </svg>
                        </div>

                        <h3 class="mb-3 text-[20px] font-bold leading-[1.6] text-ink-2 sm:text-[21px]">
                            جلسات مشاوره فردی
                        </h3>

                        <p class="text-[15px] leading-[2] text-muted-3 sm:text-[13px]">در جلسات فردی، ریشه‌های اضطراب، خستگی و الگوهای تکراری در روابط‌تان را بررسی می‌کنیم.</p>
                    </article>

                    <!-- Family relationships -->
                    <article
                        class="flex h-full flex-col rounded-[24px] bg-cream p-6 shadow-[0_12px_28px_rgba(0,0,0,0.09)] sm:p-8">

                        <div class="mb-6 text-forest-8" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 64 64" fill="#0A5C3A" aria-hidden="true"><path d="M32 13C22 6 26-1 32 4c6-5 10 2 0 9Z"/><g><circle cx="15" cy="22" r="5"/><path d="M5 44c1-9 3-15 9-15h3c4 0 9-8 13-14 1-2 4-1 3 2-3 7-8 14-11 18v11l4 13c1 4-4 5-5 2l-6-13-3 13c-1 3-6 3-5-1l3-16v-7l-2 8c-1 3-4 2-3-1Z"/></g><g transform="translate(64 0) scale(-1 1)"><circle cx="15" cy="22" r="5"/><path d="M5 44c1-9 3-15 9-15h3c4 0 9-8 13-14 1-2 4-1 3 2-3 7-8 14-11 18v11l4 13c1 4-4 5-5 2l-6-13-3 13c-1 3-6 3-5-1l3-16v-7l-2 8c-1 3-4 2-3-1Z"/></g></svg>
                        </div>

                        <h3 class="mb-3 text-[20px] font-bold leading-[1.6] text-ink-2 sm:text-[21px]">
                            مشاوره زوجین و روابط
                        </h3>

                        <p class="text-[15px] leading-[2] text-muted-3 sm:text-[13px]">از فاصله‌ها و حرف‌های ناگفته شروع می‌کنیم تا نیازهای عاطفی یکدیگر را بهتر بشناسید.</p>
                    </article>

                    <!-- Depression support -->
                    <article
                        class="flex h-full flex-col rounded-[24px] bg-cream p-6 shadow-[0_12px_28px_rgba(0,0,0,0.09)] sm:p-8">

                        <div class="mb-6 text-forest-8" aria-hidden="true">
                            <svg width="58" height="58" viewBox="0 0 58 58" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M13.3941 30.187C11.4301 28.3262 3.5766 27.7236 1.21621 27.5838C0.915966 27.5815 0.592839 27.6778 0.363708 27.8931C0.132212 28.1109 0.00158691 28.4134 0.00158691 28.7296V49.3535C0.00158691 49.986 0.514958 50.4994 1.14746 50.4994H8.02249C8.51749 50.4994 8.95749 50.1809 9.11337 49.7088C9.28062 49.1954 13.2293 37.0778 13.7471 31.1197C13.7791 30.7714 13.6463 30.4276 13.3941 30.187Z" fill="#0A5C3A"/>
                                <path d="M33.5518 24.8592C33.7671 25.0815 34.0651 25.2098 34.3768 25.2098C34.6884 25.2098 34.9864 25.0815 35.2018 24.8592L45.9589 13.6811C47.3568 12.2328 48.1268 10.3056 48.1268 8.04609C48.1268 4.11841 45.4616 0.706223 41.9278 0.108129C38.7675 -0.43035 35.9007 1.10721 34.3768 3.5982C32.8528 1.10495 29.9836 -0.432605 26.8257 0.108129C23.292 0.706223 20.6267 4.11831 20.6267 8.25921C20.6267 10.3079 21.3967 12.2328 22.7923 13.6811L33.5518 24.8592Z" fill="#0A5C3A"/>
                                <path d="M54.8803 44.2573C53.3197 41.1384 49.7012 39.4702 46.3163 40.3181L41.1348 41.6128C41.2127 42.1375 41.2517 42.6875 41.2517 43.2627V43.6248C41.2517 45.2037 40.1791 46.5763 38.646 46.959L35.5614 47.729C34.7501 47.9329 33.9091 48.0361 33.0611 48.0361C32.3759 48.0361 31.6953 47.9696 31.0399 47.8367L21.0963 45.8477C20.4753 45.7239 20.0719 45.1212 20.1957 44.5002C20.3217 43.8769 20.9359 43.4805 21.5432 43.6019L31.4891 45.5909C32.6304 45.8201 33.8701 45.7903 35.0023 45.5084L38.0891 44.7385C38.6025 44.6079 38.96 44.1519 38.96 43.6248V43.2627C38.96 42.8433 38.9325 42.4515 38.8844 42.0756C38.8706 41.9679 38.8454 41.8716 38.8271 41.7663C38.7813 41.5004 38.7309 41.2392 38.6575 40.994C38.6254 40.8863 38.5865 40.7878 38.5498 40.6847C38.4695 40.4578 38.3801 40.2356 38.2725 40.027C38.2244 39.9354 38.1716 39.846 38.119 39.7566C37.9999 39.5526 37.8669 39.3579 37.718 39.1723C37.6584 39.0966 37.5965 39.021 37.5301 38.95C37.3651 38.7644 37.1817 38.5903 36.9847 38.4253C36.916 38.368 36.8495 38.3084 36.7762 38.2512C36.5562 38.0839 36.3133 37.9281 36.059 37.7814C35.9902 37.7424 35.9261 37.6967 35.8527 37.6599C35.5571 37.5018 35.2362 37.3621 34.8948 37.2291C34.8444 37.2107 34.8008 37.1879 34.7505 37.1672C34.3471 37.0182 33.914 36.8854 33.4488 36.7685L17.0244 33.1181C16.7172 33.0516 16.401 33.1112 16.1397 33.2853C15.8785 33.4594 15.702 33.7322 15.647 34.0393C14.822 38.7347 13.0322 44.8278 12.0399 48.0429C11.8565 48.6388 12.182 49.2712 12.7709 49.4682L25.9457 53.8611C27.7103 54.528 29.0807 55.0001 30.6986 55.0001C30.6986 55.0001 30.6986 55.0001 30.7008 55.0001C33.0521 55.0001 35.715 54.0033 41.7215 51.5421L54.3258 45.8132C54.6077 45.6848 54.8277 45.4466 54.9331 45.1532C55.0408 44.86 55.0202 44.5369 54.8803 44.2573Z" fill="#0A5C3A"/>
                            </svg>
                        </div>

                        <h3 class="mb-3 text-[20px] font-bold leading-[1.6] text-ink-2 sm:text-[21px]">
                            مواجهه با افسردگی و خستگی مفرط
                        </h3>

                        <p class="text-[15px] leading-[2] text-muted-3 sm:text-[13px]">با هم بررسی می‌کنیم پشت بی‌انگیزگی و خستگی چه احساس‌هایی مجال بیان پیدا نکرده‌اند.</p>
                    </article>

                    <!-- Trauma support -->
                    <article
                        class="flex h-full flex-col rounded-[24px] bg-cream p-6 shadow-[0_12px_28px_rgba(0,0,0,0.09)] sm:p-8">

                        <div class="mb-6 text-forest-8" aria-hidden="true">
                            <svg width="65" height="65" viewBox="0 0 65 65" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.5029 36.9607L16.0971 31.9391L20.698 25.5156H16.0971V23.2342L32.551 13.7391L49.0049 23.2342V29.2627L52.4971 24.394L56.0147 29.3264V19.194L32.551 5.646L9.08725 19.194V25.5156H4.32059L9.08725 32.1813L12.5029 36.9607Z" fill="#0A5C3A"/>
                                <path d="M56.0147 33.0353L52.4971 28.1157L49.0049 32.9844L44.302 39.5608H49.0049V42.2373L32.551 51.7324L16.0971 42.2373V35.7118L12.5029 40.7334L9.08725 35.9667V46.2902L32.551 59.8255L56.0147 46.2902V39.5608H60.6794L56.0147 33.0353Z" fill="#0A5C3A"/>
                                <path d="M21.6157 32.5C21.6157 38.5412 26.5098 43.4353 32.551 43.4353C38.5922 43.4353 43.4863 38.5412 43.4863 32.5C43.4863 26.4588 38.5922 21.5647 32.551 21.5647C26.5098 21.5647 21.6157 26.4588 21.6157 32.5ZM38.1843 31.2255V33.7745H33.8255V38.1333H31.2765V33.7745H26.9177V31.2255H31.2765V26.8667H33.8255V31.2255H38.1843Z" fill="#0A5C3A"/>
                            </svg>
                        </div>

                        <h3 class="mb-3 text-[20px] font-bold leading-[1.6] text-ink-2 sm:text-[21px]">
                            تجربه‌های آسیب‌زا (تروما)
                        </h3>

                        <p class="text-[15px] leading-[2] text-muted-3 sm:text-[13px]">با سرعتی متناسب با آمادگی شما، بدون فشار برای بازگویی خاطرات، به اثر تجربه‌های گذشته می‌پردازیم.</p>
                    </article>
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
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120" width="120" height="120" fill="none" aria-hidden="true"><circle cx="60" cy="60" r="51" fill="#F4F4EA"/><g stroke="#295948" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><rect x="27" y="22" width="66" height="75" rx="10" fill="#FFFDF7"/><path d="M28 37h64"/><circle cx="60" cy="54" r="8" fill="#E8C486"/><path d="M43 78c1-17 33-17 34 0z" fill="#A1C2AA"/><rect x="66" y="77" width="36" height="22" rx="9" fill="#295948"/><path d="M76 88h15m-5-5 5 5-5 5" stroke="white"/></g></svg>
                                </div>
                                <h3 class="mb-4 text-[18px] font-bold text-ink">وارد حساب کاربری شوید</h3>
                                <p class="text-[14px] leading-7 text-muted">برای ثبت و پیگیری رزرو، ابتدا وارد حساب کاربری‌تان شوید.</p>
                            </article>
                        </li>

                        <li class="flex h-full flex-col items-center">
                            <div class="relative z-10 mb-[-1.25rem] flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gold font-bold text-white shadow-sm">۲</div>
                            <article class="flex h-full w-full flex-col items-center rounded-[30px] border border-white/50 bg-white px-6 pb-8 pt-12 text-center shadow-sm">
                                <div class="mb-6 flex h-[120px] w-[120px] shrink-0 items-center justify-center" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120" width="120" height="120" fill="none" aria-hidden="true"><circle cx="60" cy="60" r="51" fill="#F4F4EA"/><g stroke="#295948" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><rect x="22" y="28" width="68" height="63" rx="9" fill="#FFFDF7"/><path d="M23 46h66"/><path d="M38 22v13m34-13v13"/><path d="M34 56h7m8 0h7m8 0h7m-37 12h7m8 0h7" stroke="#A1C2AA"/><rect x="33" y="74" width="11" height="9" rx="2" fill="#E8C486" stroke="none"/><circle cx="83" cy="80" r="17" fill="#DDE9DB"/><path d="M83 70v10l7 4"/></g></svg>
                                </div>
                                <h3 class="mb-4 text-[18px] font-bold text-ink">زمان جلسه را انتخاب کنید</h3>
                                <p class="text-[14px] leading-7 text-muted">از میان زمان‌های موجود، تاریخ و ساعت مناسبتان را انتخاب کنید.</p>
                            </article>
                        </li>

                        <li class="flex h-full flex-col items-center">
                            <div class="relative z-10 mb-[-1.25rem] flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gold font-bold text-white shadow-sm">۳</div>
                            <article class="flex h-full w-full flex-col items-center rounded-[30px] border border-white/50 bg-white px-6 pb-8 pt-12 text-center shadow-sm">
                                <div class="mb-6 flex h-[120px] w-[120px] shrink-0 items-center justify-center" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120" width="120" height="120" fill="none" aria-hidden="true"><circle cx="60" cy="60" r="51" fill="#F4F4EA"/><g stroke="#295948" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><rect x="48" y="29" width="53" height="41" rx="5" fill="#FFFDF7"/><rect x="61" y="41" width="16" height="14" rx="3" fill="#A1C2AA"/><path d="m77 46 9-5v14l-9-5z" fill="#E8C486"/><path d="M75 71v6m-9 1h18"/><rect x="21" y="53" width="34" height="29" rx="8" fill="#A1C2AA"/><path d="M18 68q-5 0-5 5v10q0 6 6 6h38q6 0 6-6V73q0-6-6-6t-6 7v4H25v-4q0-6-7-6z" fill="#DDE9DB"/><path d="m22 90-2 6m35-6 2 6"/></g></svg>
                                </div>
                                <h3 class="mb-4 text-[18px] font-bold text-ink">نوع جلسه را انتخاب کنید</h3>
                                <p class="text-[14px] leading-7 text-muted">جلسه حضوری در مرکز مشاوره در تبریز یا جلسه آنلاین را انتخاب کنید و نوبت خودتون رو ثبت کنید.</p>
                            </article>
                        </li>

                        <li class="flex h-full flex-col items-center">
                            <div class="relative z-10 mb-[-1.25rem] flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gold font-bold text-white shadow-sm">۴</div>
                            <article class="flex h-full w-full flex-col items-center rounded-[30px] border border-white/50 bg-white px-6 pb-8 pt-12 text-center shadow-sm">
                                <div class="mb-6 flex h-[120px] w-[120px] shrink-0 items-center justify-center" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120" width="120" height="120" fill="none" aria-hidden="true"><circle cx="60" cy="60" r="51" fill="#F4F4EA"/><g stroke="#295948" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><rect x="29" y="27" width="58" height="69" rx="8" fill="#FFFDF7"/><rect x="44" y="22" width="28" height="11" rx="5" fill="#E8C486"/><path d="m39 47 3 3 5-6m-8 17 3 3 5-6"/><path d="M54 48h21M54 62h16M40 78h15" stroke="#A1C2AA"/><circle cx="82" cy="81" r="17" fill="#295948"/><path d="m74 81 5 5 10-11" stroke="#FFFDF7"/></g></svg>
                                </div>
                                <h3 class="mb-4 text-[18px] font-bold text-ink">برای جلسه آماده شوید</h3>
                                <p class="text-[14px] leading-7 text-muted">برای جلسه حضوری در زمان رزروشده به مرکز مراجعه کنید. جزئیات جلسه آنلاین نیز پس از ثبت رزرو در اختیارتان قرار می‌گیرد.</p>
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
                        <p class="text-[16px] text-muted">اطلاعات حساب و رزرو شما برای ثبت و هماهنگی جلسات استفاده می‌شود. محتوای جلسات کاملا محرمانه است</p>
                    </div>

                    <div class="mb-8 flex w-full items-center justify-end gap-4">
                        <a href="{{ route('booking.index') }}" class="flex items-center gap-3 rounded-xl bg-forest-2 px-10 py-4 font-bold text-white transition-all hover:bg-forest-1">
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


        <section class="insight bg-white" dir="rtl" aria-labelledby="approach-heading">
            <div><div>
                    <svg class="insight-dialog" xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64" fill="none" aria-hidden="true">
                        <g stroke="#295948" stroke-width="2.4" stroke-linejoin="round" stroke-linecap="round">
                            <path d="M31 29h16a10 10 0 0 1 10 10v4a10 10 0 0 1-7 9v6l-10-6h-9a10 10 0 0 1-10-10v-3a10 10 0 0 1 10-10Z" fill="#DCE9DF"/>
                            <path d="M18 9h17a12 12 0 0 1 12 12v7a12 12 0 0 1-12 12H21l-11 8 1-10a12 12 0 0 1-5-10v-7A12 12 0 0 1 18 9Z" fill="#FAF5E8"/>
                            <path d="M20 32c2-8 7-13 13-16"/>
                            <path d="M23 26c-1-8 5-12 13-12 0 8-4 14-13 12Z" fill="#A1C2AA"/>
                        </g>
                    </svg>
                    <h2 id="approach-heading" class="text-forest-2 font-bold">همراهی در مسیر شناخت</h2>
                    <div><p class="text-muted">درمان، سفری است برای دیدنِ لایه‌های پنهانِ وجود. در این مسیر، من به عنوان روان‌درمانگر، فضایی امن برای واکاوی چالش‌ها و گره‌های درونی فراهم می‌کنم تا بتوانیم با هم، الگوهای تکراری را بشناسیم و به سمت انتخاب‌های آگاهانه‌تر حرکت کنیم.</p>
                        <a class="text-forest-2" href="{{ route('website.about') }}">آشنایی با مشاور <span aria-hidden="true">←</span></a>
                    </div>
                </div></div>
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
                        <details open name="home-faq" class="home-question rounded-2xl border border-gold/40 bg-cream">
                            <summary class="home-question-label flex w-full items-start gap-3 px-4 py-3 text-right">
                                <span class="question-sign mt-0.5 text-lg leading-none text-ink" aria-hidden="true"></span>
                                <span class="text-[14px] font-medium leading-6 text-ink">
                جلسات چقدر طول می‌کشد و چه زمانی می‌توانم منتظر نتیجه باشم؟
            </span>
                            </summary>
                            <div class="home-response">
                                <div>
                                    <p class="px-4 pb-3 pr-10 text-[13px] leading-6 text-muted">
                                        روان‌درمانی یک مسیرِ فرموله‌شده با تعداد جلساتِ ثابت نیست که بگوییم دقیقاً در چند جلسه نتیجه می‌گیرید. سرعتِ پیشروی به عمقِ مسئله، آمادگی درونی شما برای مواجهه با ابعاد پنهانِ روان و پیچیدگیِ موضوع بستگی دارد. هدف ما تسکینِ موقتِ علائم نیست، بلکه ایجادِ تغییرات پایدار از طریق شناختِ ریشه‌هاست؛ بنابراین زمانِ موردنیاز برای هر فرد متفاوت <است class=""></است>                                    </p>
                                </div>
                            </div>
                        </details>

                        <details name="home-faq" class="home-question rounded-2xl border border-gold/40 bg-cream">
                            <summary class="home-question-label flex w-full items-start gap-3 px-4 py-3 text-right">
                                <span class="question-sign mt-0.5 text-lg leading-none text-ink" aria-hidden="true"></span>
                                <span class="text-[14px] font-medium leading-6 text-ink">
                از کجا بفهمم که به مشاوره نیاز دارم یا خودم از پسِ حل مسئله برمی‌آیم؟
            </span>
                            </summary>
                            <div class="home-response">
                                <div>
                                    <p class="px-4 pb-3 pr-10 text-[13px] leading-6 text-muted">
                                        تلاش برای حل مسائلِ شخصی با اتکا به توان خود ارزشمند است، اما گاهی ما در یک «چرخهٔ تکراری» گیر می‌افتیم و هرچه تلاش می‌کنیم، مسیرِ اشتباه را طی می‌کنیم. اگر احساس می‌کنید یک رنج، اضطراب یا تعارضِ فکری ماه‌هاست پابرجا است و با استدلال‌های شخصی حل نمی‌شود، یا هر بار به نقطه‌ی اول برمی‌گردید، مشاوره کمک می‌کند تا از بیرون به مسئله نگاه کنید و گره‌های کور را ببینید.                                    </p>
                                </div>
                            </div>
                        </details>

                        <details name="home-faq" class="home-question rounded-2xl border border-gold/40 bg-cream">
                            <summary class="home-question-label flex w-full items-start gap-3 px-4 py-3 text-right">
                                <span class="question-sign mt-0.5 text-lg leading-none text-ink" aria-hidden="true"></span>
                                <span class="text-[14px] font-medium leading-6 text-ink">
حریم خصوصی و امنیت اطلاعات من چقدر تضمین شده است؟                                </span>
                            </summary>
                            <div class="home-response">
                                <div>
                                    <p class="px-4 pb-3 pr-10 text-[13px] leading-6 text-muted">
                                        رازداری، اصلی‌ترین ستونِ رابطه‌ی درمانی است. ما کاملاً بر اساس نظام‌نامهٔ اخلاقی روان‌شناسان و مشاوران موظف و متعهد به رازداری هستیم. آنچه در جلسات مطرح می‌کنید، از مسائل شخصی و خانوادگی گرفته تا احساسات، نگرانی‌ها و تجربه‌های گذشته، محرمانه باقی می‌ماند و بدون رضایت شما با فرد دیگری در میان گذاشته نمی‌شود.                                </div>
                            </div>
                        </details>

                        <details name="home-faq" class="home-question rounded-2xl border border-gold/40 bg-cream">
                            <summary class="home-question-label flex w-full items-start gap-3 px-4 py-3 text-right">
                                <span class="question-sign mt-0.5 text-lg leading-none text-ink" aria-hidden="true"></span>
                                <span class="text-[14px] font-medium leading-6 text-ink">
                آیا جلسات آنلاین به اندازه‌ی جلسات حضوری اثربخش است؟
            </span>
                            </summary>
                            <div class="home-response">
                                <div>
                                    <p class="px-4 pb-3 pr-10 text-[13px] leading-6 text-muted">
                                        بله. تجربیاتِ بالینی نشان می‌دهد که در جلسات آنلاین، به دلیلِ راحتیِ مراجع در فضای شخصی و حذفِ دغدغه‌های رفت‌وآمد، می‌توان تعاملاتِ بسیار عمیقی داشت. تا زمانی که بسترِ ارتباطی باکیفیت و محیطی آرام برای شما فراهم باشد، اثربخشیِ گفت‌وگو از طریقِ شنیدنِ دقیق و شناختِ تعارضات، تفاوتی با جلسات حضوری ندارد.                                    </p>
                                </div>
                            </div>
                        </details>

                        <div class="pt-4 text-left">
                            <a href="{{ route('website.faq') }}" class="inline-flex items-center gap-2 rounded-xl bg-forest-2 px-5 py-4 text-[13px] font-medium text-white transition-all hover:bg-forest-1">
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
                                برای شروع مشاوره و انتخاب زمان مناسب، می‌توانید از طریق دکمه پایین همین بخش اقدام کنید.                            </p>

                            <div class="appointment-form">
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
