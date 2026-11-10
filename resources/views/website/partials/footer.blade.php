<footer class="cps-footer" aria-label="پاورقی سایت">
    <div class="cps-wrap">
        <div class="cps-invite">
            <div><span class="cps-eyebrow">یک قدم برای شروع</span><h2>برای شروع گفتگو آماده‌اید؟</h2><p>زمان‌های قابل رزرو را ببینید یا دربارهٔ روند جلسات از ما بپرسید.</p></div>
            <div class="cps-invite-actions"><a class="cps-button" href="{{ route('booking.index') }}">مشاهده زمان‌ها و رزرو نوبت <span aria-hidden="true">←</span></a><a class="cps-contact-link" href="{{ route('website.contact') }}">تماس و راهنمایی</a></div>
        </div>
        <div class="cps-footer-grid">
            <section aria-labelledby="cps-brand-title">
                <a class="cps-brand" href="{{ route('home') }}"><img src="{{ asset('website/images/logo-mini.webp') }}" width="52" height="52" loading="lazy" alt=""><span id="cps-brand-title"><strong>کریم محمدزاده</strong><small>روان‌شناس و روان‌درمانگر</small></span></a>
                <p>برای آشنایی با رویکرد درمانی، خدمات مشاوره و روند برگزاری جلسات، همراه شما هستیم.</p>
                <a class="cps-license" href="https://my.pcoiran.ir/member/51308/54d371a1-92cb-4157-b65f-efc840f56fcd/" target="_blank" rel="noopener noreferrer">پروانه اشتغال: ۵۱۳۰۸<br><small>مشاهده در سازمان نظام روان‌شناسی ↗ <span class="cps-sr">(پنجرهٔ جدید)</span></small></a>
            </section>
            <nav aria-labelledby="cps-links-title"><h2 id="cps-links-title">دسترسی سریع</h2><ul>
                <li><a href="{{ route('website.services') }}">خدمات مشاوره</a></li>
                <li><a href="{{ route('website.about') }}">درباره من</a></li>
                <li><a href="{{ route('website.faq') }}">سؤالات متداول</a></li>
                <li><a href="{{ route('website.contact') }}">تماس با ما</a></li>
                <li><a href="{{ route('booking.index') }}">رزرو نوبت</a></li>
            </ul></nav>
            <section aria-labelledby="cps-contact-title"><h2 id="cps-contact-title">در تماس باشیم</h2>
                <address><a href="tel:+984134244933" dir="ltr">۰۴۱ ۳۴۲۴ ۴۹۳۳</a><a href="mailto:contact@karimmohammadzadeh.ir" dir="ltr">contact@karimmohammadzadeh.ir</a><span>تبریز، آبرسان، طبقه اول ساختمان بانک سپه</span></address>
                <p class="cps-hours">ساعات پاسخ‌گویی<br><strong>شنبه تا چهارشنبه، ۸ تا ۱۶</strong></p>
            </section>
            <section class="cps-trust" aria-labelledby="cps-trust-title"><h2 id="cps-trust-title">نماد اعتماد الکترونیکی</h2>
                <div class="cps-seal"><a referrerpolicy="origin" target="_blank" rel="noopener" href="https://trustseal.enamad.ir/?id=7882320&Code=secret-b8d269bf" aria-label="استعلام نماد اعتماد الکترونیکی (پنجرهٔ جدید)"><img referrerpolicy="origin" src="https://trustseal.enamad.ir/logo.aspx?id=7882320&Code=secret-b8d269bf" alt="استعلام نماد اعتماد الکترونیکی" style="cursor:pointer" code="secret-b8d269bf"></a></div>
                <p>برای مشاهدهٔ وضعیت اعتبار، روی نماد کلیک کنید.</p>
                <a class="cps-verify" href="https://trustseal.enamad.ir/?id=7882320&Code=secret-b8d269bf" target="_blank" rel="noopener" referrerpolicy="origin">استعلام از سایت اینماد ↗<span class="cps-sr"> (پنجرهٔ جدید)</span></a>
            </section>
        </div>
        <div class="cps-bottom"><p>© {{ now()->year }} تمامی حقوق برای کریم محمدزاده محفوظ است.</p><a href="{{ route('home') }}">صفحهٔ اصلی</a></div>
    </div>
</footer>
