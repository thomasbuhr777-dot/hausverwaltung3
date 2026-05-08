<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php

$categoryIcons = [
    'Küche'                     => 'bi bi-cup-hot',
    'Bad / Sanitär'             => 'bi bi-droplet-half',
    'Bodenbeläge'               => 'bi bi-grid-3x3-gap',
    'Außenbereiche'             => 'bi bi-tree',
    'Heizung / Klima'           => 'bi bi-thermometer-half',
    'Fenster / Sonnenschutz'    => 'bi bi-window',
    'Sicherheit / Zugang'       => 'bi bi-shield-lock',
    'Medien / Kommunikation'    => 'bi bi-wifi',
    'Gebäude / Allgemein'       => 'bi bi-building',
    'Parken'                    => 'bi bi-car-front',
    'Energie / Nachhaltigkeit'  => 'bi bi-sun',
    'Luxus / Besonderheiten'    => 'bi bi-stars',
    'Sonstiges'                 => 'bi bi-box-seam',
];
?>

<div class="container py-4">

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1">
                <?= esc($title ?? 'Ausstattungsmerkmale') ?>
            </h1>

            <p class="text-body-secondary mb-0">
                Übersicht der verfügbaren Wohnungsmerkmale nach Kategorien.
            </p>
        </div>
    </div>

    <?php if (empty($gruppen)): ?>

        <div class="alert alert-info">
            Es sind noch keine Ausstattungsmerkmale vorhanden.
        </div>

    <?php else: ?>

<form method="post" action="">
    <div class="row g-4">
        <?php foreach ($gruppen as $kategorie => $merkmale): ?>
            <div class="col-12 col-lg-6 col-xl-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header">
                        <h2 class="h6 mb-0 d-flex align-items-center gap-2">
                            <?php if (isset($categoryIcons[$kategorie])): ?>
                                <i class="<?= esc($categoryIcons[$kategorie]) ?>"></i>
                            <?php endif; ?>
                            <span>
                                <?= esc($kategorie) ?>
                            </span>
                        </h2>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($merkmale as $merkmal): ?>
                                <?php
                                $id = 'merkmal_' . $merkmal['id'];
                                ?>
                                <input
                                    type="checkbox"
                                    class="btn-check"
                                    name="ausstattungsmerkmale[]"
                                    value="<?= esc($merkmal['id']) ?>"
                                    id="<?= esc($id) ?>"
                                    autocomplete="off"
                                >
                                <label
                                    class="btn btn-outline-secondary btn-sm rounded-pill"
                                    for="<?= esc($id) ?>"
                                >
                                    <?php if (! empty($merkmal['icon'])): ?>
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
</form>

    <?php endif; ?>

</div>

<?= $this->endSection() ?>