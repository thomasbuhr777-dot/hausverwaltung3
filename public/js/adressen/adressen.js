$(function(){

//////////////////////////////////////////////////
// API
//////////////////////////////////////////////////

async function apiRequest(url, method='GET', data=null){

    const options = {
        method,
        headers:{
            'Content-Type':'application/json',
            'Accept':'application/json'
        }
    };

    if(data){
        options.body = JSON.stringify(data);
    }

    const res = await fetch(url, options);

    let json = {};
    try{
        json = await res.json();
    }catch(e){
        throw {message:'Ungültige Serverantwort'};
    }

    if(!res.ok){
        throw json;
    }

    return json;
}

//////////////////////////////////////////////////
// TOAST
//////////////////////////////////////////////////

function toast(msg, type='success'){

    const el = document.getElementById('appToast');
    if(!el) return;

    el.className = 'toast text-bg-' + type;

    const msgEl = document.getElementById('toastMsg');
    if(msgEl) msgEl.textContent = msg;

    bootstrap.Toast.getOrCreateInstance(el).show();
}

//////////////////////////////////////////////////
// DATATABLE
//////////////////////////////////////////////////

let table = $('#table').DataTable({
    ajax:{
        url:'/api/adressen',
        dataSrc:'data'
    },
    processing:true,
    columns:[
        {data:'id'},
        {data:'vorname'},
        {data:'nachname'},
        {data:'email'},
        {
            data:null,
            orderable:false,
            searchable:false,
            render:function(data,type,row){

                return `
                    <button class="btn btn-sm btn-primary editBtn"
                        data-id="${row.id}">
                        edit
                    </button>

                    <button class="btn btn-sm btn-danger deleteBtn"
                        data-id="${row.id}">
                        delete
                    </button>
                `;
            }
        }
    ]
});

//////////////////////////////////////////////////
// SAVE
//////////////////////////////////////////////////

$('#saveBtn').on('click', async function(){

    try{

        const id = $('[name=id]').val() || null;

        const payload = Object.fromEntries(
            new FormData(
                document.getElementById('adresseForm')
            )
        );

        await apiRequest(
            id ? '/api/adressen/'+id : '/api/adressen',
            id ? 'PUT' : 'POST',
            payload
        );

        table.ajax.reload(null,false);

        toast('Gespeichert');

    }catch(err){

        console.error(err);

        toast(
            err.message || 'Fehler beim Speichern',
            'danger'
        );
    }

});


//////////////////////////////////////////////////
// EDIT
//////////////////////////////////////////////////

$('#table').on('click','.editBtn', async function(){

    try{

        const id = $(this).data('id');

        const res = await apiRequest('/api/adressen/'+id);

        const form = document.getElementById('adresseForm');

        Object.entries(res.data).forEach(([key,value])=>{
            const field = form.querySelector(`[name="${key}"]`);
            if(field) field.value = value ?? '';
        });

        toast('Datensatz geladen','info');

    }catch(err){
        toast('Laden fehlgeschlagen','danger');
    }

});

});