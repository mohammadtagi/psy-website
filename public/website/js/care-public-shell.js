(() => {
    'use strict';
    document.querySelectorAll('.cps-mobile').forEach(menu => {
        const summary = menu.querySelector('summary');
        menu.addEventListener('keydown', event => {
            if (event.key === 'Escape' && menu.open) {
                menu.open = false;
                summary.focus();
                event.preventDefault();
            }
        });
        menu.querySelectorAll('a').forEach(link => link.addEventListener('click', () => { menu.open = false; }));
        document.addEventListener('click', event => {
            if (menu.open && !menu.contains(event.target)) menu.open = false;
        });
        document.addEventListener('focusin', event => {
            if (menu.open && !menu.contains(event.target)) menu.open = false;
        });
        const desktop = window.matchMedia('(min-width: 1024px)');
        desktop.addEventListener('change', event => { if (event.matches) menu.open = false; });
    });
})();
