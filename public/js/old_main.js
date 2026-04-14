import "./bootstrap.bundle.min.js";
import { initTheme } from "./core/theme.js";
import "./modules/maps.js";

import Adressen from "./modules/adressen.js";
import { registerGoogleCallback } from "./modules/maps.js";

document.addEventListener("DOMContentLoaded", () => {

    initTheme();

    if (document.body.dataset.page === "adressen") {

        Adressen.init();

        // 👇 DAS FEHLT
        registerGoogleCallback(() => {
            Adressen.initAutocomplete();
        });
    }
});