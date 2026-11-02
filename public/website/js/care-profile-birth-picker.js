(() => {
    'use strict';
    const init = () => {
        const input = document.getElementById('birth_date');
        const open = document.getElementById('cpf-birth-open');
        if (!input || !open || typeof HTMLDialogElement === 'undefined') return;
        let fmt;
        try {
            fmt = new Intl.DateTimeFormat('en-US-u-ca-persian-nu-latn', {timeZone:'Asia/Tehran',year:'numeric',month:'numeric',day:'numeric'});
            if (fmt.resolvedOptions().calendar !== 'persian') return;
        } catch (_) { return; }
        const parts = date => {
            const p = Object.fromEntries(fmt.formatToParts(date).map(x => [x.type,x.value]));
            return {y:+p.year,m:+p.month,d:+p.day};
        };
        const fa = n => String(n).replace(/\d/g,x=>'۰۱۲۳۴۵۶۷۸۹'[+x]);
        const digits = s => s.replace(/[۰-۹]/g,x=>'۰۱۲۳۴۵۶۷۸۹'.indexOf(x)).replace(/[٠-٩]/g,x=>'٠١٢٣٤٥٦٧٨٩'.indexOf(x));
        const today = parts(new Date());
        const months = ['فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند'];
        const dialog = document.createElement('dialog');
        dialog.className = 'cpf-birth-dialog'; dialog.dir='rtl';
        dialog.setAttribute('aria-labelledby','cpf-birth-title');
        dialog.innerHTML = '<h2 id="cpf-birth-title">انتخاب تاریخ تولد شمسی</h2><div class="cpf-birth-selectors"><label>سال<select data-year></select></label><label>ماه<select data-month></select></label></div><p data-heading aria-live="polite"></p><div class="cpf-birth-days" role="group" aria-label="روزهای ماه"></div><p>با انتخاب روز، تاریخ در ورودی قرار می‌گیرد. برای تاریخ‌های پیش از ۱۲۰۰ از ورود دستی استفاده کنید.</p><button type="button" class="cpf-button cpf-secondary" data-close>بستن بدون تغییر</button>';
        document.body.append(dialog);
        const year = dialog.querySelector('[data-year]'), month = dialog.querySelector('[data-month]');
        for(let y=today.y;y>=1200;y--) year.add(new Option(fa(y),String(y)));
        months.forEach((m,i)=>month.add(new Option(m,String(i+1))));
        const cache = new Map();
        const dates = y => {
            if(cache.has(y)) return cache.get(y);
            const result=[];
            const start=Date.UTC(y+621,2,1,12);
            for(let i=0;i<400;i++) {
                const date=new Date(start+i*86400000), p=parts(date);
                if(p.y===y) result.push({...p,weekday:(date.getUTCDay()+1)%7});
            }
            cache.set(y,result); return result;
        };
        const render = () => {
            const y=+year.value,m=+month.value;
            const grid=dialog.querySelector('.cpf-birth-days'); grid.replaceChildren();
            dialog.querySelector('[data-heading]').textContent=months[m-1]+' '+fa(y);
            ['ش','ی','د','س','چ','پ','ج'].forEach(text=>{const s=document.createElement('span');s.textContent=text;s.setAttribute('aria-hidden','true');grid.append(s);});
            const days=dates(y).filter(p=>p.m===m);
            for(let i=0;i<(days[0]?.weekday || 0);i++) grid.append(document.createElement('span'));
            days.forEach(p=>{
                const b=document.createElement('button'); b.type='button';b.textContent=fa(p.d);
                const value=`${y}/${String(m).padStart(2,'0')}/${String(p.d).padStart(2,'0')}`;
                b.setAttribute('aria-label',`${fa(p.d)} ${months[m-1]} ${fa(y)}`);
                const current=digits(input.value).split('/').map(Number);
                b.setAttribute('aria-pressed',String(current[0]===y && current[1]===m && current[2]===p.d));
                b.disabled=y===today.y && (m>today.m || (m===today.m && p.d>today.d));
                b.addEventListener('click',()=>{input.value=value;input.dispatchEvent(new Event('input',{bubbles:true}));input.dispatchEvent(new Event('change',{bubbles:true}));dialog.close();input.focus();});
                grid.append(b);
            });
        };
        open.hidden=false;
        open.addEventListener('click',()=>{
            const match=digits(input.value.trim()).match(/^(\d{4})\/(\d{1,2})\/(\d{1,2})$/);
            year.value=String(match && +match[1]>=1200 && +match[1]<=today.y ? +match[1] : today.y);
            month.value=String(match && +match[2]>=1 && +match[2]<=12 ? +match[2] : today.m);
            render();dialog.showModal();year.focus();
        });
        year.addEventListener('change',render);month.addEventListener('change',render);
        dialog.querySelector('[data-close]').addEventListener('click',()=>dialog.close());
    };
    if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',init,{once:true}); else init();
})();
