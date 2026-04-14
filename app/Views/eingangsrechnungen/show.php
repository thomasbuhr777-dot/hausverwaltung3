<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= base_url('eingangsrechnungen') ?>">Eingangsrechnungen</a></li>
            <li class="breadcrumb-item active"><?= esc($rechnung['rechnungsnummer']) ?></li>
        </ol>
    </nav>
    <div class="d-flex gap-2">
        <?php if ($rechnung['status'] === 'offen'): ?>
        <form method="post" action="<?= base_url("eingangsrechnungen/{$rechnung['id']}/bezahlt") ?>">
            <?= csrf_field() ?>
            <button class="btn btn-success btn-sm">
                <i class="bi bi-check-lg me-1"></i> Als bezahlt markieren
            </button>
        </form>
        <?php endif; ?>
        <a href="<?= base_url("eingangsrechnungen/{$rechnung['id']}/bearbeiten") ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-pencil me-1"></i> Bearbeiten
        </a>
        <form method="post" action="<?= base_url("eingangsrechnungen/{$rechnung['id']}/loeschen") ?>"
              onsubmit="return confirm('Rechnung wirklich löschen?')">
            <?= csrf_field() ?>
            <button class="btn btn-outline-danger btn-sm">
                <i class="bi bi-trash me-1"></i> Löschen
            </button>
        </form>
    </div>
</div>

<div class="row g-3">
    <!-- Linke Spalte: Rechnungsdaten -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom-0 pt-3">
                <h6 class="fw-semibold mb-0">
                    <i class="bi bi-receipt text-warning me-2"></i>Rechnungsdaten
                </h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted">Rechnungsnr.</dt>
                    <dd class="col-7"><code><?= esc($rechnung['rechnungsnummer']) ?></code></dd>

                    <dt class="col-5 text-muted">Lieferant</dt>
                    <dd class="col-7"><?= esc($rechnung['lieferant']) ?></dd>

                    <?php if ($rechnung['lieferant_steuernummer']): ?>
                    <dt class="col-5 text-muted">Steuernummer</dt>
                    <dd class="col-7"><?= esc($rechnung['lieferant_steuernummer']) ?></dd>
                    <?php endif; ?>

                    <dt class="col-5 text-muted">Rechnungsdatum</dt>
                    <dd class="col-7"><?= date('d.m.Y', strtotime($rechnung['rechnungsdatum'])) ?></dd>

                    <dt class="col-5 text-muted">Fällig am</dt>
                    <dd class="col-7">
                        <?php if ($rechnung['faellig_datum']): ?>
                            <?php $ueberfaellig = $rechnung['status'] === 'offen' && $rechnung['faellig_datum'] < date('Y-m-d') ?>
                            <span class="<?= $ueberfaellig ? 'text-danger fw-semibold' : '' ?>">
                                <?= date('d.m.Y', strtotime($rechnung['faellig_datum'])) ?>
                                <?= $ueberfaellig ? ' <span class="badge bg-danger">Überfällig</span>' : '' ?>
                            </span>
                        <?php else: ?>
                            <span class="text-muted">–</span>
                        <?php endif; ?>
                    </dd>

                    <dt class="col-5 text-muted">Kategorie</dt>
                    <dd class="col-7 text-capitalize"><?= esc($rechnung['kategorie']) ?></dd>

                    <dt class="col-5 text-muted">Status</dt>
                    <dd class="col-7">
                        <span class="badge badge-status-<?= $rechnung['status'] ?>">
                            <?= ucfirst($rechnung['status']) ?>
                        </span>
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- Rechte Spalte: Beträge & Zuweisung -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-bottom-0 pt-3">
                <h6 class="fw-semibold mb-0">
                    <i class="bi bi-cash-coin text-success me-2"></i>Beträge
                </h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted">Nettobetrag</dt>
                    <dd class="col-7"><?= number_format($rechnung['nettobetrag'], 2, ',', '.') ?> €</dd>

                    <dt class="col-5 text-muted">MwSt. (<?= number_format($rechnung['steuersatz'], 0) ?> %)</dt>
                    <dd class="col-7"><?= number_format($rechnung['steuerbetrag'], 2, ',', '.') ?> €</dd>

                    <dt class="col-5 text-muted fw-bold">Bruttobetrag</dt>
                    <dd class="col-7 fw-bold text-primary fs-5"><?= number_format($rechnung['bruttobetrag'], 2, ',', '.') ?> €</dd>
                </dl>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 pt-3">
                <h6 class="fw-semibold mb-0">
                    <i class="bi bi-building text-info me-2"></i>Zuweisung
                </h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <?php if ($rechnung['objekt_bezeichnung'] && ! $rechnung['einheit_bezeichnung']): ?>
                        <dt class="col-5 text-muted">Objekt</dt>
                        <dd class="col-7">
                            <span class="badge bg-info text-dark me-1">Gesamt</span>
                            <a href="<?= base_url("objekte/{$rechnung['objekt_id']}") ?>">
                                <?= esc($rechnung['objekt_bezeichnung']) ?>
                            </a>
                        </dd>
                    <?php elseif ($rechnung['einheit_bezeichnung']): ?>
                        <dt class="col-5 text-muted">Objekt</dt>
                        <dd class="col-7">
                            <a href="<?= base_url("objekte/{$rechnung['objekt_id']}") ?>">
                                <?= esc($rechnung['objekt_bezeichnung'] ?? '–') ?>
                            </a>
                        </dd>
                        <dt class="col-5 text-muted">Einheit</dt>
                        <dd class="col-7">
                            <span class="badge bg-secondary me-1">Einheit</span>
                            <a href="<?= base_url("einheiten/{$rechnung['einheit_id']}") ?>">
                                <?= esc($rechnung['einheit_bezeichnung']) ?>
                            </a>
                        </dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>
    </div>

    <!-- Beschreibung -->
    <?php if ($rechnung['beschreibung']): ?>
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 pt-3">
                <h6 class="fw-semibold mb-0"><i class="bi bi-chat-text me-2"></i>Beschreibung</h6>
            </div>
            <div class="card-body text-muted">
                <?= nl2br(esc($rechnung['beschreibung'])) ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Datei-Anhang -->
    <?php if ($rechnung['datei_pfad']): ?>
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 pt-3">
                <h6 class="fw-semibold mb-0"><i class="bi bi-paperclip me-2"></i>Anhang</h6>
            </div>
            <div class="card-body">
                <a href="<?= base_url($rechnung['datei_pfad']) ?>" target="_blank" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-file-earmark-pdf me-1"></i>
                    <?= esc(basename($rechnung['datei_pfad'])) ?>
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>