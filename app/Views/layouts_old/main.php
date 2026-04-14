<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Immoverwaltung') ?> – ImmoManager</title>
    <link rel="stylesheet" href="<?= base_url('css/fontawesome/css/all.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --sidebar-width: 240px; }
        body { background: #f4f6f9; }
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: #1a2332;
            position: fixed; top: 0; left: 0;
            z-index: 100;
            padding-top: 1rem;
        }
        .sidebar .brand {
            font-size: 1.2rem; font-weight: 700;
            color: #fff; padding: 1rem 1.5rem 1.5rem;
            border-bottom: 1px solid #2d3f55;
        }
        .sidebar .nav-link {
            color: #9bacc4; padding: .6rem 1.5rem;
            border-radius: 0; transition: all .2s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff; background: #2d3f55;
        }
        .sidebar .nav-link i { width: 20px; margin-right: .5rem; }
        .sidebar .nav-section {
            font-size: .7rem; font-weight: 600; letter-spacing: .1em;
            color: #5a7494; padding: 1rem 1.5rem .3rem; text-transform: uppercase;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
        }
        .topbar {
            background: #fff;
            border-bottom: 1px solid #e5e9f0;
            padding: .75rem 2rem;
            margin-left: var(--sidebar-width);
            position: sticky; top: 0; z-index: 99;
            display: flex; justify-content: space-between; align-items: center;
        }
        .badge-status-aktiv       { background: #d1fae5; color: #065f46; }
        .badge-status-vermietet   { background: #dbeafe; color: #1e40af; }
        .badge-status-verfuegbar  { background: #d1fae5; color: #065f46; }
        .badge-status-gesperrt    { background: #fee2e2; color: #991b1b; }
        .badge-status-offen       { background: #fef9c3; color: #92400e; }
        .badge-status-bezahlt     { background: #d1fae5; color: #065f46; }
        .badge-status-ueberfaellig { background: #fee2e2; color: #991b1b; }
        .badge-status-gekuendigt  { background: #f3f4f6; color: #6b7280; }
        .stat-card {
            background: #fff; border-radius: 12px;
            padding: 1.5rem; border: 1px solid #e5e9f0;
        }
        .stat-card .value { font-size: 2rem; font-weight: 700; }
        .stat-card .label { color: #6b7280; font-size: .875rem; }
    </style>
</head>
<body>

<!-- Sidebar -->
<nav class="sidebar">
    <div class="brand"><img src="<?= base_url('img/g_logo_vs6.png') ?>" class="img-fluid" alt="Logo"></div>
    <div class="mt-2">
        <div class="nav-section">Übersicht</div>
        <a href="<?= base_url('dashboard') ?>" class="nav-link <?= url_is('dashboard*') ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <div class="nav-section">Stammdaten</div>
        <a href="<?= base_url('adressen') ?>" class="nav-link <?= url_is('adressen*') ? 'active' : '' ?>">
            <i class="bi bi-person-lines-fill"></i> Adressbuch
        </a>

        <div class="nav-section">Verwaltung</div>
        <a href="<?= base_url('objekte') ?>" class="nav-link <?= url_is('objekte*') ? 'active' : '' ?>">
            <i class="bi bi-building-fill"></i> Objekte
        </a>
        <a href="<?= base_url('einheiten') ?>" class="nav-link <?= url_is('einheiten*') ? 'active' : '' ?>">
            <i class="bi bi-door-open-fill"></i> Einheiten
        </a>

        <div class="nav-section">Mietverhältnisse</div>
        <a href="<?= base_url('mietvertraege') ?>" class="nav-link <?= url_is('mietvertraege*') ? 'active' : '' ?>">
            <i class="bi bi-file-earmark-text-fill"></i> Mietverträge
        </a>
        <a href="<?= base_url('zahlungen') ?>" class="nav-link <?= url_is('zahlungen*') ? 'active' : '' ?>">
            <i class="bi bi-cash-coin"></i> Zahlungen
        </a>
        <a href="<?= base_url('nebenkosten') ?>" class="nav-link <?= url_is('nebenkosten*') ? 'active' : '' ?>">
            <i class="bi bi-calculator-fill"></i> Nebenkostenabrechnung
        </a>

        <div class="nav-section">Finanzen</div>
        <a href="<?= base_url('eingangsrechnungen') ?>" class="nav-link <?= url_is('eingangsrechnungen*') ? 'active' : '' ?>">
            <i class="bi bi-receipt"></i> Eingangsrechnungen
        </a>
    </div>
</nav>

<!-- Topbar -->
<div class="topbar">
    <h5 class="mb-0 fw-semibold"><?= esc($title ?? '') ?></h5>
    <div class="d-flex align-items-center gap-3">
        <span class="text-muted small"><?= auth()->user()->username ?? 'Benutzer' ?></span>
        <a href="<?= base_url('logout') ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-box-arrow-right"></i> Abmelden
        </a>
    </div>
</div>

<!-- Main Content -->
<div class="main-content">

    <!-- Flash Messages -->
    <?php if (session()->has('success')): ?>
        <div class="alert alert-success alert-dismissible d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?= esc(session('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->has('error')): ?>
        <div class="alert alert-danger alert-dismissible d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= esc(session('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->has('errors')): ?>
        <div class="alert alert-danger">
            <strong><i class="bi bi-exclamation-triangle-fill me-1"></i> Fehler:</strong>
            <ul class="mb-0 mt-1">
                <?php foreach ((array) session('errors') as $field => $msg): ?>
                    <li><?= esc($msg) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?= $this->renderSection('content') ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
