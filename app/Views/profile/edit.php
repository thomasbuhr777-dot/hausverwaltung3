<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Profil bearbeiten</h1>
    
</a>
</div>
<hr class="mb-0 pb-2">
<div class="container">
    <div class="card col-lg-12 mx-auto">
        <div class="card-body">

           

            <?php if (session('message')) : ?>
                <div class="alert alert-success">
                    <?= session('message') ?>
                </div>
            <?php endif ?>

            <?php if (session('error')) : ?>
                <div class="alert alert-danger">
                    <?= session('error') ?>
                </div>
            <?php endif ?>



            <?php if (!empty($forceReset)) : ?>
                <div class="alert alert-warning">
                    Bitte setzen Sie ein neues Passwort, bevor Sie fortfahren.
                </div>
            <?php endif; ?>

            <form method="post">
                <?= csrf_field() ?>

                <!-- Anrede -->
                <div class="form-floating mb-3">
                    <select class="form-select" name="anrede">
                        <option value="">Bitte wählen</option>
                        <option value="Herr" <?= $user->anrede === 'Herr' ? 'selected' : '' ?>>Herr</option>
                        <option value="Frau" <?= $user->anrede === 'Frau' ? 'selected' : '' ?>>Frau</option>
                        <option value="Divers" <?= $user->anrede === 'Divers' ? 'selected' : '' ?>>Divers</option>
                    </select>
                    <label>Anrede</label>
                </div>

                <!-- Vorname -->
                <div class="form-floating mb-3">
                    <input type="text"
                           class="form-control"
                           name="vorname"
                           value="<?= esc($user->vorname) ?>"
                           required>
                    <label>Vorname</label>
                </div>

                <!-- Nachname -->
                <div class="form-floating mb-3">
                    <input type="text"
                           class="form-control"
                           name="nachname"
                           value="<?= esc($user->nachname) ?>"
                           required>
                    <label>Nachname</label>
                </div>

                <!-- Mobile -->
                <div class="form-floating mb-4">
                    <input type="text"
                           class="form-control"
                           name="mobile"
                           value="<?= esc($user->mobile) ?>">
                    <label>Mobilnummer</label>
                </div>

                <hr>

                <h5>Passwort ändern</h5>
                <?php if (empty($forceReset)) : ?>
                 <!-- Aktuelles Passwort -->
                <div class="form-floating mb-3">
                <input type="password"
                    class="form-control"
                    name="current_password"
                    placeholder="Aktuelles Passwort">
                  <label>Aktuelles Passwort</label>
                </div>
                <?php endif; ?>


                <!-- Neues Passwort -->
                <div class="form-floating mb-3">
                    <input type="password"
                           class="form-control"
                           name="password"
                           placeholder="Neues Passwort">
                    <label>Neues Passwort</label>
                </div>

                <!-- Passwort bestätigen -->
                <div class="form-floating mb-4">
                    <input type="password"
                           class="form-control"
                           name="password_confirm"
                           placeholder="Passwort wiederholen">
                    <label>Passwort wiederholen</label>
                </div>

                <button class="btn btn-primary">Speichern</button>

            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
