<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php $isEdit = !empty($zahlung['id']); ?>

<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= base_url('zahlungen') ?>">Zahlungen</a></li>
            <li class="breadcrumb-item active"><?= $isEdit ? 'Bearbeiten' : 'Neue Zahlung' ?></li>
        </ol>
    </nav>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="post"
                      action="<?= $isEdit ? base_url("zahlungen/{$zahlung['id']}") : base_url('zahlungen') ?>">
                    <?= csrf_field() ?>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Mietvertrag *</label>
                            <select name="mietvertrag_id" class="form-select" required>
                                <option value="">— Vertrag wählen —</option>
                                <?php foreach ($vertraege as $v): ?>
                                <option value="<?= $v['id'] ?>"
                                    <?= old('mietvertrag_id', $zahlung['mietvertrag_id'] ?? '') == $v['id'] ? 'selected' : '' ?>>
                                    <?= esc($v['mieter_name']) ?> –
                                    <?= esc($v['objekt_bezeichnung']) ?> /
                                    <?= esc($v['einheit_bezeichnung']) ?>
                                    (<?= esc($v['vertragsnummer'] ?? '#' . $v['id']) ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Betrag (€) *</label>
                            <div class="input-group">
                                <input type="number" name="betrag" class="form-control"
                                       step="0.01" min="0"
                                       value="<?= esc(old('betrag', $zahlung['betrag'] ?? '')) ?>" required>
                                <span class="input-group-text">€</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Buchungsdatum *</label>
                            <input type="date" name="datum" class="form-control"
                                   value="<?= esc(old('datum', $zahlung['datum'] ?? date('Y-m-d'))) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fälligkeitsdatum</label>
                            <input type="date" name="faellig_datum" class="form-control"
                                   value="<?= esc(old('faellig_datum', $zahlung['faellig_datum'] ?? '')) ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Typ *</label>
                            <select name="typ" class="form-select">
                                <?php foreach (['miete' => 'Miete', 'nebenkosten' => 'Nebenkosten', 'kaution' => 'Kaution', 'sonstige' => 'Sonstige'] as $v => $l): ?>
                                <option value="<?= $v ?>" <?= old('typ', $zahlung['typ'] ?? 'miete') === $v ? 'selected' : '' ?>><?= $l ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Zahlungsart</label>
                            <select name="zahlungsart" class="form-select">
                                <?php foreach (['ueberweisung' => 'Überweisung', 'lastschrift' => 'Lastschrift', 'bar' => 'Bar', 'sonstige' => 'Sonstige'] as $v => $l): ?>
                                <option value="<?= $v ?>" <?= old('zahlungsart', $zahlung['zahlungsart'] ?? 'ueberweisung') === $v ? 'selected' : '' ?>><?= $l ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <?php foreach (['offen' => 'Offen', 'bezahlt' => 'Bezahlt', 'teilbezahlt' => 'Teilbezahlt', 'storniert' => 'Storniert'] as $v => $l): ?>
                                <option value="<?= $v ?>" <?= old('status', $zahlung['status'] ?? 'bezahlt') === $v ? 'selected' : '' ?>><?= $l ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Referenz / Verwendungszweck</label>
                            <input type="text" name="referenz" class="form-control"
                                   placeholder="z.B. Miete März 2025"
                                   value="<?= esc(old('referenz', $zahlung['referenz'] ?? '')) ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Notizen</label>
                            <textarea name="notizen" class="form-control" rows="2"><?= esc(old('notizen', $zahlung['notizen'] ?? '')) ?></textarea>
                        </div>
                    </div>

                    <hr class="my-4">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url('zahlungen') ?>" class="btn btn-outline-secondary">Abbrechen</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>
                            <?= $isEdit ? 'Aktualisieren' : 'Zahlung erfassen' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
