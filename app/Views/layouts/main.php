<!doctype html>
<html lang="de" data-bs-theme="auto">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= esc($title ?? 'Hausverwaltung') ?></title>

    <!-- Bootstrap + Icons -->
    <link rel="stylesheet" href="<?= base_url('css/fontawesome/css/all.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.4/font/bootstrap-icons.css">
  

    <style>
        .avatar {
            width: 32px;
            height: 32px;
            object-fit: cover;
            border-radius: 999px;
            border: 1px;
        }

        .btn-icon {
            border: 1px;
            border-radius: 999px;
            width: 2rem;
            height: 2rem;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-icon > i { line-height: 1; }
        .navbar .vr { align-self: stretch; }
        .footer-text {font-size:14px; }
    </style>
       <style>
       
       
        .badge-status-aktiv       { background: #d1fae5; color: #065f46; }
        .badge-status-vermietet   { background: #dbeafe; color: #1e40af; }
        .badge-status-verfuegbar  { background: #d1fae5; color: #065f46; }
        .badge-status-gesperrt    { background: #fee2e2; color: #991b1b; }
        .badge-status-offen       { background: #fef9c3; color: #92400e; }
        .badge-status-bezahlt     { background: #d1fae5; color: #065f46; }
        .badge-status-ueberfaellig { background: #fee2e2; color: #991b1b; }
        .badge-status-gekuendigt  { background: #f3f4f6; color: #6b7280; }
        .stat-card {
            border-radius: 12px;
            padding: 1.5rem; border: 1px solid #e5e9f0;
        }
        .stat-card .value { font-size: 2rem; font-weight: 700; }
        .stat-card .label { font-size: .875rem; }
    </style>

    <?= $this->renderSection('styles') ?>
</head>


<body data-page="<?= $page ?? '' ?>" class="d-flex flex-column min-vh-100">
    

<!-- partials navbar -->
   <?= view('partials/navbar') ?>



    <main class="flex-fill container py-4 pt-5 bg-body-tertiary">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<script  src="<?= base_url('js/main.js') ?>"></script>
<?= $this->renderSection('scripts') ?>
 <?= view('partials/footer') ?>


