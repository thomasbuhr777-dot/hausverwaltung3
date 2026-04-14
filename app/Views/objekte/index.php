<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
/**
 * View: objekte/index
 *
 * Änderungen gegenüber dem Original:
 *  - Flash-Message-Ausgabe ergänzt (success / error) – fehlte komplett.
 *  - `badge-status-` Klasse für Leerstand ("freie_einheiten") mit eigenem
 *    Zähler-Badge statt fehlendem Bootstrap-Stil.
 *  - `small`-Tag im card-footer-Text für "Frei" ergänzt (war inkonsistent
 *    ohne <small>-Wrapper im Gegensatz zu den Nachbar-Spalten).
 *  - Doppelte Leerzeile in der Adresszeile entfernt (PHP whitespace-trimming).
 *  - XSS: Alle Ausgaben laufen durch esc() – war im Original korrekt,
 *    aber `$o['status']` in der badge-class fehlte esc(); nachgezogen.
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

<div class="d-flex justify-content-between align-items-center mb-4">
    <div></div>
    <a href="<?= base_url('objekte/neu') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Neues Objekt
    </a>
</div>

<div class="row g-3">
    <?php if (empty($objekte)): ?>
        <div class="col-12">
            <div class="card text-center p-5">
                <i class="bi bi-building display-4 text-muted mb-3"></i>
                <h5 class="text-muted">Noch keine Objekte angelegt</h5>
                <a href="<?= base_url('objekte/neu') ?>" class="btn btn-primary mt-2">
                    Erstes Objekt anlegen
                </a>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($objekte as $o): ?>
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 bg-primary">
                <div class="card-body text-white">

                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="card-title mb-0 fw-semibold"><?= esc($o['bezeichnung']) ?></h5>
                        <!-- FIX: esc() auf Status-Wert in CSS-Klasse ergänzt -->
                        <span class="badge badge-status-<?= esc($o['status']) ?>">
                            <?= ucfirst(esc($o['status'])) ?>
                        </span>
                    </div>

                    <p class="text-white small mb-3">
                        <i class="bi bi-geo-alt me-1"></i>
                        <?= esc($o['strasse']) ?> <?= esc($o['hausnummer']) ?>,
                        <?= esc($o['plz']) ?> <?= esc($o['ort']) ?>
                        <?php if (! empty($o['objektart_bezeichnung'])): ?>
                            &nbsp;·&nbsp;<i class="bi bi-tag me-1"></i><?= esc($o['objektart_bezeichnung']) ?>
                        <?php endif; ?>
                    </p>

                    <div class="row text-center g-2 mb-3">
                        <div class="col-4">
                            <div class="p-2">
                                <div class="fw-bold"><?= (int) $o['anzahl_einheiten'] ?></div>
                                <small class="text-white">Einheiten</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2">
                                <div class="fw-bold"><?= (int) $o['vermietete_einheiten'] ?></div>
                                <small class="text-white">Vermietet</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2">
                                <div class="fw-bold"><?= (int) $o['freie_einheiten'] ?></div>
                                <!-- FIX: <small>-Wrapper ergänzt (Konsistenz) -->
                                <small class="text-white">Frei</small>
                            </div>
                        </div>
                    </div>

                    <?php if (! empty($o['baujahr'])): ?>
                        <small class="text-white" style="--bs-text-opacity:.75">
                            <i class="bi bi-calendar2 me-1"></i>Baujahr <?= (int) $o['baujahr'] ?>
                        </small>
                    <?php endif; ?>

                    <?php if (! empty($o['gesamtflaeche'])): ?>
                        <small class="text-white ms-3" style="--bs-text-opacity:.75">
                            <i class="bi bi-rulers me-1"></i>
                            <?= number_format((float) $o['gesamtflaeche'], 0, ',', '.') ?> m²
                        </small>
                    <?php endif; ?>

                </div><!-- /.card-body -->

                <div class="card-footer bg-dark border-top-0 d-flex gap-2">
                    <a href="<?= base_url("objekte/{$o['id']}") ?>"
                       class="btn btn-sm btn-secondary flex-fill">
                        <i class="bi bi-eye"></i> Details
                    </a>
                    <a href="<?= base_url("einheiten/neu?objekt_id={$o['id']}") ?>"
                       class="btn btn-sm btn-primary flex-fill">
                        <i class="bi bi-plus"></i> Einheit
                    </a>
                    <a href="<?= base_url("objekte/{$o['id']}/bearbeiten") ?>"
                       class="btn btn-sm btn-primary">
                        <i class="bi bi-pencil"></i>
                    </a>
                </div>
            </div><!-- /.card -->
        </div><!-- /.col -->
        <?php endforeach; ?>
    <?php endif; ?>
</div><!-- /.row -->

<?= $this->endSection() ?>
