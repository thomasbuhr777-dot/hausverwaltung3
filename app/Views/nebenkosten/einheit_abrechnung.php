<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php $d = $daten; $saldo = (float) $d['saldo']; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= base_url('nebenkosten') ?>">Nebenkostenabrechnungen</a></li>
            <li class="breadcrumb-item">
                <a href="<?= base_url("nebenkosten/{$d['abrechnung_id']}") ?>">
                    <?= esc($d['abrechnung_bezeichnung']) ?>
                </a>
            </li>
            <li class="breadcrumb-item active"><?= esc($d['mieter_name']) ?></li>
        </ol>
    </nav>
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-printer me-1"></i> Drucken / PDF
    </button>
</div>

<!-- Briefkopf -->
<div class="card border-0 shadow-sm mb-4" id="abrechnungsblatt">
    <div class="card-body p-4">

        <!-- Absender / Empfänger -->
        <div class="row mb-4">
            <div class="col-6">
                <div class="text-muted small mb-1">Absender (Verwalter)</div>
                <div class="fw-semibold"><?= esc($d['objekt_bezeichnung']) ?></div>
                <div><?= esc($d['strasse']) ?> <?= esc($d['hausnummer']) ?></div>
                <div><?= esc($d['plz']) ?> <?= esc($d['ort']) ?></div>
            </div>
            <div class="col-6 text-end">
                <div class="text-muted small mb-1">Mieter</div>
                <div class="fw-semibold"><?= esc($d['mieter_name']) ?></div>
                <div><?= esc($d['einheit_bezeichnung']) ?></div>
            </div>
        </div>

        <hr>

        <h5 class="fw-bold mb-1">Nebenkostenabrechnung <?= $d['jahr'] ?></h5>
        <p class="text-muted mb-4">
            Abrechnungszeitraum:
            <?= date('d.m.Y', strtotime($d['zeitraum_von'])) ?> –
            <?= date('d.m.Y', strtotime($d['zeitraum_bis'])) ?>
        </p>

        <!-- Einheit-Parameter -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="bg-light rounded p-3 text-center">
                    <div class="fw-bold fs-5"><?= number_format($d['wohnflaeche'], 2, ',', '.') ?> m²</div>
                    <div class="text-muted small">Wohnfläche</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-light rounded p-3 text-center">
                    <div class="fw-bold fs-5"><?= $d['personenanzahl'] ?></div>
                    <div class="text-muted small">Personen</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-light rounded p-3 text-center">
                    <div class="fw-bold fs-5">
                        <?= number_format($d['vorauszahlungen_gesamt'], 2, ',', '.') ?> €
                    </div>
                    <div class="text-muted small">Geleistete Vorauszahlungen</div>
                </div>
            </div>
        </div>

        <!-- Positionstabelle -->
        <table class="table table-bordered table-sm mb-4">
            <thead class="table-light">
                <tr>
                    <th>Kostenart</th>
                    <th class="text-end">Gesamtkosten</th>
                    <th>Verteilung</th>
                    <th>Ihr Anteil</th>
                    <th class="text-end">Ihr Betrag</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($d['positionen'] as $p): ?>
            <tr>
                <td>
                    <div class="fw-medium"><?= esc($p['bezeichnung']) ?></div>
                    <small class="text-muted"><?= esc($p['kategorie']) ?></small>
                </td>
                <td class="text-end"><?= number_format($p['gesamtbetrag'], 2, ',', '.') ?> €</td>
                <td class="text-muted small">
                    <?= match($p['verteilerschluessel']) {
                        'wohnflaeche'   => 'nach Fläche',
                        'personenanzahl'=> 'nach Personen',
                        'gleich'        => 'gleich',
                        'verbrauch'     => 'nach Verbrauch',
                        default         => $p['verteilerschluessel'],
                    } ?>
                </td>
                <td class="text-muted small"><?= esc($p['berechnungsgrundlage'] ?? '–') ?></td>
                <td class="text-end fw-semibold"><?= number_format($p['anteil_betrag'], 2, ',', '.') ?> €</td>
            </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot class="table-light">
                <tr class="fw-bold">
                    <td colspan="4" class="text-end">Ihre Gesamtkosten</td>
                    <td class="text-end"><?= number_format($d['kosten_gesamt'], 2, ',', '.') ?> €</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-end text-muted">./. Vorauszahlungen</td>
                    <td class="text-end text-muted">– <?= number_format($d['vorauszahlungen_gesamt'], 2, ',', '.') ?> €</td>
                </tr>
            </tfoot>
        </table>

        <!-- Ergebnis -->
        <div class="p-4 rounded text-center <?= $saldo > 0 ? 'bg-danger bg-opacity-10 border border-danger' : 'bg-success bg-opacity-10 border border-success' ?>">
            <?php if ($saldo > 0): ?>
                <div class="fw-bold fs-4 text-danger">
                    Nachzahlung: <?= number_format($saldo, 2, ',', '.') ?> €
                </div>
                <div class="text-muted mt-1 small">
                    Bitte überweisen Sie den Betrag innerhalb von 30 Tagen.
                </div>
            <?php elseif ($saldo < 0): ?>
                <div class="fw-bold fs-4 text-success">
                    Guthaben: <?= number_format(abs($saldo), 2, ',', '.') ?> €
                </div>
                <div class="text-muted mt-1 small">
                    Der Betrag wird mit Ihrer nächsten Nebenkostenvorauszahlung verrechnet.
                </div>
            <?php else: ?>
                <div class="fw-bold fs-4 text-muted">Ausgeglichen – keine Nachzahlung</div>
            <?php endif; ?>
        </div>

        <p class="text-muted small mt-4 mb-0">
            Erstellt am <?= date('d.m.Y') ?>.
            Diese Abrechnung wurde maschinell erstellt.
        </p>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<style>
@media print {
    .topbar, .sidebar, .breadcrumb, button, nav { display: none !important; }
    .main-content { margin-left: 0 !important; }
    #abrechnungsblatt { box-shadow: none !important; border: none !important; }
}
</style>
<?= $this->endSection() ?>
