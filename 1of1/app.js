document.querySelectorAll('form[data-confirm]').forEach(f=>f.addEventListener('submit',e=>{if(!confirm(f.dataset.confirm))e.preventDefault();}));
const img=document.getElementById('img'),prev=document.getElementById('prev');
if(img)img.addEventListener('change',()=>{const f=img.files[0];if(f){prev.src=URL.createObjectURL(f);prev.hidden=false;}});
