import Adressen from "../modules/adressen.js";
import { registerGoogleCallback } from "../modules/maps.js";

document.addEventListener("DOMContentLoaded", () => {

    registerGoogleCallback(() => {
        Adressen.initAutocomplete();
    });

    Adressen.init();
});