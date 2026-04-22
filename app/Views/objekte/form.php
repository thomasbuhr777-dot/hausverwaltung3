<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$isEdit = ! empty($objekt['id']);
$errors = session('errors') ?? [];

// [1] CSRF-Meta-Tag – wird von JS gelesen statt Hash direkt einzubetten
?>
<meta name="csrf-token-name"  content="<?= csrf_token() ?>">
<meta name="csrf-token-value" content="<?= csrf_hash() ?>">

<!-- Breadcrumb -->
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="<?= base_url('objekte') ?>">Objekte</a>
            </li>
            <?php if ($isEdit): ?>
                <li class="breadcrumb-item">
                    <a href="<?= base_url("objekte/{$objekt['id']}") ?>">
                        <?= esc($objekt['bezeichnung']) ?>
                    </a>
                </li>
            <?php endif; ?>
            <li class="breadcrumb-item active">
                <?= $isEdit ? 'Bearbeiten' : 'Neu' ?>
            </li>
        </ol>
    </nav>
</div>

<!-- [2] Validierungsfehler (fehlte im Original komplett) -->
<?php if (! empty($errors)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Bitte korrigiere folgende Fehler:</strong>
        <ul class="mb-0 mt-1 ps-3">
            <?php foreach ($errors as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">

                <form method="post"
                      action="<?= $isEdit ? base_url("objekte/{$objekt['id']}") : base_url('objekte') ?>"
                      id="objektForm">
                    <?= csrf_field() ?>

                    <div class="row g-3">

                        <!-- Objektart -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Objektart</label>
                            <select name="objektart_id" id="objektart_id" class="form-select">
                                <option value="">— bitte wählen —</option>
                                <?php foreach ($objektarten as $art): ?>
                                    <option value="<?= (int) $art['id'] ?>"
                                        <?= (int) old('objektart_id', $objekt['objektart_id'] ?? 0) === (int) $art['id'] ? 'selected' : '' ?>>
                                        <?= esc($art['bezeichnung']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Status -->
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

              

                        <!-- Eigentümer-Typeahead -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Eigentümer</label>
                            <div class="input-group">
                                <!--
                                <span class="input-group-text bg-primary border-end-0">
                                    <i class="bi bi-person-fill"></i>
                                </span>
                                -->
                                  <button type="button"
                                        class="btn btn-primary"
                                        title="Neuen Eigentümer anlegen">
                                    <i class="bi bi-person"></i>
                                </button>
                                <input type="text"
                                       id="eigentuemer_search"
                                       class="form-control border-start-0 border-end-0"
                                       placeholder="Name oder Firma suchen…"
                                       autocomplete="off"
                                       value="<?= esc($eigentuemer_anzeigename ?? '') ?>">
                                <button type="button"
                                        class="btn btn-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalNeueAdresse"
                                        title="Neuen Eigentümer anlegen">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>

                            <ul id="eigentuemer_dropdown"
                                class="list-group shadow-sm position-absolute z-3"
                                style="max-width:500px; display:none; max-height:260px; overflow-y:auto">
                            </ul>

                            <input type="hidden"
                                   name="eigentuemer_id"
                                   id="eigentuemer_id"
                                   value="<?= esc(old('eigentuemer_id', $objekt['eigentuemer_id'] ?? '')) ?>">

                            <div class="form-text">
                                Tippen Sie zur Suche min. 2 Zeichen – oder legen Sie einen neuen Eigentümer an.
                            </div>
                        </div>

                  

                        <!-- Google Places Adresssuche -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Adresssuche</label>
                            <div class="input-group">
                                <!--
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                -->
                                   <button type="button"
                                        class="btn btn-primary">
                                    <i class="bi bi-search"></i>
                                </button>
                                <input type="text"
                                       id="places_search"
                                       class="form-control border-start-0"
                                       placeholder="Straße und Ort eingeben…"
                                       autocomplete="off">
                            </div>

                            <?php if ($isEdit && ! empty($objekt['strasse'])): ?>
                                <div class="form-text text-warning">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    Neue Suche überschreibt die bestehende Adresse.
                                </div>
                            <?php else: ?>
                                <div class="form-text">
                                      <i class="bi bi-geo-alt-fill text-primary me-1"></i>
                                Adresse über die Suche eingeben – die Felder werden automatisch befüllt.
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Adressfelder (readonly – befüllt durch Google Places) -->
                        <div class="col-md-9">
                            <label class="form-label fw-semibold">
                                Straße *
                                <span id="adresse_status" class="ms-2 small"></span>
                            </label>
                            <input type="text"
                                   name="strasse"
                                   id="field_strasse"
                                   class="form-control"
                                   value="<?= esc(old('strasse', $objekt['strasse'] ?? '')) ?>"
                                   readonly
                                   placeholder="Wird durch Adresssuche befüllt">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Hausnr.</label>
                            <input type="text"
                                   name="hausnummer"
                                   id="field_hausnummer"
                                   class="form-control"
                                   value="<?= esc(old('hausnummer', $objekt['hausnummer'] ?? '')) ?>"
                                   placeholder="–">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">PLZ *</label>
                            <input type="text"
                                   name="plz"
                                   id="field_plz"
                                   class="form-control"
                                   value="<?= esc(old('plz', $objekt['plz'] ?? '')) ?>"
                                   readonly
                                   placeholder="–">
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Ort *</label>
                            <input type="text"
                                   name="ort"
                                   id="field_ort"
                                   class="form-control"
                                   value="<?= esc(old('ort', $objekt['ort'] ?? '')) ?>"
                                   readonly
                                   placeholder="–">
                        </div>

                        <!-- Versteckte Felder für Geo-Daten -->
                        <input type="hidden" name="land"      id="field_land"
                               value="<?= esc(old('land',      $objekt['land']      ?? '')) ?>">
                        <input type="hidden" name="latitude"  id="field_latitude"
                               value="<?= esc(old('latitude',  $objekt['latitude']  ?? '')) ?>">
                        <input type="hidden" name="longitude" id="field_longitude"
                               value="<?= esc(old('longitude', $objekt['longitude'] ?? '')) ?>">
                        <input type="hidden" name="place_id"  id="field_place_id"
                               value="<?= esc(old('place_id',  $objekt['place_id']  ?? '')) ?>">

                        <!-- Karte (wird bei vorhandenen Koordinaten eingeblendet) -->
                        <div class="col-12" id="map_wrapper"
                             style="<?= empty($objekt['latitude']) ? 'display:none' : '' ?>">
                            <div id="map_canvas" class="rounded border" style="height:220px"></div>
                        </div>

                        <div class="col-12"><hr class="my-1"></div>

                        <!-- Stammdaten -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Baujahr</label>
                            <input type="number"
                                   name="baujahr"
                                   class="form-control<?= isset($errors['baujahr']) ? ' is-invalid' : '' ?>"
                                   min="1800"
                                   max="<?= date('Y') ?>"
                                   value="<?= esc(old('baujahr', $objekt['baujahr'] ?? '')) ?>">
                            <?php if (isset($errors['baujahr'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['baujahr']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Gesamtfläche (m²)</label>
                            <div class="input-group">
                                <input type="number"
                                       name="gesamtflaeche"
                                       class="form-control"
                                       step="0.01"
                                       min="0"
                                       value="<?= esc(old('gesamtflaeche', $objekt['gesamtflaeche'] ?? '')) ?>">
                                <span class="input-group-text">m²</span>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Beschreibung</label>
                            <textarea name="beschreibung"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Optionale Beschreibung..."><?= esc(old('beschreibung', $objekt['beschreibung'] ?? '')) ?></textarea>
                        </div>

                    </div><!-- /.row -->

                    <hr class="my-4">

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="<?= base_url('objekte') ?>" class="btn btn-outline-secondary">
                            Abbrechen
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>
                            <?= $isEdit ? 'Änderungen speichern' : 'Objekt anlegen' ?>
                        </button>
                    </div>
                </form>

                <?php if ($isEdit): ?>
                    <hr>
                    <form method="post"
                          action="<?= base_url("objekte/{$objekt['id']}/loeschen") ?>"
                          onsubmit="return confirm('Objekt wirklich löschen? Alle Einheiten werden ebenfalls gelöscht.')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash me-1"></i> Objekt löschen
                        </button>
                    </form>
                <?php endif; ?>

            </div><!-- /.card-body -->
        </div><!-- /.card -->
    </div><!-- /.col -->
</div><!-- /.row -->

<!-- =========================================================================
     Modal: Neuen Eigentümer anlegen
     Muss innerhalb der content-Section stehen, damit CI4's View-Renderer
     es ins Layout einbettet. Außerhalb von endSection() wird der Block
     nicht gerendert → Bootstrap findet kein backdrop-Element → TypeError.
     ========================================================================= -->
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
                <div id="modal_alert" class="alert alert-danger d-none" role="alert"></div>

                <div class="mb-3">
                    <div class="btn-group w-100" role="group" aria-label="Kontakttyp">
                        <input type="radio" class="btn-check" name="modal_kontakt_typ"
                               id="typ_person" value="person" checked>
                        <label class="btn btn-outline-primary" for="typ_person">
                            <i class="bi bi-person me-1"></i> Person
                        </label>

                        <input type="radio" class="btn-check" name="modal_kontakt_typ"
                               id="typ_firma" value="firma">
                        <label class="btn btn-outline-primary" for="typ_firma">
                            <i class="bi bi-building me-1"></i> Firma
                        </label>
                    </div>
                </div>

                <!-- Person -->
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

                <!-- Firma -->
                <div id="block_firma" style="display:none">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Firmenname *</label>
                            <input type="text" id="m_firmenname" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Umsatzsteuer-ID</label>
                            <input type="text" id="m_umsatzsteuer_id" class="form-control"
                                   placeholder="DE123456789">
                        </div>
                    </div>
                </div>

                <hr>

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
                        <input type="text" id="m_iban" class="form-control"
                               placeholder="DE00 0000 0000 0000 0000 00">
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
            </div><!-- /.modal-body -->

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">Abbrechen</button>
                <button type="button" class="btn btn-primary" id="btn_adresse_speichern">
                    <i class="bi bi-check-lg me-1"></i> Anlegen &amp; übernehmen
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div>
</div>

<?= $this->endSection() ?>

<!-- =========================================================================
     Scripts (zusammengeführt in einen Block – war vorher 3 separate IIFEs)
     ========================================================================= -->
<?= $this->section('scripts') ?>
<script>
(function () {
    'use strict';

    // -------------------------------------------------------------------------
    // [1] CSRF-Helper: Token dynamisch aus Meta-Tag lesen + nach Response
    //     aktualisieren, damit Token-Rotation (CI4-Standard) nicht bricht.
    // -------------------------------------------------------------------------
    function getCsrfName()  { return document.querySelector('meta[name="csrf-token-name"]')?.content  ?? ''; }
    function getCsrfValue() { return document.querySelector('meta[name="csrf-token-value"]')?.content ?? ''; }

    function updateCsrfFromResponse(res) {
        const newToken = res.headers.get('X-CSRF-TOKEN');
        if (newToken) {
            const meta = document.querySelector('meta[name="csrf-token-value"]');
            if (meta) {
                meta.content = newToken;
            }
        }
    }

    // -------------------------------------------------------------------------
    // XSS-Helper für JS-seitige DOM-Interpolation
    // -------------------------------------------------------------------------
    function escHtml(str) {
        return String(str).replace(/[&<>"']/g, c => (
            { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]
        ));
    }

    // =========================================================================
    // 1) Google Places Adresssuche
    // =========================================================================
    const fields = {
        strasse   : document.getElementById('field_strasse'),
        hausnummer: document.getElementById('field_hausnummer'),
        plz       : document.getElementById('field_plz'),
        ort       : document.getElementById('field_ort'),
        land      : document.getElementById('field_land'),
        latitude  : document.getElementById('field_latitude'),
        longitude : document.getElementById('field_longitude'),
        place_id  : document.getElementById('field_place_id'),
    };

    const searchInput = document.getElementById('places_search');
    const statusBadge = document.getElementById('adresse_status');
    const mapWrapper  = document.getElementById('map_wrapper');
    const mapCanvas   = document.getElementById('map_canvas');
    const objektForm  = document.getElementById('objektForm');

    let map    = null;
    let marker = null;

    function setStatus(text, type) {
        if (statusBadge) {
            statusBadge.innerHTML = text ? `<span class="badge bg-${escHtml(type)}">${escHtml(text)}</span>` : '';
        }
    }

    function resetAddressFields() {
        Object.values(fields).forEach(el => { if (el) el.value = ''; });
        setStatus('', '');
    }

    function updateMap(lat, lng) {
        const pos = { lat, lng };
        if (mapWrapper) mapWrapper.style.display = '';

        if (! map && mapCanvas) {
            map = new google.maps.Map(mapCanvas, {
                zoom              : 16,
                center            : pos,
                mapTypeControl    : false,
                streetViewControl : false,
                fullscreenControl : false,
            });

            // TODO: Bei Google Maps JS API >= v3.58 auf AdvancedMarkerElement
            // migrieren (benötigt eine Map-ID in der Maps Platform Console).
            marker = new google.maps.Marker({
                position  : pos,
                map,
                animation : google.maps.Animation.DROP,
            });
        } else if (map && marker) {
            map.setCenter(pos);
            map.setZoom(16);
            marker.setPosition(pos);
        }
    }

    // [7] Maps-Script nur einbinden wenn API-Key vorhanden (siehe PHP unten)
    window.initGooglePlaces = function () {
        if (! searchInput) return;

        const autocomplete = new google.maps.places.Autocomplete(searchInput, {
            types               : ['address'],
            fields              : ['address_components', 'geometry', 'place_id', 'formatted_address'],
            componentRestrictions: { country: ['de', 'at', 'ch'] },
        });

        autocomplete.addListener('place_changed', function () {
            const place = autocomplete.getPlace();

            if (! place.geometry?.location) {
                setStatus('Adresse nicht gefunden', 'warning');
                return;
            }

            resetAddressFields();

            const comp = {};
            (place.address_components ?? []).forEach(c => {
                c.types.forEach(t => { comp[t] = c; });
            });

            fields.strasse.value    = comp['route']?.long_name ?? '';
            fields.hausnummer.value = comp['street_number']?.long_name ?? '';
            fields.plz.value        = comp['postal_code']?.long_name ?? '';
            fields.ort.value        = comp['locality']?.long_name ?? comp['postal_town']?.long_name ?? '';
            fields.land.value       = comp['country']?.long_name ?? '';
            fields.place_id.value   = place.place_id ?? '';

            const lat = place.geometry.location.lat();
            const lng = place.geometry.location.lng();
            fields.latitude.value  = lat.toFixed(7);
            fields.longitude.value = lng.toFixed(7);

            if (searchInput) searchInput.value = place.formatted_address ?? '';
            if (searchInput) searchInput.classList.remove('is-invalid');
            setStatus('Adresse übernommen ✓', 'success');
            updateMap(lat, lng);
        });

        // Bestehende Koordinaten beim Editieren sofort auf Karte zeigen
        const existingLat = parseFloat(fields.latitude?.value ?? '');
        const existingLng = parseFloat(fields.longitude?.value ?? '');
        if (! isNaN(existingLat) && ! isNaN(existingLng)) {
            updateMap(existingLat, existingLng);
        }
    };

    // Submit-Guard: Straße muss befüllt sein
    if (objektForm) {
        objektForm.addEventListener('submit', function (e) {
            if (! fields.strasse?.value.trim()) {
                e.preventDefault();
                if (searchInput) {
                    searchInput.classList.add('is-invalid');
                    searchInput.focus();
                }
                setStatus('Bitte Adresse über die Suche auswählen', 'danger');
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            if (! this.value.trim()) resetAddressFields();
            this.classList.remove('is-invalid');
        });
    }

    // =========================================================================
    // 2) Eigentümer-Typeahead
    // =========================================================================
    const eigentuemerSearch  = document.getElementById('eigentuemer_search');
    const eigentuemerHidden  = document.getElementById('eigentuemer_id');
    const eigentuemerDropdown = document.getElementById('eigentuemer_dropdown');

    if (eigentuemerSearch && eigentuemerHidden && eigentuemerDropdown) {

        let debounceTimer = null;

        function showDropdown(items) {
            eigentuemerDropdown.innerHTML = '';

            if (! items.length) {
                eigentuemerDropdown.innerHTML =
                    '<li class="list-group-item text-muted small py-2">Keine Treffer</li>';
                eigentuemerDropdown.style.display = '';
                return;
            }

            items.forEach(item => {
                const li  = document.createElement('li');
                li.className = 'list-group-item list-group-item-action py-2 px-3';
                li.style.cursor = 'pointer';

                const name = item.anzeigename ?? '';
                const sub  = [item.plz, item.ort].filter(Boolean).join(' ');
                li.innerHTML = `
                    <div class="fw-medium">${escHtml(name)}</div>
                    ${sub ? `<small class="text-muted">${escHtml(sub)}</small>` : ''}
                `;

                li.addEventListener('mousedown', e => {
                    e.preventDefault();
                    eigentuemerSearch.value = name;
                    eigentuemerHidden.value = item.id;
                    hideDropdown();
                });

                eigentuemerDropdown.appendChild(li);
            });

            eigentuemerDropdown.style.display = '';
        }

        function hideDropdown() {
            eigentuemerDropdown.style.display = 'none';
            eigentuemerDropdown.innerHTML = '';
        }

        eigentuemerSearch.addEventListener('input', function () {
            const q = this.value.trim();
            eigentuemerHidden.value = '';

            clearTimeout(debounceTimer);

            if (q.length < 2) {
                hideDropdown();
                return;
            }

            debounceTimer = setTimeout(async () => {
                try {
                    const res = await fetch(
                        `<?= base_url('adressen/suche') ?>?q=${encodeURIComponent(q)}`
                    );

                    // [9] Nicht-OK-Response abfangen
                    if (! res.ok) {
                        hideDropdown();
                        return;
                    }

                    const data = await res.json();
                    showDropdown(Array.isArray(data) ? data : []);
                } catch (e) {
                    console.error('Typeahead-Fehler:', e);
                    hideDropdown();
                }
            }, 250);
        });

        eigentuemerSearch.addEventListener('blur',    () => setTimeout(hideDropdown, 150));
        eigentuemerSearch.addEventListener('keydown', e => { if (e.key === 'Escape') hideDropdown(); });
    }

    // =========================================================================
    // 3) Modal: Neuen Eigentümer anlegen
    // =========================================================================
    const modal        = document.getElementById('modalNeueAdresse');
    const alertBox     = document.getElementById('modal_alert');
    const btnSpeichern = document.getElementById('btn_adresse_speichern');

    if (modal && alertBox && btnSpeichern && eigentuemerSearch && eigentuemerHidden) {

        // Kontakttyp-Toggle
        document.querySelectorAll('input[name="modal_kontakt_typ"]').forEach(radio => {
            radio.addEventListener('change', function () {
                const isPerson = this.value === 'person';
                const blockPerson = document.getElementById('block_person');
                const blockFirma  = document.getElementById('block_firma');
                if (blockPerson) blockPerson.style.display = isPerson ? '' : 'none';
                if (blockFirma)  blockFirma.style.display  = isPerson ? 'none' : '';
            });
        });

        // Modal öffnen: Felder zurücksetzen
        modal.addEventListener('show.bs.modal', () => {
            alertBox.classList.add('d-none');
            alertBox.textContent = '';

            // [10] Alle Eingabefelder zurücksetzen (inkl. select – war vorher vergessen)
            modal.querySelectorAll('input[type=text], input[type=email], textarea').forEach(el => {
                if (el.id !== 'm_land') el.value = '';
            });
            modal.querySelectorAll('select').forEach(el => { el.selectedIndex = 0; });

            const landField = document.getElementById('m_land');
            if (landField) landField.value = 'Deutschland';

            // Kontakttyp auf "Person" zurücksetzen
            const typPerson   = document.getElementById('typ_person');
            const blockPerson = document.getElementById('block_person');
            const blockFirma  = document.getElementById('block_firma');
            if (typPerson)   typPerson.checked        = true;
            if (blockPerson) blockPerson.style.display = '';
            if (blockFirma)  blockFirma.style.display  = 'none';

            // Suchbegriff als Nachname-Vorschlag übernehmen
            const q            = eigentuemerSearch?.value.trim() ?? '';
            const nachnameField = document.getElementById('m_nachname');
            if (q && nachnameField) nachnameField.value = q;
        });

        // Speichern per AJAX
        btnSpeichern.addEventListener('click', async () => {
            alertBox.classList.add('d-none');
            btnSpeichern.disabled = true;
            btnSpeichern.innerHTML =
                '<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span> Speichere…';

            const typRadio = document.querySelector('input[name="modal_kontakt_typ"]:checked');
            const typ      = typRadio?.value ?? 'person';

            // [1/4] CSRF-Token aus Meta-Tag lesen (nicht hardcoded im Script)
            const payload = new URLSearchParams({
                kontakt_typ     : typ,
                anrede          : document.getElementById('m_anrede')?.value        ?? '',
                titel           : document.getElementById('m_titel')?.value         ?? '',
                vorname         : document.getElementById('m_vorname')?.value       ?? '',
                nachname        : document.getElementById('m_nachname')?.value      ?? '',
                firmenname      : document.getElementById('m_firmenname')?.value    ?? '',
                umsatzsteuer_id : document.getElementById('m_umsatzsteuer_id')?.value ?? '',
                email           : document.getElementById('m_email')?.value         ?? '',
                telefon1        : document.getElementById('m_telefon1')?.value      ?? '',
                strasse         : document.getElementById('m_strasse')?.value       ?? '',
                hsnr            : document.getElementById('m_hsnr')?.value          ?? '',
                plz             : document.getElementById('m_plz')?.value           ?? '',
                ort             : document.getElementById('m_ort')?.value           ?? '',
                land            : document.getElementById('m_land')?.value          ?? '',
                iban            : document.getElementById('m_iban')?.value          ?? '',
                bank            : document.getElementById('m_bank')?.value          ?? '',
                bemerkungen     : document.getElementById('m_bemerkungen')?.value   ?? '',
                [getCsrfName()] : getCsrfValue(),
            });

            try {
                const res = await fetch('<?= base_url('adressen/schnell') ?>', {
                    method  : 'POST',
                    headers : { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body    : payload.toString(),
                });

                // [4] Token nach Response aktualisieren
                updateCsrfFromResponse(res);

                const data = await res.json();

                if (! res.ok) {
                    alertBox.textContent = data.error ?? 'Unbekannter Fehler.';
                    alertBox.classList.remove('d-none');
                    return;
                }

                eigentuemerHidden.value  = data.id;
                eigentuemerSearch.value  = data.anzeigename;

                bootstrap.Modal.getInstance(modal)?.hide();

            } catch (e) {
                alertBox.textContent = 'Netzwerkfehler. Bitte erneut versuchen.';
                alertBox.classList.remove('d-none');
            } finally {
                btnSpeichern.disabled = false;
                btnSpeichern.innerHTML = '<i class="bi bi-check-lg me-1"></i> Anlegen &amp; übernehmen';
            }
        });
    }

}());
</script>

<?php
// [7] Google Maps Script nur einbinden, wenn API-Key konfiguriert ist.
//     Fehlendes Key → kein Script-Tag, kein JS-Fehler in der Konsole.
$apiKey = env('google.maps.api_key', '');
?>
<?php if ($apiKey): ?>
<script
    src="https://maps.googleapis.com/maps/api/js?key=<?= esc($apiKey) ?>&libraries=places&callback=initGooglePlaces&loading=async"
    async defer>
</script>
<?php else: ?>
<script>
// Google Maps API-Key nicht konfiguriert (google.maps.api_key in .env fehlt).
// Adresssuche und Kartenvorschau sind deaktiviert.
console.warn('Google Maps API-Key nicht konfiguriert. Adresssuche deaktiviert.');
</script>
<?php endif; ?>

<?= $this->endSection() ?>