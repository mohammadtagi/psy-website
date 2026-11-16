(() => {
    'use strict';
    function initInlineTime(root, summaryRoot = root) {
        const form = root.querySelector('[data-booking-form]');
        if (!form) return;
        const buttons = [...root.querySelectorAll('[data-slot]')];
        const submit = form.querySelector('[data-submit]');
        const availability = form.elements.availability_id;
        const start = form.elements.start_time;
        const selection = form.querySelector('[data-selection]');
        let chosen = null;
        let submitting = false;
        availability.value = '';
        start.value = '';
        submit.disabled = true;
        function updateType() {
            const radio = form.querySelector('[name="session_type"]:checked');
            const summary = summaryRoot.querySelector('[data-summary-type]');
            if (summary) summary.textContent = radio?.closest('label').querySelector('span')?.textContent || 'انتخاب نشده';
        }
        buttons.forEach(button => button.addEventListener('click', () => {
            if (submitting || button.disabled) return;
            chosen = button;
            buttons.forEach(item => item.setAttribute('aria-pressed', String(item === button)));
            availability.value = button.dataset.availability;
            start.value = button.dataset.start;
            const time = `${button.dataset.start} تا ${button.dataset.end}`;
            const summary = summaryRoot.querySelector('[data-summary-time]');
            if (summary) summary.textContent = time;
            selection.textContent = `ساعت ${time} انتخاب شد. شیوهٔ برگزاری را بررسی کنید و ادامه دهید.`;
            submit.disabled = false;
        }));
        form.querySelectorAll('[name="session_type"]').forEach(radio => radio.addEventListener('change', updateType));
        updateType();
        form.addEventListener('submit', event => {
            if (submitting || !chosen) {
                event.preventDefault();
                if (!chosen) { selection.textContent = 'لطفاً ابتدا ساعت جلسه را انتخاب کنید.'; buttons[0]?.focus(); }
                return;
            }
            submitting = true;
            submit.disabled = true;
            submit.textContent = 'در حال ثبت نوبت موقت…';
            form.setAttribute('aria-busy', 'true');
            buttons.forEach(button => { button.disabled = true; });
        });
    }
    const host = document.querySelector('[data-booking-calendar]');
    const fallback = document.querySelector('[data-calendar-fallback]');
    if (!host || !fallback) return;
    const companion = host.closest('[data-booking-account]')?.querySelector('.bka-summary');
    let summaryBox = null;
    const durationText = companion?.querySelector('dd')?.textContent.trim() || 'مشخص نشده';
    function resetSummary(dateText = 'انتخاب نشده') {
        if (!companion) return;
        if (!summaryBox) {
            summaryBox = document.createElement('aside');
            summaryBox.className = 'bks-summary';
            companion.replaceWith(summaryBox);
        }
        const heading = document.createElement('h2');
        heading.textContent = 'خلاصهٔ جلسه';
        const list = document.createElement('dl');
        [['تاریخ', dateText], ['ساعت', 'انتخاب نشده'], ['مدت', durationText], ['شیوهٔ برگزاری', 'انتخاب نشده']].forEach(([label, value]) => {
            const row = document.createElement('div');
            const dt = document.createElement('dt');
            const dd = document.createElement('dd');
            dt.textContent = label;
            dd.textContent = value;
            row.append(dt, dd);
            list.append(row);
        });
        const note = document.createElement('p');
        note.textContent = 'مبلغ نهایی در مرحلهٔ بررسی و پرداخت نمایش داده می‌شود.';
        summaryBox.replaceChildren(heading, list, note);
    }
    resetSummary();
    try {
        const format = options => new Intl.DateTimeFormat('fa-IR-u-ca-persian', {timeZone: 'UTC', ...options});
        const dateFormat = format({day: 'numeric', month: 'long'});
        if (dateFormat.resolvedOptions().calendar !== 'persian') return;
        const weekday = format({weekday: 'long'});
        const full = format({weekday: 'long', day: 'numeric', month: 'long'});
        const tehran = new Intl.DateTimeFormat('en-CA', {timeZone: 'Asia/Tehran', year: 'numeric', month: '2-digit', day: '2-digit'});
        const todayParts = {};
        tehran.formatToParts(new Date()).forEach(p => { todayParts[p.type] = p.value; });
        const today = `${todayParts.year}-${todayParts.month}-${todayParts.day}`;
        const tomorrow = new Date(new Date(today + 'T12:00:00Z').getTime() + 86400000).toISOString().slice(0, 10);
        const dates = new Map();
        fallback.querySelectorAll('[data-calendar-date]').forEach(link => {
            const key = link.dataset.calendarDate;
            const date = new Date(key + 'T12:00:00Z');
            if (!/^\d{4}-\d{2}-\d{2}$/.test(key) || !Number.isFinite(date.getTime()) || date.toISOString().slice(0, 10) !== key) throw new Error('Invalid date');
            const url = new URL(link.href, location.href);
            if (url.origin !== location.origin) throw new Error('Unexpected origin');
            dates.set(key, {key, date, href: url.href});
        });
        const entries = [...dates.values()].sort((a, b) => a.key.localeCompare(b.key));
        if (!entries.length) return;
        function element(tag, text, className) {
            const node = document.createElement(tag);
            node.textContent = text;
            if (className) node.className = className;
            return node;
        }
        host.className = 'bkd';
        host.dir = 'rtl';
        const toolbar = element('div', '', 'bkd-toolbar');
        const title = element('h3', 'چه روزی برای شما مناسب است؟');
        const controls = element('div', '', 'bkd-controls');
        const prev = element('button', 'روزهای قبل');
        const next = element('button', 'روزهای بعد');
        prev.type = next.type = 'button';
        controls.append(prev, next);
        toolbar.append(title, controls);
        const strip = element('div', '', 'bkd-strip');
        strip.id = 'booking-day-strip';
        strip.setAttribute('role', 'group');
        strip.setAttribute('aria-label', 'روز جلسه را انتخاب کنید');
        prev.setAttribute('aria-controls', strip.id);
        next.setAttribute('aria-controls', strip.id);
        const status = element('p', '', 'bkd-status');
        status.setAttribute('role', 'status');
        const hint = element('p', 'روی روز دلخواه بزنید تا ساعت‌های آن روز را ببینید. زمان‌ها به وقت تهران هستند.', 'bkd-hint');
        const panel = element('section', '', 'bkd-panel');
        panel.id = 'booking-inline-times';
        panel.setAttribute('aria-label', 'ساعت‌های روز انتخاب‌شده');
        const loading = element('p', 'برای دیدن ساعت‌ها، یک روز را انتخاب کنید.', 'bkd-hint');
        loading.setAttribute('role', 'status');
        host.replaceChildren(toolbar, strip, status, hint, loading, panel);
        let selected = null;
        let request = null;
        let sequence = 0;
        let posting = false;
        function markSelected() {
            strip.querySelectorAll('.bkd-day').forEach(card => {
                if (card.dataset.date === selected) card.setAttribute('aria-current', 'date');
                else card.removeAttribute('aria-current');
            });
        }
        async function loadDay(entry) {
            if (posting) return;
            const token = ++sequence;
            request?.abort();
            request = new AbortController();
            const controller = request;
            selected = entry.key;
            markSelected();
            resetSummary(full.format(entry.date));
            panel.replaceChildren();
            panel.setAttribute('aria-busy', 'true');
            loading.textContent = 'در حال دریافت ساعت‌های ' + full.format(entry.date) + '…';
            const timeout = setTimeout(() => controller.abort(), 20000);
            try {
                const response = await fetch(entry.href, {credentials: 'same-origin', cache: 'no-store', signal: controller.signal});
                if (!response.ok || response.redirected) throw new Error('Unavailable booking page');
                const html = await response.text();
                if (token !== sequence) return;
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const source = doc.querySelector('[data-booking-time]');
                if (!source || !source.querySelector('.bkt-grid')) throw new Error('Unsupported booking page');
                // Only the booking section is imported; page scripts are never executed.
                source.querySelectorAll('script, style, link, iframe, object, embed, base').forEach(node => node.remove());
                source.querySelectorAll('*').forEach(node => {
                    [...node.attributes].forEach(attr => { if (/^on/i.test(attr.name)) node.removeAttribute(attr.name); });
                });
                const form = source.querySelector('[data-booking-form]');
                if (form && (new URL(form.action, location.href).origin !== location.origin || form.method.toLowerCase() !== 'post')) throw new Error('Invalid booking form');
                source.querySelectorAll('[data-slot] small').forEach(node => node.remove());
                source.querySelectorAll('[data-slot]').forEach(node => {
                    node.setAttribute('aria-label', `از ساعت ${node.dataset.start} تا ${node.dataset.end}`);
                });
                source.querySelector(':scope > header')?.remove();
                source.querySelector('.bkt-steps')?.remove();
                const content = element('div', '', 'bkt bkd-times');
                content.setAttribute('data-booking-time', '');
                while (source.firstChild) content.append(document.importNode(source.firstChild, true)), source.firstChild.remove();
                const heading = element('h3', 'ساعت‌های ' + full.format(entry.date), 'bkd-times-title');
                panel.replaceChildren(heading, content);
                const loadedSummary = content.querySelector('.bkt-summary');
                if (summaryBox && loadedSummary) {
                    summaryBox.replaceChildren(...loadedSummary.childNodes);
                    loadedSummary.remove();
                }
                initInlineTime(content, summaryBox || content);
                const indexUrl = new URL(entry.href);
                content.querySelectorAll('a').forEach(link => {
                    const url = new URL(link.getAttribute('href'), indexUrl);
                    // The empty-day back link now takes focus to the date cards.
                    if (link.textContent.trim() === 'انتخاب تاریخ دیگر') {
                        link.href = '#booking-day-strip';
                        link.addEventListener('click', event => { event.preventDefault(); strip.querySelector('a')?.focus(); });
                    }
                });
                form && content.querySelector('form').addEventListener('submit', event => {
                    if (event.defaultPrevented) return;
                    posting = true;
                    host.setAttribute('data-posting', 'true');
                });
                loading.textContent = 'ساعت‌های ' + full.format(entry.date) + ' در بخش زیر نمایش داده شد.';
            } catch (error) {
                if (token !== sequence) return;
                loading.textContent = 'دریافت ساعت‌ها انجام نشد. دوباره تلاش کنید؛ در صورت پایان اعتبار ورود، وارد حساب شوید.';
                const retry = element('button', 'تلاش دوباره', 'bkd-retry');
                retry.type = 'button';
                retry.addEventListener('click', () => loadDay(entry));
                const link = element('a', 'باز کردن صفحهٔ ساعت‌ها', 'bkd-fallback-link');
                link.href = entry.href;
                panel.replaceChildren(retry, link);
            } finally {
                clearTimeout(timeout);
                if (token === sequence) panel.removeAttribute('aria-busy');
            }
        }
        window.addEventListener('pageshow', event => {
            if (event.persisted && selected) {
                posting = false;
                host.removeAttribute('data-posting');
                loadDay(entries.find(entry => entry.key === selected));
            }
        });
        let index = 0;
        const mobile = window.matchMedia('(max-width: 600px)');
        const size = () => mobile.matches ? 3 : 5;
        function render() {
            const page = entries.slice(index, index + size());
            strip.replaceChildren();
            page.forEach(({key, date, href}) => {
                const card = element('a', '', 'bkd-day');
                card.href = href;
                card.setAttribute('aria-label', full.format(date) + '؛ مشاهده ساعت‌ها');
                card.dataset.date = key;
                card.setAttribute('aria-controls', panel.id);
                card.append(element('span', key === today ? 'امروز' : key === tomorrow ? 'فردا' : weekday.format(date), 'bkd-weekday'), element('strong', dateFormat.format(date)));
                card.addEventListener('click', event => {
                    if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
                    event.preventDefault();
                    loadDay({key, date, href});
                });
                strip.append(card);
            });
            markSelected();
            prev.disabled = index === 0;
            next.disabled = index + size() >= entries.length;
            status.textContent = full.format(page[0].date) + (page.length > 1 ? ' تا ' + full.format(page[page.length - 1].date) : '');
        }
        // Keep focus on the navigation control instead of jumping the page.
        prev.addEventListener('click', () => { index = Math.max(0, index - size()); render(); if (prev.disabled) next.focus(); });
        next.addEventListener('click', () => { if (index + size() < entries.length) index += size(); render(); if (next.disabled) prev.focus(); });
        if (mobile.addEventListener) mobile.addEventListener('change', render);
        render();
        host.hidden = false;
        fallback.hidden = true;
    } catch (error) {
        host.hidden = true;
        fallback.hidden = false;
    }
})();
