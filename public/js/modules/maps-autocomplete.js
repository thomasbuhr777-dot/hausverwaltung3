import { onMapsReady } from "../core/maps-loader.js";

export function attachAutocomplete(options) {

    onMapsReady(() => {

        const input = document.getElementById(options.input);
        if (!input) return;

        const ac = new google.maps.places.Autocomplete(input, {
            types: ["address"],
            componentRestrictions: { country: ["de"] },
            fields: ["address_components", "geometry"]
        });

        ac.addListener("place_changed", () => {

            const place = ac.getPlace();
            if (!place?.geometry) return;

            const c = {};

            for (const comp of place.address_components || []) {
                for (const t of comp.types)
                    c[t] = comp.long_name;
            }

            options.onSelect(place, c);
        });
    });
}