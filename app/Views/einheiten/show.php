<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php $e = $einheit; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= base_url('objekte') ?>">Objekte</a></li>
            <li class="breadcrumb-item">
                <a href="<?= base_url("objekte/{$e['objekt_id']}") ?>">
                    <?= esc($e['objekt_bezeichnung']) ?>
                </a>
            </li>
            <li class="breadcrumb-item"><a href="<?= base_url('einheiten') ?>">Einheiten</a></li>
            <li class="breadcrumb-item active"><?= esc($e['bezeichnung']) ?></li>
        </ol>
    </nav>
    <div class="d-flex gap-2">
        <?php if ($e['status'] === 'verfuegbar'): ?>
            <a href="<?= base_url("mietvertraege/neu?einheit_id={$e['id']}") ?>"
               class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-plus me-1"></i> Mietvertrag anlegen
            </a>
        <?php endif; ?>
        <a href="<?= base_url("eingangsrechnungen/neu?einheit_id={$e['id']}") ?>"
           class="btn btn-outline-warning btn-sm">
            <i class="bi bi-receipt me-1"></i> Rechnung zuweisen
        </a>
        <a href="<?= base_url("einheiten/{$e['id']}/bearbeiten") ?>"
           class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-pencil me-1"></i> Bearbeiten
        </a>
    </div>
</div>

<!-- Header -->
<div class="card  mb-4">
    <div class="card-body d-flex align-items-center gap-4 py-4">
        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
             style="width:64px;height:64px;font-size:1.75rem;
                    background:<?= $e['status'] === 'vermietet' ? '#dbeafe' : ($e['status'] === 'verfuegbar' ? '#d1fae5' : '#fee2e2') ?>;
                    color:<?= $e['status'] === 'vermietet' ? '#1e40af' : ($e['status'] === 'verfuegbar' ? '#065f46' : '#991b1b') ?>">
            <i class="bi bi-<?= $e['typ'] === 'stellplatz' ? 'p-square' : ($e['typ'] === 'gewerbe' ? 'building' : 'door-open') ?>-fill"></i>
        </div>
        <div class="flex-grow-1">
            <div class="d-flex align-items-center gap-2 mb-1">
                <h4 class="fw-bold mb-0"><?= esc($e['bezeichnung']) ?></h4>
                <span class="badge badge-status-<?= $e['status'] ?>"><?= ucfirst($e['status']) ?></span>
            </div>
            <div class="text-muted">
                <span class="text-capitalize me-3"><?= esc($e['typ']) ?></span>
                <?php if ($e['etage'] !== null): ?>
                    <span class="me-3">
                        <i class="bi bi-layers me-1"></i>
                        <?= $e['etage'] == 0 ? 'Erdgeschoss' : $e['etage'] . '. Obergeschoss' ?>
                    </span>
                <?php endif; ?>
                <?php if ($e['flaeche']): ?>
                    <span class="me-3">
                        <i class="bi bi-rulers me-1"></i>
                        <?= number_format($e['flaeche'], 2, ',', '.') ?> m²
                    </span>
                <?php endif; ?>
                <?php if ($e['zimmer']): ?>
                    <span>
                        <i class="bi bi-grid me-1"></i>
                        <?= number_format($e['zimmer'], 1, ',', '.') ?> Zimmer
                    </span>
                <?php endif; ?>
            </div>
            <div class="text-muted small mt-1">
                <a href="<?= base_url("objekte/{$e['objekt_id']}") ?>" class="text-decoration-none text-muted">
                    <i class="bi bi-building me-1"></i><?= esc($e['objekt_bezeichnung']) ?>
                    <?php if (!empty($e['strasse'])): ?>
                        · <?= esc($e['strasse']) ?> <?= esc($e['hsnr'] ?? '') ?>,
                        <?= esc($e['ort'] ?? '') ?>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">

    <!-- Aktiver Mietvertrag / Leerstand -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom-0 pt-3">
                <h6 class="fw-semibold mb-0">
                    <i class="bi bi-file-earmark-text-fill text-primary me-2"></i>
                    Aktuelles Mietverhältnis
                </h6>
            </div>
            <div class="card-body">
                <?php
                $aktiverVertrag = null;
                foreach ($vertraege as $v) {
                    if ($v['status'] === 'aktiv') { $aktiverVertrag = $v; break; }
                }
                ?>
                <?php if ($aktiverVertrag): ?>
                    <dl class="row mb-3">
                        <dt class="col-5 text-muted">Mieter</dt>
                        <dd class="col-7 fw-semibold">
                            <?= esc($aktiverVertrag['mieter_name']) ?>
                            <?= $aktiverVertrag['mieter_vorname'] ? ', ' . esc($aktiverVertrag['mieter_vorname']) : '' ?>
                        </dd>
                        <?php if ($aktiverVertrag['mieter_email']): ?>
                        <dt class="col-5 text-muted">E-Mail</dt>
                        <dd class="col-7">
                            <a href="mailto:<?= esc($aktiverVertrag['mieter_email']) ?>">
                                <?= esc($aktiverVertrag['mieter_email']) ?>
                            </a>
                        </dd>
                        <?php endif; ?>
                        <?php if ($aktiverVertrag['mieter_telefon']): ?>
                        <dt class="col-5 text-muted">Telefon</dt>
                        <dd class="col-7"><?= esc($aktiverVertrag['mieter_telefon']) ?></dd>
                        <?php endif; ?>
                        <dt class="col-5 text-muted">Vertragsnr.</dt>
                        <dd class="col-7"><code><?= esc($aktiverVertrag['vertragsnummer'] ?? '–') ?></code></dd>
                        <dt class="col-5 text-muted">Beginn</dt>
                        <dd class="col-7"><?= date('d.m.Y', strtotime($aktiverVertrag['beginn_datum'])) ?></dd>
                        <dt class="col-5 text-muted">Ende</dt>
                        <dd class="col-7">
                            <?= $aktiverVertrag['ende_datum']
                                ? date('d.m.Y', strtotime($aktiverVertrag['ende_datum']))
                                : '<em class="text-muted">unbefristet</em>' ?>
                        </dd>
                        <dt class="col-5 text-muted">Kaltmiete</dt>
                        <dd class="col-7"><?= number_format($aktiverVertrag['kaltmiete'], 2, ',', '.') ?> €</dd>
                        <dt class="col-5 text-muted">Nebenkosten</dt>
                        <dd class="col-7"><?= number_format($aktiverVertrag['nebenkosten'], 2, ',', '.') ?> €</dd>
                        <dt class="col-5 text-muted">Warmmiete</dt>
                        <dd class="col-7 fw-bold text-primary">
                            <?= number_format($aktiverVertrag['kaltmiete'] + $aktiverVertrag['nebenkosten'], 2, ',', '.') ?> €
                        </dd>
                    </dl>
                    <a href="<?= base_url("mietvertraege/{$aktiverVertrag['id']}") ?>"
                       class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye me-1"></i> Zum Mietvertrag
                    </a>
                <?php else: ?>
                    <div class="text-center py-3">
                        <i class="bi bi-door-open display-6 text-muted d-block mb-2"></i>
                        <p class="text-muted mb-3">Einheit ist aktuell nicht vermietet.</p>
                        <a href="<?= base_url("mietvertraege/neu?einheit_id={$e['id']}") ?>"
                           class="btn btn-success btn-sm">
                            <i class="bi bi-plus me-1"></i> Mietvertrag anlegen
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Beschreibung + Stammdaten -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom-0 pt-3">
                <h6 class="fw-semibold mb-0">
                    <i class="bi bi-info-circle-fill text-info me-2"></i>
                    Stammdaten
                </h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted">Typ</dt>
                    <dd class="col-7 text-capitalize"><?= esc($e['typ']) ?></dd>

                    <dt class="col-5 text-muted">Etage</dt>
                    <dd class="col-7">
                        <?php if ($e['etage'] !== null): ?>
                            <?= $e['etage'] == 0 ? 'Erdgeschoss' : ($e['etage'] < 0 ? 'Untergeschoss ' . abs($e['etage']) : $e['etage'] . '. Obergeschoss') ?>
                        <?php else: ?>
                            <span class="text-muted">–</span>
                        <?php endif; ?>
                    </dd>
                    <dt class="col-5 text-muted">Geschoss</dt>
<dd class="col-7"><?= esc($e['geschoss_bezeichnung'] ?? '–') ?></dd>

<dt class="col-5 text-muted">Lage</dt>
<dd class="col-7"><?= esc($e['lage_bezeichnung'] ?? '–') ?></dd>
                    <dt class="col-5 text-muted">Wohnfläche</dt>
                    <dd class="col-7">
                        <?= $e['flaeche'] ? number_format($e['flaeche'], 2, ',', '.') . ' m²' : '–' ?>
                    </dd>

                    <dt class="col-5 text-muted">Zimmer</dt>
                    <dd class="col-7">
                        <?= $e['zimmer'] ? number_format($e['zimmer'], 1, ',', '.') : '–' ?>
                    </dd>

                    <dt class="col-5 text-muted">Status</dt>
                    <dd class="col-7">
                        <span class="badge badge-status-<?= $e['status'] ?>"><?= ucfirst($e['status']) ?></span>
                    </dd>
                </dl>
                <?php if ($e['beschreibung']): ?>
                    <hr>
                    <p class="text-muted mb-0 small"><?= nl2br(esc($e['beschreibung'])) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Vertragshistorie -->
    <?php if (count($vertraege) > 1 || ($vertraege && ! $aktiverVertrag)): ?>
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-semibold mb-0">
                    <i class="bi bi-clock-history text-secondary me-2"></i>
                    Vertragshistorie (<?= count($vertraege) ?>)
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mieter</th>
                            <th>Vertragsnr.</th>
                            <th>Beginn</th>
                            <th>Ende</th>
                            <th class="text-end">Kaltmiete</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($vertraege as $v): ?>
                    <tr>
                        <td class="fw-medium">
                            <?= esc($v['mieter_name']) ?>
                            <?= $v['mieter_vorname'] ? ', ' . esc($v['mieter_vorname']) : '' ?>
                        </td>
                        <td><code><?= esc($v['vertragsnummer'] ?? '–') ?></code></td>
                        <td><?= date('d.m.Y', strtotime($v['beginn_datum'])) ?></td>
                        <td>
                            <?= $v['ende_datum']
                                ? date('d.m.Y', strtotime($v['ende_datum']))
                                : '<em class="text-muted">unbefristet</em>' ?>
                        </td>
                        <td class="text-end"><?= number_format($v['kaltmiete'], 2, ',', '.') ?> €</td>
                        <td>
                            <span class="badge badge-status-<?= $v['status'] ?>">
                                <?= ucfirst($v['status']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= base_url("mietvertraege/{$v['id']}") ?>"
                               class="btn btn-sm btn-outline-primary">Details</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<?= $this->endSection() ?>