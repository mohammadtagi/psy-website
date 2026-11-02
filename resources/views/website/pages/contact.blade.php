@extends('website.layouts.app')

@section('title', 'تماس با ما | کریم محمدزاده')
@section('description', 'برای شروع درمان، انتخاب نوع جلسه، رزرو نوبت یا هماهنگی زمان با کریم محمدزاده در تماس باشید.')
@section('canonical', route('website.contact'))
@section('social_image_alt', 'تصویر صفحه تماس با کریم محمدزاده')

@push('styles')
    <link rel="stylesheet" href="{{ asset('website/style/contact.css') }}">
@endpush

@section('content')
    <div class="hero-bg min-h-screen text-white">
        <div class="layout">

            @include('website.partials.header')

            <section
                class="layout flex min-h-[555px] items-center justify-center text-center"
                aria-labelledby="page-title"
            >
                <div class="max-w-[580px]">
                    <h1
                        id="page-title"
                        class="text-4xl leading-[1.4!important] font-bold tracking-tight md:text-5xl"
                    >
                        تماس با ما </h1>

                    <p class="mt-6 text-sm leading-8 text-white/90">
                        اگر برای شروع درمان، نوع جلسه، یا هماهنگی زمان سؤال دارید، از همین‌جا پیام بفرستید. پاسخ‌گویی در
                        ساعات کاری انجام می‌شود و پیام‌ها با احترام به حریم خصوصی شما بررسی می‌شوند.
                    </p>
                </div>
            </section>

        </div>
    </div>

    <main id="main-content">

        <section
            id="contact-form"
            aria-labelledby="contact-form-title"
            class="contact-section py-20 sm:py-24"
        >
            <div
                class="layout grid items-start gap-12 md:grid-cols-12 md:gap-8 lg:gap-20"
            >
                <div
                    class="order-2 min-w-0 w-full md:col-span-5"
                >
                    <div class="contact-form-card w-full" aria-describedby="contact-form-notice">
                        <div class="contact-form-content p-6 sm:p-8">
                            <div class="mb-8">
                                <h2 class="text-[24px] font-semibold leading-[1.8] text-white">
                                    پیام شما را می‌خوانیم
                                </h2>

                                <p class="mt-2 text-[12px] leading-7 text-white/65">
                                    اطلاعات شما فقط برای پاسخ‌گویی به همین درخواست استفاده می‌شود.
                                </p>
                            </div>

                            <p id="contact-form-notice" class="mb-6 text-sm leading-7 text-white">فرم ارسال پیام هنوز فعال نیست. لطفاً از شماره‌های تماس یا ایمیل درج‌شده در این صفحه استفاده کنید.</p>
                            <fieldset disabled class="m-0 grid gap-5 border-0 p-0">
                                <legend class="sr-only">
                                    فرم ارسال پیام
                                </legend>

                                <div class="contact-field">
                                    <label for="contact-name">
                                        نام و نام خانوادگی
                                    </label>

                                    <input
                                        id="contact-name"
                                        type="text"
                                        name="name"
                                        autocomplete="name"
                                        required
                                        aria-required="true"
                                        placeholder="نام خود را وارد کنید"
                                    />
                                </div>

                                <div class="contact-field">
                                    <label for="contact-email">
                                        ایمیل
                                    </label>

                                    <input
                                        id="contact-email"
                                        type="email"
                                        name="email"
                                        autocomplete="email"
                                        inputmode="email"
                                        required
                                        aria-required="true"
                                        placeholder="example@email.com"
                                        dir="ltr"
                                    />
                                </div>

                                <div class="contact-field">
                                    <label for="contact-message">
                                        پیام شما
                                    </label>

                                    <textarea
                                        id="contact-message"
                                        name="message"
                                        rows="5"
                                        required
                                        aria-required="true"
                                        placeholder="موضوع یا پرسش خود را بنویسید"
                                    ></textarea>
                                </div>

                                <div class="flex pt-1">
                                    <button
                                        type="button" disabled
                                        class="contact-submit"
                                    >
                                        <span>ارسال پیام</span>
                                        <span aria-hidden="true">←</span>
                                    </button>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>

                <div
                    class="order-1 min-w-0 w-full md:col-span-7 md:pt-5"
                >
                    <div
                        class="max-w-[570px] text-center md:text-right"
                    >
                        <h2
                            id="contact-form-title"
                            class="mx-auto max-w-[520px] text-[28px] font-normal leading-[1.8] text-[#171717] sm:text-[34px] md:mx-0"
                        >
                            برای شروع یک گفت‌وگوی مناسب، با ما در تماس باشید
                        </h2>

                        <p
                            class="mx-auto mt-5 max-w-[510px] text-[13px] font-light leading-8 text-[#777777] md:mx-0"
                        >
                            برای انتخاب نوع جلسه، رزرو نوبت، پرسیدن درباره روند درمان یا هماهنگی زمان مناسب، می‌توانید از
                            فرم روبه‌رو استفاده کنید یا مستقیماً با ما تماس بگیرید.
                        </p>

                        <div
                            class="contact-detail-list mx-auto mt-10 max-w-[360px] text-right md:mx-0 md:max-w-none"
                        >
                            <div class="contact-detail">
                        <span class="contact-detail-icon" aria-hidden="true">
                            <svg
                                width="20"
                                height="20"
                                viewBox="0 0 20 20"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M18.3332 14.1V16.6C18.3341 16.8321 18.2866 17.0618 18.1936 17.2745C18.1006 17.4871 17.9643 17.678 17.7933 17.8349C17.6222 17.9918 17.4203 18.1113 17.2005 18.1856C16.9806 18.26 16.7477 18.2876 16.5165 18.2667C13.9522 17.9881 11.489 17.1118 9.32486 15.7084C7.31139 14.4289 5.60431 12.7219 4.32486 10.7084C2.91651 8.53438 2.04007 6.0592 1.76653 3.48337C1.7457 3.25293 1.77309 3.02067 1.84695 2.80139C1.9208 2.58211 2.03951 2.38061 2.1955 2.20972C2.3515 2.03883 2.54137 1.9023 2.75302 1.80881C2.96468 1.71532 3.19348 1.66692 3.42486 1.66671H5.92486C6.32928 1.66273 6.72136 1.80594 7.028 2.06965C7.33464 2.33336 7.53493 2.69958 7.59153 3.10004C7.69705 3.9001 7.89274 4.68565 8.17486 5.44171C8.28698 5.73998 8.31125 6.06414 8.24478 6.37577C8.17832 6.68741 8.02392 6.97347 7.79986 7.20004L6.74153 8.25837C7.92783 10.3447 9.65524 12.0721 11.7415 13.2584L12.7999 12.2C13.0264 11.976 13.3125 11.8216 13.6241 11.7551C13.9358 11.6887 14.2599 11.7129 14.5582 11.825C15.3143 12.1072 16.0998 12.3029 16.8999 12.4084C17.3047 12.4655 17.6744 12.6694 17.9386 12.9813C18.2029 13.2932 18.3433 13.6914 18.3332 14.1Z"
                                    stroke="#0A5C3A"
                                    stroke-width="1.5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>
                        </span>

                                <div>
                                    <p class="text-[14px] font-medium text-[#171717]">
                                        شماره تماس
                                    </p>

                                    <a
                                        href="tel:+989300673186"
                                        dir="ltr"
                                        class="mt-1 inline-block text-[12px] text-[#858585] transition hover:text-forest-6 hover:underline"
                                    >
                                        +98 0930 067 3186
                                    </a>
                                </div>
                            </div>

                            <div class="contact-detail">
                        <span class="contact-detail-icon" aria-hidden="true">
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.7"
                            >
                                <circle cx="12" cy="12" r="9"/>
                                <path
                                    stroke-linecap="round"
                                    d="M12 7v5l3 2"
                                />
                            </svg>
                        </span>

                                <div>
                                    <p class="text-[14px] font-medium text-[#171717]">
                                        ساعات کاری
                                    </p>

                                    <time class="mt-1 block text-[12px] text-[#858585]">
                                        شنبه تا پنجشنبه، ۹ تا ۱۸
                                    </time>
                                </div>
                            </div>

                            <div class="contact-detail">
                        <span class="contact-detail-icon" aria-hidden="true">
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.7"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 21s7-6.1 7-11.5A7 7 0 0 0 5 9.5C5 14.9 12 21 12 21Z"
                                />
                                <circle cx="12" cy="9.5" r="2.2"/>
                            </svg>
                        </span>

                                <div>
                                    <p class="text-[14px] font-medium text-[#171717]">
                                        آدرس
                                    </p>

                                    <address class="mt-1 text-[12px] not-italic leading-6 text-[#858585]">
                                        تبریز، آبرسان، طبقه اول ساختمان بانک سپه
                                    </address>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ۳. مرور آماری بر اساس اعداد و ارقام -->
        <section
            aria-labelledby="statistics-title"
            class="bg-cream py-16 md:py-20 lg:py-24"
        >
            <div
                class="layout grid items-center gap-12 md:grid-cols-2 md:gap-8 lg:grid-cols-12 lg:gap-20"
            >
                <!-- متن -->
                <div class="order-1 md:col-span-1 lg:col-span-6">
                    <div class="mx-auto max-w-[470px] text-center">
                        <h2
                            id="statistics-title"
                            class="text-[27px] font-normal leading-[1.7] text-brandDark md:text-[28px] lg:text-[32px]"
                        >
                            مرور آماری بر اساس
                            <br/>
                            اعداد و ارقام
                        </h2>

                        <p class="mt-4 text-[11px] leading-7 text-[#375B4D] md:mt-5">
                            این اعداد، فقط یک نمای کلی از تجربه و فعالیت‌های حرفه‌ای هستند.
                            کیفیت درمان در تعامل با شرایط و مسیر هر فرد پیوسته است.
                        </p>

                        <!-- TODO: اعداد را با داده واقعی و تاریخ به‌روزرسانی جایگزین کنید -->
                        <p class="mt-3 text-[10px] leading-6 text-[#375B4D] md:mt-4">
                            [نیازمند تأیید اعداد و تاریخ به‌روزرسانی]
                        </p>

                        <div
                            class="mt-9 grid grid-cols-2 gap-x-6 gap-y-8 md:mt-10 md:gap-x-4 md:gap-y-7 lg:mt-12 lg:gap-x-12 lg:gap-y-10"
                        >
                            <div>
                                <p
                                    class="text-[25px] font-light text-brandDark md:text-[25px] lg:text-[29px]"
                                    dir="ltr"
                                >
                                    +1500
                                </p>
                                <p class="mt-2 text-[10px] leading-5 text-[#375B4D]">
                                    مراجعان همراه در جلسات
                                </p>
                            </div>

                            <div>
                                <p
                                    class="text-[25px] font-light text-brandDark md:text-[25px] lg:text-[29px]"
                                    dir="ltr"
                                >
                                    +1200
                                </p>
                                <p class="mt-2 text-[10px] leading-5 text-[#375B4D]">
                                    جلسات برگزارشده
                                </p>
                            </div>

                            <div>
                                <p
                                    class="text-[25px] font-light text-brandDark md:text-[25px] lg:text-[29px]"
                                    dir="ltr"
                                >
                                    +200
                                </p>
                                <p class="mt-2 text-[10px] leading-5 text-[#375B4D]">
                                    کارگاه‌ها و نشست‌ها
                                </p>
                            </div>

                            <div>
                                <p
                                    class="text-[25px] font-light text-brandDark md:text-[25px] lg:text-[29px]"
                                    dir="ltr"
                                >
                                    +10
                                </p>
                                <p class="mt-2 text-[10px] leading-5 text-[#375B4D]">
                                    سال‌های فعالیت حرفه‌ای
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تصویر -->
                <div class="order-2 md:col-span-1 lg:col-span-6">
                    <div
                        class="relative aspect-[4/3] w-full overflow-hidden rounded-[18px] bg-[#969696] md:aspect-[1/1] lg:aspect-auto lg:h-[390px]"
                    >
                        <!-- TODO: تصویر واقعی را جایگزین کنید -->
                        <img
                            src="https://picsum.photos/seed/statistics/850/700"
                            alt="نمایی از فضای کاری و جلسات مشاوره"
                            width="850"
                            height="700"
                            loading="lazy"
                            decoding="async"
                            class="h-full w-full object-cover"
                        />

                        <div
                            class="absolute -bottom-20 -right-20 h-[280px] w-[280px] rounded-full bg-[#A9B897]"
                            aria-hidden="true"
                        ></div>

                        <div
                            class="absolute -bottom-20 -right-20 h-[190px] w-[190px] rounded-full bg-[#B5D48B]"
                            aria-hidden="true"
                        ></div>
                    </div>
                </div>
            </div>
        </section>

        <section
            class="faq-section py-16 sm:py-20 md:py-24"
            aria-labelledby="faq-title"
        >
            <div class="layout">
                <div
                    class="grid items-start gap-12 md:grid-cols-12 md:gap-8 lg:gap-20"
                >
                    <!-- متن معرفی -->
                    <div
                        class="md:col-span-5 md:pt-2 lg:col-span-4 lg:sticky lg:top-8 lg:self-center"
                    >
                        <div class="mx-auto max-w-[360px] text-center md:mx-0 md:text-right">
                            <h2
                                id="faq-title"
                                class="mx-auto max-w-[330px] text-[24px] font-medium leading-[1.85] text-ink sm:text-[28px] lg:mx-0 lg:max-w-[360px] lg:text-[30px]"
                            >
                                گاهی آرامش از فهمیدن خودمان شروع می‌شود.
                            </h2>

                            <p
                                class="mx-auto mt-4 max-w-[310px] text-[11px] leading-8 text-muted sm:mt-5 sm:max-w-[330px] sm:text-[12px] md:mx-0"
                            >
                                پاسخ پرسش‌های رایج درباره شروع روان‌درمانی، مشاوره و روند برگزاری جلسات را در این بخش بخوانید.
                            </p>

                            <a
                                href="{{ url('/faq') }}"
                                class="mx-auto mt-6 inline-flex items-center justify-center gap-2 rounded-full border border-forest-2 px-4 py-2.5 text-[11px] font-medium text-forest-2 transition hover:bg-forest-2 hover:text-white sm:mt-7 sm:px-5 sm:py-3 sm:text-[12px] md:mx-0"
                            >
                                مشاهده همه پرسش‌ها
                                <span aria-hidden="true">←</span>
                            </a>
                        </div>
                    </div>

                    <!-- لیست پرسش‌ها -->
                    <div
                        class="faq-list min-w-0 md:col-span-7 lg:col-span-8"
                    >
                        <article class="faq-item is-open">
                            <h3 class="m-0">
                                <button
                                    type="button"
                                    class="faq-toggle faq-button flex w-full items-start gap-3 text-right sm:gap-4"
                                    aria-expanded="true"
                                    aria-controls="faq-answer-1"
                                >
                            <span
                                class="faq-icon shrink-0"
                                aria-hidden="true"
                            >
                                −
                            </span>

                                    <span
                                        id="faq-question-1"
                                        class="faq-question min-w-0 text-[12px] leading-7 sm:text-[13px] sm:leading-8"
                                    >
                                چرا با وجود اینکه می‌دانم چه کاری درست است، باز به الگوهای قبلی برمی‌گردم؟
                            </span>
                                </button>
                            </h3>

                            <div
                                id="faq-answer-1"
                                class="faq-answer"
                                role="region"
                                aria-labelledby="faq-question-1"
                            >
                                <div>
                                    <p class="text-[11px] leading-7 sm:text-[12px] sm:leading-8">
                                        چون تغییر فقط به دانستن وابسته نیست. تجربه‌های گذشته، ترس‌ها، نیازهای برآورده‌نشده و
                                        عادت‌های ذهنی می‌توانند ما را به سمت واکنش‌های آشنا برگردانند. در جلسات، این الگوها
                                        را با دقت بررسی می‌کنیم تا امکان انتخاب‌های تازه‌تر و آگاهانه‌تر فراهم شود.
                                    </p>
                                </div>
                            </div>
                        </article>

                        <article class="faq-item">
                            <h3 class="m-0">
                                <button
                                    type="button"
                                    class="faq-toggle faq-button flex w-full items-start gap-3 text-right sm:gap-4"
                                    aria-expanded="false"
                                    aria-controls="faq-answer-2"
                                >
                            <span
                                class="faq-icon shrink-0"
                                aria-hidden="true"
                            >
                                +
                            </span>

                                    <span
                                        id="faq-question-2"
                                        class="faq-question min-w-0 text-[12px] leading-7 sm:text-[13px] sm:leading-8"
                                    >
                                وقتی ذهنم آرام نمی‌گیرد، تراپی چطور می‌تواند کمکم کند؟
                            </span>
                                </button>
                            </h3>

                            <div
                                id="faq-answer-2"
                                class="faq-answer"
                                role="region"
                                aria-labelledby="faq-question-2"
                            >
                                <div>
                                    <p class="text-[11px] leading-7 sm:text-[12px] sm:leading-8">
                                        جلسات تراپی فرصتی امن برای مکث‌کردن و مرتب‌کردن افکار و احساسات فراهم می‌کنند. با
                                        شناختن ریشه‌های نگرانی، فشار روانی یا بی‌قراری، می‌توان به‌تدریج راه‌های مناسب‌تری
                                        برای مواجهه با آن‌ها پیدا کرد.
                                    </p>
                                </div>
                            </div>
                        </article>

                        <article class="faq-item">
                            <h3 class="m-0">
                                <button
                                    type="button"
                                    class="faq-toggle faq-button flex w-full items-start gap-3 text-right sm:gap-4"
                                    aria-expanded="false"
                                    aria-controls="faq-answer-3"
                                >
                            <span
                                class="faq-icon shrink-0"
                                aria-hidden="true"
                            >
                                +
                            </span>

                                    <span
                                        id="faq-question-3"
                                        class="faq-question min-w-0 text-[12px] leading-7 sm:text-[13px] sm:leading-8"
                                    >
                                آیا جلسات مشاوره قرار است مرا تغییر دهد؟
                            </span>
                                </button>
                            </h3>

                            <div
                                id="faq-answer-3"
                                class="faq-answer"
                                role="region"
                                aria-labelledby="faq-question-3"
                            >
                                <div>
                                    <p class="text-[11px] leading-7 sm:text-[12px] sm:leading-8">
                                        روان‌درمانی قرار نیست شما را به فرد دیگری تبدیل کند یا تصمیم‌هایتان را به جای شما
                                        بگیرد. هدف، شناخت عمیق‌تر خود، احساسات و الگوهای رفتاری‌تان است.
                                    </p>
                                </div>
                            </div>
                        </article>

                        <article class="faq-item">
                            <h3 class="m-0">
                                <button
                                    type="button"
                                    class="faq-toggle faq-button flex w-full items-start gap-3 text-right sm:gap-4"
                                    aria-expanded="false"
                                    aria-controls="faq-answer-4"
                                >
                            <span
                                class="faq-icon shrink-0"
                                aria-hidden="true"
                            >
                                +
                            </span>

                                    <span
                                        id="faq-question-4"
                                        class="faq-question min-w-0 text-[12px] leading-7 sm:text-[13px] sm:leading-8"
                                    >
                                آیا برای شروع تراپی باید حالم خیلی بد باشد؟
                            </span>
                                </button>
                            </h3>

                            <div
                                id="faq-answer-4"
                                class="faq-answer"
                                role="region"
                                aria-labelledby="faq-question-4"
                            >
                                <div>
                                    <p class="text-[11px] leading-7 sm:text-[12px] sm:leading-8">
                                        خیر. لازم نیست حتماً در بحران باشید تا از روان‌درمانی کمک بگیرید. گاهی شناخت بهتر
                                        خود، بهبود رابطه‌ها، مدیریت اضطراب یا روبه‌روشدن با یک تصمیم مهم باعث می‌شود افراد
                                        به درمان مراجعه کنند.
                                    </p>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </section>


    </main>
@endsection

@push('scripts')
    <script src="{{ asset('website/js/contact.js') }}" defer></script>
@endpush
