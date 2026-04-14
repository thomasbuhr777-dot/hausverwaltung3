<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php $isEdit = !empty($objekt['id']); ?>

<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= base_url('objekte') ?>">Objekte</a></li>
            <?php if ($isEdit): ?>
                <li class="breadcrumb-item"><a href="<?= base_url("objekte/{$objekt['id']}") ?>"><?= esc($objekt['bezeichnung']) ?></a></li>
            <?php endif; ?>
            <li class="breadcrumb-item active"><?= $isEdit ? 'Bearbeiten' : 'Neu' ?></li>
        </ol>
    </nav>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="post"
                      action="<?= $isEdit ? base_url("objekte/{$objekt['id']}") : base_url('objekte') ?>"
                      id="objektForm">
                    <?= csrf_field() ?>

                    <div class="row g-3">

                        <!-- 1. Objektart + Status -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Objektart</label>
                            <select name="objektart_id" id="objektart_id" class="form-select">
                                <option value="">— bitte wählen —</option>
                                <?php foreach ($objektarten as $art): ?>
                                    <option value="<?= $art['id'] ?>"
                                        <?= (int) old('objektart_id', $objekt['objektart_id'] ?? 0) === (int) $art['id'] ? 'selected' : '' ?>>
                                        <?= esc($art['bezeichnung']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <?php foreach (['aktiv' => 'Aktiv', 'inaktiv' => 'Inaktiv'] as $val => $label): ?>
                                    <option value="<?= $val ?>"
                                        <?= old('status', $objekt['status'] ?? 'aktiv') === $val ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Divider Eigentümer -->
                        <div class="col-12"><hr class="my-1"></div>

                        <!-- Eigentümer Typeahead -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Eigentümer</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-person-fill text-muted"></i>
                                </span>
                                <input type="text"
                                       id="eigentuemer_search"
                                       class="form-control border-start-0 border-end-0"
                                       placeholder="Name oder Firma suchen…"
                                       autocomplete="off"
                                       value="<?= esc($eigentuemer_anzeigename) ?>">
                                <button type="button"
                                        class="btn btn-outline-secondary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalNeueAdresse"
                                        title="Neuen Eigentümer anlegen">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                            <!-- Dropdown-Liste -->
                            <ul id="eigentuemer_dropdown"
                                class="list-group shadow-sm position-absolute z-3 w-100"
                                style="max-width:500px; display:none; max-height:260px; overflow-y:auto">
                            </ul>
                            <input type="hidden"
                                   name="eigentuemer_id"
                                   id="eigentuemer_id"
                                   value="<?= esc(old('eigentuemer_id', $objekt['eigentuemer_id'] ?? '')) ?>">
                            <div class="form-text">Tippen Sie mind. 2 Zeichen – oder legen Sie einen neuen Eigentümer an.</div>
                        </div>

                        <!-- Divider Adresse -->
                        <div class="col-12">
                            <hr class="my-1">
                            <p class="text-muted small mb-0">
                                <i class="bi bi-geo-alt-fill text-primary me-1"></i>
                                Adresse über die Suche eingeben – die Felder werden automatisch befüllt.
                            </p>
                        </div>

                        <!-- 3. Google Places Adresssuche -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Adresssuche</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text"
                                       id="places_search"
                                       class="form-control border-start-0"
                                       placeholder="Straße und Ort eingeben…"
                                       autocomplete="off">
                            </div>
                            <?php if ($isEdit && !empty($objekt['strasse'])): ?>
                                <div class="form-text text-warning">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    Neue Suche überschreibt die bestehende Adresse.
                                </div>
                            <?php else: ?>
                                <div class="form-text">Tippen Sie die Adresse ein und wählen Sie einen Vorschlag.</div>
                            <?php endif; ?>
                        </div>

                        <!-- 4. Strasse + Hausnummer (readonly) -->
                        <div class="col-md-9">
                            <label class="form-label fw-semibold">
                                Straße *
                                <span id="adresse_status" class="ms-2 small"></span>
                            </label>
                            <input type="text"
                                   name="strasse"
                                   id="field_strasse"
                                   class="form-control bg-light"
                                   value="<?= esc(old('strasse', $objekt['strasse'] ?? '')) ?>"
                                   readonly
                                   placeholder="Wird durch Adresssuche befüllt">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Hausnr.</label>
                            <input type="text"
                                   name="hausnummer"
                                   id="field_hausnummer"
                                   class="form-control bg-light"
                                   value="<?= esc(old('hausnummer', $objekt['hausnummer'] ?? '')) ?>"
                                   readonly
                                   placeholder="–">
                        </div>

                        <!-- 5. PLZ + Ort (readonly) -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">PLZ *</label>
                            <input type="text"
                                   name="plz"
                                   id="field_plz"
                                   class="form-control bg-light"
                                   value="<?= esc(old('plz', $objekt['plz'] ?? '')) ?>"
                                   readonly
                                   placeholder="–">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Ort *</label>
                            <input type="text"
                                   name="ort"
                                   id="field_ort"
                                   class="form-control bg-light"
                                   value="<?= esc(old('ort', $objekt['ort'] ?? '')) ?>"
                                   readonly
                                   placeholder="–">
                        </div>

                        <!-- Hidden: Land, Koordinaten, Place ID -->
                        <input type="hidden" name="land"      id="field_land"
                               value="<?= esc(old('land',      $objekt['land']      ?? '')) ?>">
                        <input type="hidden" name="latitude"  id="field_latitude"
                               value="<?= esc(old('latitude',  $objekt['latitude']  ?? '')) ?>">
                        <input type="hidden" name="longitude" id="field_longitude"
                               value="<?= esc(old('longitude', $objekt['longitude'] ?? '')) ?>">
                        <input type="hidden" name="place_id"  id="field_place_id"
                               value="<?= esc(old('place_id',  $objekt['place_id']  ?? '')) ?>">

                        <!-- Kartenvorschau (erscheint nach Auswahl oder bei Edit) -->
                        <div class="col-12" id="map_wrapper"
                             style="<?= empty($objekt['latitude']) ? 'display:none' : '' ?>">
                            <div id="map_canvas" class="rounded border" style="height: 220px;"></div>
                        </div>

                        <!-- Weitere Stammdaten -->
                        <div class="col-12"><hr class="my-1"></div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Baujahr</label>
                            <input type="number" name="baujahr" class="form-control"
                                   min="1800" max="<?= date('Y') ?>"
                                   value="<?= esc(old('baujahr', $objekt['baujahr'] ?? '')) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Gesamtfläche (m²)</label>
                            <div class="input-group">
                                <input type="number" name="gesamtflaeche" class="form-control"
                                       step="0.01" min="0"
                                       value="<?= esc(old('gesamtflaeche', $objekt['gesamtflaeche'] ?? '')) ?>">
                                <span class="input-group-text">m²</span>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Beschreibung</label>
                            <textarea name="beschreibung" class="form-control" rows="3"
                                      placeholder="Optionale Beschreibung..."><?= esc(old('beschreibung', $objekt['beschreibung'] ?? '')) ?></textarea>
                        </div>

                    </div><!-- /.row -->

                    <hr class="my-4">
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="<?= base_url('objekte') ?>" class="btn btn-outline-secondary">Abbrechen</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>
                            <?= $isEdit ? 'Änderungen speichern' : 'Objekt anlegen' ?>
                        </button>
                    </div>
                </form>

                <?php if ($isEdit): ?>
                <hr>
                <form method="post" action="<?= base_url("objekte/{$objekt['id']}/loeschen") ?>"
                      onsubmit="return confirm('Objekt wirklich löschen? Alle Einheiten werden ebenfalls gelöscht.')">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-trash me-1"></i> Objekt löschen
                    </button>
                </form>
                <?php endif; ?>

            </div><!-- /.card-body -->
        </div><!-- /.card -->
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
    'use strict';

    const fields = {
        strasse    : document.getElementById('field_strasse'),
        hausnummer : document.getElementById('field_hausnummer'),
        plz        : document.getElementById('field_plz'),
        ort        : document.getElementById('field_ort'),
        land       : document.getElementById('field_land'),
        latitude   : document.getElementById('field_latitude'),
        longitude  : document.getElementById('field_longitude'),
        place_id   : document.getElementById('field_place_id'),
    };

    const searchInput = document.getElementById('places_search');
    const statusBadge = document.getElementById('adresse_status');
    const mapWrapper  = document.getElementById('map_wrapper');
    const mapCanvas   = document.getElementById('map_canvas');

    let map    = null;
    let marker = null;

    function setStatus(text, type) {
        statusBadge.innerHTML = text
            ? `<span class="badge bg-${type}">${text}</span>`
            : '';
    }

    function resetAddressFields() {
        ['strasse','hausnummer','plz','ort','land','latitude','longitude','place_id']
            .forEach(k => { fields[k].value = ''; });
        setStatus('', '');
    }

    function updateMap(lat, lng) {
        const pos = { lat, lng };
        mapWrapper.style.display = '';          // Karte sichtbar machen

        if (!map) {
            map = new google.maps.Map(mapCanvas, {
                zoom               : 16,
                center             : pos,
                mapTypeControl     : false,
                streetViewControl  : false,
                fullscreenControl  : false,
            });
            marker = new google.maps.Marker({
                position  : pos,
                map,
                animation : google.maps.Animation.DROP,
            });
        } else {
            map.setCenter(pos);
            map.setZoom(16);
            marker.setPosition(pos);
        }
    }

    // Globaler Callback für die Google Maps API
    window.initGooglePlaces = function () {

        const autocomplete = new google.maps.places.Autocomplete(searchInput, {
            types                : ['address'],
            fields               : ['address_components', 'geometry', 'place_id', 'formatted_address'],
            componentRestrictions: { country: ['de', 'at', 'ch'] },
        });

        autocomplete.addListener('place_changed', function () {
            const place = autocomplete.getPlace();

            if (!place.geometry?.location) {
                setStatus('Adresse nicht gefunden', 'warning');
                return;
            }

            resetAddressFields();

            // Adresskomponenten in ein Lookup-Objekt überführen
            const comp = {};
            (place.address_components ?? []).forEach(c => {
                c.types.forEach(t => { comp[t] = c; });
            });

            fields.strasse.value    = comp['route']?.long_name         ?? '';
            fields.hausnummer.value = comp['street_number']?.long_name ?? '';
            fields.plz.value        = comp['postal_code']?.long_name   ?? '';
            fields.ort.value        = comp['locality']?.long_name
                                   ?? comp['postal_town']?.long_name   ?? '';
            fields.land.value       = comp['country']?.long_name       ?? '';
            fields.place_id.value   = place.place_id                   ?? '';

            const lat = place.geometry.location.lat();
            const lng = place.geometry.location.lng();
            fields.latitude.value  = lat.toFixed(7);
            fields.longitude.value = lng.toFixed(7);

            searchInput.value = place.formatted_address ?? '';
            searchInput.classList.remove('is-invalid');
            setStatus('Adresse übernommen ✓', 'success');
            updateMap(lat, lng);
            updateBezeichnung();
        });

        // Beim Bearbeiten: vorhandene Koordinaten sofort auf Karte zeigen
        const existingLat = parseFloat(fields.latitude.value);
        const existingLng = parseFloat(fields.longitude.value);
        if (!isNaN(existingLat) && !isNaN(existingLng)) {
            updateMap(existingLat, existingLng);
        }
    };

    // Formularvalidierung: Straße muss gesetzt sein
    document.getElementById('objektForm').addEventListener('submit', function (e) {
        if (!fields.strasse.value.trim()) {
            e.preventDefault();
            searchInput.classList.add('is-invalid');
            searchInput.focus();
            setStatus('Bitte Adresse über die Suche auswählen', 'danger');
        }
    });

    // Felder leeren wenn Sucheingabe geleert wird
    searchInput.addEventListener('input', function () {
        if (!this.value.trim()) resetAddressFields();
        this.classList.remove('is-invalid');
    });

    // Bezeichnung automatisch aus Adresse + Objektart zusammensetzen
    const objektarten = <?= json_encode(array_column($objektarten, 'bezeichnung', 'id')) ?>;

    function updateBezeichnung() {
        const strasse    = fields.strasse.value.trim();
        const hausnummer = fields.hausnummer.value.trim();
        const artId      = document.getElementById('objektart_id')?.value;
        const artLabel   = artId ? (objektarten[artId] ?? '') : '';

        if (strasse) {
            const adresse     = hausnummer ? `${strasse} ${hausnummer}` : strasse;
            window._bezeichnung = artLabel ? `${adresse} - ${artLabel}` : adresse;
        }
    }

    // Bezeichnung bei Objektart-Wechsel neu berechnen
    document.getElementById('objektart_id')?.addEventListener('change', updateBezeichnung);

}());
</script>
<script>
// -----------------------------------------------------------------------
// Eigentümer Typeahead
// -----------------------------------------------------------------------
(function () {
    'use strict';

    const searchInput = document.getElementById('eigentuemer_search');
    const hiddenId    = document.getElementById('eigentuemer_id');
    const dropdown    = document.getElementById('eigentuemer_dropdown');
    let debounceTimer = null;

    function showDropdown(items) {
        dropdown.innerHTML = '';

        if (!items.length) {
            dropdown.innerHTML = '<li class="list-group-item text-muted small py-2">Keine Treffer</li>';
            dropdown.style.display = '';
            return;
        }

        items.forEach(item => {
            const li = document.createElement('li');
            li.className = 'list-group-item list-group-item-action py-2 px-3';
            li.style.cursor = 'pointer';

            const name = item.anzeigename || '';
            const sub  = [item.plz, item.ort].filter(Boolean).join(' ');

            li.innerHTML = `
                <div class="fw-medium">${escHtml(name)}</div>
                ${sub ? `<small class="text-muted">${escHtml(sub)}</small>` : ''}`;

            li.addEventListener('mousedown', e => {
                e.preventDefault(); // Blur verhindern
                searchInput.value = name;
                hiddenId.value    = item.id;
                hideDropdown();
            });

            dropdown.appendChild(li);
        });

        dropdown.style.display = '';
    }

    function hideDropdown() {
        dropdown.style.display = 'none';
        dropdown.innerHTML = '';
    }

    function escHtml(str) {
        return str.replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }

    searchInput.addEventListener('input', function () {
        const q = this.value.trim();
        hiddenId.value = ''; // Auswahl zurücksetzen wenn erneut getippt

        clearTimeout(debounceTimer);
        if (q.length < 2) { hideDropdown(); return; }

        debounceTimer = setTimeout(async () => {
            try {
                const res  = await fetch(`<?= base_url('adressen/suche') ?>?q=${encodeURIComponent(q)}`);
                const data = await res.json();
                showDropdown(data);
            } catch (e) {
                console.error('Typeahead-Fehler:', e);
            }
        }, 250);
    });

    searchInput.addEventListener('blur', () => setTimeout(hideDropdown, 150));
    searchInput.addEventListener('keydown', e => {
        if (e.key === 'Escape') hideDropdown();
    });

}());

// -----------------------------------------------------------------------
// Modal: Neue Adresse anlegen
// -----------------------------------------------------------------------
(function () {
    'use strict';

    const modal       = document.getElementById('modalNeueAdresse');
    const alertBox    = document.getElementById('modal_alert');
    const btnSpeichern = document.getElementById('btn_adresse_speichern');
    const searchInput = document.getElementById('eigentuemer_search');
    const hiddenId    = document.getElementById('eigentuemer_id');

    // Typ-Umschalter Person / Firma
    document.querySelectorAll('input[name="modal_kontakt_typ"]').forEach(radio => {
        radio.addEventListener('change', function () {
            document.getElementById('block_person').style.display = this.value === 'person' ? '' : 'none';
            document.getElementById('block_firma').style.display  = this.value === 'firma'  ? '' : 'none';
        });
    });

    // Modal zurücksetzen wenn geöffnet
    modal.addEventListener('show.bs.modal', () => {
        alertBox.classList.add('d-none');
        alertBox.textContent = '';
        modal.querySelectorAll('input[type=text], input[type=email], textarea').forEach(el => {
            if (el.id !== 'm_land') el.value = '';
        });
        document.getElementById('m_land').value = 'Deutschland';
        document.getElementById('typ_person').checked = true;
        document.getElementById('block_person').style.display = '';
        document.getElementById('block_firma').style.display  = 'none';
        // Suchwort als Nachnamen vorbelegen
        const q = searchInput.value.trim();
        if (q) document.getElementById('m_nachname').value = q;
    });

    btnSpeichern.addEventListener('click', async () => {
        alertBox.classList.add('d-none');
        btnSpeichern.disabled = true;
        btnSpeichern.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Speichere…';

        const typ = document.querySelector('input[name="modal_kontakt_typ"]:checked').value;

        const payload = new URLSearchParams({
            kontakt_typ     : typ,
            anrede          : document.getElementById('m_anrede')?.value        ?? '',
            titel           : document.getElementById('m_titel').value,
            vorname         : document.getElementById('m_vorname').value,
            nachname        : document.getElementById('m_nachname').value,
            firmenname      : document.getElementById('m_firmenname').value,
            umsatzsteuer_id : document.getElementById('m_umsatzsteuer_id').value,
            email           : document.getElementById('m_email').value,
            telefon1        : document.getElementById('m_telefon1').value,
            strasse         : document.getElementById('m_strasse').value,
            hsnr            : document.getElementById('m_hsnr').value,
            plz             : document.getElementById('m_plz').value,
            ort             : document.getElementById('m_ort').value,
            land            : document.getElementById('m_land').value,
            iban            : document.getElementById('m_iban').value,
            bank            : document.getElementById('m_bank').value,
            bemerkungen     : document.getElementById('m_bemerkungen').value,
            '<?= csrf_token() ?>' : '<?= csrf_hash() ?>',
        });

        try {
            const res  = await fetch('<?= base_url('adressen/schnell') ?>', {
                method  : 'POST',
                headers : { 'Content-Type': 'application/x-www-form-urlencoded' },
                body    : payload.toString(),
            });
            const data = await res.json();

            if (!res.ok) {
                alertBox.textContent = data.error ?? 'Unbekannter Fehler.';
                alertBox.classList.remove('d-none');
                return;
            }

            // ID + Name ins Hauptformular übernehmen
            hiddenId.value    = data.id;
            searchInput.value = data.anzeigename;

            // Modal schließen
            bootstrap.Modal.getInstance(modal).hide();

        } catch (e) {
            alertBox.textContent = 'Netzwerkfehler. Bitte erneut versuchen.';
            alertBox.classList.remove('d-none');
        } finally {
            btnSpeichern.disabled = false;
            btnSpeichern.innerHTML = '<i class="bi bi-check-lg me-1"></i> Anlegen & übernehmen';
        }
    });

}());
</script>

<?php $apiKey = env('google.maps.api_key', '') ?>
<script
    src="https://maps.googleapis.com/maps/api/js?key=<?= esc($apiKey) ?>&libraries=places&callback=initGooglePlaces&loading=async"
    async defer>
</script>

<!-- ================================================================
     Modal: Neue Adresse / Eigentümer schnell anlegen
     ================================================================ -->
<div class="modal fade" id="modalNeueAdresse" tabindex="-1" aria-labelledby="modalNeueAdresseLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNeueAdresseLabel">
                    <i class="bi bi-person-plus-fill me-2"></i>Neuen Eigentümer anlegen
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="modal_alert" class="alert alert-danger d-none"></div>

                <!-- Typ-Umschalter -->
                <div class="mb-3">
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="modal_kontakt_typ" id="typ_person" value="person" checked>
                        <label class="btn btn-outline-primary" for="typ_person">
                            <i class="bi bi-person me-1"></i> Person
                        </label>
                        <input type="radio" class="btn-check" name="modal_kontakt_typ" id="typ_firma" value="firma">
                        <label class="btn btn-outline-primary" for="typ_firma">
                            <i class="bi bi-building me-1"></i> Firma
                        </label>
                    </div>
                </div>

                <!-- Personen-Felder -->
                <div id="block_person">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Anrede</label>
                            <select id="m_anrede" class="form-select">
                                <option value="">–</option>
                                <option>Herr</option>
                                <option>Frau</option>
                                <option>Divers</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Titel</label>
                            <input type="text" id="m_titel" class="form-control" placeholder="Dr., Prof.">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Vorname</label>
                            <input type="text" id="m_vorname" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Nachname *</label>
                            <input type="text" id="m_nachname" class="form-control">
                        </div>
                    </div>
                </div>

                <!-- Firmen-Felder -->
                <div id="block_firma" style="display:none">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Firmenname *</label>
                            <input type="text" id="m_firmenname" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Umsatzsteuer-ID</label>
                            <input type="text" id="m_umsatzsteuer_id" class="form-control" placeholder="DE123456789">
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Gemeinsame Felder -->
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">E-Mail</label>
                        <input type="email" id="m_email" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Telefon</label>
                        <input type="text" id="m_telefon1" class="form-control">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Straße</label>
                        <input type="text" id="m_strasse" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Hausnr.</label>
                        <input type="text" id="m_hsnr" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">PLZ</label>
                        <input type="text" id="m_plz" class="form-control">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Ort</label>
                        <input type="text" id="m_ort" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Land</label>
                        <input type="text" id="m_land" class="form-control" value="Deutschland">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">IBAN</label>
                        <input type="text" id="m_iban" class="form-control" placeholder="DE00 0000 0000 0000 0000 00">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Bank</label>
                        <input type="text" id="m_bank" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Bemerkungen</label>
                        <textarea id="m_bemerkungen" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Abbrechen</button>
                <button type="button" class="btn btn-primary" id="btn_adresse_speichern">
                    <i class="bi bi-check-lg me-1"></i> Anlegen & übernehmen
                </button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>