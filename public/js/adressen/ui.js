import { apiRequest } from './api.js';
import { getTable } from './table.js';
import { formData } from './binder.js';

export let form;
export let modal;

let formDirty = false;
let skipDirtyCheck = false;

//////////////////////////////////////////////////
// INIT UI
//////////////////////////////////////////////////

export function initUI(){

    form = document.getElementById('adresseForm');

    const modalEl = document.getElementById('formModal');
    modal = new bootstrap.Modal(modalEl);

    const saveBtn = document.getElementById('saveBtn');

    //////////////////////////////////////////////////
    // DIRTY FORM
    //////////////////////////////////////////////////

    form.addEventListener('input', ()=> formDirty=true);

    modalEl.addEventListener('hide.bs.modal', e => {

        if(skipDirtyCheck){
            skipDirtyCheck=false;
            return;
        }

        if(!formDirty) return;

        if(!confirm('Ungespeicherte Änderungen verwerfen?'))
            e.preventDefault();
    });

   //////////////////////////////////////////////////
// SAVE BUTTON
//////////////////////////////////////////////////

saveBtn.addEventListener('click', async ()=>{

    startSaving(saveBtn);

    try{

        const id = form.querySelector('[name="id"]')?.value || null;
        const payload = formData(form);

        //////////////////////////////////////////////////
        // API CALL
        //////////////////////////////////////////////////

        const result = await apiRequest(
            id ? '/api/adressen/'+id : '/api/adressen',
            id ? 'PUT' : 'POST',
            payload
        );

        const dt = getTable();

        //////////////////////////////////////////////////
        // TABLE UPDATE ⭐
        //////////////////////////////////////////////////

        if(!id){
            // CREATE
            dt.row.add(result).draw(false);

        }else{
            // UPDATE
            const row = dt.row('.selected');

            if(row.length){
                row.data(result)
                   .invalidate()
                   .draw(false);
            }
        }

        //////////////////////////////////////////////////
        // UI SUCCESS
        //////////////////////////////////////////////////

        closeModalClean();
        toast('Gespeichert');

    }catch(err){

        console.error('REAL ERROR:', err);

        if(err?.status === 422){
            showErrors(err.data.messages);
        }else{
            toast('Serverfehler','danger');
        }

    }finally{
        stopSaving(saveBtn);
    }
});



//////////////////////////////////////////////////
// FORM HELPERS
//////////////////////////////////////////////////



function showErrors(errors){

    form.querySelectorAll('.is-invalid')
        .forEach(e=>e.classList.remove('is-invalid'));

    Object.entries(errors).forEach(([k,msg])=>{

        const input=form.querySelector(`[name="${k}"]`);
        if(!input) return;

        input.classList.add('is-invalid');

        const fb=input.parentElement.querySelector('.invalid-feedback');
        if(fb) fb.textContent=msg;
    });
}

//////////////////////////////////////////////////
// BUTTON STATE
//////////////////////////////////////////////////

function startSaving(btn){
    btn.disabled=true;
    btn.innerHTML=
        '<span class="spinner-border spinner-border-sm me-2"></span>Speichern...';
}

function stopSaving(btn){
    btn.disabled=false;
    btn.innerHTML='<span class="label">Speichern</span>';
}

//////////////////////////////////////////////////
// TOAST
//////////////////////////////////////////////////

export function toast(msg,type='success'){

    const el=document.getElementById('appToast');

    el.className='toast text-bg-'+type;
    document.getElementById('toastMsg').textContent=msg;

    bootstrap.Toast.getOrCreateInstance(el).show();
}

//////////////////////////////////////////////////
// MODAL CLOSE CLEAN
//////////////////////////////////////////////////

export function closeModalClean(){
    skipDirtyCheck=true;
    formDirty=false;
    modal.hide();
}