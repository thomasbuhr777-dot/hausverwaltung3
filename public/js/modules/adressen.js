/* ==========================================================
   Adressen Module
   ========================================================== */

let autocomplete = null;

const Adressen = {

    /* ======================================================
       ENTRY POINT
    ====================================================== */
    init() {
        this.bindEditButtons();
        this.resetModals();
        this.initValidation();
        this.bindAutocompleteToModal();
    },

    /* ======================================================
       EDIT BUTTONS
    ====================================================== */
    bindEditButtons() {

        document.addEventListener("click", e => {

            const btn = e.target.closest(".editBtn");
            if (!btn) return;

            for (const key in btn.dataset) {
                const el = document.getElementById("edit-" + key);
                if (el) el.value = btn.dataset[key] ?? "";
            }
        });
    },

    /* ======================================================
       MODAL RESET
    ====================================================== */
    resetModals() {

        document.addEventListener("hidden.bs.modal", e => {

            const modal = e.target;
            if (!modal.classList.contains("modal")) return;

            const form = modal.querySelector("form");
            if (form) form.reset();

            modal.querySelectorAll('[name="firmenname"]')
                .forEach(el => {
                    el.classList.add("d-none");
                    el.required = false;
                });

            // autocomplete neu erlauben
            const input = modal.querySelector("#autocomplete");
            if (input) delete input.dataset.autocompleteBound;
        });
    },

    /* ======================================================
       FORM VALIDATION
    ====================================================== */
    initValidation() {

        document.querySelectorAll(".needs-validation")
            .forEach(form => {

                form.addEventListener("submit", e => {

                    if (!form.checkValidity()) {
                        e.preventDefault();
                        e.stopPropagation();
                    }

                    form.classList.add("was-validated");
                });
            });
    },

    /* ======================================================
       GOOGLE AUTOCOMPLETE — MODAL HOOK
    ====================================================== */
    bindAutocompleteToModal() {

        const modal = document.getElementById("createModal");
        if (!modal) return;

        modal.addEventListener("shown.bs.modal", () => {
            this.initAutocomplete();
        });
    },

    /* ======================================================
       GOOGLE AUTOCOMPLETE INIT
    ====================================================== */
    initAutocomplete() {

        const input = document.getElementById("autocomplete");

        if (!input) return;

        // verhindert doppelte Initialisierung
        if (input.dataset.autocompleteBound === "1") return;

        // Google noch nicht geladen?
        if (!window.google?.maps?.places) {
            console.warn("Google Maps not ready yet");
            return;
        }

        input.dataset.autocompleteBound = "1";

        autocomplete = new google.maps.places.Autocomplete(input, {
            types: ["address"],
            fields: ["address_components", "geometry"]
        });

        autocomplete.addListener(
            "place_changed",
            this.onPlaceChanged.bind(this)
        );
    },

    /* ======================================================
       PLACE SELECTED
    ====================================================== */
    onPlaceChanged() {

        const place = autocomplete.getPlace();
        if (!place?.geometry) return;

        const components = {};

        for (const c of place.address_components || []) {
            for (const t of c.types) {
                components[t] = c.long_name;
            }
        }

        this.set("street", components.route || "");
        this.set("street_number", components.street_number || "");
        this.set("postal_code", components.postal_code || "");

        this.set(
            "city",
            components.locality ||
            components.postal_town ||
            components.administrative_area_level_2 ||
            ""
        );

        const lat = place.geometry.location.lat();
        const lng = place.geometry.location.lng();

        this.set("lat", lat);
        this.set("lng", lng);

        this.updateStaticMap(lat, lng);
    },

    /* ======================================================
       STATIC MAP PREVIEW
    ====================================================== */
    updateStaticMap(lat, lng) {

        const map = document.getElementById("staticMap");
        if (!map) return;

        const url =
            "https://maps.googleapis.com/maps/api/staticmap" +
            `?center=${lat},${lng}` +
            "&zoom=16" +
            "&size=600x400" +
            "&scale=2" +
            `&markers=color:red|${lat},${lng}` +
            "&key=AIzaSyAjYGLg_OTBjnwGyWTLjDKaOsXT0lTQa6k";

        map.src = url;
        map.style.display = "block";
    },

    /* ======================================================
       HELPER
    ====================================================== */
    set(id, val) {
        const el = document.getElementById(id);
        if (el) el.value = val ?? "";
    }
};

export default Adressen;