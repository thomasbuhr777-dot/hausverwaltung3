//////////////////////////////////////////////////
// FORM → JSON
//////////////////////////////////////////////////

export function formData(form){

    const obj = {};

    const forbidden = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    new FormData(form).forEach((value,key)=>{

        if(forbidden.includes(key)) return;

        obj[key] = value === '' ? null : value;
    });

    return obj;
}
//////////////////////////////////////////////////
// JSON → FORM
//////////////////////////////////////////////////

export function bindForm(form,data){

    form.querySelectorAll('[name]').forEach(el=>{

        if(!(el.name in data)) return;

        const value = data[el.name] ?? '';

        switch(el.type){

            case 'checkbox':
                el.checked = !!value;
                break;

            case 'radio':
                el.checked = el.value == value;
                break;

            default:
                el.value = value;
        }
    });
}

//////////////////////////////////////////////////
// RESET FORM CLEAN
//////////////////////////////////////////////////

export function resetForm(form){

    form.reset();

    form.querySelectorAll('.is-invalid')
        .forEach(e=>e.classList.remove('is-invalid'));
}