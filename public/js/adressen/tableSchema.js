//////////////////////////////////////////////////
// AUTO COLUMN BUILDER
//////////////////////////////////////////////////

export function buildColumns(rows){

    if(!rows || !rows.length) return [];

    const sample = rows[0];

    const hidden = new Set([
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by'
    ]);

    const cols = Object.keys(sample)
        .filter(key => !hidden.has(key))
        .map(key => {

            // EMAIL automatisch klickbar
            if(key === 'email'){
                return {
                    data:key,
                    title:capitalize(key),
                    render:d =>
                        d ? `<a href="mailto:${d}">${d}</a>` : ''
                };
            }

            // ORT Spezialanzeige
            if(key === 'ort'){
                return {
                    data:key,
                    title:'Ort',
                    render:(d,t,row)=>
                        [row.plz,d].filter(Boolean).join(' ')
                };
            }

            return {
                data:key,
                title:capitalize(key)
            };
        });

    //////////////////////////////////////////////////
    // ACTION COLUMN automatisch anhängen
    //////////////////////////////////////////////////

    cols.push({
        data:null,
        title:'Aktionen',
        orderable:false,
        render:()=>`
            <div class="btn-group btn-group-sm">
                <button class="btn btn-secondary edit">Edit</button>
                <button class="btn btn-danger delete">Delete</button>
            </div>`
    });

    return cols;
}

function capitalize(str){
    return str.charAt(0).toUpperCase()+str.slice(1);
}