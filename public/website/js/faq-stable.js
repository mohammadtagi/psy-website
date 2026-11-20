(() => {
  const items = [...document.querySelectorAll('.website-faq details.faq-item')];
  for (const [g, parent] of [...new Set(items.map(el => el.parentElement))].entries()) {
    const group = items.filter(el => el.parentElement === parent);
    const box = document.createElement('div'); box.className = 'faq-reader';
    const panel = document.createElement('div'); panel.className = 'faq-reader-panel'; panel.setAttribute('aria-live','polite');
    const pairs = group.map((el,i) => {
      const b = document.createElement('button'); b.type = 'button'; b.textContent = el.querySelector('summary').textContent;
      const answer = el.querySelector('p').cloneNode(true); answer.id = `faq-answer-${g}-${i}`;
      b.setAttribute('aria-controls',answer.id); box.append(b); panel.append(answer); return [b,answer];
    });
    function show(index){ pairs.forEach(([b,p],i) => {b.setAttribute('aria-pressed',String(i===index));p.style.visibility=i===index?'visible':'hidden';}); }
    pairs.forEach(([b],i)=>b.addEventListener('click',()=>show(i))); show(0);
    box.append(panel); group[0].before(box); group.forEach(el=>el.remove());
  }
})();
