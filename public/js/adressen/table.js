import { apiRequest } from './api.js';
import { toast} from './ui.js';
import { bindForm, resetForm } from './binder.js';
import { form, modal } from './ui.js';
import { buildColumns } from './tableSchema.js';

let tableInstance = null;

export function getTable(){
    return tableInstance;
}

export async function initTable(){

    $.fn.dataTable.ext.errMode='none';

    // Daten einmal laden
    const res = await fetch('/api/adressen');
    const json = await res.json();

    const columns = buildColumns(json.data);

        //////////////////////////////////////////////////
    // HEADER AUTO GENERIEREN ⭐
    //////////////////////////////////////////////////

    const thead = document.querySelector('#table thead');
    const tr = document.createElement('tr');

    columns.forEach(col=>{
        const th = document.createElement('th');
        th.textContent = col.title ?? '';
        tr.appendChild(th);
    });

    thead.appendChild(tr);

    //////////////////////////////////////////////////

    tableInstance = $('#table').DataTable({

        data: json.data,
        columns: columns,

        pageLength:25,
        stateSave:true
    });

    registerRowEvents();
}

function registerRowEvents(){

    $('#btnNew').on('click',()=>{
        resetForm(form);
        modal.show();
    });



$('#table tbody').on('click','.edit',function(){

    $('#table tbody tr').removeClass('selected');

    const tr = $(this).closest('tr');
    tr.addClass('selected');

    const data = getTable().row(tr).data();

    bindForm(form,data);
    modal.show();
});

    $('#table tbody').on('click','.delete', async function(){

        const data = table.row($(this).closest('tr')).data();

        if(!confirm('Adresse löschen?')) return;

        await apiRequest('/api/adressen/'+data.id,'DELETE');

        toast('Adresse gelöscht','warning');

        table.row($(this).parents('tr')).remove().draw();
    });
}