<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$isEdit = !empty($einheit['id']);
$errors = session('errors') ?? [];
?>

<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= base_url('einheiten') ?>">Einheiten</a></li>
            <li class="breadcrumb-item active"><?= $isEdit ? 'Bearbeiten' : 'Neue Einheit' ?></li>
        </ol>
    </nav>
</div>

<div class="row justify-content-center">
    <div class="col-lg-12">
        <div class="card border-0 rounded-0">
            <div class="card-body p-4">

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($errors as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post"
                      action="<?= $isEdit ? base_url("einheiten/{$einheit['id']}") : base_url('einheiten') ?>">
                    <?= csrf_field() ?>

                    <div class="row g-3">

                        <!-- Objekt -->
                        <div class="col-8">
                            <label class="form-label fw-semibold">Objekt *</label>
                            <select name="objekt_id" class="form-select" required>
                                <option value="">— Objekt wählen —</option>
                                <?php foreach ($objekte as $o): ?>
                                    <option value="<?= $o['id'] ?>"
                                        <?= (int) old('objekt_id', $einheit['objekt_id'] ?? 0) === (int) $o['id'] ? 'selected' : '' ?>>
                                        <?= esc($o['bezeichnung']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Einheitenart (ehem. Typ) -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Einheitenart *</label>
                            <select name="einheitenart_id"
                                    class="form-select<?= isset($errors['einheitenart_id']) ? ' is-invalid' : '' ?>"
                                    required>
                                <option value="">— Einheitenart wählen —</option>
                                <?php foreach ($einheitenarten as $artId => $artBez): ?>
                                    <option value="<?= $artId ?>"
                                        <?= (int) old('einheitenart_id', $einheit['einheitenart_id'] ?? 0) === (int) $artId ? 'selected' : '' ?>>
                                        <?= esc($artBez) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Etage -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Etage *</label>
                            <input type="number"
                                   name="etage"
                                   class="form-control<?= isset($errors['etage']) ? ' is-invalid' : '' ?>"
                                   min="-2" max="20"
                                   placeholder="-2 bis max. 20"
                                   value="<?= esc(old('etage', $einheit['etage'] ?? '')) ?>"
                                   required>
                        </div>

                        <!-- Lage -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Lage *</label>
                            <select name="einheitenlage_id"
                                    class="form-select<?= isset($errors['einheitenlage_id']) ? ' is-invalid' : '' ?>"
                                    required>
                                <option value="">— wählen —</option>
                                <?php foreach ($lagen as $l): ?>
                                    <option value="<?= $l['id'] ?>"
                                        <?= (int) old('einheitenlage_id', $einheit['einheitenlage_id'] ?? 0) === (int) $l['id'] ? 'selected' : '' ?>>
                                        <?= esc($l['bezeichnung']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Bezeichnung -->
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Bezeichnung *</label>
                            <input type="text"
                                   name="bezeichnung"
                                   class="form-control<?= isset($errors['bezeichnung']) ? ' is-invalid' : '' ?>"
                                   placeholder="z.B. Büro 2.OG"
                                   value="<?= esc(old('bezeichnung', $einheit['bezeichnung'] ?? '')) ?>"
                                   required>
                        </div>

                        <!-- Fläche -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Fläche (m²)</label>
                            <div class="input-group">
                                <input type="number"
                                       name="flaeche"
                                       class="form-control"
                                       step="0.01" min="0"
                                       value="<?= esc(old('flaeche', $einheit['flaeche'] ?? '')) ?>">
                                <span class="input-group-text">m²</span>
                            </div>
                        </div>

                        <!-- Zimmer -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Zimmeranzahl</label>
                            <input type="number"
                                   name="zimmer"
                                   class="form-control"
                                   step="0.5" min="0"
                                   value="<?= esc(old('zimmer', $einheit['zimmer'] ?? '')) ?>">
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <?php foreach ([
                                    'verfuegbar' => 'Verfügbar',
                                    'vermietet'  => 'Vermietet',
                                    'gesperrt'   => 'Gesperrt'
                                ] as $v => $l): ?>
                                    <option value="<?= $v ?>"
                                        <?= old('status', $einheit['status'] ?? 'verfuegbar') === $v ? 'selected' : '' ?>>
                                        <?= $l ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Beschreibung -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Beschreibung</label>
                            <textarea name="beschreibung"
                                      class="form-control"
                                      rows="2"><?= esc(old('beschreibung', $einheit['beschreibung'] ?? '')) ?></textarea>
                        </div>

                    </div>

                    <!-- ============================================================
                         Ausstattungsmerkmale / Tags
                    ============================================================ -->
                    <hr class="my-4">
                    <h6 class="fw-semibold mb-3">
                        <i class="bi bi-tags me-2 text-primary"></i>Ausstattungsmerkmale
                    </h6>

                    <?php
                    // Beim Zurückspringen nach Validierungsfehler alte POST-Werte nutzen
                    $oldTags = old('ausstattungsmerkmale', $selected_tags ?? []);
                    $oldTags = array_map('intval', (array) $oldTags);
                    ?>

                    <?php if (!empty($merkmal_gruppen)): ?>
                        <div class="row g-3">
                            <?php foreach ($merkmal_gruppen as $kategorie => $merkmale): ?>
                                <div class="col-12 col-lg-6 col-xl-4">
                                    <div class="card h-100 border shadow-none">
                                        <div class="card-header py-2 bg-light">
                                            <span class="fw-semibold small"><?= esc($kategorie) ?></span>
                                        </div>
                                        <div class="card-body py-2">
                                            <div class="d-flex flex-wrap gap-2">
                                                <?php foreach ($merkmale as $merkmal): ?>
                                                    <?php $mid = (int) $merkmal['id']; ?>
                                                    <input
                                                        type="checkbox"
                                                        class="btn-check"
                                                        name="ausstattungsmerkmale[]"
                                                        value="<?= $mid ?>"
                                                        id="tag_<?= $mid ?>"
                                                        autocomplete="off"
                                                        <?= in_array($mid, $oldTags, true) ? 'checked' : '' ?>
                                                    >
                                                    <label
                                                        class="btn btn-outline-secondary btn-sm rounded-pill"
                                                        for="tag_<?= $mid ?>"
                                                    >
                                                        <?php if (!empty($merkmal['icon'])): ?>
                                                            <i class="<?= esc($merkmal['icon']) ?> me-1"></i>
                                                        <?php endif; ?>
                                                        <?= esc($merkmal['bezeichnung']) ?>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted small">Keine Ausstattungsmerkmale vorhanden. Bitte zuerst Merkmale anlegen.</p>
                    <?php endif; ?>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url('einheiten') ?>" class="btn btn-outline-secondary">Abbrechen</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>
                            <?= $isEdit ? 'Änderungen speichern' : 'Einheit anlegen' ?>
                        </button>
                    </div>
                </form>

                <?php if ($isEdit): ?>
                    <hr>
                    <form method="post"
                          action="<?= base_url("einheiten/{$einheit['id']}/loeschen") ?>"
                          onsubmit="return confirm('Einheit wirklich löschen?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash me-1"></i> Einheit löschen
                        </button>
                    </form>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
