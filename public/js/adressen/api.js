export async function apiRequest(url, method='GET', data=null){

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

    // ⭐ response kann leer sein!
    let json = null;

    const text = await res.text();

    if(text.length){
        try{
            json = JSON.parse(text);
        }catch(e){
            console.warn('Invalid JSON response', text);
        }
    }

    if(!res.ok){
        throw {
            status: res.status,
            data: json
        };
    }

    

    return json;
}