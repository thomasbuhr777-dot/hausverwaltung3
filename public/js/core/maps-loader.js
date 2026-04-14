let ready = false;
let callbacks = [];

window.__googleMapsReady = () => {
    ready = true;
    callbacks.forEach(cb => cb());
    callbacks = [];
};

export function onMapsReady(cb) {
    if (ready) cb();
    else callbacks.push(cb);
}