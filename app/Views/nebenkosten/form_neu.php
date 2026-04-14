<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= base_url('nebenkosten') ?>">Nebenkostenabrechnungen</a></li>
            <li class="breadcrumb-item active">Neu</li>
        </ol>
    </nav>
</div>

<!-- Schritt-Anzeige -->
<div class="d-flex align-items-center gap-3 mb-4">
    <div class="d-flex align-items-center gap-2">
        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
             style="width:32px;height:32px;background:var(--bs-primary);color:#fff;font-size:.875rem">1</div>
        <span class="fw-semibold">Objekt &amp; Zeitraum</span>
    </div>
    <div style="flex:1;height:2px;background:var(--bs-border-color)"></div>
    <div class="d-flex align-items-center gap-2 text-muted">
        <div class="rounded-circle d-flex align-items-center justify-content-center border fw-bold"
             style="width:32px;height:32px;font-size:.875rem">2</div>
        <span>Positionen &amp; Einheiten</span>
    </div>
    <div style="flex:1;height:2px;background:var(--bs-border-color)"></div>
    <div class="d-flex align-items-center gap-2 text-muted">
        <div class="rounded-circle d-flex align-items-center justify-content-center border fw-bold"
             style="width:32px;height:32px;font-size:.875rem">3</div>
        <span>Speichern &amp; Berechnen</span>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="get" action="<?= base_url('nebenkosten/vorschau') ?>">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Objekt *</label>
                            <select name="objekt_id" class="form-select" required>
                                <option value="">— bitte wählen —</option>
                                <?php foreach ($objekte as $o): ?>
                                <option value="<?= $o['id'] ?>"><?= esc($o['bezeichnung']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Abrechnungsjahr *</label>
                            <select name="jahr" class="form-select" required>
                                <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                                <option value="<?= $y ?>" <?= $y == date('Y') - 1 ? 'selected' : '' ?>>
                                    <?= $y ?>
                                </option>
                                <?php endfor; ?>
                            </select>
                            <div class="form-text">
                                Die Abrechnungsperiode läuft standardmäßig vom 01.01. bis 31.12.
                                des gewählten Jahres.
                            </div>
                        </div>
                    </div>
                    <hr class="my-4">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url('nebenkosten') ?>" class="btn btn-outline-secondary">Abbrechen</a>
                        <button type="submit" class="btn btn-primary">
                            Vorschau laden <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
