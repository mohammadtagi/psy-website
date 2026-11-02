(() => {
    'use strict';
    const init = () => {
        const openers = document.querySelectorAll('[data-date-target]');
        const form = document.getElementById('cv-form');
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
        const sourceFrom = form.querySelector('#cv-from'), sourceTo = form.querySelector('#cv-to');
        const from = { value: '' }, to = { value: '' };
        let recurring;
        const dayInputs = [...form.querySelectorAll('[name="weekdays[]"]')];
        let opener, target;
        const dialog = document.createElement('dialog');
        dialog.className = 'cdp cap';
        const style = document.createElement('style');
        style.textContent = `.cap{max-height:90vh;overflow:auto}.cap [hidden]{display:none!important}.cap-dates,.cap-times,.cap-actions{display:flex;gap:10px;margin:12px 0;flex-wrap:wrap}.cap-dates button,.cap-times label{flex:1;min-width:120px}.cap-dates [aria-pressed="true"]{outline:2px solid #16836b;outline-offset:2px}.cap-times input{display:block;box-sizing:border-box;width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:10px;font:inherit}.cap-days{display:flex;flex-wrap:wrap;gap:12px}.cap-days label{display:flex;align-items:center;gap:5px}.cap fieldset{border:1px solid #cbd5e1;border-radius:10px;padding:12px}.cap-actions button{padding:10px 14px}.cap [data-confirm]{background:#147d65;color:white;border:0;border-radius:10px}`;
        document.head.append(style);
        dialog.dir = 'rtl';
        dialog.setAttribute('aria-labelledby', 'cdp-title');
        dialog.innerHTML = `
            <header class="cdp-head"><h2 id="cdp-title">تنظیم تاریخ و ساعت حضور</h2><button type="button" data-close aria-label="بستن انتخابگر">×</button></header>
            <div class="cap-dates"><button type="button" data-from></button><button type="button" data-to></button></div>
            <p class="cdp-step" aria-live="polite"></p>
            <section data-calendar>
                <div class="cdp-nav"><button type="button" data-prev aria-label="ماه قبل">ماه قبل</button><strong data-month></strong><button type="button" data-next aria-label="ماه بعد">ماه بعد</button></div>
                <div class="cdp-week" aria-hidden="true"><span>شنبه</span><span>یکشنبه</span><span>دوشنبه</span><span>سه‌شنبه</span><span>چهارشنبه</span><span>پنجشنبه</span><span>جمعه</span></div>
                <div class="cdp-days" role="group" aria-label="روزهای ماه"></div>
                <button type="button" data-today>رفتن به امروز</button>
                <p class="cdp-help">روزهای قبل از تاریخ مجاز قابل انتخاب نیستند. تاریخ نهایی هنگام ثبت در سرور بررسی می‌شود.</p>
            </section>
            <div class="cap-times"><label>ساعت شروع<input type="time" data-start required dir="ltr"></label><label>ساعت پایان<input type="time" data-end required dir="ltr"></label></div>
            <p class="cdp-help">ساعت‌ها به وقت تهران؛ پایان باید بعد از شروع و در همان روز باشد. اعتبارسنجی نهایی هنگام ثبت انجام می‌شود.</p>
            <fieldset data-weekdays><legend>روزهای تکرار</legend><div class="cap-days"></div></fieldset>
            <p class="cdp-error" role="alert"></p>
            <footer class="cap-actions"><button type="button" data-confirm>تأیید و انتقال به فرم</button><button type="button" data-cancel>انصراف</button></footer>`;
        document.body.append(dialog);
        const q = selector => dialog.querySelector(selector);
        let month, focusDate, selected;
        const lower = () => target === to && parse(from.value) > today() ? parse(from.value) : today();
        const upper = () => target === to && parse(from.value) ? add(parse(from.value), 365) : null;
        const permitted = date => date >= lower() && (!upper() || date <= upper());
        const render = (focus = false) => {
            selected = parse(target.value);
            q('[data-from]').textContent = `${recurring ? 'شروع' : 'تاریخ'}: ${fa(from.value || 'انتخاب کنید')}`;
            q('[data-to]').textContent = `پایان: ${fa(to.value || 'انتخاب کنید')}`;
            q('[data-from]').setAttribute('aria-pressed', String(target === from));
            q('[data-to]').setAttribute('aria-pressed', String(target === to));
            q('.cdp-step').textContent = target === to ? 'تاریخ پایان محدوده را انتخاب کنید' : 'تاریخ شروع را انتخاب کنید';
            const p = parts(month);
            q('[data-month]').textContent = `${months[p.m - 1]} ${fa(p.y)}`;
            const grid = q('.cdp-days'); grid.replaceChildren();
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
                    target.value = key(date);
                    focusDate = date;
                    q('.cdp-error').textContent = '';
                    render(true);
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
                opener = button;
                recurring = form.querySelector('[name="mode"]:checked')?.value === 'recurring';
                from.value = sourceFrom.value; to.value = sourceTo.value;
                target = recurring && button.dataset.dateTarget === 'cv-to' ? to : from;
                q('[data-to]').hidden = !recurring;
                q('[data-weekdays]').hidden = !recurring;
                q('[data-start]').value = form.querySelector('#cv-start').value;
                q('[data-end]').value = form.querySelector('#cv-end').value;
                q('.cdp-error').textContent = '';
                q('.cap-days').replaceChildren();
                dayInputs.forEach(input => {
                    const label = document.createElement('label');
                    const check = document.createElement('input');
                    check.type = 'checkbox'; check.value = input.value; check.checked = input.checked;
                    label.append(check, document.createTextNode(input.closest('label').textContent.trim()));
                    q('.cap-days').append(label);
                });
                selected = parse(target.value);
                focusDate = selected && permitted(selected) ? selected : lower();
                month = monthStart(focusDate);
                render(); dialog.showModal(); render(true);
            });
        });
        q('[data-cancel]').addEventListener('click', () => dialog.close());
        const switchDate = next => {
            target = next; selected = parse(target.value);
            focusDate = selected && permitted(selected) ? selected : lower();
            month = monthStart(focusDate); render(true);
        };
        q('[data-from]').addEventListener('click', () => switchDate(from));
        q('[data-to]').addEventListener('click', () => switchDate(to));
        q('[data-confirm]').addEventListener('click', () => {
            const startDate = parse(from.value), endDate = recurring ? parse(to.value) : startDate;
            const start = q('[data-start]'), end = q('[data-end]');
            const chosen = [...q('.cap-days').querySelectorAll('input:checked')].map(input => input.value);
            const fail = message => { q('.cdp-error').textContent = message; };
            if (!startDate || !endDate) return fail('تاریخ شروع و پایان را انتخاب کنید.');
            if (startDate < today()) return fail('تاریخ شروع نمی‌تواند قبل از امروز باشد.');
            if (endDate < startDate || endDate > add(startDate, 365)) return fail('پایان باید از شروع تا حداکثر ۳۶۵ روز بعد از آن باشد.');
            if (!start.reportValidity() || !end.reportValidity()) return;
            if (end.value <= start.value) return fail('ساعت پایان باید بعد از ساعت شروع باشد.');
            if (recurring) {
                if (!chosen.length) return fail('حداقل یک روز هفته انتخاب کنید.');
                let matches = false;
                for (let date = startDate; date <= endDate; date = add(date, 1)) {
                    if (chosen.includes(String(date.getUTCDay()))) { matches = true; break; }
                }
                if (!matches) return fail('روزهای انتخابی در این محدودهٔ تاریخ وجود ندارند.');
            }
            const values = [[sourceFrom, key(startDate)], [sourceTo, key(endDate)], [form.querySelector('#cv-start'), start.value], [form.querySelector('#cv-end'), end.value]];
            values.forEach(([input, value]) => { input.value = value; });
            if (recurring) dayInputs.forEach(input => { input.checked = chosen.includes(input.value); });
            values.forEach(([input]) => {
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
