<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$isFirma = $adresse['kontakt_typ'] === 'firma';
$anzeigename = $isFirma
    ? $adresse['firmenname']
    : implode(' ', array_filter([
        $adresse['titel']    ?? '',
        $adresse['vorname']  ?? '',
        $adresse['nachname'] ?? '',
      ]));
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= base_url('adressen') ?>">Adressbuch</a></li>
            <li class="breadcrumb-item active"><?= esc($anzeigename) ?></li>
        </ol>
    </nav>
    <div class="d-flex gap-2">
        <a href="<?= base_url('adressen') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Zurück
        </a>
       <a href="<?= base_url('adressen') . '?edit=' . $adresse['id'] ?>"
   class="btn btn-outline-primary btn-sm">
    <i class="bi bi-pencil me-1"></i> Bearbeiten
</a>
        <form method="post" action="<?= base_url("adressen/{$adresse['id']}/loeschen") ?>"
              onsubmit="return confirm('Adresse wirklich löschen?')">
            <?= csrf_field() ?>
            <button class="btn btn-outline-danger btn-sm">
                <i class="bi bi-trash me-1"></i> Löschen
            </button>
        </form>
    </div>
</div>

<!-- Header-Karte -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body d-flex align-items-center gap-4 py-4">
        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
             style="width:72px;height:72px;font-size:2rem;
                    background:<?= $isFirma ? '#dbeafe' : '#d1fae5' ?>;
                    color:<?= $isFirma ? '#1e40af' : '#065f46' ?>">
            <i class="bi bi-<?= $isFirma ? 'building' : 'person' ?>-fill"></i>
        </div>
        <div>
            <h4 class="fw-bold mb-1"><?= esc($anzeigename) ?></h4>
            <?php if (! $isFirma && $adresse['anrede']): ?>
                <span class="text-muted"><?= esc($adresse['anrede']) ?></span>
            <?php endif; ?>
            <span class="badge <?= $isFirma ? 'bg-info text-dark' : 'bg-secondary' ?> ms-2">
                <?= $isFirma ? 'Firma' : 'Person' ?>
            </span>
            <?php if ($adresse['ort']): ?>
                <div class="text-muted mt-1 small">
                    <i class="bi bi-geo-alt me-1"></i>
                    <?= esc($adresse['plz'] ?? '') ?> <?= esc($adresse['ort']) ?>,
                    <?= esc($adresse['land'] ?? '') ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Kontaktdaten -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom-0 pt-3">
                <h6 class="fw-semibold mb-0">
                    <i class="bi bi-telephone-fill text-primary me-2"></i>Kontakt
                </h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <?php if ($adresse['email']): ?>
                    <dt class="col-4 text-muted">E-Mail</dt>
                    <dd class="col-8">
                        <a href="mailto:<?= esc($adresse['email']) ?>"><?= esc($adresse['email']) ?></a>
                    </dd>
                    <?php endif; ?>

                    <?php if ($adresse['telefon1']): ?>
                    <dt class="col-4 text-muted">Telefon 1</dt>
                    <dd class="col-8">
                        <a href="tel:<?= esc($adresse['telefon1']) ?>"><?= esc($adresse['telefon1']) ?></a>
                    </dd>
                    <?php endif; ?>

                    <?php if ($adresse['telefon2']): ?>
                    <dt class="col-4 text-muted">Telefon 2</dt>
                    <dd class="col-8">
                        <a href="tel:<?= esc($adresse['telefon2']) ?>"><?= esc($adresse['telefon2']) ?></a>
                    </dd>
                    <?php endif; ?>

                    <?php if ($isFirma && $adresse['umsatzsteuer_id']): ?>
                    <dt class="col-4 text-muted">USt-ID</dt>
                    <dd class="col-8"><code><?= esc($adresse['umsatzsteuer_id']) ?></code></dd>
                    <?php endif; ?>

                    <?php if (! $adresse['email'] && ! $adresse['telefon1']): ?>
                    <dd class="col-12 text-muted">Keine Kontaktdaten hinterlegt.</dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>
    </div>

    <!-- Adresse -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom-0 pt-3">
                <h6 class="fw-semibold mb-0">
                    <i class="bi bi-geo-alt-fill text-success me-2"></i>Adresse
                </h6>
            </div>
            <div class="card-body">
                <?php if ($adresse['strasse']): ?>
                <address class="mb-0">
                    <?= esc($adresse['strasse']) ?> <?= esc($adresse['hsnr'] ?? '') ?><br>
                    <?= esc($adresse['plz'] ?? '') ?> <?= esc($adresse['ort'] ?? '') ?><br>
                    <?= esc($adresse['land'] ?? '') ?>
                </address>
                <?php if ($adresse['lat'] && $adresse['lon']): ?>
                <a href="https://www.google.com/maps?q=<?= $adresse['lat'] ?>,<?= $adresse['lon'] ?>"
                   target="_blank" class="btn btn-sm btn-outline-secondary mt-3">
                    <i class="bi bi-map me-1"></i> Google Maps
                </a>
                <?php endif; ?>
                <?php else: ?>
                <p class="text-muted mb-0">Keine Adresse hinterlegt.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bankdaten -->
    <?php if ($adresse['iban'] || $adresse['bank']): ?>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 pt-3">
                <h6 class="fw-semibold mb-0">
                    <i class="bi bi-bank2 text-warning me-2"></i>Bankdaten
                </h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <?php if ($adresse['iban']): ?>
                    <dt class="col-3 text-muted">IBAN</dt>
                    <dd class="col-9"><code><?= esc($adresse['iban']) ?></code></dd>
                    <?php endif; ?>
                    <?php if ($adresse['bank']): ?>
                    <dt class="col-3 text-muted">Bank</dt>
                    <dd class="col-9"><?= esc($adresse['bank']) ?></dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Bemerkungen -->
    <?php if ($adresse['bemerkungen']): ?>
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 pt-3">
                <h6 class="fw-semibold mb-0">
                    <i class="bi bi-chat-text me-2"></i>Bemerkungen
                </h6>
            </div>
            <div class="card-body text-muted">
                <?= nl2br(esc($adresse['bemerkungen'])) ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Metadaten -->
    <div class="col-12">
        <small class="text-muted">
            Angelegt: <?= $adresse['erstellt_am'] ? date('d.m.Y H:i', strtotime($adresse['erstellt_am'])) : '–' ?>
            &nbsp;·&nbsp;
            Geändert: <?= $adresse['updated_am'] ? date('d.m.Y H:i', strtotime($adresse['updated_am'])) : '–' ?>
        </small>
    </div>
</div>

<?= $this->endSection() ?>
