<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

      <title><?= esc($title ?? 'Gizyn-Hausverwaltung') ?></title>

    <!-- Bootstrap + Icons -->
    <link rel="stylesheet" href="<?= base_url('css/fontawesome/css/all.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.4/font/bootstrap-icons.css">

    <?= $this->renderSection('pageStyles') ?>
</head>

<body>

    <main role="main" class="container">
        <?= $this->renderSection('main') ?>
    </main>

<?= $this->renderSection('pageScripts') ?>
</body>
</html>
