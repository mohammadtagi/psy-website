<header
    class="hero-header relative z-40 h-[72px] border-b border-white/10 sm:h-[88px] lg:h-[100px] lg:border-none"
    x-data="{
            open: false,
            opener: null,
            closeMenu() {
                this.open = false;

                this.$nextTick(() => {
                    if (this.opener) {
                        this.opener.focus();
                    }
                });
            }
        }"
    x-effect="document.body.classList.toggle('menu-open', open)"
    @keydown.escape.window="if (open) closeMenu()"
>
    <!-- نوار اصلی هدر -->
    <div
        class="flex h-full items-center justify-between gap-3 sm:gap-4 lg:gap-6"
        style="direction: ltr;"
    >
        <!-- دکمه منوی موبایل -->
        <button
            type="button"
            class="order-1 inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-white/25 bg-white/5 text-white shadow-sm backdrop-blur-sm transition-[background-color,border-color,transform] duration-200 ease-out hover:-translate-y-0.5 hover:border-white/40 hover:bg-white/15 focus:outline-none focus-visible:ring-2 focus-visible:ring-gold focus-visible:ring-offset-2 focus-visible:ring-offset-transparent md:hidden"
            @click="
        opener = $event.currentTarget;
        open = !open;
    "
            :aria-expanded="open.toString()"
            aria-controls="mobile-nav"
            :aria-label="open ? 'بستن منوی اصلی' : 'باز کردن منوی اصلی'"
        >

            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="1.8"
                aria-hidden="true"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M4 7h16M4 12h16M4 17h16"
                />
            </svg>
        </button>

        <!-- دکمه رزرو نوبت دسکتاپ -->
        <a
            href="{{ route('booking.index') }}"
            class="order-1 hidden h-10 shrink-0 items-center justify-center rounded-full bg-forest-6 px-4 text-[13px] font-medium text-white shadow-sm transition hover:bg-forest-7 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-gold focus-visible:ring-offset-2 focus-visible:ring-offset-transparent sm:h-[48px] sm:min-w-[143px] sm:px-6 sm:text-[15px] md:order-none md:inline-flex"
        >
            رزرو نوبت
        </a>

        <!-- منوی دسکتاپ -->
        <nav
            class="hidden flex-1 items-center justify-center gap-4 md:flex lg:justify-start lg:gap-5"
            dir="rtl"
            aria-label="منوی اصلی"
        >
            <a
                href="{{ url('/') }}"
                class="whitespace-nowrap text-[13px] text-white/90 transition-[color,transform] duration-200 ease-out hover:-translate-y-0.5 hover:text-gold focus:outline-none focus-visible:text-gold focus-visible:underline focus-visible:underline-offset-8 lg:text-[15px]"
            >
                خانه
            </a>

            <a
                href="{{ url('/services') }}"
                @if(request()->is('services', 'services/*')) aria-current="page" @endif
                class="relative whitespace-nowrap text-[13px] font-semibold text-white transition focus:outline-none focus-visible:text-gold focus-visible:underline focus-visible:underline-offset-8 lg:text-[15px]"
            >
                خدمات

                @if(request()->is('services', 'services/*'))
                    <span
                        class="absolute -bottom-3 right-0 left-0 mx-auto hidden h-0.5 rounded-full bg-gold lg:block"
                        aria-hidden="true"
                    ></span>
                @endif
            </a>

            <a
                href="{{ url('/about-us') }}"
                class="whitespace-nowrap text-[13px] text-white/90 transition-[color,transform] duration-200 ease-out hover:-translate-y-0.5 hover:text-gold focus:outline-none focus-visible:text-gold focus-visible:underline focus-visible:underline-offset-8 lg:text-[15px]"
            >
                درباره ما
            </a>

            <a
                href="{{ url('/contact-us') }}"
                class="whitespace-nowrap text-[13px] text-white/90 transition-[color,transform] duration-200 ease-out hover:-translate-y-0.5 hover:text-gold focus:outline-none focus-visible:text-gold focus-visible:underline focus-visible:underline-offset-8 lg:text-[15px]"
            >
                تماس با ما
            </a>
            <a href="{{ url('/faq') }}" class="whitespace-nowrap text-[13px] text-white/90 transition hover:text-gold lg:text-[15px]">سؤالات متداول</a>
            <a href="{{ url('/blog') }}" class="whitespace-nowrap text-[13px] text-white/90 transition hover:text-gold lg:text-[15px]">وبلاگ</a>
            @guest
                <a href="{{ route('auth.login') }}" class="whitespace-nowrap text-[13px] text-white/90 transition hover:text-gold lg:text-[15px]">ورود</a>
            @endguest
        </nav>

        <!-- لوگو و نام -->
        <a
            href="{{ url('/') }}"
            class="order-2 flex min-w-0 shrink items-center gap-2 rounded-2xl px-1 py-1 text-white transition-[background-color,transform] duration-200 ease-out hover:-translate-y-0.5 hover:bg-white/5 focus:outline-none focus-visible:ring-2 focus-visible:ring-gold focus-visible:ring-offset-2 focus-visible:ring-offset-transparent sm:gap-4 md:order-none"
            dir="ltr"
            aria-label="صفحه اصلی کریم محمدزاده"
        >
            <span
                class="truncate text-[13px] font-semibold sm:whitespace-nowrap sm:text-[17px] lg:text-[20px]"
            >
                <span class="lg:hidden">محمدزاده</span>
                <span class="hidden lg:inline">کریم محمدزاده</span>
            </span>

            <span
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border-2 border-cream bg-cream shadow-md sm:h-[58px] sm:w-[58px]"
            >
                <img
                    src="{{ asset('website/images/logo.png') }}"
                    alt="لوگوی کریم محمدزاده"
                    width="53"
                    height="53"
                    class="h-9 w-9 object-contain sm:h-[53px] sm:w-[53px]"
                />
            </span>
        </a>
    </div>

    <!-- منوی تمام‌صفحه موبایل -->
    <div
        id="mobile-nav"
        x-cloak
        x-show="open"
        x-transition:enter="transform-gpu transition-[opacity,transform] duration-300 ease-out"
        x-transition:enter-start="translate-x-6 opacity-0"
        x-transition:enter-end="translate-x-0 opacity-100"
        x-transition:leave="transform-gpu transition-[opacity,transform] duration-200 ease-in"
        x-transition:leave-start="translate-x-0 opacity-100"
        x-transition:leave-end="translate-x-6 opacity-0"
        class="fixed inset-0 z-50 min-h-screen overflow-y-auto bg-forest md:hidden"
        dir="rtl"
        role="dialog"
        aria-modal="true"
        aria-labelledby="mobile-menu-title"
        @click.self="closeMenu()"
    >

        <div class="flex min-h-screen flex-col px-5 py-5 sm:px-8">
            <!-- سربرگ منوی موبایل -->
            <div
                class="flex items-center justify-between border-b border-white/10 pb-5"
                dir="rtl"
            >
                <!-- لوگو و نام -->
                <a
                    href="{{ url('/') }}"
                    class="flex items-center gap-3 rounded-xl text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-gold focus-visible:ring-offset-2 focus-visible:ring-offset-forest"
                    @click="closeMenu()"
                >
                    <span
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full border-2 border-cream bg-cream shadow-md"
                    >
                        <img
                            src="{{ asset('website/images/logo.png') }}"
                            alt="لوگوی کریم محمدزاده"
                            width="44"
                            height="44"
                            class="h-10 w-10 object-contain"
                        />
                    </span>

                    <span
                        id="mobile-menu-title"
                        class="whitespace-nowrap text-[16px] font-semibold"
                    >
                        کریم محمدزاده
                    </span>
                </a>

                <!-- دکمه بستن -->
                <button
                    type="button"
                    class="inline-flex p-2 h--0 items-center justify-center rounded-full border justify-center rounded-full border border-white/20 text-white transition-[background-color,transform] duration-200 ease-out hover:rotate-90 hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-gold focus-visible:ring-offset-2 focus-visible:ring-offset-forest"
                    @click="closeMenu()"
                    aria-label="بستن منوی اصلی"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6 6l12 12M18 6L6 18"
                        />
                    </svg>
                </button>
            </div>

            <!-- لینک‌های منوی موبایل -->
            <nav
                class="mt-8 flex flex-col gap-2"
                aria-label="منوی موبایل"
            >
                <a
                    href="{{ url('/') }}"
                    class="rounded-xl px-4 py-4 text-[17px] text-white transition-[background-color,color,transform] duration-200 ease-out hover:-translate-x-1 hover:bg-white/10 hover:text-gold focus:outline-none focus-visible:bg-white/10 focus-visible:text-gold"
                    @click="closeMenu()"
                >
                    خانه
                </a>

                <a
                    href="{{ url('/services') }}"
                    @if(request()->is('services', 'services/*')) aria-current="page" @endif
                    class="rounded-xl bg-white/10 px-4 py-4 text-[17px] font-semibold text-white transition-[background-color,transform] duration-200 ease-out hover:-translate-x-1 hover:bg-white/15 focus:outline-none focus-visible:ring-2 focus-visible:ring-gold"
                    @click="closeMenu()"
                >
                    خدمات
                </a>

                <a
                    href="{{ url('/about-us') }}"
                    class="rounded-xl px-4 py-4 text-[17px] text-white transition-[background-color,color,transform] duration-200 ease-out hover:-translate-x-1 hover:bg-white/10 hover:text-gold focus:outline-none focus-visible:bg-white/10 focus-visible:text-gold"
                    @click="closeMenu()"
                >
                    درباره ما
                </a>

                <a
                    href="{{ url('/contact-us') }}"
                    class="rounded-xl px-4 py-4 text-[17px] text-white transition-[background-color,color,transform] duration-200 ease-out hover:-translate-x-1 hover:bg-white/10 hover:text-gold focus:outline-none focus-visible:bg-white/10 focus-visible:text-gold"
                    @click="closeMenu()"
                >
                    تماس با ما
                </a>
                <a href="{{ url('/faq') }}" class="rounded-xl px-4 py-4 text-[17px] text-white transition hover:bg-white/10 hover:text-gold" @click="closeMenu()">سؤالات متداول</a>
                <a href="{{ url('/blog') }}" class="rounded-xl px-4 py-4 text-[17px] text-white transition hover:bg-white/10 hover:text-gold" @click="closeMenu()">وبلاگ</a>
                @guest
                    <a href="{{ route('auth.login') }}" class="rounded-xl px-4 py-4 text-[17px] text-white transition hover:bg-white/10 hover:text-gold" @click="closeMenu()">ورود</a>
                @endguest
            </nav>

            <!-- اطلاعات تماس کوتاه -->
            <div class="mt-8 border-t border-white/10 pt-6 text-sm leading-7 text-white/70">
                <p class="m-0">
                    برای شروع روند مشاوره با ما در ارتباط باشید.
                </p>

                <a
                    href="tel:+984134244933"
                    dir="ltr"
                    class="mt-2 inline-block text-left text-white transition hover:text-gold focus:outline-none focus-visible:text-gold focus-visible:underline"
                >
                    +98 41 3424 4933
                </a>
            </div>

            <!-- دکمه رزرو نوبت -->
            <a
                href="{{ route('booking.index') }}"
                class="mt-auto flex h-14 w-full items-center justify-center rounded-full bg-cream-2 text-[15px] font-medium text-forest transition hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-gold focus-visible:ring-offset-2 focus-visible:ring-offset-forest"
                @click="closeMenu()"
            >
                درخواست وقت مشاوره
            </a>
        </div>
    </div>
</header>
