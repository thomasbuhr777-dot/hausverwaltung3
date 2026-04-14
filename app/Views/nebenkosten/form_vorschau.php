<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= base_url('nebenkosten') ?>">Nebenkostenabrechnungen</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('nebenkosten/neu') ?>">Neu</a></li>
            <li class="breadcrumb-item active">Vorschau &amp; Prüfung</li>
        </ol>
    </nav>
</div>

<!-- Schritt-Anzeige -->
<div class="d-flex align-items-center gap-3 mb-4">
    <div class="d-flex align-items-center gap-2 text-muted">
        <div class="rounded-circle d-flex align-items-center justify-content-center border fw-bold"
             style="width:32px;height:32px;font-size:.875rem">1</div>
        <span>Objekt &amp; Zeitraum</span>
    </div>
    <div style="flex:1;height:2px;background:var(--bs-primary)"></div>
    <div class="d-flex align-items-center gap-2">
        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
             style="width:32px;height:32px;background:var(--bs-primary);color:#fff;font-size:.875rem">2</div>
        <span class="fw-semibold">Positionen &amp; Einheiten</span>
    </div>
    <div style="flex:1;height:2px;background:var(--bs-border-color)"></div>
    <div class="d-flex align-items-center gap-2 text-muted">
        <div class="rounded-circle d-flex align-items-center justify-content-center border fw-bold"
             style="width:32px;height:32px;font-size:.875rem">3</div>
        <span>Speichern &amp; Berechnen</span>
    </div>
</div>

<div class="alert alert-info d-flex gap-2">
    <i class="bi bi-info-circle-fill mt-1"></i>
    <div>
        Vorschlag aus <strong>Eingangsrechnungen</strong> und <strong>Mietverträgen</strong> des Jahres
        <strong><?= $jahr ?></strong>. Bitte prüfen und bei Bedarf anpassen.
    </div>
</div>

<form method="post" action="<?= base_url('nebenkosten') ?>" id="abrechnungForm">
    <?= csrf_field() ?>
    <input type="hidden" name="objekt_id"    value="<?= $objekt['id'] ?>">
    <input type="hidden" name="jahr"         value="<?= $jahr ?>">
    <input type="hidden" name="zeitraum_von" value="<?= $von ?>">
    <input type="hidden" name="zeitraum_bis" value="<?= $bis ?>">

    <div class="row g-4">

        <!-- ---- Kopfdaten ---- -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-semibold mb-0">
                        <i class="bi bi-building text-primary me-2"></i>
                        <?= esc($objekt['bezeichnung']) ?> – Abrechnung <?= $jahr ?>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Bezeichnung *</label>
                            <input type="text" name="bezeichnung" class="form-control" required
                                   value="Nebenkostenabrechnung <?= $jahr ?> – <?= esc($objekt['bezeichnung']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Zeitraum</label>
                            <input type="text" class="form-control bg-light" readonly
                                   value="<?= date('d.m.Y', strtotime($von)) ?> – <?= date('d.m.Y', strtotime($bis)) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notizen</label>
                            <textarea name="notizen" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ---- Kostenpositionen ---- -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-semibold mb-0">
                        <i class="bi bi-receipt text-warning me-2"></i>
                        Kostenpositionen
                    </h6>
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="positionHinzufuegen()">
                        <i class="bi bi-plus"></i> Position
                    </button>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0" id="positionenTable">
                        <thead class="table-light">
                            <tr>
                                <th>Bezeichnung</th>
                                <th style="width:110px">Kategorie</th>
                                <th style="width:120px">Betrag</th>
                                <th style="width:110px">Schlüssel</th>
                                <th style="width:36px"></th>
                            </tr>
                        </thead>
                        <tbody id="positionenBody">
                        <?php foreach ($positionen as $idx => $p): ?>
                        <tr>
                            <td>
                                <input type="text" name="positionen[<?= $idx ?>][bezeichnung]"
                                       class="form-control form-control-sm"
                                       value="<?= esc($p['bezeichnung']) ?>" required>
                                <input type="hidden" name="positionen[<?= $idx ?>][kategorie]"
                                       value="<?= esc($p['kategorie']) ?>">
                                <input type="hidden" name="positionen[<?= $idx ?>][sortierung]"
                                       value="<?= $idx ?>">
                            </td>
                            <td>
                                <select name="positionen[<?= $idx ?>][kategorie]"
                                        class="form-select form-select-sm">
                                    <?php foreach (['heizung','warmwasser','wasser_abwasser','muell',
                                                   'hausmeister','versicherung','strom_allgemein',
                                                   'reinigung','aufzug','gartenpflege','verwaltung','sonstige'] as $k): ?>
                                    <option value="<?= $k ?>" <?= $p['kategorie'] === $k ? 'selected' : '' ?>>
                                        <?= ucfirst(str_replace('_', ' ', $k)) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="positionen[<?= $idx ?>][gesamtbetrag]"
                                           class="form-control form-control-sm pos-betrag"
                                           step="0.01" min="0"
                                           value="<?= number_format($p['gesamtbetrag'], 2, '.', '') ?>"
                                           oninput="updateSumme()">
                                    <span class="input-group-text">€</span>
                                </div>
                            </td>
                            <td>
                                <select name="positionen[<?= $idx ?>][verteilerschluessel]"
                                        class="form-select form-select-sm">
                                    <?php foreach (['wohnflaeche' => 'Fläche', 'personenanzahl' => 'Personen',
                                                   'gleich' => 'Gleich', 'verbrauch' => 'Verbrauch'] as $v => $l): ?>
                                    <option value="<?= $v ?>" <?= $p['verteilerschluessel'] === $v ? 'selected' : '' ?>>
                                        <?= $l ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-danger p-1"
                                        onclick="this.closest('tr').remove(); updateSumme()">
                                    <i class="bi bi-trash" style="font-size:.75rem"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="2" class="fw-semibold text-end pe-3">Gesamt</td>
                                <td><span id="summeAnzeige" class="fw-bold">–</span></td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                    <?php if (empty($positionen)): ?>
                    <p class="text-muted p-3 mb-0 small">
                        <i class="bi bi-info-circle me-1"></i>
                        Keine Eingangsrechnungen im Zeitraum gefunden. Bitte Positionen manuell erfassen.
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ---- Einheiten ---- -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-semibold mb-0">
                        <i class="bi bi-door-open-fill text-success me-2"></i>
                        Einheiten &amp; Vorauszahlungen
                    </h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Einheit / Mieter</th>
                                <th style="width:80px">m²</th>
                                <th style="width:60px">Pers.</th>
                                <th style="width:110px">Voraus.</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($einheiten as $idx => $e): ?>
                        <tr>
                            <td>
                                <div class="fw-medium small"><?= esc($e['bezeichnung']) ?></div>
                                <div class="text-muted" style="font-size:.75rem">
                                    <?= esc($e['mieter_name'] ?? '–') ?>
                                </div>
                                <input type="hidden" name="einheiten[<?= $idx ?>][einheit_id]"
                                       value="<?= $e['einheit_id'] ?>">
                                <input type="hidden" name="einheiten[<?= $idx ?>][mietvertrag_id]"
                                       value="<?= $e['mietvertrag_id'] ?? '' ?>">
                                <input type="hidden" name="einheiten[<?= $idx ?>][mieter_name]"
                                       value="<?= esc($e['mieter_name'] ?? '') ?>">
                            </td>
                            <td>
                                <input type="number" name="einheiten[<?= $idx ?>][wohnflaeche]"
                                       class="form-control form-control-sm"
                                       step="0.01" min="0"
                                       value="<?= number_format($e['wohnflaeche'], 2, '.', '') ?>">
                            </td>
                            <td>
                                <input type="number" name="einheiten[<?= $idx ?>][personenanzahl]"
                                       class="form-control form-control-sm"
                                       min="1" max="20"
                                       value="<?= (int) ($e['personenanzahl'] ?? 1) ?>">
                            </td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="einheiten[<?= $idx ?>][vorauszahlungen_gesamt]"
                                           class="form-control form-control-sm"
                                           step="0.01" min="0"
                                           value="<?= number_format($e['vorauszahlungen_gesamt'], 2, '.', '') ?>">
                                    <span class="input-group-text">€</span>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php if (empty($einheiten)): ?>
                    <p class="text-muted p-3 mb-0 small">
                        <i class="bi bi-exclamation-triangle me-1 text-warning"></i>
                        Keine aktiven Mietverträge im Zeitraum gefunden.
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div><!-- /.row -->

    <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="<?= base_url('nebenkosten/neu') ?>" class="btn btn-outline-secondary">Zurück</a>
        <button type="submit" class="btn btn-primary" <?= empty($einheiten) ? 'disabled' : '' ?>>
            <i class="bi bi-check-lg me-1"></i> Abrechnung anlegen
        </button>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let posIdx = <?= count($positionen) ?>;

const kategorien = ['heizung','warmwasser','wasser_abwasser','muell','hausmeister',
                    'versicherung','strom_allgemein','reinigung','aufzug','gartenpflege',
                    'verwaltung','sonstige'];
const schluessel = {wohnflaeche:'Fläche', personenanzahl:'Personen', gleich:'Gleich', verbrauch:'Verbrauch'};

function positionHinzufuegen() {
    const tbody = document.getElementById('positionenBody');
    const i = posIdx++;
    const katOpts = kategorien.map(k =>
        `<option value="${k}">${k.replace('_',' ').replace(/^\w/,c=>c.toUpperCase())}</option>`
    ).join('');
    const schlOpts = Object.entries(schluessel).map(([v,l]) =>
        `<option value="${v}">${l}</option>`
    ).join('');
    tbody.insertAdjacentHTML('beforeend', `
        <tr>
            <td><input type="text" name="positionen[${i}][bezeichnung]"
                       class="form-control form-control-sm" placeholder="Bezeichnung" required>
                <input type="hidden" name="positionen[${i}][sortierung]" value="${i}">
            </td>
            <td><select name="positionen[${i}][kategorie]" class="form-select form-select-sm">${katOpts}</select></td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" name="positionen[${i}][gesamtbetrag]"
                           class="form-control form-control-sm pos-betrag"
                           step="0.01" min="0" value="0.00" oninput="updateSumme()">
                    <span class="input-group-text">€</span>
                </div>
            </td>
            <td><select name="positionen[${i}][verteilerschluessel]" class="form-select form-select-sm">${schlOpts}</select></td>
            <td><button type="button" class="btn btn-sm btn-outline-danger p-1"
                        onclick="this.closest('tr').remove(); updateSumme()">
                    <i class="bi bi-trash" style="font-size:.75rem"></i>
                </button></td>
        </tr>`);
    updateSumme();
}

function updateSumme() {
    let summe = 0;
    document.querySelectorAll('.pos-betrag').forEach(el => {
        summe += parseFloat(el.value) || 0;
    });
    document.getElementById('summeAnzeige').textContent =
        summe.toLocaleString('de-DE', {minimumFractionDigits:2, maximumFractionDigits:2}) + ' €';
}
updateSumme();
</script>
<?= $this->endSection() ?>
