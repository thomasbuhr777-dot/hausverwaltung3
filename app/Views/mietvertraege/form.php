<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php $isEdit = !empty($vertrag['id']); ?>

<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= base_url('mietvertraege') ?>">Mietverträge</a></li>
            <li class="breadcrumb-item active"><?= $isEdit ? 'Bearbeiten' : 'Neu anlegen' ?></li>
        </ol>
    </nav>
</div>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="post" action="<?= $isEdit ? base_url("mietvertraege/{$vertrag['id']}") : base_url('mietvertraege') ?>">
                    <?= csrf_field() ?>

                    <!-- Einheit -->
                    <h6 class="fw-bold text-muted mb-3 text-uppercase" style="font-size:.75rem;letter-spacing:.08em">Mietobjekt</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Einheit *</label>
                            <select name="einheit_id" class="form-select" required>
                                <option value="">— Einheit wählen —</option>
                                <?php foreach ($einheiten as $e): ?>
                                <option value="<?= $e['id'] ?>"
                                    <?= old('einheit_id', $vertrag['einheit_id'] ?? '') == $e['id'] ? 'selected' : '' ?>>
                                    <?= esc($e['bezeichnung']) ?> (<?= esc($e['typ']) ?>)
                                    <?= $e['status'] !== 'verfuegbar' ? ' [' . ucfirst($e['status']) . ']' : '' ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Nur verfügbare Einheiten werden empfohlen.</div>
                        </div>
                    </div>

                    <!-- Mieter -->
                    <h6 class="fw-bold text-muted mb-3 text-uppercase" style="font-size:.75rem;letter-spacing:.08em">Mieterdaten</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nachname *</label>
                            <input type="text" name="mieter_name" class="form-control"
                                   value="<?= esc(old('mieter_name', $vertrag['mieter_name'] ?? '')) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Vorname</label>
                            <input type="text" name="mieter_vorname" class="form-control"
                                   value="<?= esc(old('mieter_vorname', $vertrag['mieter_vorname'] ?? '')) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">E-Mail</label>
                            <input type="email" name="mieter_email" class="form-control"
                                   value="<?= esc(old('mieter_email', $vertrag['mieter_email'] ?? '')) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Telefon</label>
                            <input type="text" name="mieter_telefon" class="form-control"
                                   value="<?= esc(old('mieter_telefon', $vertrag['mieter_telefon'] ?? '')) ?>">
                        </div>
                    </div>

                    <!-- Vertragslaufzeit -->
                    <h6 class="fw-bold text-muted mb-3 text-uppercase" style="font-size:.75rem;letter-spacing:.08em">Laufzeit</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Beginn *</label>
                            <input type="date" name="beginn_datum" class="form-control"
                                   value="<?= esc(old('beginn_datum', $vertrag['beginn_datum'] ?? date('Y-m-01'))) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Ende <small class="text-muted">(leer = unbefristet)</small></label>
                            <input type="date" name="ende_datum" class="form-control"
                                   value="<?= esc(old('ende_datum', $vertrag['ende_datum'] ?? '')) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Zahlungstag (des Monats)</label>
                            <input type="number" name="zahlungstag" class="form-control"
                                   min="1" max="28"
                                   value="<?= esc(old('zahlungstag', $vertrag['zahlungstag'] ?? 1)) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <?php foreach (['aktiv' => 'Aktiv', 'beendet' => 'Beendet', 'gekuendigt' => 'Gekündigt'] as $val => $lbl): ?>
                                <option value="<?= $val ?>" <?= old('status', $vertrag['status'] ?? 'aktiv') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Finanzen -->
                    <h6 class="fw-bold text-muted mb-3 text-uppercase" style="font-size:.75rem;letter-spacing:.08em">Finanzen</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kaltmiete (€) *</label>
                            <div class="input-group">
                                <input type="number" name="kaltmiete" class="form-control"
                                       step="0.01" min="0"
                                       value="<?= esc(old('kaltmiete', $vertrag['kaltmiete'] ?? '')) ?>" required>
                                <span class="input-group-text">€</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nebenkosten (€)</label>
                            <div class="input-group">
                                <input type="number" name="nebenkosten" class="form-control"
                                       step="0.01" min="0"
                                       value="<?= esc(old('nebenkosten', $vertrag['nebenkosten'] ?? '0')) ?>">
                                <span class="input-group-text">€</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kaution (€)</label>
                            <div class="input-group">
                                <input type="number" name="kaution" class="form-control"
                                       step="0.01" min="0"
                                       value="<?= esc(old('kaution', $vertrag['kaution'] ?? '0')) ?>">
                                <span class="input-group-text">€</span>
                            </div>
                        </div>
                    </div>

                    <?php if (! $isEdit): ?>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="zahlungen_erstellen" value="1" id="zahlungenCheck" checked>
                        <label class="form-check-label" for="zahlungenCheck">
                            Zahlungspositionen für die ersten 12 Monate automatisch erstellen
                        </label>
                    </div>
                    <?php endif; ?>

                    <!-- Notizen -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Notizen</label>
                        <textarea name="notizen" class="form-control" rows="2"><?= esc(old('notizen', $vertrag['notizen'] ?? '')) ?></textarea>
                    </div>

                    <hr class="my-3">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url('mietvertraege') ?>" class="btn btn-outline-secondary">Abbrechen</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>
                            <?= $isEdit ? 'Änderungen speichern' : 'Mietvertrag anlegen' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
