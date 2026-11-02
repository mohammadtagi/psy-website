(() => {
    'use strict';
    if (!window.fetch || !window.FormData || !window.AbortController) return;
    document.querySelectorAll('[data-ccd-note]').forEach(form => {
        const field = form.querySelector('[name="body"]');
        const button = form.querySelector('[type="submit"]');
        const status = form.querySelector('[role="status"]');
        let busy = false;
        form.addEventListener('submit', async event => {
            event.preventDefault();
            if (busy || !form.reportValidity()) return;
            busy = true;
            const label = button.textContent;
            const payload = new FormData(form);
            button.disabled = true;
            field.readOnly = true;
            form.setAttribute('aria-busy', 'true');
            field.removeAttribute('aria-invalid');
            const oldError = form.querySelector('#ccd-note-error');
            if (oldError) oldError.hidden = true;
            status.textContent = 'در حال ذخیره…';
            button.textContent = 'در حال ذخیره…';
            const controller = new AbortController();
            const timeout = setTimeout(() => controller.abort(), 30000);
            try {
                const response = await fetch(form.action, {
                    method: 'POST', body: payload, credentials: 'same-origin',
                    headers: { Accept: 'application/json' }, signal: controller.signal
                });
                if (response.status === 422) {
                    const result = await response.json();
                    status.textContent = result.errors?.body?.[0] || 'متن یادداشت را بررسی کنید.';
                    field.setAttribute('aria-invalid', 'true');
                    field.focus();
                } else if (response.status === 419 || response.status === 401) {
                    status.textContent = 'نشست شما پایان یافته است. متن را کپی کنید و پس از ورود دوباره، تلاش کنید.';
                } else if (response.status === 403) {
                    status.textContent = 'اجازه ثبت یادداشت برای این نوبت را ندارید. متن شما حفظ شده است.';
                } else {
                    const expected = new URL(form.dataset.successUrl, location.href);
                    const actual = new URL(response.url);
                    if (!response.ok || !response.redirected || actual.origin !== expected.origin || actual.pathname !== expected.pathname || actual.search !== expected.search) {
                        throw new Error('Unconfirmed save');
                    }
                    field.value = '';
                    status.textContent = 'یادداشت برای این جلسه ذخیره شد. از لینک زیر می‌توانید آن را ببینید.';
                }
            } catch (_) {
                status.textContent = 'نتیجه ذخیره مشخص نشد؛ متن حفظ شده است. پیش از ارسال دوباره، یادداشت‌های جلسه را در زبانه دیگری بررسی کنید تا یادداشت تکراری ساخته نشود.';
            } finally {
                clearTimeout(timeout);
                busy = false;
                button.disabled = false;
                field.readOnly = false;
                button.textContent = label;
                form.removeAttribute('aria-busy');
            }
        });
    });
})();
