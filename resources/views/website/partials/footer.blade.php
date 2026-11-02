<footer class="bg-forest-2/15 text-forest-2" aria-label="پاورقی سایت">
    <div class="layout relative grid min-h-[420px] grid-cols-1 items-start gap-[38px] py-[42px] pb-7 md:min-h-0 md:grid-cols-2 md:gap-x-[70px] md:gap-y-[54px] md:py-[58px] md:pb-[74px] lg:min-h-[420px] lg:grid-cols-4 lg:gap-x-[30px] lg:gap-y-0 lg:pb-0 lg:pt-[72px]">
        <section class="min-w-0" aria-labelledby="footer-brand-title">
            <a href="#home" aria-label="صفحه اصلی" class="mb-4 flex min-h-[25px] min-w-0 items-start gap-2 sm:gap-4">
                <img
                    src="{{ asset('website/images/logo.png') }}"
                    width="53"
                    height="53"
                    alt="لوگوی کریم محمدزاده"
                    class="relative -mt-3.5 h-9 w-9 shrink-0 object-contain sm:h-[53px] sm:w-[53px]"
                >
                <span id="footer-brand-title" class="truncate text-[20px] font-semibold leading-[1.4] sm:whitespace-nowrap">
                    <span class="lg:hidden"> محمدزاده</span>
                    <span class="hidden lg:inline"> کریم محمدزاده</span>
                </span>
            </a>

            <p class="m-0 max-w-[230px] text-[13px] leading-[2] text-muted">
                اگر درباره جلسات یا روند شروع درمان سؤالی دارید، در ساعات کاری با ما تماس بگیرید.
                شنبه تا چهارشنبه، ۸ تا ۱۶
                <br>
                <a href="https://my.pcoiran.ir/member/51308/54d371a1-92cb-4157-b65f-efc840f56fcd/" rel="noopener noreferrer">
                    شماره پروانه اشتغال سازمان نظام روان‌شناسی و مشاوره: ۵۱۳۰۸
                </a>
            </p>
        </section>

        <nav class="min-w-0" aria-labelledby="footer-services-title">
            <h2 id="footer-services-title" class="m-0 mb-[25px] whitespace-nowrap text-[18px] font-bold leading-[1.4] text-forest-2 max-md:mb-[14px]">
                خدمات
            </h2>

            <ul class="m-0 list-none p-0 text-[14px] leading-[2.25] text-muted">
                <li>
                    <a href="{{ url('/services/individual-therapy') }}" class="whitespace-nowrap no-underline transition hover:text-forest-2 hover:underline hover:underline-offset-4 focus-visible:text-forest-2 focus-visible:underline focus-visible:underline-offset-4">
                        مشاوره فردی
                    </a>
                </li>
                <li>
                    <a href="{{ url('/services/couples-therapy') }}" class="whitespace-nowrap no-underline transition hover:text-forest-2 hover:underline hover:underline-offset-4 focus-visible:text-forest-2 focus-visible:underline focus-visible:underline-offset-4">
                        مشاوره روابط و زوجین
                    </a>
                </li>
                <li>
                    <a href="{{ url('/services/depression-counseling') }}" class="whitespace-nowrap no-underline transition hover:text-forest-2 hover:underline hover:underline-offset-4 focus-visible:text-forest-2 focus-visible:underline focus-visible:underline-offset-4">
                        حمایت در مواجهه با افسردگی
                    </a>
                </li>
                <li>
                    <a href="{{ url('/services/trauma-therapy') }}" class="whitespace-nowrap no-underline transition hover:text-forest-2 hover:underline hover:underline-offset-4 focus-visible:text-forest-2 focus-visible:underline focus-visible:underline-offset-4">
                        تروما و تجربه‌های آسیب‌زا
                    </a>
                </li>
            </ul>
        </nav>

        <nav class="min-w-0" aria-labelledby="footer-quick-title">
            <h2 id="footer-quick-title" class="m-0 mb-[25px] whitespace-nowrap text-[18px] font-bold leading-[1.4] text-forest-2 max-md:mb-[14px]">
                دسترسی سریع
            </h2>

            <ul class="m-0 list-none p-0 text-[14px] leading-[2.25] text-muted">
                <li>
                    <a href="{{ url('/') }}" class="whitespace-nowrap no-underline transition hover:text-forest-2 hover:underline hover:underline-offset-4 focus-visible:text-forest-2 focus-visible:underline focus-visible:underline-offset-4">
                        صفحه اصلی
                    </a>
                </li>
                <li>
                    <a href="{{ url('/services') }}" class="whitespace-nowrap no-underline transition hover:text-forest-2 hover:underline hover:underline-offset-4 focus-visible:text-forest-2 focus-visible:underline focus-visible:underline-offset-4">
                        خدمات ما
                    </a>
                </li>
                <li>
                    <a href="{{ url('/about-us') }}" class="whitespace-nowrap no-underline transition hover:text-forest-2 hover:underline hover:underline-offset-4 focus-visible:text-forest-2 focus-visible:underline focus-visible:underline-offset-4">
                        درباره ما
                    </a>
                </li>
                <li>
                    <a href="{{ url('/blog') }}" class="whitespace-nowrap no-underline transition hover:text-forest-2 hover:underline hover:underline-offset-4 focus-visible:text-forest-2 focus-visible:underline focus-visible:underline-offset-4">
                        وبلاگ
                    </a>
                </li>
            </ul>
        </nav>

        <section class="min-w-0" aria-labelledby="footer-contact-title">
            <h2 id="footer-contact-title" class="m-0 mb-[25px] whitespace-nowrap text-[18px] font-bold leading-[1.4] text-forest-2 max-md:mb-[14px]">
                تماس با ما
            </h2>

            <address class="grid gap-[17px] text-[14px] not-italic text-muted max-md:gap-[13px]">
                <div class="flex min-w-0 items-center gap-[10px]">
                    <svg class="h-5 w-5 shrink-0 fill-none stroke-forest-2 stroke-[1.7]" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M4 5.5A2.5 2.5 0 0 1 6.5 3h11A2.5 2.5 0 0 1 20 5.5v8a2.5 2.5 0 0 1-2.5 2.5H10l-4.5 4v-4.1A2.5 2.5 0 0 1 4 13.5v-8Z"/>
                        <path d="m7 6 5 4 5-4"/>
                    </svg>
                    <a href="mailto:contact@karimmohammadzadeh.ir" class="min-w-0 overflow-wrap-anywhere text-inherit no-underline transition hover:text-forest-2 hover:underline hover:underline-offset-4 focus-visible:text-forest-2 focus-visible:underline focus-visible:underline-offset-4">
                        contact@karimmohammadzadeh.ir
                    </a>
                </div>

                <div class="flex min-w-0 items-center gap-[10px]">
                    <svg class="h-5 w-5 shrink-0 fill-none stroke-forest-2 stroke-[1.7]" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M6.6 3.8 9 3l2 5-2.2 1.5a14.3 14.3 0 0 0 5.7 5.7L16 13l5 2-.8 2.4a3 3 0 0 1-3.2 2.1C10.4 18.8 5.2 13.6 4.5 7a3 3 0 0 1 2.1-3.2Z"/>
                    </svg>
                    <a href="tel:+984134244933" class="min-w-0 overflow-wrap-anywhere text-inherit no-underline transition hover:text-forest-2 hover:underline hover:underline-offset-4 focus-visible:text-forest-2 focus-visible:underline focus-visible:underline-offset-4" dir="ltr">
                        +98 41 3424 4933
                    </a>
                </div>

                <div class="flex min-w-0 items-center gap-[10px]">
                    <svg class="h-5 w-5 shrink-0 fill-none stroke-forest-2 stroke-[1.7]" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 21s7-6.1 7-11.5A7 7 0 0 0 5 9.5C5 14.9 12 21 12 21Z"/>
                        <circle cx="12" cy="9.5" r="2.2"/>
                    </svg>
                    <span>تبریز، آبرسان، طبقه اول ساختمان بانک سپه</span>
                </div>
            </address>
        </section>

        <div class="border-t border-muted/65 pt-4 text-center text-[12px] text-muted max-md:mt-1 md:absolute md:bottom-6 md:left-0 md:right-0 md:mt-0 md:pt-[17px] lg:bottom-6">
            © ۲۰۲۶ تمامی حقوق برای کریم محمدزاده محفوظ است.
        </div>
    </div>
</footer>
