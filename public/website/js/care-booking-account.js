(() => {
    'use strict';
    const root = document.querySelector('[data-booking-account]');
    if (!root) return;
    const q = s => root.querySelector(s);
    const dialog = q('dialog');
    const mobileForm = q('[data-mobile-form]');
    const codeForm = q('[data-code-form]');
    const profileForm = q('[data-profile-form]');
    const message = q('[data-login-message]');
    const digits = value => String(value).replace(/[۰-۹٠-٩]/g, c => String('۰۱۲۳۴۵۶۷۸۹'.includes(c) ? '۰۱۲۳۴۵۶۷۸۹'.indexOf(c) : '٠١٢٣٤٥٦٧٨٩'.indexOf(c)));
    let busy = false, resendAt = 0, expiresAt = 0, opener;
    function token() { return document.querySelector('meta[name="csrf-token"]')?.content || ''; }
    function setToken(value) {
        if (!value) return;
        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) meta.content = value;
        document.querySelectorAll('input[name="_token"]').forEach(el => { el.value = value; });
    }
    async function request(url, method = 'GET', body) {
        const response = await fetch(url, {
            method, credentials: 'same-origin', cache: 'no-store',
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token(), ...(body ? { 'Content-Type': 'application/json' } : {}) },
            ...(body ? { body: JSON.stringify(body) } : {})
        });
        const data = await response.json().catch(() => ({}));
        if (!response.ok || response.redirected) {
            const error = new Error(response.status === 419 ? 'نشست شما منقضی شده است. صفحه را تازه کنید و دوباره تلاش کنید.' : response.status === 401 ? 'برای ادامه دوباره وارد حساب شوید.' : response.status === 403 ? 'این بخش فقط برای حساب مراجع در دسترس است.' : response.status === 429 ? 'تعداد درخواست‌ها زیاد است؛ کمی صبر کنید و دوباره تلاش کنید.' : data.message || 'دریافت پاسخ ممکن نشد. اتصال اینترنت را بررسی و دوباره تلاش کنید.');
            error.errors = data.errors || {};
            error.status = response.status;
            error.retry = Number(response.headers.get('Retry-After')) || 60;
            throw error;
        }
        setToken(data.csrf_token);
        return data;
    }
    function clearErrors(form) {
        form.querySelectorAll('[data-error]').forEach(el => { el.textContent = ''; });
        form.querySelectorAll('[aria-invalid]').forEach(el => el.removeAttribute('aria-invalid'));
    }
    function report(error, form, box) {
        box.textContent = error.message || 'ارتباط با سرور برقرار نشد. دوباره تلاش کنید.';
        Object.entries(error.errors || {}).forEach(([name, errors]) => {
            const output = Array.from(form.querySelectorAll('[data-error]')).find(el => el.dataset.error === name);
            if (output) output.textContent = Array.isArray(errors) ? errors.join(' ') : String(errors);
            form.elements.namedItem(name)?.setAttribute('aria-invalid', 'true');
        });
        form.querySelector('[aria-invalid]')?.focus();
        if (error.status === 429) resendAt = Date.now() + error.retry * 1000;
    }
    async function run(task) {
        if (busy) return;
        busy = true;
        root.setAttribute('aria-busy', 'true');
        root.querySelectorAll('button').forEach(el => { el.disabled = true; });
        try { await task(); }
        finally {
            busy = false; root.removeAttribute('aria-busy');
            root.querySelectorAll('button').forEach(el => { el.disabled = false; });
            tick();
        }
    }
    function render(data) {
        const user = data.user;
        if (data.authenticated && user) {
            const dashboard = { client: root.dataset.clientDashboard, psychologist: root.dataset.psychologistDashboard, admin: root.dataset.adminDashboard }[user.role];
            document.querySelectorAll('a').forEach(link => {
                if (link.href === root.dataset.login && dashboard) {
                    link.removeEventListener('click', openLogin);
                    link.href = dashboard;
                    const label = link.querySelector('span');
                    if (label) label.textContent = 'داشبورد';
                    else if (!link.querySelector('svg')) link.textContent = 'داشبورد';
                }
            });
        }
        q('[data-status]').hidden = true;
        q('[data-retry]').hidden = true;
        q('[data-guest]').hidden = data.authenticated;
        q('[data-profile]').hidden = !data.authenticated || user?.role !== 'client';
        q('[data-dates]').hidden = true;
        if (data.authenticated && user?.role !== 'client') {
            q('[data-status]').hidden = false;
            q('[data-status]').textContent = 'شما با حساب مشاور یا مدیر وارد شده‌اید. برای رزرو، از حساب مراجع استفاده کنید.';
            return;
        }
        if (user) ['mobile', 'first_name', 'last_name', 'birth_date_jalali'].forEach(name => { profileForm.elements.namedItem(name).value = user[name] || ''; });
    }
    async function load() {
        await run(async () => {
            q('[data-status]').hidden = false;
            q('[data-status]').textContent = 'در حال دریافت اطلاعات حساب…';
            try { render(await request(root.dataset.account)); }
            catch (error) {
                q('[data-status]').textContent = error.message;
                q('[data-retry]').hidden = false;
            }
        });
    }
    function openLogin(event) {
        event?.preventDefault();
        opener = event?.currentTarget || document.activeElement;
        if (!dialog.open) dialog.showModal();
        (codeForm.hidden ? mobileForm.elements.mobile : codeForm.elements.code).focus();
    }
    q('[data-open-login]').addEventListener('click', openLogin);
    // Use the same modal for the existing guest login links; no layout change is needed.
    document.querySelectorAll('a').forEach(link => {
        if (link.href === root.dataset.login) link.addEventListener('click', openLogin);
    });
    q('[data-close]').addEventListener('click', () => dialog.close());
    dialog.addEventListener('cancel', event => { if (busy) event.preventDefault(); });
    dialog.addEventListener('close', () => opener?.focus());
    function sent(data) {
        mobileForm.hidden = true; codeForm.hidden = false;
        message.textContent = '';
        q('[data-otp-description]').textContent = `کد تأیید به ${data.mobile_masked || 'شمارهٔ شما'} ارسال شد.`;
        const length = Number(data.otp_length) || 6;
        codeForm.elements.code.maxLength = length;
        codeForm.elements.code.minLength = length;
        codeForm.elements.code.value = '';
        resendAt = Date.now() + Math.max(0, Number(data.resend_after) || 0) * 1000;
        expiresAt = Date.now() + Math.max(0, Number(data.expires_in) || 0) * 1000;
        codeForm.elements.code.focus(); tick();
    }
    mobileForm.addEventListener('submit', event => {
        event.preventDefault();
        run(async () => {
            clearErrors(mobileForm); message.textContent = 'در حال ارسال کد…';
            try { sent(await request(root.dataset.send, 'POST', { mobile: digits(mobileForm.elements.mobile.value.trim()), remember: false })); }
            catch (error) { report(error, mobileForm, message); }
        });
    });
    codeForm.addEventListener('submit', event => {
        event.preventDefault();
        run(async () => {
            clearErrors(codeForm); message.textContent = 'در حال بررسی کد…';
            try {
                const data = await request(root.dataset.verify, 'POST', { code: digits(codeForm.elements.code.value.trim()) });
                render(data); dialog.close();
                if (data.user?.role === 'client') profileForm.elements.first_name.focus();
                else q('.bka-heading h1').focus();
                message.textContent = '';
            } catch (error) { report(error, codeForm, message); }
        });
    });
    q('[data-resend-code]').addEventListener('click', () => {
        if (Date.now() < resendAt) return;
        run(async () => {
            message.textContent = 'در حال ارسال مجدد…';
            try { sent(await request(root.dataset.resend, 'POST', {})); }
            catch (error) { report(error, codeForm, message); }
        });
    });
    q('[data-change-mobile]').addEventListener('click', () => {
        mobileForm.hidden = false; codeForm.hidden = true;
        message.textContent = ''; clearErrors(mobileForm); clearErrors(codeForm);
        q('[data-otp-description]').textContent = 'شمارهٔ صحیح را وارد کنید تا کد تازه ارسال شود.';
        mobileForm.elements.mobile.focus();
    });
    function stage(number) {
        root.querySelectorAll('.bka-steps li').forEach((el, i) => {
            el.removeAttribute('aria-current');
            if (i === number - 1) el.setAttribute('aria-current', 'step');
        });
    }
    profileForm.addEventListener('submit', event => {
        event.preventDefault();
        run(async () => {
            clearErrors(profileForm);
            const box = q('[data-profile-message]'); box.textContent = 'در حال ذخیرهٔ اطلاعات…';
            const body = {};
            ['first_name', 'last_name', 'birth_date_jalali'].forEach(name => { body[name] = profileForm.elements.namedItem(name).value.trim(); });
            body.birth_date_jalali = digits(body.birth_date_jalali);
            try {
                const data = await request(root.dataset.save, 'PUT', body);
                if (!data.profile_complete) throw new Error('لطفاً اطلاعات حساب را کامل کنید.');
                render(data); box.textContent = '';
                q('[data-profile]').hidden = true; q('[data-dates]').hidden = false;
                stage(2); q('[data-dates] h2').focus();
            } catch (error) {
                report(error, profileForm, box);
                if (error.status === 401) openLogin();
            }
        });
    });
    q('[data-back]').addEventListener('click', () => {
        q('[data-dates]').hidden = true; q('[data-profile]').hidden = false;
        stage(1); profileForm.elements.first_name.focus();
    });
    q('[data-retry]').addEventListener('click', load);
    function tick() {
        const seconds = Math.max(0, Math.ceil((resendAt - Date.now()) / 1000));
        const resend = q('[data-resend-code]');
        resend.disabled = busy || seconds > 0;
        resend.textContent = seconds ? `ارسال مجدد تا ${seconds.toLocaleString('fa-IR')} ثانیه` : 'ارسال مجدد کد';
        const remaining = Math.max(0, Math.ceil((expiresAt - Date.now()) / 1000));
        q('[data-expiry]').textContent = expiresAt ? remaining ? `اعتبار کد: ${remaining.toLocaleString('fa-IR')} ثانیه` : 'اعتبار کد تمام شده است؛ کد تازه دریافت کنید.' : '';
    }
    setInterval(tick, 1000);
    load();
})();
