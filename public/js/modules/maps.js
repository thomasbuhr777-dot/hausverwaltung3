let readyCallbacks = [];
let isReady = false;

/* ===================================================
   GLOBAL CALLBACK — sofort registrieren
   =================================================== */

window.__googleMapsReady = function () {

    isReady = true;

    readyCallbacks.forEach(cb => {
        try {
            cb();
        } catch (e) {
            console.error(e);
        }
    });

    readyCallbacks = [];
};

/* ===================================================
   PUBLIC API
   =================================================== */

export function registerGoogleCallback(cb) {

    if (isReady) {
        cb();
        return;
    }

    readyCallbacks.push(cb);
}