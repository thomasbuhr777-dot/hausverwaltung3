<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$a = $abrechnung;
$istBerechnet = in_array($a['status'], ['fertig', 'versendet', 'abgeschlossen']);
$badgeClass = match($a['status']) {
    'entwurf'      => 'bg-warning text-dark',
    'fertig'       => 'bg-info text-dark',
    'versendet'    => 'bg-primary',
    'abgeschlossen'=> 'bg-success',
    default        => 'bg-secondary',
};
?>

<div class="d-flex justify-content-between align-items-start mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= base_url('nebenkosten') ?>">Nebenkostenabrechnungen</a></li>
            <li class="breadcrumb-item active"><?= esc($a['bezeichnung']) ?></li>
        </ol>
    </nav>
    <div class="d-flex gap-2">
        <?php if ($a['status'] === 'entwurf' || $a['status'] === 'fertig'): ?>
        <form method="post" action="<?= base_url("nebenkosten/{$a['id']}/berechnen") ?>">
            <?= csrf_field() ?>
            <button class="btn btn-success btn-sm">
                <i class="bi bi-calculator me-1"></i>
                <?= $a['status'] === 'entwurf' ? 'Berechnung starten' : 'Neu berechnen' ?>
            </button>
        </form>
        <?php endif; ?>

        <!-- Status-Änderung -->
        <div class="dropdown">
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                <span class="badge <?= $badgeClass ?>"><?= ucfirst($a['status']) ?></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <?php foreach (['entwurf' => 'Entwurf', 'fertig' => 'Fertig', 'versendet' => 'Versendet', 'abgeschlossen' => 'Abgeschlossen'] as $s => $l): ?>
                <?php if ($s !== $a['status']): ?>
                <li>
                    <form method="post" action="<?= base_url("nebenkosten/{$a['id']}/status") ?>" class="d-inline">
                        <?= csrf_field() ?>
                        <input type="hidden" name="status" value="<?= $s ?>">
                        <button type="submit" class="dropdown-item"><?= $l ?></button>
                    </form>
                </li>
                <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>

        <form method="post" action="<?= base_url("nebenkosten/{$a['id']}/loeschen") ?>"
              onsubmit="return confirm('Abrechnung wirklich löschen?')">
            <?= csrf_field() ?>
            <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
        </form>
    </div>
</div>

<!-- Kopfinfo -->
<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-4 text-muted">Objekt</dt>
                    <dd class="col-8 fw-semibold">
                        <a href="<?= base_url("objekte/{$a['objekt_id']}") ?>">
                            <?= esc($a['objekt_bezeichnung']) ?>
                        </a>
                        <div class="text-muted small fw-normal">
                            <?= esc($a['strasse']) ?> <?= esc($a['hausnummer']) ?>,
                            <?= esc($a['plz']) ?> <?= esc($a['ort']) ?>
                        </div>
                    </dd>
                    <dt class="col-4 text-muted">Zeitraum</dt>
                    <dd class="col-8">
                        <?= date('d.m.Y', strtotime($a['zeitraum_von'])) ?> –
                        <?= date('d.m.Y', strtotime($a['zeitraum_bis'])) ?>
                    </dd>
                    <?php if ($a['notizen']): ?>
                    <dt class="col-4 text-muted">Notizen</dt>
                    <dd class="col-8"><?= nl2br(esc($a['notizen'])) ?></dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>
    </div>

    <!-- KPIs -->
    <div class="col-md-4">
        <div class="stat-card text-center mb-3">
            <div class="value text-primary">
                <?= number_format(array_sum(array_column($a['positionen'], 'gesamtbetrag')), 2, ',', '.') ?> €
            </div>
            <div class="label">Kosten gesamt</div>
        </div>
        <div class="stat-card text-center">
            <?php
            $saldoGesamt = array_sum(array_column($a['einheiten'], 'saldo'));
            ?>
            <div class="value <?= $saldoGesamt > 0 ? 'text-danger' : ($saldoGesamt < 0 ? 'text-success' : 'text-muted') ?>">
                <?= number_format(abs($saldoGesamt), 2, ',', '.') ?> €
            </div>
            <div class="label">
                <?= $saldoGesamt > 0 ? 'Offene Nachzahlungen' : ($saldoGesamt < 0 ? 'Gesamtguthaben' : 'Ausgeglichen') ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Kostenpositionen -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-semibold mb-0">
                    <i class="bi bi-receipt text-warning me-2"></i>
                    Kostenpositionen (<?= count($a['positionen']) ?>)
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Position</th>
                            <th>Schlüssel</th>
                            <th class="text-end">Betrag</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($a['positionen'] as $p): ?>
                    <tr>
                        <td>
                            <div class="fw-medium small"><?= esc($p['bezeichnung']) ?></div>
                            <span class="badge bg-light text-dark border" style="font-size:.65rem">
                                <?= esc($p['kategorie']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="text-muted small">
                                <?= match($p['verteilerschluessel']) {
                                    'wohnflaeche'   => 'Fläche',
                                    'personenanzahl'=> 'Personen',
                                    'gleich'        => 'Gleich',
                                    'verbrauch'     => 'Verbrauch',
                                    default         => $p['verteilerschluessel'],
                                } ?>
                            </span>
                        </td>
                        <td class="text-end fw-semibold">
                            <?= number_format($p['gesamtbetrag'], 2, ',', '.') ?> €
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="2">Gesamt</td>
                            <td class="text-end">
                                <?= number_format(array_sum(array_column($a['positionen'], 'gesamtbetrag')), 2, ',', '.') ?> €
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Einheiten-Ergebnisse -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-semibold mb-0">
                    <i class="bi bi-door-open-fill text-success me-2"></i>
                    Abrechnung je Einheit
                </h6>
            </div>
            <?php if (! $istBerechnet): ?>
            <div class="card-body text-muted text-center py-4">
                <i class="bi bi-calculator display-6 d-block mb-2 text-muted"></i>
                Noch nicht berechnet. Bitte „Berechnung starten" klicken.
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Einheit / Mieter</th>
                            <th class="text-end">m²</th>
                            <th class="text-end">Kosten</th>
                            <th class="text-end">Voraus.</th>
                            <th class="text-end fw-bold">Saldo</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($a['einheiten'] as $e): ?>
                    <?php $saldo = (float) ($e['saldo'] ?? 0); ?>
                    <tr>
                        <td>
                            <div class="fw-medium small"><?= esc($e['einheit_bezeichnung']) ?></div>
                            <div class="text-muted" style="font-size:.75rem"><?= esc($e['mieter_name']) ?></div>
                        </td>
                        <td class="text-end small"><?= number_format($e['wohnflaeche'], 2, ',', '.') ?></td>
                        <td class="text-end small">
                            <?= $e['kosten_gesamt'] !== null
                                ? number_format($e['kosten_gesamt'], 2, ',', '.') . ' €'
                                : '–' ?>
                        </td>
                        <td class="text-end small">
                            <?= number_format($e['vorauszahlungen_gesamt'], 2, ',', '.') ?> €
                        </td>
                        <td class="text-end fw-bold <?= $saldo > 0 ? 'text-danger' : ($saldo < 0 ? 'text-success' : '') ?>">
                            <?php if ($e['saldo'] !== null): ?>
                                <?= $saldo > 0 ? '+' : '' ?><?= number_format($saldo, 2, ',', '.') ?> €
                                <div style="font-size:.65rem" class="text-muted fw-normal">
                                    <?= $saldo > 0 ? 'Nachzahlung' : ($saldo < 0 ? 'Guthaben' : 'Ausgeglichen') ?>
                                </div>
                            <?php else: ?>
                                <span class="text-muted">–</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= base_url("nebenkosten/einheit/{$e['id']}") ?>"
                               class="btn btn-sm btn-outline-primary" title="Einzelabrechnung">
                                <i class="bi bi-file-earmark-text"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
