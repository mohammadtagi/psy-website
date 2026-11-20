@extends('website.layouts.app')

@section('title', 'پرسش‌های متداول مراجعان | کریم محمدزاده')
@section('description', 'پاسخ به پرسش‌های رایج درباره شروع مشاوره، رزرو نوبت، جلسات فردی، خانواده و زوج‌درمانی.')
@section('canonical', url('/faq'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('website/style/faq.css') }}">
    <link rel="stylesheet" href="{{ asset('website/style/inner-hero.css') }}">
    <link rel="stylesheet" href="{{ asset('website/style/faq-motion.css') }}">
    <link rel="stylesheet" href="{{ asset('website/style/faq-directory.css') }}">
    <link rel="stylesheet" href="{{ asset('website/style/faq-stable.css') }}">
    <link rel="stylesheet" href="{{ asset('website/style/faq-palette.css') }}">
    <script defer src="{{ asset('website/js/faq-stable.js') }}"></script>
@endpush

@section('content')
    <main class="website-faq">
        @include('website.partials.inner-hero', [
            'crumb' => 'پرسش‌های متداول',
            'heading' => 'سوالات متداول مراجعان',
            'intro' => 'پیش از شروع درمان، داشتن سوال و تردید کاملاً طبیعی است. در این بخش به چند سوال درباره‌ی روند و نحوه‌ی همکاری ما پاسخ داده‌ایم تا با خیال راحت‌تری تصمیم بگیرید و تصویر روشن‌تری از مسیر پیش‌رو داشته باشید.',
        ])

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
                        <details class="faq-item" open><summary>تفاوت مشاوره با درد و دل کردن با دوستان چیست؟</summary><p>درد و دل کردن با اطرافیان، اگرچه حمایت‌کننده است، اما معمولا بر اساس همدلی سطحی یا توصیه‌هایی است که از تجربه‌های شخصیِ آن‌ها برمی‌آید. در جلسات مشاوره، من به عنوان درمانگر، به جای دادن راهکار یا همدلی ساده، با نگاهی علمی و تخصصی، الگوها، تعارضات ناخودآگاه و گره‌هایی را واکاوی می‌کنم که شاید حتی نزدیک‌ترین اطرافیان تان هم نتوانند آن‌ها را ببینند. هدف ما آرام کردن موقت نیست، بلکه شناخت عمیق است.</p></details>
                        <details class="faq-item"><summary> اگر در جلسات حرفی برای گفتن نداشته باشم یا حالم بدتر شود، چه می‌شود؟</summary><p>این نگرانی کاملا طبیعی است. خیلی از مراجعان پیش از شروع می‌ترسند در جلسه سکوت کنند یا ندانند از کجا شروع کنند. در اتاق درمان هیچ اجباری برای عجله کردن نیست. سکوت بخشی از روند کشف است. همچنین گاهی در مسیر شناخت تعارضات، مواجهه با هیجانات سرکوب شده ممکن است موقتا سنگین یا ناخوشایند به نظر برسد، اما این دقیقا همان نقطه‌ای است که فرایند التیام و حل واقعی از انجا آغاز می‌شود و من در تمام این مسیر در کنار شما هستم.</p></details>
                        <details class="faq-item"><summary>آیا می‌توانم بعد از رزرو، زمان جلسه را تغییر دهم؟</summary><p>خیر. پس از رزرو پیشنهاد میشه در زمان مقرر تشریف بیارید برای شرکت در جلسه، درصورتی امکان حضور براتون فراهم نبود شرایط لغو جلسه رزرو شده و بازگشت وجه رو از <a href="{{  route("website.terms") }} " style="text-decoration: underline">این بخش</a> مشاهده کنید</p></details>
                        <details class="faq-item"><summary>از کجا بدانم این نوع مشاوره برای من مناسب است؟</summary><p>مناسب‌بودن این شیوه در جلسهٔ ارزیابی و با توجه به شرایط، نیازها و انتظارات شما بررسی می‌شود. در همان جلسه می‌توانید درباره دغدغه‌هایتان صحبت کنید و با هم مشخص کنیم که این مسیر برای شما مناسب است یا به شکل دیگری از کمک نیاز دارید.</p></details>
                        <a
                            href="#all-questions"
                            class="faq-more mt-5 inline-flex items-center gap-4 rounded-full bg-primary px-6 py-2 text-[12px] text-white"
                        >
                            سوالات بیشتر
                            <svg aria-hidden="true" focusable="false" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4v15m-6-6 6 6 6-6"/></svg>
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
                            برای انتخاب نوع جلسه (آنلاین یا حضوری)، پرسش درباره روند درمان، یا هماهنگی حضور در جلسه، می‌توانید با ما در تماس باشید.
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
                                <p class="mt-1 text-[11px] text-[#8A9B91]">10 صبح تا 19 عصر، پنجشنبه و جمعه از 10 صبح تا 1 ظهر</p>
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
                <header class="faq-directory"><p>راهنمای جلسات</p><h2>از کدام موضوع شروع کنیم؟</h2><nav aria-label="رفتن به پرسش‌های هر موضوع"><a href="#faq-personal">جلسات فردی</a><a href="#faq-family">افسردگی و خستگی مفرط</a><a href="#faq-trauma">تجربه‌های آسیب‌زا</a><a href="#faq-couple">مشاوره روابط</a></nav></header>
                <div class="faq-grid grid grid-cols-1 gap-x-24 gap-y-20 lg:grid-cols-2">

                    <section class="faq-column">
                        <h2 id="faq-personal" tabindex="-1" class="mb-7 text-[17px] font-semibold">جلسات فردی</h2>

                        <div class="space-y-3">
                            <details class="faq-item"><summary>نمیدانم درباره چه چیزی میخوام صحبت کنم چیکار کنم؟</summary><p>لازم نیست موضوع را از قبل مرتب یا دقیق تعریف کرده باشید. می‌توانید از چیزی شروع کنید که این روزها بیشتر ذهنتان را درگیر کرده؛ در گفت‌وگو کمک می‌کنیم مسئله و بخش‌های مهم آن روشن‌تر شود.</p></details>

                            <details class="faq-item"><summary>چند جلسه‌ی فردی نیاز دارم و هر جلسه چقدر طول می‌کشد؟</summary><p>تعداد جلسات از ابتدا برای همه یکسان نیست و به مسئله، هدف و شرایط شما بستگی دارد. در جلسهٔ ارزیابی، دربارهٔ نیازهای شما گفت‌وگو می‌کنیم تا مسیر مناسب‌تری مشخص شود. ممکن است برای برخی افراد چند جلسه کافی باشد و برخی دیگر به همراهی طولانی‌تری نیاز داشته باشند.</p></details>

                        </div>
                    </section>

                    <section class="faq-column">
                        <h2 id="faq-family" tabindex="-1" class="mb-7 text-[17px] font-semibold">مواجهه با افسردگی و خستگی مفرط</h2>

                        <div class="space-y-3">
                            <details class="faq-item"><summary>آیا برای افسردگی حتماً به دارو نیاز دارم یا فقط مشاوره کافی است؟</summary><p>تراپی به واکاوی ریشه‌ها و تعارض‌های درونی کمک می‌کند، اما تشخیص نیاز به دارو به شدت علائم و شرایط شما بستگی دارد. در صورت نیاز به ارزیابی دارویی، این موضوع به صورت شفاف بررسی می‌شود و گاهی ترکیب دارو و تراپی بهترین نتیجه را می‌دهد.</p></details>

                            <details class="faq-item"><summary>از کجا بفهمم این خستگی، یک خستگی معمولی کاری است یا نشانهٔ افسردگی؟</summary><p>خستگی معمولی با استراحت برطرف می‌شود، اما خستگی عمیق یا افسردگی معمولاً با حس بی‌رغبتی، خالی شدن انرژی روانی، افکار تکراری و مختل شدن کارهای روزمره همراه است. در جلسات می‌توانیم این موضوع و ریشه‌های آن را دقیق‌تر بررسی کنیم تا روشن شود با چه وضعیتی روبه‌رو هستیم.</p></details>

                            <details class="faq-item"><summary>احساس می‌کنم حالم به هیچ عنوان بهتر نمی‌شود؛ چطور به شما اعتماد کنم؟</summary><p>وقتی مدتی است حالتان بهتر نشده، طبیعی است به مؤثر بودن مشاوره هم شک کنید. در جلسهٔ ارزیابی، به‌جای وعده‌دادن، شرایط شما را دقیق‌تر بررسی می‌کنیم؛ از جمله اینکه چه چیزهایی تا امروز امتحان کرده‌اید و چه عواملی ممکن است در ادامه‌دارشدن این حال نقش داشته باشند. شما می‌توانید با همین تردید و ناامیدی دربارهٔ حالتان صحبت کنید.</p></details>
                        </div>
                    </section>

                    <section class="faq-column">
                        <h2 id="faq-trauma" tabindex="-1" class="mb-7 text-[17px] font-semibold">تروما و تجربه‌های آسیب‌زا</h2>

                        <div class="space-y-3">
                            <details class="faq-item"><summary> نمی‌دانم تجربه‌ای که داشته‌ام واقعاً تروما محسوب می‌شود یا نه</summary><p> لازم نیست خودتان دربارهٔ تجربه‌تان تشخیص‌گذاری کنید. اگر اتفاقی هنوز شما را درگیر می‌کند، باعث اضطراب، بی‌اعتمادی، اجتناب، خشم، بی‌حسی عاطفی یا دشواری در روابط شده است، می‌توانید دربارهٔ آن در جلسه صحبت کنید. در فرایند ارزیابی، اثرات آن تجربه و نیازهای شما دقیق‌تر بررسی می‌شود.</p></details>

                            <details class="faq-item"><summary>صحبت‌کردن دربارهٔ تجربهٔ آسیب‌زا حالم را به شدت بد میکند چیکار کنم؟</summary><p>قرار نیست بدون آمادگی وارد جزئیات دشوار شوید. در جلسات، سرعت پیش‌رفتن با توجه به وضعیت و ظرفیت شما تنظیم می‌شود و می‌توانید هر زمان که نیاز داشتید مکث کنید یا دربارهٔ دشواری ادامه‌دادن صحبت کنید. هدف، ایجاد فضایی امن برای شناختن اثرات تجربه است، نه وادارکردن شما به بازگویی آن.</p></details>

                            <details class="faq-item"><summary>آیا بعد از تجربهٔ آسیب‌زا می‌توانم دوباره احساس امنیت و اعتماد کنم؟</summary><p>تجربهٔ آسیب‌زا معمولاً احساس امنیت و اعتماد را در چند سطح تحت تأثیر قرار می‌دهد؛ اعتماد به دیگران، اعتماد به خود و احساس امنیت در موقعیت‌هایی که یادآور آن تجربه هستند. در جلسات، این اثرات را با هم بررسی می‌کنیم تا بفهمیم در شرایط شما چه چیزهایی احساس ناامنی را زنده نگه می‌دارد و چه واکنش‌هایی به‌صورت خودکار شکل گرفته‌اند. هدف، بازگشت به همان وضعیت پیش از تجربه نیست؛ بلکه ساختن تدریجی احساس امنیت و اعتماد بر پایهٔ شناخت دقیق‌تر از خود و مرزهایتان است.</p></details>
                        </div>
                    </section>

                    <section class="faq-column">
                        <h2 id="faq-couple" tabindex="-1" class="mb-7 text-[17px] font-semibold">مشاوره زوجین و روابط</h2>

                        <div class="space-y-3">
                            <details class="faq-item"><summary>ما دعوای زیادی نداریم، اما از هم دور شده‌ایم. مشاوره می‌تواند به ما کمک کند؟</summary><p>بله. لازم نیست منتظر بحران بمانید. گاهی نشانه فاصله این است که درباره کارهای روزمره حرف می‌زنید، اما دیگر از دلتنگی‌ها، خواسته‌ها یا حتی اتفاق‌های خوب روزتان به هم نمی‌گویید. جلسه فرصتی است تا هرکدام بگویید دلتان برای کدام بخش رابطه تنگ شده و چه تغییری می‌تواند برایتان معنادار باشد.</p></details>

                            <details class="faq-item"><summary>اگر شریک عاطفی‌ام حاضر به مشاوره نباشد، مراجعه من به‌تنهایی فایده دارد؟</summary><p>بله. در مشاوره فردی می‌توانید روشن کنید چه چیزی در رابطه آزارتان می‌دهد، چه مرزهایی برایتان مهم است و چطور خواسته‌تان را بیان کنید. این کار جای حضور هر دو نفر را در زوج‌درمانی نمی‌گیرد، اما برای شناخت نیازها و تصمیم‌های خودتان لازم نیست منتظر همراهی طرف مقابل بمانید.</p></details>

                            <details class="faq-item"><summary> بعد از خیانت، اگر هنوز ندانم می‌خواهم در رابطه بمانم یا نه، برای مشاوره زود است؟</summary><p>نه. برای شروع مشاوره لازم نیست از قبل تصمیم گرفته باشید یا آماده بخشیدن باشید. می‌توانید از همان چیزی شروع کنید که امروز برایتان مهم‌تر است: فرصتی برای حرف زدن درباره آسیب، مشخص کردن مرزها، یا فهمیدن اینکه برای بررسی امکان ادامه رابطه به چه چیزی نیاز دارید. هدف جلسه این نیست که به ماندن یا جدایی ترغیب شوید.</p></details>
                        </div>
                    </section>

                </div>
            </div>
        </section>


    </main>
@endsection
