(() => {
    'use strict';
    // Delegation also works when this script is loaded before page content.
    document.addEventListener('click', event => {
        const link = event.target.closest('.care-record .cr-nav a[href^="#"]');
        if (!link || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || event.button !== 0) return;
        const target = document.getElementById(link.hash.slice(1));
        if (!target) return;
        event.preventDefault();
        target.setAttribute('tabindex', '-1');
        target.focus({ preventScroll: true });
        target.scrollIntoView({
            behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth',
            block: 'start'
        });
        if (location.hash !== link.hash) history.pushState(null, '', link.hash);
    });
})();
