(() => {
'use strict';
if (!window.fetch || !window.AbortController) return;
let busy = false;
const dirty = f => [...f.querySelectorAll('textarea')].some(t => t.value !== t.defaultValue);
function enhance(root) {
 root.querySelectorAll('[data-cancel-edit]').forEach(b => {
  b.hidden = false;
  b.onclick = () => {
   const f = b.closest('form');
   if (dirty(f) && !confirm('تغییرات ذخیره‌نشده کنار گذاشته شوند؟')) return;
   f.reset(); f.closest('details').open = false; f.closest('details').querySelector('summary').focus();
  };
 });
}
enhance(document);
window.addEventListener('beforeunload', e => {
 if (busy || [...document.querySelectorAll('[data-note-action]')].some(dirty)) { e.preventDefault(); e.returnValue = ''; }
});
document.addEventListener('submit', async e => {
 const form = e.target.closest('[data-note-action]');
 if (!form) return;
 e.preventDefault();
 if (busy || !form.reportValidity()) return;
 const root = form.closest('.ccs-notes');
 const message = form.querySelector('.ccs-form-message');
 // Keep other unsaved forms intact, even when pagination changes after a mutation.
 const drafts = [...root.querySelectorAll('[data-note-action]')].filter(f => f !== form && dirty(f)).map(f => ({
  id: f.querySelector('textarea').id, value: f.querySelector('textarea').value,
  article: f.closest('article')?.cloneNode(true)
 }));
 const payload = new FormData(form);
 const controls = [...root.querySelectorAll('button, textarea')];
 busy = true; root.setAttribute('aria-busy', 'true');
 controls.forEach(c => { if (c.tagName === 'TEXTAREA') c.readOnly = true; else c.disabled = true; });
 message.textContent = 'در حال ذخیره تغییرات…';
 const controller = new AbortController();
 const timer = setTimeout(() => controller.abort(), 30000);
 let saved = false;
 try {
  const response = await fetch(form.action, { method: 'POST', body: payload, credentials: 'same-origin', headers: {Accept:'application/json'}, signal:controller.signal });
  if (response.status === 422) {
   const data = await response.json();
   message.textContent = data.errors?.body?.[0] || 'متن یادداشت را بررسی کنید.';
   form.querySelector('textarea')?.setAttribute('aria-invalid','true');
   message.focus(); return;
  }
  if ([401,403,419].includes(response.status)) {
   message.textContent = 'نشست یا مجوز دسترسی معتبر نیست. متن را کپی کنید و پس از بررسی ورود و دسترسی دوباره تلاش کنید.';
   message.focus(); return;
  }
  const expected = new URL(root.dataset.sessionUrl, location.href), actual = new URL(response.url);
  if (!response.ok || !response.redirected || actual.origin !== expected.origin || actual.pathname !== expected.pathname || actual.search !== expected.search) throw Error('unconfirmed');
  saved = true;
  const html = await response.text();
  const next = new DOMParser().parseFromString(html,'text/html').querySelector('.ccs-notes');
  if (!next) throw Error('missing view');
  drafts.forEach(d => {
   let field = next.querySelector('#' + d.id);
   if (!field && d.article) {
    next.querySelector('.ccs-note-list').append(d.article);
    field = next.querySelector('#' + d.id);
   }
   if (field) { field.value = d.value; field.readOnly = false; const details = field.closest('details'); if(details) details.open = true; }
  });
  next.querySelectorAll('button').forEach(b => b.disabled = false);
  root.replaceWith(next); enhance(next);
  const feedback = next.querySelector('.ccs-feedback');
  feedback.textContent = ({create:'یادداشت ثبت شد.',edit:'تغییرات یادداشت ذخیره شد.',delete:'یادداشت حذف شد.'})[form.dataset.noteAction] + (drafts.length ? ' متن‌های ذخیره‌نشده دیگر حفظ شده‌اند.' : '');
  feedback.focus({preventScroll:true});
  // The returned list is page one, matching the existing controller redirect.
  try { history.replaceState(null,'',expected.pathname + expected.search); } catch (_) {}
 } catch (_) {
  message.textContent = saved ? 'تغییرات ذخیره شد، اما نمایش فهرست به‌روز نشد. دوباره ارسال نکنید؛ پس از کپی متن‌های ذخیره‌نشده، صفحه را تازه کنید.' : 'نتیجه ذخیره مشخص نشد؛ متن حفظ شده است. پیش از ارسال دوباره، یادداشت‌ها را در زبانه دیگری بررسی کنید تا ثبت تکراری انجام نشود.';
  message.focus();
 } finally {
  clearTimeout(timer); busy = false; root.removeAttribute('aria-busy');
  controls.forEach(c => { if(c.tagName === 'TEXTAREA') c.readOnly = false; else c.disabled = false; });
 }
});
})();
