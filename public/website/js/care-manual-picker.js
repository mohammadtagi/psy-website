(() => {
    'use strict';
    const init = () => {
        const launch = document.getElementById('cmp-open');
        const form = document.querySelector('#manual-session form');
        if (!launch || !form || typeof HTMLDialogElement === 'undefined' || !HTMLDialogElement.prototype.showModal) return;
        let fmt;
        try {
            fmt = new Intl.DateTimeFormat('en-US-u-ca-persian-nu-latn', { timeZone: 'Asia/Tehran', year: 'numeric', month: 'numeric', day: 'numeric' });
            if (fmt.resolvedOptions().calendar !== 'persian') return;
        } catch (_) { return; }
        const dayMs = 86400000;
        const parts = date => {
            const p = Object.fromEntries(fmt.formatToParts(date).map(x => [x.type, x.value]));
            return { y: Number(p.year), m: Number(p.month), d: Number(p.day) };
        };
        const digits = value => String(value).replace(/[۰-۹]/g, x => '۰۱۲۳۴۵۶۷۸۹'.indexOf(x)).replace(/[٠-٩]/g, x => '٠١٢٣٤٥٦٧٨٩'.indexOf(x));
        const fa = value => String(value).replace(/\d/g, x => '۰۱۲۳۴۵۶۷۸۹'[Number(x)]);
        const key = date => { const p = parts(date); return `${p.y}/${String(p.m).padStart(2, '0')}/${String(p.d).padStart(2, '0')}`; };
        const add = (date, n) => new Date(date.getTime() + n * dayMs);
        const parse = value => {
            const match = digits(value.trim()).match(/^(\d{4})\/(\d{1,2})\/(\d{1,2})$/);
            if (!match) return null;
            const [y, m, d] = match.slice(1).map(Number);
            if (y < 1200 || y > 1600 || m < 1 || m > 12 || d < 1 || d > 31) return null;
            const base = new Date(Date.UTC(y + 621, 2, 1, 12));
            for (let i = 0; i < 400; i++) {
                const date = add(base, i), p = parts(date);
                if (p.y === y && p.m === m && p.d === d) return date;
            }
            return null;
        };
        const monthStart = date => add(date, 1 - parts(date).d);
        const monthDays = date => { let n = 28; while (parts(add(date, n)).m === parts(date).m) n++; return n; };
        const today = () => parse(key(new Date()));
        const months = ['فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند'];
        const from = form.querySelector('#manual-date'), start = form.querySelector('#manual-time');
        const opener = launch;
        const dialog = document.createElement('dialog');
        dialog.className = 'cdp cmp-dialog';
        dialog.dir = 'rtl';
        dialog.setAttribute('aria-labelledby', 'cmp-title');
        dialog.innerHTML = `
            <header class="cdp-head"><h2 id="cmp-title">انتخاب تاریخ و ساعت</h2><button type="button" data-close aria-label="بستن انتخابگر">×</button></header>
            <p class="cdp-step" aria-live="polite"></p>
            <section data-calendar>
                <div class="cdp-nav"><button type="button" data-prev aria-label="ماه قبل">ماه قبل</button><strong data-month></strong><button type="button" data-next aria-label="ماه بعد">ماه بعد</button></div>
                <div class="cdp-week" aria-hidden="true"><span>شنبه</span><span>یکشنبه</span><span>دوشنبه</span><span>سه‌شنبه</span><span>چهارشنبه</span><span>پنجشنبه</span><span>جمعه</span></div>
                <div class="cdp-days" role="group" aria-label="روزهای ماه"></div>
                <button type="button" data-today>رفتن به امروز</button>
                <p class="cdp-help">تاریخ‌های گذشته و آینده قابل انتخاب‌اند؛ اعتبارسنجی نهایی در سرور انجام می‌شود.</p>
            </section>
            <section data-times hidden><div class="cdp-times"><label>ساعت شروع<input type="time" data-start dir="ltr" required></label></div><p class="cdp-help">ساعت شروع به وقت تهران؛ مدت جلسه در فرم اصلی تعیین می‌شود.</p></section>
            <p class="cdp-summary"></p><p class="cdp-error" role="alert"></p>
            <footer class="cdp-footer"><button type="button" data-back>مرحله قبل</button><button type="button" data-forward class="cdp-primary">ادامه</button></footer>`;
        document.body.append(dialog);
        const q = selector => dialog.querySelector(selector);
        let step = 0, recurring = false, selection = [], month, focusDate;
        const timeStep = () => 1;
        const selectedIndex = () => 0;
        const lower = () => today();
        const permitted = date => !!date && parts(date).y >= 1200 && parts(date).y <= 1600;
        const render = (focus = false) => {
            const isTime = step === timeStep();
            q('.cdp-step').textContent = `مرحله ${fa(step + 1)} از ${fa(timeStep() + 1)} · ${isTime ? 'ساعت شروع' : step === 0 ? (recurring ? 'تاریخ شروع محدوده' : 'تاریخ حضور') : 'تاریخ پایان محدوده'}`;
            q('[data-calendar]').hidden = isTime;
            q('[data-times]').hidden = !isTime;
            q('[data-back]').hidden = step === 0;
            q('[data-forward]').textContent = isTime ? 'تأیید تاریخ و ساعت' : 'ادامه';
            q('.cdp-error').textContent = '';
            q('.cdp-summary').textContent = selection[0] ? `تاریخ: ${fa(key(selection[0]))}${recurring && selection[1] ? ' تا ' + fa(key(selection[1])) : ''}${recurring ? ' · ساعت برای همه روزهای منتخب یکسان است.' : ''}` : 'تاریخ موردنظر را انتخاب کنید.';
            if (isTime) { if (focus) q('[data-start]').focus(); return; }
            const p = parts(month);
            q('[data-month]').textContent = `${months[p.m - 1]} ${fa(p.y)}`;
            const grid = q('.cdp-days'); grid.replaceChildren();
            const offset = (month.getUTCDay() + 1) % 7;
            for (let i = 0; i < offset; i++) grid.append(document.createElement('span'));
            const current = selection[selectedIndex()];
            let firstEnabled;
            for (let i = 0; i < monthDays(month); i++) {
                const date = add(month, i), button = document.createElement('button');
                button.type = 'button'; button.textContent = fa(i + 1); button.dataset.date = key(date);
                button.setAttribute('aria-label', `${fa(i + 1)} ${months[p.m - 1]} ${fa(p.y)}`);
                button.setAttribute('aria-pressed', String(!!current && key(current) === key(date)));
                if (key(date) === key(today())) button.setAttribute('aria-current', 'date');
                button.disabled = !permitted(date);
                if (!button.disabled && !firstEnabled) firstEnabled = button;
                button.addEventListener('click', () => {
                    selection[selectedIndex()] = date;
                    if (step === 0 && selection[1] && (selection[1] < date || selection[1] > add(date, 365))) selection[1] = null;
                    focusDate = date; render(true);
                });
                button.addEventListener('keydown', event => {
                    const delta = { ArrowLeft: 1, ArrowRight: -1, ArrowUp: -7, ArrowDown: 7 }[event.key];
                    if (delta === undefined) return;
                    event.preventDefault(); const next = add(date, delta);
                    if (!permitted(next)) return;
                    focusDate = next; month = monthStart(next); render(true);
                });
                grid.append(button);
            }
            const target = grid.querySelector(`[data-date="${focusDate ? key(focusDate) : current ? key(current) : ''}"]:not(:disabled)`) || firstEnabled;
            grid.querySelectorAll('button').forEach(button => button.tabIndex = button === target ? 0 : -1);
            if (focus) (target || q('[data-next]')).focus();
        };
        opener.addEventListener('click', () => {
            selection = [parse(from.value)];
            step = 0; focusDate = selection[0] || today(); month = monthStart(focusDate);
            q('[data-start]').value = start.value;
            render(); dialog.showModal(); render(true);
        });
        q('[data-close]').addEventListener('click', () => dialog.close());
        dialog.addEventListener('close', () => opener.focus());
        q('[data-prev]').addEventListener('click', () => { month = monthStart(add(month, -1)); focusDate = null; render(); });
        q('[data-next]').addEventListener('click', () => { month = add(month, monthDays(month)); focusDate = null; render(); });
        q('[data-today]').addEventListener('click', () => { focusDate = lower(); month = monthStart(focusDate); render(true); });
        q('[data-back]').addEventListener('click', () => { step--; focusDate = selection[selectedIndex()] || lower(); month = monthStart(focusDate); render(true); });
        q('[data-forward]').addEventListener('click', () => {
            if (step < timeStep()) {
                if (!selection[selectedIndex()] || !permitted(selection[selectedIndex()])) { q('.cdp-error').textContent = 'یک تاریخ مجاز انتخاب کنید.'; return; }
                step++; focusDate = selection[selectedIndex()] || lower(); month = monthStart(focusDate); render(true); return;
            }
            const a = q('[data-start]');
            if (!a.reportValidity()) return;
            from.value = key(selection[0]);
            start.value = a.value;
            [from, start].forEach(input => {
                input.dispatchEvent(new Event('input', { bubbles: true }));
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });
            dialog.close();
        });
        launch.hidden = false;
    };
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
