<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php $isAktiv = $vertrag['status'] === 'aktiv'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= base_url('mietvertraege') ?>">Mietverträge</a></li>
            <li class="breadcrumb-item active"><?= esc($vertrag['vertragsnummer'] ?? "Vertrag #{$vertrag['id']}") ?></li>
        </ol>
    </nav>
    <div class="d-flex gap-2">
        <a href="<?= base_url("zahlungen/neu?mietvertrag_id={$vertrag['id']}") ?>" class="btn btn-success btn-sm">
            <i class="bi bi-cash-coin me-1"></i> Zahlung erfassen
        </a>
        <?php if ($isAktiv): ?>
        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#kuendigenModal">
            <i class="bi bi-x-circle me-1"></i> Kündigen
        </button>
        <?php endif; ?>
        <a href="<?= base_url("mietvertraege/{$vertrag['id']}/bearbeiten") ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-pencil"></i>
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <!-- Vertragsdaten -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom-0 pt-3">
                <h6 class="fw-semibold mb-0"><i class="bi bi-file-earmark-text-fill text-primary me-2"></i>Vertragsdaten</h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted">Vertragsnr.</dt>
                    <dd class="col-7"><code><?= esc($vertrag['vertragsnummer'] ?? '–') ?></code></dd>
                    <dt class="col-5 text-muted">Status</dt>
                    <dd class="col-7"><span class="badge badge-status-<?= $vertrag['status'] ?>"><?= ucfirst($vertrag['status']) ?></span></dd>
                    <dt class="col-5 text-muted">Objekt</dt>
                    <dd class="col-7"><?= esc($vertrag['objekt_bezeichnung']) ?></dd>
                    <dt class="col-5 text-muted">Einheit</dt>
                    <dd class="col-7"><?= esc($vertrag['einheit_bezeichnung']) ?></dd>
                    <dt class="col-5 text-muted">Beginn</dt>
                    <dd class="col-7"><?= date('d.m.Y', strtotime($vertrag['beginn_datum'])) ?></dd>
                    <dt class="col-5 text-muted">Ende</dt>
                    <dd class="col-7"><?= $vertrag['ende_datum'] ? date('d.m.Y', strtotime($vertrag['ende_datum'])) : '<em class="text-muted">unbefristet</em>' ?></dd>
                    <dt class="col-5 text-muted">Zahlungstag</dt>
                    <dd class="col-7"><?= $vertrag['zahlungstag'] ?>. des Monats</dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- Mieter & Finanzen -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom-0 pt-3">
                <h6 class="fw-semibold mb-0"><i class="bi bi-person-fill text-success me-2"></i>Mieter & Finanzen</h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted">Name</dt>
                    <dd class="col-7"><?= esc($vertrag['mieter_name']) ?><?= $vertrag['mieter_vorname'] ? ', ' . esc($vertrag['mieter_vorname']) : '' ?></dd>
                    <?php if ($vertrag['mieter_email']): ?>
                    <dt class="col-5 text-muted">E-Mail</dt>
                    <dd class="col-7"><a href="mailto:<?= esc($vertrag['mieter_email']) ?>"><?= esc($vertrag['mieter_email']) ?></a></dd>
                    <?php endif; ?>
                    <?php if ($vertrag['mieter_telefon']): ?>
                    <dt class="col-5 text-muted">Telefon</dt>
                    <dd class="col-7"><?= esc($vertrag['mieter_telefon']) ?></dd>
                    <?php endif; ?>
                    <dt class="col-5 text-muted">Kaltmiete</dt>
                    <dd class="col-7 fw-semibold"><?= number_format($vertrag['kaltmiete'], 2, ',', '.') ?> €</dd>
                    <dt class="col-5 text-muted">Nebenkosten</dt>
                    <dd class="col-7"><?= number_format($vertrag['nebenkosten'], 2, ',', '.') ?> €</dd>
                    <dt class="col-5 text-muted">Warmmiete</dt>
                    <dd class="col-7 fw-bold text-primary"><?= number_format($vertrag['kaltmiete'] + $vertrag['nebenkosten'], 2, ',', '.') ?> €</dd>
                    <dt class="col-5 text-muted">Kaution</dt>
                    <dd class="col-7"><?= number_format($vertrag['kaution'], 2, ',', '.') ?> €</dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<!-- Zahlungsstatistik -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card text-center">
            <div class="value text-success"><?= number_format($vertrag['gesamt_bezahlt'], 2, ',', '.') ?> €</div>
            <div class="label">Gesamt bezahlt</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card text-center">
            <div class="value <?= $vertrag['gesamt_offen'] > 0 ? 'text-danger' : 'text-muted' ?>">
                <?= number_format($vertrag['gesamt_offen'], 2, ',', '.') ?> €
            </div>
            <div class="label">Offen / Überfällig</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card text-center">
            <div class="value"><?= count($vertrag['zahlungen']) ?></div>
            <div class="label">Zahlungspositionen gesamt</div>
        </div>
    </div>
</div>

<!-- Zahlungsliste -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-semibold mb-0"><i class="bi bi-cash-coin text-success me-2"></i>Zahlungshistorie</h6>
        <a href="<?= base_url("zahlungen/neu?mietvertrag_id={$vertrag['id']}") ?>" class="btn btn-sm btn-success">
            <i class="bi bi-plus"></i> Zahlung erfassen
        </a>
    </div>
    <?php if (empty($vertrag['zahlungen'])): ?>
        <div class="card-body text-muted">Noch keine Zahlungen erfasst.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>Datum</th><th>Typ</th><th>Referenz</th>
                        <th class="text-end">Betrag</th><th>Fällig</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                    <?php foreach ($vertrag['zahlungen'] as $z): ?>
                    <tr>
                        <td><?= date('d.m.Y', strtotime($z['datum'])) ?></td>
                        <td class="text-capitalize"><?= esc($z['typ']) ?></td>
                        <td class="text-muted small"><?= esc($z['referenz'] ?? '–') ?></td>
                        <td class="text-end fw-semibold"><?= number_format($z['betrag'], 2, ',', '.') ?> €</td>
                        <td><?= $z['faellig_datum'] ? date('d.m.Y', strtotime($z['faellig_datum'])) : '–' ?></td>
                        <td><span class="badge badge-status-<?= $z['status'] ?>"><?= ucfirst($z['status']) ?></span></td>
                        <td class="d-flex gap-1">
                            <?php if (in_array($z['status'], ['offen', 'ueberfaellig'])): ?>
                            <form method="post" action="<?= base_url("zahlungen/{$z['id']}/bezahlt") ?>">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-success" title="Als bezahlt markieren">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                            <a href="<?= base_url("zahlungen/{$z['id']}/bearbeiten") ?>" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Kündigen Modal -->
<?php if ($isAktiv): ?>
<div class="modal fade" id="kuendigenModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mietvertrag kündigen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="<?= base_url("mietvertraege/{$vertrag['id']}/kuendigen") ?>">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p>Bitte das Enddatum der Kündigung angeben. Die Einheit wird danach automatisch als <em>verfügbar</em> markiert.</p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Vertragsende</label>
                        <input type="date" name="ende_datum" class="form-control"
                               value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Abbrechen</button>
                    <button type="submit" class="btn btn-warning">Kündigung bestätigen</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
