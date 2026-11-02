@extends('website.layouts.app')

@section('title', 'پرسش‌های متداول مراجعان | کریم محمدزاده')
@section('description', 'پاسخ به پرسش‌های رایج درباره شروع مشاوره، رزرو نوبت، جلسات فردی، خانواده و زوج‌درمانی.')
@section('canonical', url('/faq'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('website/style/faq.css') }}">
@endpush

@section('content')
    <main class="website-faq">
        <!-- 1. هدر و بخش سوالات متداول مراجعان -->
        <div class="faq-hero text-white">
            <div class="container-grid">@include('website.partials.header')</div>
            <div class="container-grid hero-content flex items-center justify-center text-center">
                <div class="max-w-[650px]">
                    <nav aria-label="مسیر صفحه" class="mb-5 text-sm text-white/80"><ol class="flex justify-center gap-3"><li><a href="{{ route('home') }}">خانه</a></li><li aria-hidden="true">/</li><li aria-current="page">پرسش‌های متداول</li></ol></nav>
                    <h1 class="hero-title text-[48px] font-bold leading-[1.7]">
                        سوالات متداول مراجعان
                    </h1>

                    <p class="mx-auto mt-5 max-w-[570px] text-[13px] font-light leading-8 opacity-90">
                        پیش از شروع درمان، داشتن سوال و تردید کاملاً طبیعی است. در این بخش
                        به چند سوال درباره‌ی روند و نحوه‌ی همکاری ما پاسخ داده‌ایم تا با خیال راحت‌تری
                        تصمیم بگیرید و تصویر روشن‌تری از مسیر پیش‌رو داشته باشید.
                    </p>
                </div>
            </div>
        </div>

        <!-- 2. معرفی و سوالات کوتاه -->
        <section class="bg-white py-[145px]">
            <div class="container-grid">

                <div class="intro-grid grid grid-cols-1 items-center gap-20 lg:grid-cols-2">

                    <div class="intro-copy order-1 text-right">
                        <h2 class="section-title text-[38px] font-medium leading-[1.9] text-forest">
                            گاهی آرامش از تغییر شرایط<br>
                            شروع نمی‌شود؛ از فهمیدن<br>
                            خودمان شروع می‌شود.
                        </h2>
                    </div>

                    <div class="order-2">
                        <details class="faq-item" open><summary>چرا می‌دانیم چه کاری درست است، اما باز هم الگوهای قبلی را تکرار می‌کنیم؟</summary><p>چون دانستن همیشه برای تغییر کافی نیست؛ شناخت احساسات، نیازها و الگوهای پشت رفتار می‌تواند به تغییر کمک کند.</p></details>
                        <details class="faq-item"><summary>وقتی ذهنم آرام نمی‌گیرد، جلسات چطور می‌توانند کمکم کنند؟</summary><p>در جلسات می‌توانید درباره نگرانی‌ها صحبت کنید و با کمک متخصص، راه‌های متناسب با شرایط خود برای مدیریت آن‌ها را بررسی کنید.</p></details>
                        <details class="faq-item"><summary>تراپی قرار است مرا تغییر دهد یا کمک کند خودم را بهتر بفهمم؟</summary><p>هدف جلسات با مشارکت شما تعیین می‌شود؛ خودشناسی و بررسی تغییراتی که برایتان اهمیت دارند می‌تواند بخشی از این مسیر باشد.</p></details>
                        <details class="faq-item"><summary>آیا برای شروع درمان باید حالم واقعاً خیلی بد باشد؟</summary><p>خیر؛ برای خودشناسی، بررسی دغدغه‌ها یا بهبود روابط هم می‌توانید درخواست مشاوره کنید.</p></details>
                        <a
                            href="#all-questions"
                            class="mt-5 inline-flex items-center gap-4 rounded-full bg-primary px-6 py-2 text-[12px] text-white"
                        >
                            سوالات بیشتر
                            <span>‹</span>
                        </a>
                    </div>
                </div>

                <div class="mt-[110px] grid grid-cols-1 gap-5 md:grid-cols-3">

                    <article class="rounded-[10px] bg-[#FAFAFA] px-7 py-8">
                        <div class="mb-7 flex justify-end">
            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-lime text-white">
              ؟
            </span>
                        </div>

                        <h3 class="text-[17px] font-medium leading-8">
                            چطور می‌توانم وقت جلسه بگیرم؟
                        </h3>

                        <p class="mt-4 text-[11px] font-light leading-7 text-gray-400">
                            برای انتخاب زمان و ثبت درخواست، به <a class="underline" href="{{ route('booking.index') }}">صفحه رزرو نوبت</a> بروید.
                        </p>
                    </article>

                    <article class="rounded-[10px] bg-[#FAFAFA] px-7 py-8">
                        <div class="mb-7 flex justify-end">
            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-lime text-white">
              ؟
            </span>
                        </div>

                        <h3 class="text-[17px] font-medium leading-8">
                            شروع جلسات چگونه است؟
                        </h3>

                        <p class="mt-4 text-[11px] font-light leading-7 text-gray-400">
                            روند آغاز جلسات و هماهنگی زمان مناسب در تماس اولیه توضیح داده می‌شود.
                        </p>
                    </article>

                    <article class="rounded-[10px] bg-[#FAFAFA] px-7 py-8">
                        <div class="mb-7 flex justify-end">
            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-lime text-white">
              ؟
            </span>
                        </div>

                        <h3 class="text-[17px] font-medium leading-8">
                            چه مسائلی را می‌توانم در جلسات مطرح کنم؟
                        </h3>

                        <p class="mt-4 text-[11px] font-light leading-7 text-gray-400">
                            هر موضوعی که برای شما مهم است و بر زندگی، احساسات یا رابطه‌هایتان اثر گذاشته است.
                        </p>
                    </article>

                </div>
            </div>
        </section>

        <!-- 3. خوشحال می‌شویم پیام شما را دریافت کنیم -->
        <section id="contact" class="bg-contact py-[105px]">
            <div class="container-grid">
                <div class="contact-grid grid grid-cols-1 items-center gap-20 lg:grid-cols-2">

                    <div class="order-1">
                        <h2 class="section-title text-[38px] font-medium leading-[1.8] text-forest">
                            خوشحال می‌شویم پیام<br>
                            شما را دریافت کنیم
                        </h2>

                        <p class="mt-5 max-w-[440px] text-[13px] font-light leading-8 text-[#8A9B91]">
                            برای انتخاب نوع جلسه (فردی/زوجی/خانواده)، پرسش درباره روند درمان، یا هماهنگی زمان مناسب، می‌توانید با ما در تماس باشید.
                        </p>
                    </div>

                    <div class="order-2">

                        <div class="flex items-center gap-4 border-b border-[#DCE8E0] py-5">
            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-primary text-white">
              ⌕
            </span>
                            <div>
                                <h3 class="text-[16px] font-medium">شماره تماس</h3>
                                <p class="mt-1 text-[11px] text-[#8A9B91]"><a href="tel:+984134244933" dir="ltr">041 3424 4933</a></p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 border-b border-[#DCE8E0] py-5">
            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-primary text-white">
              ◷
            </span>
                            <div>
                                <h3 class="text-[16px] font-medium">ساعات کاری</h3>
                                <p class="mt-1 text-[11px] text-[#8A9B91]">برای هماهنگی زمان مراجعه، با ما تماس بگیرید.</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 py-5">
            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-primary text-white">
              ⌖
            </span>
                            <div>
                                <h3 class="text-[16px] font-medium">آدرس</h3>
                                <p class="mt-1 text-[11px] text-[#8A9B91]">تبریز، آبرسان، طبقه اول ساختمان بانک سپه</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>

        <!-- 4. بخش اصلی سوالات -->
        <section id="all-questions" class="bg-white py-[120px]">
            <div class="container-grid">
                <div class="faq-grid grid grid-cols-1 gap-x-24 gap-y-20 lg:grid-cols-2">

                    <section class="faq-column">
                        <h2 class="mb-7 text-[17px] font-semibold">جلسات فردی</h2>

                        <div class="space-y-3">
                            <details class="faq-item"><summary>در جلسات فردی معمولاً روی چه موضوعاتی کار می‌کنیم؟</summary><p>بسته به نیاز شما، موضوعاتی مانند احساسات، نگرانی‌ها، روابط و الگوهای رفتاری بررسی می‌شوند. هدف جلسات با مشارکت شما مشخص می‌شود.</p></details>

                            <details class="faq-item"><summary>چند جلسه‌ی فردی نیاز دارم و هر جلسه چقدر طول می‌کشد؟</summary><p>تعداد جلسات به شرایط و اهداف شما بستگی دارد و از پیش برای همه یکسان نیست. مدت و هزینه جلسه را هنگام رزرو بررسی کنید.</p></details>

                            <details class="faq-item"><summary>اگر دقیق نمی‌دانم مشکل من چیست، باز هم می‌توانم برای جلسات فردی اقدام کنم؟</summary><p>بله؛ لازم نیست از قبل تشخیص مشخصی داشته باشید. می‌توانید از تجربه‌ها و دغدغه‌های فعلی خود شروع کنید.</p></details>
                        </div>
                    </section>

                    <section class="faq-column">
                        <h2 class="mb-7 text-[17px] font-semibold">مشاوره خانواده</h2>

                        <div class="space-y-3">
                            <details class="faq-item"><summary>مشاوره خانواده چه تفاوتی با زوج‌درمانی دارد؟</summary><p>مشاوره خانواده به تعاملات اعضای خانواده می‌پردازد؛ زوج‌درمانی بیشتر بر رابطه میان دو شریک تمرکز دارد.</p></details>

                            <details class="faq-item"><summary>برای مشاوره خانواده، همه اعضای خانواده باید حضور داشته باشند؟</summary><p>لزوم حضور اعضا با توجه به مسئله و ارزیابی متخصص تعیین می‌شود؛ پیش از جلسه درباره افراد حاضر هماهنگ کنید.</p></details>

                            <details class="faq-item"><summary>اگر فقط من بیام، به مشاوره خانواده کمک می‌کند؟</summary><p>حضور فردی می‌تواند به شناخت نقش و واکنش‌های خودتان کمک کند، اما جای مشارکت سایر اعضا را در همه شرایط نمی‌گیرد.</p></details>
                        </div>
                    </section>

                    <section class="faq-column">
                        <h2 class="mb-7 text-[17px] font-semibold">تروما و تجربه‌های آسیب‌زا</h2>

                        <div class="space-y-3">
                            <details class="faq-item"><summary>از کجا بفهمم تجربه‌ای که داشته‌ام، نیاز به کار درمانی دارد؟</summary><p>اگر تجربه‌ای همچنان بر خواب، احساس امنیت، روابط یا زندگی روزمره شما اثر می‌گذارد، ارزیابی توسط متخصص می‌تواند کمک‌کننده باشد.</p></details>

                            <details class="faq-item"><summary>در جلسات مربوط به تروما چه کار می‌کنیم؟ آیا لازم است همه جزئیات اتفاق را دوباره تعریف کنیم؟</summary><p>روند کار با توجه به آمادگی و نیاز شما تعیین می‌شود. لازم نیست در شروع همه جزئیات را بیان کنید؛ درباره مرزها و احساس امنیت خود با متخصص صحبت کنید.</p></details>

                            <details class="faq-item"><summary>تروما دقیقاً یعنی چه؟ فقط مربوط به حوادث خیلی شدید است؟</summary><p>تروما به پیامدهای روانی تجربه‌های آسیب‌زا اشاره دارد. تأثیر تجربه‌ها در افراد متفاوت است و ارزیابی آن فقط به شدت ظاهری اتفاق وابسته نیست.</p></details>
                        </div>
                    </section>

                    <section class="faq-column">
                        <h2 class="mb-7 text-[17px] font-semibold">کار روی رابطه و زوج‌درمانی</h2>

                        <div class="space-y-3">
                            <details class="faq-item"><summary>زوج‌درمانی فقط برای زمانی است که رابطه در آستانه جدایی است؟</summary><p>خیر؛ برای بهبود ارتباط، شناخت الگوهای تعارض و بررسی تصمیم‌های مشترک نیز می‌توان از مشاوره کمک گرفت.</p></details>

                            <details class="faq-item"><summary>اگر فقط خودم مایل به جلسه باشم و شریکم موافق نباشد چه؟</summary><p>می‌توانید مشاوره فردی را برای بررسی نیازها و انتخاب‌های خود شروع کنید؛ این جلسات جای زوج‌درمانی مشترک را نمی‌گیرند.</p></details>

                            <details class="faq-item"><summary>در جلسات زوج‌درمانی چه کار می‌کنیم؟</summary><p>با توجه به اهداف مشترک، الگوهای تعامل و تعارض بررسی می‌شوند و روی مهارت‌های ارتباطی کار می‌شود. نتیجه برای همه یکسان یا تضمین‌شده نیست.</p></details>
                        </div>
                    </section>

                </div>
            </div>
        </section>


    </main>
@endsection
