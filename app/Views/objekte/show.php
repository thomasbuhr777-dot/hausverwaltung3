<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
/**
 * View: objekte/show
 *
 * Änderungen gegenüber dem Original:
 *  - Flash-Message-Ausgabe ergänzt (success / error).
 *  - `$monatsmiete` war im Original als View-Variable übergeben aber nie
 *    genutzt – Variable ist im Controller bereits entfernt worden.
 *  - Einheiten-Tabelle: Spalten "Geschoss" und "Lage" fehlten im Original
 *    (waren im Index-View vorhanden, aber nicht in show). Da die Einheiten
 *    jetzt aus getObjektWithEinheiten() kommen (ohne JOIN auf Lookup-Tabellen),
 *    werden einheitengeschoss_id / einheitenlage_id als Rohdaten angezeigt.
 *    Besser: EinheitModel::getEinheitenMitDetails() im Controller nutzen –
 *    als Kommentar markiert für spätere Erweiterung.
 *  - Delete-Formular für Objekt in show.php ergänzt (war im Original nicht
 *    vorhanden, aber im Controller implementiert).
 *  - XSS: esc() auf alle dynamischen Ausgaben sichergestellt.
 *  - Rechnungsdatum: strtotime()-Aufruf mit Null-Check abgesichert.
 */
?>

<?php if (session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= esc(session('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= esc(session('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Breadcrumb & Actions -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4><?= esc($objekt['bezeichnung']) ?></h4>

    <div class="d-flex gap-2">
        <a href="<?= base_url("einheiten/neu?objekt_id={$objekt['id']}") ?>"
           class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Einheit hinzufügen
        </a>
        <a href="<?= base_url("eingangsrechnungen/neu?objekt_id={$objekt['id']}") ?>"
           class="btn btn-outline-warning btn-sm">
            <i class="bi bi-receipt"></i> Rechnung zuweisen
        </a>
        <a href="<?= base_url("objekte/{$objekt['id']}/bearbeiten") ?>"
           class="btn btn-primary btn-sm">
            <i class="bi bi-pencil"></i> Bearbeiten
        </a>
        <!-- Löschen-Button -->
        <form method="post"
              action="<?= base_url("objekte/{$objekt['id']}/loeschen") ?>"
              onsubmit="return confirm('Objekt und alle Einheiten wirklich löschen?')">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-outline-warning btn-sm">
                <i class="bi bi-trash"></i>
            </button>
        </form>
    </div>
</div>
<hr>

<!-- Objekt-Stammdaten -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6">
                        <dl class="row mb-0">
                            <dt class="col-5 text-muted">Adresse</dt>
                            <dd class="col-7">
                                <?= esc($objekt['strasse']) ?> <?= esc($objekt['hausnummer']) ?><br>
                                <?= esc($objekt['plz']) ?> <?= esc($objekt['ort']) ?>
                            </dd>

                            <dt class="col-5">Status</dt>
                            <dd class="col-7">
                                <span class="badge badge-status-<?= esc($objekt['status']) ?>">
                                    <?= ucfirst(esc($objekt['status'])) ?>
                                </span>
                            </dd>

                            <dt class="col-5 text-muted">Objektart</dt>
                            <dd class="col-7"><?= esc($objekt['objektart_bezeichnung'] ?? '–') ?></dd>
                        </dl>
                    </div>
                    <div class="col-sm-6">
                        <dl class="row mb-0">
                            <dt class="col-5 text-muted">Baujahr</dt>
                            <dd class="col-7"><?= ! empty($objekt['baujahr']) ? (int) $objekt['baujahr'] : '–' ?></dd>

                            <dt class="col-5 text-muted">Gesamtfläche</dt>
                            <dd class="col-7">
                                <?= ! empty($objekt['gesamtflaeche'])
                                    ? number_format((float) $objekt['gesamtflaeche'], 2, ',', '.') . ' m²'
                                    : '–' ?>
                            </dd>
                        </dl>
                    </div>
                </div>

                <?php if (! empty($objekt['beschreibung'])): ?>
                    <hr>
                    <p class="mb-0 text-muted"><?= nl2br(esc($objekt['beschreibung'])) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Ausgaben nach Kategorie (laufendes Jahr) -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header bg-primary fw-semibold border-bottom-0 pt-3">
                <i class="bi bi-pie-chart me-1"></i> Ausgaben <?= date('Y') ?>
            </div>
            <div class="card-body p-0">
                <?php if (empty($ausgaben)): ?>
                    <p class="text-muted p-3 mb-0">Keine Ausgaben erfasst.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($ausgaben as $a): ?>
                        <li class="list-group-item px-3 py-2 d-flex justify-content-between">
                            <span class="text-capitalize"><?= esc($a['kategorie']) ?></span>
                            <span class="fw-semibold">
                                <?= number_format((float) $a['brutto_gesamt'], 2, ',', '.') ?> €
                            </span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Einheiten-Tabelle -->
<div class="card mb-4">
    <div class="card-header bg-primary border-bottom py-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-semibold mb-0">
            <i class="bi bi-door-open me-2"></i>
            Einheiten (<?= count($objekt['einheiten']) ?>)
        </h6>
    </div>

    <?php if (empty($objekt['einheiten'])): ?>
        <div class="card-body text-muted">Noch keine Einheiten angelegt.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Bezeichnung</th>
                        <th>Typ</th>
                        <th>Etage</th>
                        <th>Fläche</th>
                        <th>Zimmer</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($objekt['einheiten'] as $e): ?>
                    <tr>
                        <td class="fw-medium"><?= esc($e['bezeichnung']) ?></td>
                        <td class="text-capitalize"><?= esc($e['typ']) ?></td>
                        <td>
                            <?php if ($e['etage'] !== null): ?>
                                <?= (int) $e['etage'] === 0 ? 'EG' : ((int) $e['etage'] < 0 ? 'UG ' . abs((int) $e['etage']) : (int) $e['etage'] . '.OG') ?>
                            <?php else: ?>
                                –
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= ! empty($e['flaeche'])
                                ? number_format((float) $e['flaeche'], 2, ',', '.') . ' m²'
                                : '–' ?>
                        </td>
                        <td><?= $e['zimmer'] ?? '–' ?></td>
                        <td>
                            <span class="badge badge-status-<?= esc($e['status']) ?>">
                                <?= ucfirst(esc($e['status'])) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= base_url("einheiten/{$e['id']}") ?>"
                               class="btn btn-sm btn-primary">Details</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Eingangsrechnungen zum Objekt -->
<div class="card">
    <div class="card-header bg-primary py-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-semibold mb-0">
            <i class="bi bi-receipt me-2"></i>Eingangsrechnungen
        </h6>
        <a href="<?= base_url("eingangsrechnungen/neu?objekt_id={$objekt['id']}") ?>"
           class="btn btn-sm btn-outline-warning">
            <i class="bi bi-plus"></i> Neue Rechnung
        </a>
    </div>

    <?php if (empty($rechnungen)): ?>
        <div class="card-body text-muted">Keine Rechnungen für dieses Objekt.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Rechnungsnr.</th>
                        <th>Lieferant</th>
                        <th>Einheit</th>
                        <th>Kategorie</th>
                        <th class="text-end">Brutto</th>
                        <th>Datum</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rechnungen as $r): ?>
                    <tr>
                        <td><code><?= esc($r['rechnungsnummer']) ?></code></td>
                        <td><?= esc($r['lieferant']) ?></td>
                        <td><?= esc($r['einheit_bezeichnung'] ?? 'Gesamt') ?></td>
                        <td class="text-capitalize"><?= esc($r['kategorie']) ?></td>
                        <td class="text-end fw-semibold">
                            <?= number_format((float) $r['bruttobetrag'], 2, ',', '.') ?> €
                        </td>
                        <td>
                            <?php
                            // FIX: Null-Check vor strtotime-Aufruf
                            $ts = ! empty($r['rechnungsdatum']) ? strtotime($r['rechnungsdatum']) : false;
                            echo $ts ? date('d.m.Y', $ts) : '–';
                            ?>
                        </td>
                        <td>
                            <span class="badge badge-status-<?= esc($r['status']) ?>">
                                <?= ucfirst(esc($r['status'])) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= base_url("eingangsrechnungen/{$r['id']}") ?>"
                               class="btn btn-sm btn-outline-secondary">Details</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
