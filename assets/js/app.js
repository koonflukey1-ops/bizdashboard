document.addEventListener('DOMContentLoaded',()=>{
  const toast=document.querySelector('[data-toast]'); if(toast)setTimeout(()=>toast.classList.add('hide'),4200);
  document.querySelector('[data-menu]')?.addEventListener('click',()=>document.querySelector('.sidebar')?.classList.toggle('open'));
  document.querySelectorAll('[data-confirm]').forEach(b=>b.addEventListener('click',e=>{if(!confirm(b.dataset.confirm||'ยืนยันการทำรายการ?'))e.preventDefault()}));
  document.querySelectorAll('[data-option-group]').forEach(g=>g.addEventListener('click',e=>{const b=e.target.closest('[data-value]');if(!b)return;g.querySelectorAll('[data-value]').forEach(x=>x.classList.remove('selected'));b.classList.add('selected');g.querySelector('input[type=hidden]').value=b.dataset.value;g.dispatchEvent(new Event('change'))}));
  const calc=document.querySelector('[data-rental-calc]'); if(calc){const refresh=()=>{const pick=document.querySelector('[name=pickup_date]')?.value||new Date().toISOString().slice(0,10),days=Number(document.querySelector('[name=rental_days]')?.value||3),due=new Date(pick+'T12:00:00');due.setDate(due.getDate()+days);calc.innerHTML=`<span>วันรับหนังสือ <b>${new Date(pick).toLocaleDateString('th-TH',{dateStyle:'medium'})}</b></span><span>กำหนดคืน <b>${due.toLocaleDateString('th-TH',{dateStyle:'medium'})}</b></span><span>ระยะเวลา <b>${days} วัน</b></span>`};document.querySelectorAll('[data-option-group]').forEach(x=>x.addEventListener('change',refresh));refresh()}
  document.querySelectorAll('[data-modal-open]').forEach(b=>b.addEventListener('click',()=>document.getElementById(b.dataset.modalOpen)?.showModal()));
  document.querySelectorAll('[data-modal-close]').forEach(b=>b.addEventListener('click',()=>b.closest('dialog')?.close()));
});
