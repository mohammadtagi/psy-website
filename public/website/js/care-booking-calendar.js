(() => {
    'use strict';
    const host = document.querySelector('[data-booking-calendar]');
    const fallback = document.querySelector('[data-calendar-fallback]');
    if (!host || !fallback) return;
    try {
        const partsFormat = new Intl.DateTimeFormat('en-US-u-ca-persian', {timeZone: 'UTC', year: 'numeric', month: 'numeric', day: 'numeric'});
        if (partsFormat.resolvedOptions().calendar !== 'persian') return;
        const titleFormat = new Intl.DateTimeFormat('fa-IR-u-ca-persian', {timeZone: 'UTC', year: 'numeric', month: 'long'});
        const fullFormat = new Intl.DateTimeFormat('fa-IR-u-ca-persian', {timeZone: 'UTC', year: 'numeric', month: 'long', day: 'numeric', weekday: 'long'});
        const number = new Intl.NumberFormat('fa-IR', {useGrouping: false});
        const links = new Map();
        fallback.querySelectorAll('[data-calendar-date]').forEach(link => {
            const key = link.dataset.calendarDate;
            if (!/^\d{4}-\d{2}-\d{2}$/.test(key)) throw new Error('Invalid date');
            const date = new Date(key + 'T12:00:00Z');
            if (!Number.isFinite(date.getTime()) || date.toISOString().slice(0, 10) !== key) throw new Error('Invalid date');
            links.set(key, link.href);
        });
        const keys = [...links.keys()].sort();
        if (!keys.length) return;
        const dayMs = 86400000;
        const first = new Date(keys[0] + 'T12:00:00Z').getTime();
        const last = new Date(keys[keys.length - 1] + 'T12:00:00Z').getTime();
        // Bound work; an unexpectedly large server range keeps the original list.
        if ((last - first) / dayMs > 3660) return;
        function parts(date) {
            const values = {};
            partsFormat.formatToParts(date).forEach(p => { values[p.type] = p.value; });
            return {month: values.year + '-' + values.month, day: Number(values.day)};
        }
        const startMonth = parts(new Date(first)).month;
        const endMonth = parts(new Date(last)).month;
        const months = [];
        let collecting = false;
        for (let stamp = first - 32 * dayMs; stamp <= last + 32 * dayMs; stamp += dayMs) {
            const date = new Date(stamp);
            const p = parts(date);
            if (p.month === startMonth) collecting = true;
            if (!collecting) continue;
            const previous = months[months.length - 1];
            if (previous && previous.key === endMonth && p.month !== endMonth) break;
            if (!previous || previous.key !== p.month) months.push({key: p.month, days: []});
            months[months.length - 1].days.push({date, day: p.day});
        }
        function element(tag, text, className) {
            const node = document.createElement(tag);
            if (text) node.textContent = text;
            if (className) node.className = className;
            return node;
        }
        host.className = 'bkc';
        const toolbar = element('div', '', 'bkc-toolbar');
        const prev = element('button', 'ماه قبل');
        const next = element('button', 'ماه بعد');
        prev.type = next.type = 'button';
        const heading = element('h3');
        heading.id = 'booking-calendar-month';
        heading.tabIndex = -1;
        heading.setAttribute('aria-live', 'polite');
        toolbar.append(prev, heading, next);
        const grid = element('div', '', 'bkc-grid');
        grid.setAttribute('role', 'group');
        grid.setAttribute('aria-labelledby', heading.id);
        const status = element('p', '', 'bkc-hint');
        status.setAttribute('role', 'status');
        host.append(toolbar, grid, status, element('p', 'روزهای سبز قابل انتخاب‌اند؛ برای بررسی ساعت‌های آزاد روی روز موردنظر بزنید. زمان‌ها به وقت تهران هستند.', 'bkc-hint'));
        let index = 0;
        function render() {
            const month = months[index];
            heading.textContent = titleFormat.format(month.days[0].date);
            prev.disabled = index === 0;
            next.disabled = index === months.length - 1;
            grid.replaceChildren();
            ['ش', 'ی', 'د', 'س', 'چ', 'پ', 'ج'].forEach((day, i) => {
                const cell = element('span', day, 'bkc-weekday');
                cell.setAttribute('aria-label', ['شنبه', 'یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه'][i]);
                grid.append(cell);
            });
            const offset = (month.days[0].date.getUTCDay() + 1) % 7;
            for (let i = 0; i < offset; i++) {
                const blank = element('span');
                blank.setAttribute('aria-hidden', 'true');
                grid.append(blank);
            }
            let count = 0;
            month.days.forEach(({date, day}) => {
                const key = date.toISOString().slice(0, 10);
                const href = links.get(key);
                const cell = element(href ? 'a' : 'span', number.format(day), href ? 'bkc-day bkc-available' : 'bkc-day bkc-unavailable');
                cell.setAttribute('aria-label', fullFormat.format(date) + (href ? '؛ مشاهده ساعت‌ها' : '؛ غیرقابل انتخاب'));
                if (href) { cell.href = href; count++; }
                grid.append(cell);
            });
            status.textContent = count ? number.format(count) + ' روز قابل انتخاب در این ماه' : 'در این ماه تاریخی برای انتخاب ارائه نشده است.';
        }
        prev.addEventListener('click', () => { if (index > 0) { index--; render(); heading.focus(); } });
        next.addEventListener('click', () => { if (index < months.length - 1) { index++; render(); heading.focus(); } });
        render();
        host.hidden = false;
        fallback.hidden = true;
    } catch (error) {
        host.hidden = true;
        fallback.hidden = false;
    }
})();
