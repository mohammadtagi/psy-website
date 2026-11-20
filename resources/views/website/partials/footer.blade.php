<footer class="cps-footer" aria-label="پاورقی سایت">
    <div class="cps-wrap">
        <div class="cps-footer-grid">
            <section aria-labelledby="cps-brand-title">
                <a class="cps-brand" href="{{ route('home') }}"><img src="{{ asset('website/images/logo-mini.png') }}" width="52" height="52" loading="lazy" alt=""><span id="cps-brand-title"><strong>کریم محمدزاده</strong><small>روان‌شناس و روان‌درمانگر</small></span></a>
                <p>برای آشنایی با رویکرد درمانی، خدمات مشاوره و روند برگزاری جلسات، همراه شما هستیم.</p>
                <div class="cps-socials" role="group" aria-label="شبکه‌های اجتماعی">
                    @foreach (['telegram'=>'تلگرام','rubika'=>'روبیکا','instagram'=>'اینستاگرام','whatsapp'=>'واتساپ'] as $key => $label)
                        @if ($url = config('footer_social.'.$key))
                            <a class="cps-social" href="{{ $url }}" aria-label="{{ $label }}" title="{{ $label }}">@include('website.partials.footer-icon', ['icon'=>$key])</a>
                        @else
                            <span class="cps-social" role="img" aria-label="{{ $label }}؛ لینک ثبت نشده" title="{{ $label }}؛ لینک ثبت نشده">@include('website.partials.footer-icon', ['icon'=>$key])</span>
                        @endif
                    @endforeach
                </div>
            </section>
            <nav aria-labelledby="cps-links-title"><h2 id="cps-links-title">دسترسی سریع</h2><ul>
                <li><a href="{{ route('website.services') }}">خدمات مشاوره</a></li>
                <li><a href="{{ route('website.about') }}">درباره من</a></li>
                <li><a href="{{ route('website.faq') }}">سؤالات متداول</a></li>
                <li><a href="{{ route('website.contact') }}">تماس با ما</a></li>
                <li><a href="{{ route('booking.index') }}">رزرو نوبت</a></li>
                <li><a href="{{ route('website.terms') }}">قوانین و مقررات</a></li>
            </ul></nav>
            <section aria-labelledby="cps-contact-title"><h2 id="cps-contact-title">در تماس باشیم</h2>
                <address><a class="cps-contact-row" href="tel:+984134244933">@include('website.partials.footer-icon',['icon'=>'phone'])<bdi dir="ltr">۰۴۱ ۳۴۲۴ ۴۹۳۳</bdi></a><a class="cps-contact-row" href="mailto:contact@karimmohammadzadeh.ir">@include('website.partials.footer-icon',['icon'=>'mail'])<bdi dir="ltr">contact@karimmohammadzadeh.ir</bdi></a><span class="cps-contact-row">@include('website.partials.footer-icon',['icon'=>'pin'])تبریز، آبرسان، طبقه اول ساختمان بانک سپه</span></address>
                <p class="cps-hours cps-contact-row">@include('website.partials.footer-icon',['icon'=>'clock'])<span>10 تا 19، پنجشنبه و جمعه از 10 تا 13 </span></p>
            </section>
            <section aria-labelledby="cps-trust-title"><h2 id="cps-trust-title">مجوزها</h2>
                <div class="cps-badges">
                    <a class="cps-badge" referrerpolicy="origin" target="_blank" rel="noopener" href="https://trustseal.enamad.ir/?id=7882320&Code=secret-b8d269bf" aria-label="استعلام اینماد (پنجرهٔ جدید)"><img referrerpolicy="origin" src="https://trustseal.enamad.ir/logo.aspx?id=7882320&Code=secret-b8d269bf" alt="اینماد" code="secret-b8d269bf"></a>
                    <a class="cps-badge" href="https://my.pcoiran.ir/member/51308/54d371a1-92cb-4157-b65f-efc840f56fcd/" target="_blank" rel="noopener noreferrer" aria-label="استعلام پروانه اشتغال ۵۱۳۰۸ (پنجرهٔ جدید)"><span>پروانه اشتغال<br><b>۵۱۳۰۸</b></span></a>
                </div>
            </section>
        </div>
        <div class="cps-bottom"><p>© {{ now()->year }} تمامی حقوق برای کریم محمدزاده محفوظ است.</p></div>
    </div>
</footer>
