<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between mb-4">
    <div></div>
    <a href="<?= base_url('zahlungen/neu') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Zahlung erfassen
    </a>
</div>

<!-- Jahresstatistik-Kacheln -->
<?php
$jahresEinnahmen = array_sum(array_column($jahresstatistik, 'gesamt'));
$jahresMiete     = array_sum(array_column($jahresstatistik, 'miete'));
?>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="value text-primary"><?= number_format($jahresEinnahmen, 2, ',', '.') ?> €</div>
            <div class="label">Einnahmen <?= date('Y') ?> gesamt</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="value text-success"><?= number_format($jahresMiete, 2, ',', '.') ?> €</div>
            <div class="label">davon Kaltmiete</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="value"><?= count($zahlungen) ?></div>
            <div class="label">Positionen angezeigt</div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Datum</th>
                    <th>Mieter</th>
                    <th>Objekt / Einheit</th>
                    <th>Typ</th>
                    <th>Zahlungsart</th>
                    <th>Referenz</th>
                    <th class="text-end">Betrag</th>
                    <th>Fällig</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($zahlungen)): ?>
                <tr><td colspan="10" class="text-center text-muted py-4">Keine Zahlungen vorhanden.</td></tr>
                <?php else: ?>
                <?php foreach ($zahlungen as $z): ?>
                <tr>
                    <td><?= date('d.m.Y', strtotime($z['datum'])) ?></td>
                    <td><?= esc($z['mieter_name']) ?></td>
                    <td>
                        <div><?= esc($z['objekt_bezeichnung'] ?? '–') ?></div>
                        <small class="text-muted"><?= esc($z['einheit_bezeichnung'] ?? '') ?></small>
                    </td>
                    <td class="text-capitalize"><?= esc($z['typ']) ?></td>
                    <td class="text-capitalize text-muted small"><?= esc($z['zahlungsart']) ?></td>
                    <td class="text-muted small"><?= esc($z['referenz'] ?? '–') ?></td>
                    <td class="text-end fw-semibold"><?= number_format($z['betrag'], 2, ',', '.') ?> €</td>
                    <td>
                        <?php if ($z['faellig_datum']): ?>
                            <?php $isDue = $z['status'] === 'offen' && $z['faellig_datum'] < date('Y-m-d') ?>
                            <span class="<?= $isDue ? 'text-danger fw-semibold' : '' ?>">
                                <?= date('d.m.Y', strtotime($z['faellig_datum'])) ?>
                            </span>
                        <?php else: ?>–<?php endif; ?>
                    </td>
                    <td><span class="badge badge-status-<?= $z['status'] ?>"><?= ucfirst($z['status']) ?></span></td>
                    <td class="d-flex gap-1">
                        <?php if (in_array($z['status'], ['offen', 'ueberfaellig'])): ?>
                        <form method="post" action="<?= base_url("zahlungen/{$z['id']}/bezahlt") ?>">
                            <?= csrf_field() ?>
                            <button class="btn btn-sm btn-success" title="Bezahlt"><i class="bi bi-check-lg"></i></button>
                        </form>
                        <?php endif; ?>
                        <a href="<?= base_url("zahlungen/{$z['id']}/bearbeiten") ?>" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
