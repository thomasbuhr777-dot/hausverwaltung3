import { attachAutocomplete } from "../modules/maps-autocomplete.js";

document.addEventListener("DOMContentLoaded", () => {

    attachAutocomplete({
        input: "autocomplete",

        onSelect(place, c) {

            set("street", c.route);
            set("street_number", c.street_number);
            set("postal_code", c.postal_code);

            set(
                "city",
                c.locality ||
                c.postal_town ||
                c.administrative_area_level_2
            );

            const lat = place.geometry.location.lat();
            const lng = place.geometry.location.lng();

            set("lat", lat);
            set("lng", lng);

            updateMap(lat, lng);
        }
    });

});

function set(id, val) {
    const el = document.getElementById(id);
    if (el) el.value = val ?? "";
}

function updateMap(lat, lng) {
    const map = document.getElementById("staticMap");
    if (!map) return;

    map.src =
        `https://maps.googleapis.com/maps/api/staticmap` +
        `?center=${lat},${lng}` +
        `&zoom=16&size=600x400&scale=2` +
        `&markers=color:red|${lat},${lng}` +
        `&key=API_KEY`;

    map.style.display = "block";
}