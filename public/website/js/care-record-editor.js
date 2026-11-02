(() => {
    function init() {
        const form = document.querySelector('.care-record-edit #clinical-record-form');
        if (!form || form.dataset.guarded) return;
        form.dataset.guarded = 'true';
        const snapshot = () => JSON.stringify(Array.from(new FormData(form).entries()));
        const initial = snapshot();
        let submitting = false;
        const dirty = () => snapshot() !== initial;
        window.addEventListener('beforeunload', event => {
            if (!submitting && dirty()) { event.preventDefault(); event.returnValue = ''; }
        });
        document.addEventListener('click', event => {
            const link = event.target.closest('a[href]');
            if (!link || link.target === '_blank' || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || link.hasAttribute('download')) return;
            const url = new URL(link.href, location.href);
            if (url.pathname === location.pathname && url.search === location.search && url.hash) return;
            if (dirty() && !confirm('تغییرات ذخیره نشده‌اند. بدون ذخیره خارج می‌شوید؟')) {
                event.preventDefault();
                event.stopImmediatePropagation();
            } else {
                submitting = true;
                setTimeout(() => { submitting = false; }, 1000);
            }
        }, true);
        form.addEventListener('submit', () => {
            submitting = true;
            // Restore the guard if another handler cancels submission.
            setTimeout(() => { submitting = false; }, 1500);
        });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();
