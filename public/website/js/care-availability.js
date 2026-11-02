(() => {
    const init = () => {
        const form = document.getElementById('cv-form');
        if (!form) return;
        const from = form.querySelector('#cv-from');
        const to = form.querySelector('#cv-to');
        const toField = form.querySelector('#cv-to-field');
        const days = form.querySelector('#cv-weekdays');
        let recurringEnd = to.value;
        let previousMode = form.querySelector('[name="mode"]:checked')?.value;
        const recurring = () => form.querySelector('[name="mode"]:checked')?.value === 'recurring';
        const sync = () => {
            const isRecurring = recurring();
            if (previousMode === 'recurring' && !isRecurring) recurringEnd = to.value;
            if (previousMode === 'single' && isRecurring) to.value = recurringEnd;
            toField.hidden = !isRecurring;
            days.hidden = !isRecurring;
            days.disabled = !isRecurring;
            to.required = isRecurring;
            if (!isRecurring) to.value = from.value;
            previousMode = isRecurring ? 'recurring' : 'single';
        };
        form.querySelectorAll('[name="mode"]').forEach(input => input.addEventListener('change', sync));
        from.addEventListener('input', () => { if (!recurring()) to.value = from.value; });
        form.addEventListener('submit', () => { if (!recurring()) to.value = from.value; });
        window.addEventListener('pageshow', sync);
        sync();
    };
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();
