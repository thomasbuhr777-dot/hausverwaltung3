<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?><?= lang('Auth.register') ?> <?= $this->endSection() ?>

<?= $this->section('main') ?>

    <div class="container d-flex justify-content-center p-5">
        <div class="card col-12 col-md-5 shadow-sm">
            <div class="card-body">
                
                <img src="<?= base_url('img/g_logo_vs6.png') ?>" class="mx-auto d-block pb-4">

                <?php if (session('error') !== null) : ?>
                    <div class="alert alert-danger" role="alert"><?= esc(session('error')) ?></div>
                <?php elseif (session('errors') !== null) : ?>
                    <div class="alert alert-danger" role="alert">
                        <?php if (is_array(session('errors'))) : ?>
                            <?php foreach (session('errors') as $error) : ?>
                                <?= esc($error) ?>
                                <br>
                            <?php endforeach ?>
                        <?php else : ?>
                            <?= esc(session('errors')) ?>
                        <?php endif ?>
                    </div>
                <?php endif ?>

                <form action="<?= url_to('register') ?>" method="post">
                    <?= csrf_field() ?>

                    <!-- Email -->
                    <div class="form-floating mb-2">
                        <input type="email" class="form-control" id="floatingEmailInput" name="email" inputmode="email" autocomplete="email" placeholder="<?= lang('Auth.email') ?>" value="<?= old('email') ?>" required>
                        <label for="floatingEmailInput"><?= lang('Auth.email') ?></label>
                    </div>

                     <!-- Anrede -->
                    <div class="form-floating mb-2">
                        <select class="form-select" name="anrede">
                            <option value="">Bitte wählen</option>
                            <option value="Herr" <?= old('anrede') === 'Herr' ? 'selected' : '' ?>>Herr</option>
                            <option value="Frau" <?= old('anrede') === 'Frau' ? 'selected' : '' ?>>Frau</option>
                            <option value="Divers" <?= old('anrede') === 'Divers' ? 'selected' : '' ?>>Divers</option>
                        </select>
                        <label for="floatingPasswordInput">Anrede</label>
                    </div>

                    <!-- Vorname -->
                    <div class="form-floating mb-2">
                        <input type="text" class="form-control"
                            name="vorname"
                            placeholder="Vorname"
                            value="<?= old('vorname') ?>"
                            required>
                    <label for="floatingPasswordInput">Vorname</label>
                    </div>

                    <!-- Nachname -->
                    <div class="form-floating mb-2">
                        <input type="text" class="form-control"
                        name="nachname"
                        placeholder="Nachname"
                        value="<?= old('nachname') ?>"
                        required>
                    <label for="floatingPasswordInput">Nachname</label>
                    </div>

                    <!-- Mobilnummer -->
                    <div class="form-floating mb-4">
                        <input type="text" class="form-control"
                        name="mobile"
                        placeholder="Mobilnummer"
                        value="<?= old('mobile') ?>">
                    <label for="floatingPasswordInput">Mobilnummer</label>
                    </div>





                    <!-- Password -->
                    <div class="form-floating mb-2">
                        <input type="password" class="form-control" id="floatingPasswordInput" name="password" inputmode="text" autocomplete="new-password" placeholder="<?= lang('Auth.password') ?>" required>
                        <label for="floatingPasswordInput"><?= lang('Auth.password') ?></label>
                    </div>

                    <!-- Password (Again) -->
                    <div class="form-floating mb-5">
                        <input type="password" class="form-control" id="floatingPasswordConfirmInput" name="password_confirm" inputmode="text" autocomplete="new-password" placeholder="<?= lang('Auth.passwordConfirm') ?>" required>
                        <label for="floatingPasswordConfirmInput"><?= lang('Auth.passwordConfirm') ?></label>
                    </div>

                    <div class="d-grid col-12 col-md-8 mx-auto m-3">
                        <button type="submit" class="btn btn-primary btn-block"><?= lang('Auth.register') ?></button>
                    </div>

                    <p class="text-center"><?= lang('Auth.haveAccount') ?> <a href="<?= url_to('login') ?>"><?= lang('Auth.login') ?></a></p>

                </form>
            </div>
        </div>
    </div>

<?= $this->endSection() ?>
