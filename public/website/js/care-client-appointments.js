(() => {
  'use strict';
  const root = document.querySelector('.cca');
  if (!root) return;
  const filters = root.querySelector('[data-filters]');
  if (!filters) return;
  const cards = Array.from(root.querySelectorAll('[data-appointment]'));
  const status = root.querySelector('.cca-filter-status');
  const empty = root.querySelector('.cca-filter-empty');
  filters.hidden = false;
  filters.addEventListener('click', (event) => {
    const button = event.target.closest('button[data-filter]');
    if (!button || !filters.contains(button)) return;
    const value = button.dataset.filter;
    let count = 0;
    cards.forEach((card) => {
      card.hidden = value !== 'all' && card.dataset.group !== value;
      if (!card.hidden) count += 1;
    });
    filters.querySelectorAll('button').forEach((item) => {
      item.setAttribute('aria-pressed', String(item === button));
    });
    status.textContent = `${count.toLocaleString('fa-IR')} نوبت از ${cards.length.toLocaleString('fa-IR')} نوبت همین صفحه نمایش داده می‌شود.`;
    empty.hidden = count !== 0;
  });
})();
