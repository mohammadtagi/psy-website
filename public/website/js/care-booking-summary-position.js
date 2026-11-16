(() => {
    'use strict';
    const root = document.querySelector('[data-booking-account]');
    const grid = root?.querySelector('.bka-grid');
    // Runs after the existing calendar has created the single session summary.
    const summary = root?.querySelector('.bks-summary, .bka-summary');
    if (!grid || !summary) return;
    const home = document.createComment('session-summary-desktop-position');
    summary.before(home);
    const mobile = window.matchMedia('(max-width:760px)');
    function place() {
        if (!mobile.matches) {
            summary.removeAttribute('data-mobile-summary');
            if (home.nextSibling !== summary) home.after(summary);
            return;
        }
        summary.setAttribute('data-mobile-summary', '');
        const dates = root.querySelector('[data-dates]');
        const profile = root.querySelector('[data-profile]');
        const guest = root.querySelector('[data-guest]');
        let parent = grid;
        let before = null;
        if (dates && !dates.hidden) {
            const button = dates.querySelector('[data-booking-form] [data-submit]');
            parent = button?.parentNode || dates;
            before = button || null;
        } else if (profile && !profile.hidden) {
            before = profile.querySelector('[data-profile-form] button[type="submit"]');
            parent = before?.parentNode || profile;
        } else if (guest && !guest.hidden) {
            before = guest.querySelector('[data-open-login]');
            parent = before?.parentNode || guest;
        }
        // Keep the same node (and live summary fields), never clone it.
        // It may be detached when the existing calendar clears an old day's form.
        if (summary.parentNode !== parent || summary.nextSibling !== before) {
            parent.insertBefore(summary, before);
        }
    }
    const observer = new MutationObserver(place);
    observer.observe(root, {childList:true, subtree:true, attributes:true, attributeFilter:['hidden', 'data-booking-stage']});
    mobile.addEventListener('change', place);
    place();
})();
