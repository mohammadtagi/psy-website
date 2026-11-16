(() => {
    'use strict';
    const root = document.querySelector('[data-booking-time]');
    const form = root?.querySelector('[data-booking-form]');
    if (!form) return;
    const buttons = Array.from(root.querySelectorAll('[data-slot]'));
    const submit = form.querySelector('[data-submit]');
    const availability = form.elements.availability_id;
    const start = form.elements.start_time;
    const selection = form.querySelector('[data-selection]');
    let chosen = null;
    let submitting = false;
    function updateType() {
        const radio = form.querySelector('[name="session_type"]:checked');
        root.querySelector('[data-summary-type]').textContent = radio?.closest('label').querySelector('span').textContent || 'انتخاب نشده';
    }
    function choose(button) {
        if (submitting || button.disabled) return;
        chosen = button;
        buttons.forEach(item => item.setAttribute('aria-pressed', String(item === button)));
        availability.value = button.dataset.availability;
        start.value = button.dataset.start;
        const time = `${button.dataset.start} تا ${button.dataset.end}`;
        root.querySelector('[data-summary-time]').textContent = time;
        selection.textContent = `ساعت ${time} انتخاب شد. شیوهٔ برگزاری را بررسی کنید و ادامه دهید.`;
        submit.disabled = false;
    }
    const previous = buttons.find(button => button.dataset.availability === availability.value && button.dataset.start === start.value);
    availability.value = ''; start.value = '';
    buttons.forEach(button => button.addEventListener('click', () => choose(button)));
    form.querySelectorAll('[name="session_type"]').forEach(radio => radio.addEventListener('change', updateType));
    if (previous) choose(previous);
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
        // Keep successful controls enabled so the original POST contract is preserved.
        buttons.forEach(button => { button.disabled = true; });
    });
    window.addEventListener('pageshow', () => {
        submitting = false;
        form.removeAttribute('aria-busy');
        buttons.forEach(button => { button.disabled = false; });
        submit.disabled = !chosen;
        submit.textContent = 'ادامه به بررسی و پرداخت ←';
        updateType();
    });
})();
