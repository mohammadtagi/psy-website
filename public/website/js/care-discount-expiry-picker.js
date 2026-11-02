(() => {
    'use strict';
    const init = () => {
        const openers = document.querySelectorAll('#dc-expiry-open');
        const form = document.getElementById('expires-date')?.form;
        if (!openers.length || !form || typeof HTMLDialogElement === 'undefined') return;
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
        const dateInput = form.querySelector('#expires-date'), timeInput = form.querySelector('#expires-time');
        if (!dateInput || !timeInput) return;
        let opener, target;
        const dialog = document.createElement('dialog');
        dialog.className = 'dcep';
        dialog.dir = 'rtl';
        dialog.setAttribute('aria-labelledby', 'dcep-title');
        dialog.innerHTML = `
            <header class="dcep-head"><h2 id="dcep-title">انتخاب تاریخ و ساعت انقضا</h2><button type="button" data-close aria-label="بستن انتخابگر">×</button></header>
            <p class="dcep-step" aria-live="polite"></p>
            <section data-calendar>
                <div class="dcep-nav"><button type="button" data-prev aria-label="ماه قبل">ماه قبل</button><strong data-month></strong><button type="button" data-next aria-label="ماه بعد">ماه بعد</button></div>
                <div class="dcep-week" aria-hidden="true"><span>شنبه</span><span>یکشنبه</span><span>دوشنبه</span><span>سه‌شنبه</span><span>چهارشنبه</span><span>پنجشنبه</span><span>جمعه</span></div>
                <div class="dcep-days" role="group" aria-label="روزهای ماه"></div>
                <button type="button" data-today>رفتن به امروز</button>
                <p class="dcep-help">تاریخ و ساعت به وقت تهران است. اعتبار کد در ابتدای دقیقه انتخاب‌شده پایان می‌یابد.</p>
            </section>
            <label class="dcep-time-label">ساعت انقضا به وقت تهران<input type="time" data-time dir="ltr" step="60" required></label>
            <p class="dcep-selection" aria-live="polite"></p>
            <p class="dcep-error" role="alert"></p>
            <footer class="dcep-footer"><button type="button" data-confirm class="dcep-primary">تأیید تاریخ و ساعت</button></footer>`;
        document.body.append(dialog);
        const q = selector => dialog.querySelector(selector);
        let month, focusDate, selected;
        const lower = () => today();
        const permitted = date => date >= today();
        const render = (focus = false) => {
            q('.dcep-step').textContent = 'روز موردنظر و سپس ساعت انقضا را انتخاب کنید.';
            q('.dcep-selection').textContent = selected ? `تاریخ انتخابی: ${fa(key(selected))}` : 'هنوز تاریخی انتخاب نشده است.';
            const p = parts(month);
            q('[data-month]').textContent = `${months[p.m - 1]} ${fa(p.y)}`;
            const grid = q('.dcep-days'); grid.replaceChildren();
            const offset = (month.getUTCDay() + 1) % 7;
            for (let i = 0; i < offset; i++) grid.append(document.createElement('span'));
            const current = selected;
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
                    selected = date; focusDate = date; q('.dcep-error').textContent = ''; render(true);
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
            const focusTarget = grid.querySelector(`[data-date="${focusDate ? key(focusDate) : current ? key(current) : ''}"]:not(:disabled)`) || firstEnabled;
            grid.querySelectorAll('button').forEach(button => button.tabIndex = button === focusTarget ? 0 : -1);
            if (focus) (focusTarget || q('[data-next]')).focus();
        };
        openers.forEach(button => {
            button.hidden = false;
            button.addEventListener('click', () => {
                opener = button; target = dateInput;
                selected = parse(target.value);
                if (selected && !permitted(selected)) selected = null;
                const normalizedTime = digits(timeInput.value.trim());
                const timeMatch = normalizedTime.match(/^(\d{1,2}):(\d{2})$/);
                q('[data-time]').value = timeMatch ? `${timeMatch[1].padStart(2, '0')}:${timeMatch[2]}` : '';
                q('.dcep-error').textContent = '';
                focusDate = selected && permitted(selected) ? selected : lower();
                month = monthStart(focusDate);
                render(); dialog.showModal(); render(true);
            });
        });
        q('[data-confirm]').addEventListener('click', () => {
            const clock = q('[data-time]');
            if (!selected || !permitted(selected)) { q('.dcep-error').textContent = 'یک تاریخ از امروز به بعد انتخاب کنید.'; return; }
            if (!clock.reportValidity()) return;
            const nowParts = Object.fromEntries(new Intl.DateTimeFormat('en-GB', { timeZone: 'Asia/Tehran', hour: '2-digit', minute: '2-digit', hourCycle: 'h23' }).formatToParts(new Date()).map(part => [part.type, part.value]));
            if (key(selected) === key(today()) && clock.value <= `${nowParts.hour}:${nowParts.minute}`) {
                q('.dcep-error').textContent = 'برای امروز، ساعتی بعد از زمان فعلی تهران انتخاب کنید.';
                clock.focus(); return;
            }
            dateInput.value = key(selected); timeInput.value = clock.value;
            [dateInput, timeInput].forEach(input => {
                input.dispatchEvent(new Event('input', { bubbles: true }));
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });
            dialog.close();
        });
        q('[data-close]').addEventListener('click', () => dialog.close());
        dialog.addEventListener('close', () => opener.focus());
        q('[data-prev]').addEventListener('click', () => { month = monthStart(add(month, -1)); focusDate = null; render(); });
        q('[data-next]').addEventListener('click', () => { month = add(month, monthDays(month)); focusDate = null; render(); });
        q('[data-today]').addEventListener('click', () => { focusDate = lower(); month = monthStart(focusDate); render(true); });
    };
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
