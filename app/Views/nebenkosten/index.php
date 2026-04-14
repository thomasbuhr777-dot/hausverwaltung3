<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
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
    <a href="<?= base_url('nebenkosten/neu') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Neue Abrechnung
    </a>
</div>

<?php if (empty($abrechnungen)): ?>
<div class="card border-0 shadow-sm text-center p-5">
    <i class="bi bi-calculator display-4 text-muted mb-3"></i>
    <h5 class="text-muted">Noch keine Nebenkostenabrechnungen</h5>
    <a href="<?= base_url('nebenkosten/neu') ?>" class="btn btn-primary mt-2">
        Erste Abrechnung erstellen
    </a>
</div>
<?php else: ?>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Bezeichnung</th>
                    <th>Objekt</th>
                    <th>Jahr</th>
                    <th>Zeitraum</th>
                    <th class="text-end">Kosten gesamt</th>
                    <th class="text-center">Einheiten</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($abrechnungen as $a): ?>
            <?php
            $badgeClass = match($a['status']) {
                'entwurf'      => 'bg-warning text-dark',
                'fertig'       => 'bg-info text-dark',
                'versendet'    => 'bg-primary',
                'abgeschlossen'=> 'bg-success',
                default        => 'bg-secondary',
            };
            ?>
            <tr>
                <td>
                    <a href="<?= base_url("nebenkosten/{$a['id']}") ?>"
                       class="fw-medium text-decoration-none text-dark">
                        <?= esc($a['bezeichnung']) ?>
                    </a>
                </td>
                <td><?= esc($a['objekt_bezeichnung']) ?></td>
                <td><?= $a['jahr'] ?></td>
                <td class="text-muted small">
                    <?= date('d.m.Y', strtotime($a['zeitraum_von'])) ?> –
                    <?= date('d.m.Y', strtotime($a['zeitraum_bis'])) ?>
                </td>
                <td class="text-end fw-semibold">
                    <?= $a['kosten_gesamt'] ? number_format($a['kosten_gesamt'], 2, ',', '.') . ' €' : '–' ?>
                </td>
                <td class="text-center"><?= $a['anzahl_einheiten'] ?></td>
                <td><span class="badge <?= $badgeClass ?>"><?= ucfirst($a['status']) ?></span></td>
                <td>
                    <a href="<?= base_url("nebenkosten/{$a['id']}") ?>"
                       class="btn btn-sm btn-outline-primary">Details</a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
