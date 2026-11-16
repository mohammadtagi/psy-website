(() => {
    'use strict';
    const root = document.querySelector('.cbc');
    if (!root) return;
    const code = root.querySelector('[data-tracking-code]');
    const button = root.querySelector('[data-copy-tracking]');
    const status = root.querySelector('[data-copy-status]');
    if (!code || !button || !status || !code.textContent.trim()) return;
    button.hidden = false;
    button.addEventListener('click', async () => {
        button.disabled = true;
        status.textContent = '';
        try {
            if (!navigator.clipboard || !navigator.clipboard.writeText) throw new Error('Clipboard unavailable');
            await navigator.clipboard.writeText(code.textContent.trim());
            status.textContent = 'کد پیگیری کپی شد.';
        } catch (error) {
            status.textContent = 'کپی خودکار ممکن نشد؛ کد پیگیری بالا را انتخاب و دستی کپی کنید.';
        } finally {
            button.disabled = false;
        }
    });
})();
