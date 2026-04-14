<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php $isEdit = !empty($rechnung['id']); ?>
<?php
$kategorien = [
    'instandhaltung' => 'Instandhaltung',
    'renovierung'    => 'Renovierung',
    'verwaltung'     => 'Verwaltung',
    'versicherung'   => 'Versicherung',
    'nebenkosten'    => 'Nebenkosten',
    'strom'          => 'Strom',
    'wasser'         => 'Wasser',
    'heizung'        => 'Heizung',
    'reinigung'      => 'Reinigung',
    'sonstige'       => 'Sonstige',
];
?>

<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= base_url('eingangsrechnungen') ?>">Eingangsrechnungen</a></li>
            <li class="breadcrumb-item active"><?= $isEdit ? 'Bearbeiten' : 'Neue Rechnung' ?></li>
        </ol>
    </nav>
</div>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="post" enctype="multipart/form-data"
                      action="<?= $isEdit ? base_url("eingangsrechnungen/{$rechnung['id']}") : base_url('eingangsrechnungen') ?>">
                    <?= csrf_field() ?>

                    <!-- Zuweisung -->
                    <div class="alert alert-warning d-flex align-items-start mb-4">
                        <i class="bi bi-info-circle pe-2"></i>
                        <div>Bitte entweder ein <strong>Objekt</strong> (für objektweite Kosten) <em>oder</em> eine <strong>Einheit</strong> (für einheitsspezifische Kosten) zuweisen.</div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Objekt (gesamt)</label>
                            <select name="objekt_id" class="form-select" id="objektSelect">
                                <option value="">— kein Objekt —</option>
                                <?php foreach ($objekte as $o): ?>
                                <option value="<?= $o['id'] ?>"
                                    <?= old('objekt_id', $rechnung['objekt_id'] ?? '') == $o['id'] ? 'selected' : '' ?>>
                                    <?= esc($o['bezeichnung']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Einheit (spezifisch)</label>
                            <select name="einheit_id" class="form-select" id="einheitSelect">
                                <option value="">— keine Einheit —</option>
                                <?php foreach ($einheiten as $e): ?>
                                <option value="<?= $e['id'] ?>"
                                    data-objekt="<?= $e['objekt_id'] ?? '' ?>"
                                    <?= old('einheit_id', $rechnung['einheit_id'] ?? '') == $e['id'] ? 'selected' : '' ?>>
                                    <?= esc($e['objekt_bezeichnung'] ?? '') ?> – <?= esc($e['bezeichnung']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Rechnungsdaten -->
                    <h6 class="fw-bold text-muted mb-3 text-uppercase" style="font-size:.75rem;letter-spacing:.08em">Rechnungsdaten</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Rechnungsnummer *</label>
                            <input type="text" name="rechnungsnummer" class="form-control"
                                   value="<?= esc(old('rechnungsnummer', $rechnung['rechnungsnummer'] ?? '')) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Lieferant *</label>
                            <input type="text" name="lieferant" class="form-control"
                                   value="<?= esc(old('lieferant', $rechnung['lieferant'] ?? '')) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Lieferant Steuernummer</label>
                            <input type="text" name="lieferant_steuernummer" class="form-control"
                                   value="<?= esc(old('lieferant_steuernummer', $rechnung['lieferant_steuernummer'] ?? '')) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Rechnungsdatum *</label>
                            <input type="date" name="rechnungsdatum" class="form-control"
                                   value="<?= esc(old('rechnungsdatum', $rechnung['rechnungsdatum'] ?? date('Y-m-d'))) ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Fällig am</label>
                            <input type="date" name="faellig_datum" class="form-control"
                                   value="<?= esc(old('faellig_datum', $rechnung['faellig_datum'] ?? '')) ?>">
                        </div>
                    </div>

                    <!-- Beträge -->
                    <h6 class="fw-bold text-muted mb-3 text-uppercase" style="font-size:.75rem;letter-spacing:.08em">Beträge</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nettobetrag (€) *</label>
                            <div class="input-group">
                                <input type="number" name="nettobetrag" id="nettobetrag" class="form-control"
                                       step="0.01" min="0"
                                       value="<?= esc(old('nettobetrag', $rechnung['nettobetrag'] ?? '')) ?>" required>
                                <span class="input-group-text">€</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Steuersatz (%)</label>
                            <select name="steuersatz" id="steuersatz" class="form-select">
                                <?php foreach (['0.00' => '0 %', '7.00' => '7 %', '19.00' => '19 %'] as $v => $l): ?>
                                <option value="<?= $v ?>" <?= old('steuersatz', $rechnung['steuersatz'] ?? '19.00') == $v ? 'selected' : '' ?>><?= $l ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Brutto (berechnet)</label>
                            <div class="input-group">
                                <input type="number" name="bruttobetrag" id="bruttobetrag" class="form-control bg-light"
                                       step="0.01" readonly
                                       value="<?= esc(old('bruttobetrag', $rechnung['bruttobetrag'] ?? '')) ?>">
                                <span class="input-group-text">€</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kategorie *</label>
                            <select name="kategorie" class="form-select">
                                <?php foreach ($kategorien as $v => $l): ?>
                                <option value="<?= $v ?>" <?= old('kategorie', $rechnung['kategorie'] ?? 'sonstige') === $v ? 'selected' : '' ?>><?= $l ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <?php foreach (['offen' => 'Offen', 'bezahlt' => 'Bezahlt', 'storniert' => 'Storniert'] as $v => $l): ?>
                                <option value="<?= $v ?>" <?= old('status', $rechnung['status'] ?? 'offen') === $v ? 'selected' : '' ?>><?= $l ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Dokument & Beschreibung -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Beschreibung</label>
                            <textarea name="beschreibung" class="form-control" rows="2"><?= esc(old('beschreibung', $rechnung['beschreibung'] ?? '')) ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Rechnung hochladen (PDF)</label>
                            <input type="file" name="rechnung_datei" class="form-control" accept=".pdf,image/*">
                            <?php if (!empty($rechnung['datei_pfad'])): ?>
                                <div class="form-text">Aktuell: <?= esc(basename($rechnung['datei_pfad'])) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <hr class="my-3">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url('eingangsrechnungen') ?>" class="btn btn-outline-secondary">Abbrechen</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>
                            <?= $isEdit ? 'Änderungen speichern' : 'Rechnung speichern' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
// Brutto automatisch berechnen
function berechne() {
    const netto = parseFloat(document.getElementById('nettobetrag').value) || 0;
    const steuer = parseFloat(document.getElementById('steuersatz').value) || 0;
    const brutto = netto + (netto * steuer / 100);
    document.getElementById('bruttobetrag').value = brutto.toFixed(2);
}
document.getElementById('nettobetrag').addEventListener('input', berechne);
document.getElementById('steuersatz').addEventListener('change', berechne);
berechne();
</script>
<?= $this->endSection() ?>
