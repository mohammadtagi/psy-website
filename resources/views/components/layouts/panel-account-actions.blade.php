<div class="panel-mobile-account-actions" aria-label="دسترسی‌های حساب">
    <a href="{{ route('home') }}">بازگشت به وب‌سایت</a>
    <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">خروج از حساب</button></form>
</div>
