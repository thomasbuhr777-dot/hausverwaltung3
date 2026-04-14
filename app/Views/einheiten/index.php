<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between mb-4">
    <div class="d-flex gap-2 align-items-center">
        <select class="form-select form-select-sm" style="width:auto"
                onchange="window.location='?objekt_id='+this.value">
            <option value="">Alle Objekte</option>
            <?php foreach ($objekte as $o): ?>
            <option value="<?= $o['id'] ?>" <?= $filter_objekt_id == $o['id'] ? 'selected' : '' ?>>
                <?= esc($o['bezeichnung']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>
    <a href="<?= base_url('einheiten/neu') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Neue Einheit
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <!-- class="text-end" -->
                    <th>Objekt</th>
                    <th>Bezeichnung</th>
                    <th>Typ</th>
                    <th>Etage</th>
                    <th>Geschoss</th>
                    <th>Lage</th>
                    <th>Fläche</th>
                    <th>Zimmer</th>
                    <th>Mieter</th>
                    <th>Kaltmiete</th>
                    <th>Status</th>
                    <th>Geschoss</th>
                    <th>Lage</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($einheiten)): ?>
                <tr><td colspan="10" class="text-center text-muted py-4">Keine Einheiten vorhanden.</td></tr>
                <?php else: ?>
                <?php foreach ($einheiten as $e): ?>
                <tr>
                    <td>
                        <a href="<?= base_url("objekte/{$e['objekt_id']}") ?>" class="text-decoration-none">
                            <?= esc($e['objekt_bezeichnung']) ?>
                        </a>
                        <div class="text-muted small"><?= esc($e['ort'] ?? '') ?></div>
                    </td>
                    <td class="fw-medium"><?= esc($e['bezeichnung']) ?></td>
                    <td class="text-capitalize"><?= esc($e['typ']) ?></td>
                    <td><?= $e['etage'] !== null ? ($e['etage'] == 0 ? 'EG' : $e['etage'] . '.OG') : '–' ?></td>
                    <td><?= esc($e['geschoss_bezeichnung'] ?? '–') ?></td>
                    <td><?= esc($e['lage_bezeichnung'] ?? '–') ?></td>
                    <td><?= $e['flaeche'] ? number_format($e['flaeche'], 2, ',', '.') . ' m²' : '–' ?></td>
                    <td><?= $e['zimmer'] ?? '–' ?></td>
                    <td>
                        <?php if ($e['mieter_name']): ?>
                            <span class="fw-medium"><?= esc($e['mieter_name']) ?></span>
                            <?php if ($e['mietvertrag_id']): ?>
                                <br><a href="<?= base_url("mietvertraege/{$e['mietvertrag_id']}") ?>" class="text-muted small">
                                    seit <?= date('d.m.Y', strtotime($e['beginn_datum'])) ?>
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted">–</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <?= $e['kaltmiete'] ? number_format($e['kaltmiete'], 2, ',', '.') . ' €' : '–' ?>
                    </td>
                    <td><span class="badge badge-status-<?= $e['status'] ?>"><?= ucfirst($e['status']) ?></span></td>
                    <td>
                        <a href="<?= base_url("einheiten/{$e['id']}") ?>" class="btn btn-sm btn-outline-primary">Details</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
