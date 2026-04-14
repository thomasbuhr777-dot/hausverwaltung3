<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Adressen</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-4">

<h2 class="mb-4">Adressverwaltung</h2>

<!-- =========================
     SUCCESS MESSAGE
========================= -->
<?php if(session()->getFlashdata('success')): ?>
<div class="alert alert-success">
    <?= session()->getFlashdata('success') ?>
</div>
<?php endif; ?>


<!-- =========================
     SEARCH
========================= -->
<form method="get" class="mb-3">
<div class="input-group">
    <input
        name="q"
        class="form-control"
        placeholder="Suche..."
        value="<?= esc($search ?? '') ?>"
    >
    <button class="btn btn-outline-secondary">Suchen</button>
</div>
</form>


<!-- =========================
     CREATE BUTTON
========================= -->
<button class="btn btn-success mb-3"
        data-bs-toggle="modal"
        data-bs-target="#createModal">
    Neue Adresse
</button>


<!-- =========================
     TABLE
========================= -->
<table class="table table-striped table-bordered bg-white">

<thead class="table-light">
<tr>
    <th>ID</th>
    <th>Vorname</th>
    <th>Nachname</th>
    <th>Email</th>
    <th width="160"></th>
</tr>
</thead>

<tbody>
<?php foreach($adressen as $a): ?>
<tr>
    <td><?= $a['id'] ?></td>
    <td><?= esc($a['vorname']) ?></td>
    <td><?= esc($a['nachname']) ?></td>
    <td><?= esc($a['email']) ?></td>

    <td>

        <!-- EDIT -->
        <button
            class="btn btn-sm btn-primary editBtn"
            data-bs-toggle="modal"
            data-bs-target="#editModal"
            data-id="<?= $a['id'] ?>"
            data-vorname="<?= esc($a['vorname']) ?>"
            data-nachname="<?= esc($a['nachname']) ?>"
            data-email="<?= esc($a['email']) ?>"
        >
            Edit
        </button>

        <!-- DELETE -->
        <a href="/adressen/delete/<?= $a['id'] ?>"
           class="btn btn-sm btn-danger"
           onclick="return confirm('Datensatz wirklich löschen?')">
           Delete
        </a>

    </td>
</tr>
<?php endforeach ?>
</tbody>

</table>


<!-- =========================
     PAGINATION
========================= -->
<div class="mt-3">
<?= $pager->links() ?>
</div>

</div>



<!-- =====================================================
     CREATE MODAL
===================================================== -->
<div class="modal fade" id="createModal">
<div class="modal-dialog">
<form method="post" action="/adressen/create">

<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title">Neue Adresse</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<input class="form-control mb-2" name="vorname" placeholder="Vorname" required>
<input class="form-control mb-2" name="nachname" placeholder="Nachname" required>
<input class="form-control" name="email" placeholder="Email" required>

</div>

<div class="modal-footer">
<button class="btn btn-success">Speichern</button>
</div>

</div>
</form>
</div>
</div>



<!-- =====================================================
     EDIT MODAL
===================================================== -->
<div class="modal fade" id="editModal">
<div class="modal-dialog">
<form method="post" action="/adressen/update">

<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title">Adresse bearbeiten</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<input type="hidden" name="id" id="edit-id">

<input class="form-control mb-2" name="vorname" id="edit-vorname" required>
<input class="form-control mb-2" name="nachname" id="edit-nachname" required>
<input class="form-control" name="email" id="edit-email" required>

</div>

<div class="modal-footer">
<button class="btn btn-primary">Speichern</button>
</div>

</div>
</form>
</div>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- =====================================================
     MINI JS (NUR EDIT PREFILL)
===================================================== -->
<script>
document.querySelectorAll('.editBtn').forEach(btn => {

    btn.addEventListener('click', () => {

        document.getElementById('edit-id').value =
            btn.dataset.id;

        document.getElementById('edit-vorname').value =
            btn.dataset.vorname;

        document.getElementById('edit-nachname').value =
            btn.dataset.nachname;

        document.getElementById('edit-email').value =
            btn.dataset.email;
    });

});
</script>

</body>
</html>