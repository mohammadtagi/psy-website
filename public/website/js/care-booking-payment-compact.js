(() => {
    'use strict';
    document.querySelectorAll('[data-cancel-appointment]').forEach(form => {
        form.addEventListener('submit', event => {
            if (!window.confirm('آیا از لغو این نوبت مطمئن هستید؟')) event.preventDefault();
        });
    });
    const timer = document.querySelector('[data-payment-countdown]');
    if (!timer) return;
    const value = timer.querySelector('[data-countdown-value]');
    const remaining = Number(timer.dataset.remaining);
    if (!value || !Number.isFinite(remaining) || remaining < 0) return;
    // Server-derived duration; no local-storage reset and no client clock dependency.
    const deadline = Date.now() + remaining * 1000;
    let interval;
    let expired = false;
    function tick() {
        if (expired) return;
        const seconds = Math.max(0, Math.ceil((deadline - Date.now()) / 1000));
        value.textContent = `${String(Math.floor(seconds / 60)).padStart(2, '0')}:${String(seconds % 60).padStart(2, '0')}`;
        if (seconds > 0) return;
        expired = true;
        clearInterval(interval);
        timer.textContent = 'مهلت تکمیل پایان یافت؛ صفحه را تازه کنید.';
        const notice = timer.closest('.cbp-pending')?.querySelector('p');
        if (notice) notice.textContent = 'نوبت شما تکمیل نشده و مهلت ادامهٔ پرداخت پایان یافته است.';
        document.querySelectorAll('.cbc-invoice form').forEach(form => {
            form.querySelectorAll('button[type="submit"]').forEach(button => { button.disabled = true; });
            form.addEventListener('submit', event => event.preventDefault());
        });
    }
    interval = setInterval(tick, 1000);
    tick();
    document.addEventListener('visibilitychange', tick);
    window.addEventListener('pageshow', tick);
})();
