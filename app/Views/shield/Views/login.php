<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?>Gizyn-Hausverwaltung<?= $this->endSection() ?>

<?= $this->section('main') ?>

<style>


/* Button exakt wie form-control */
.password-group .btn-password-toggle {
  background-color: var(--bs-body-bg);
  border: var(--bs-border-width) solid var(--bs-border-color);
  border-left: var(--bs-border-width) solid var(--bs-border-color);
  border-radius: 0 .375rem .375rem 0;
  padding: 0 .75rem;
}

/* Fokus-Zustand synchronisieren */
.password-group:focus-within .btn-password-toggle {
  border-color: var(--bs-primary);
}

/* Micro-Animation & Icon */
.btn-password-toggle i {
  font-size: 1.2rem;
  transition:
    transform 160ms ease,
    opacity 120ms ease,
    color 160ms ease;
}


/* Button wie Input aussehen lassen */
.password-group .btn-password-toggle {
  border-left: 0;
  border-radius: 0 .375rem .375rem 0;
  background-color: var(--bs-body-bg);
}

/* Fokus-Zustand gemeinsam mit Input */
.password-group:focus-within .btn-password-toggle {
  /*border-color: var(--bs-primary);*/
}

 


/* Icon-Größe */
.btn-password-toggle i {
  font-size: 1.2rem;
  transition:
    transform 160ms ease,
    opacity 120ms ease,
    color 160ms ease;
}

/* Hover (Dark + Light Mode geeignet) */
.btn-password-toggle:hover i {
  color: var(--bs-body-color);
}

/* Micro-Animation beim Umschalten */
.btn-password-toggle.is-active i {
  transform: scale(1.15) rotate(-8deg);
  opacity: 0.85;
}

/* Optional: Button wirkt "eingebaut" 
.password-group .btn-password-toggle {
  border-left: 0;
}
*/

.password-group .btn-password-toggle:hover {
  background-color: color-mix(
    in srgb,
    var(--bs-body-bg) 90%,
    var(--bs-body-color)
  );
}




</style>

<main class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">
    <div class="card col-12 col-sm-10 col-md-6 col-lg-5">
        <div class="bg-primary text-center py-3 rounded-top">
            <img src="http://localhost:8080/img/g_logo_vs6.png"
                 class="img-fluid mx-auto d-block"
                 style="max-height: 80px;">
        </div>
            <div class="card-body">

         

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

                <?php if (session('message') !== null) : ?>
                    <div class="alert alert-success" role="alert"><?= esc(session('message')) ?></div>
                <?php endif ?>

                <form action="<?= url_to('login') ?>" method="post">
                    <?= csrf_field() ?>

                    <!-- Email -->
                    <div class="form-floating mb-3 mt-4">
                        <input type="email" class="form-control" id="floatingEmailInput" name="email" inputmode="email" autocomplete="email" placeholder="Email-Adresse" value="<?= old('email') ?>" required>
                        <label for="floatingEmailInput">Email-Adresse</label>
                    </div>

<div class="input-group mb-3 password-group">

  <div class="form-floating flex-grow-1">
    <input type="password"
           class="form-control"
           id="floatingPasswordInput"
           name="password"
           placeholder="Passwort"
           required>
    <label for="floatingPasswordInput">Passwort</label>
  </div>

  <button type="button"
        class="btn btn-password-toggle"
        aria-label="Passwort anzeigen">
  <i class="bi bi-eye"></i>
</button>



</div>



                    <!-- Remember me -->
                    <?php if (setting('Auth.sessionConfig')['allowRemembering']): ?>
                        <div class="form-check">
                            <label class="form-check-label">
                                <input type="checkbox" name="remember" class="form-check-input" <?php if (old('remember')): ?> checked<?php endif ?>>
                                Angemeldet bleiben?
                            </label>
                        </div>
                    <?php endif; ?>

                    <div class="d-grid col-12 col-md-8 mx-auto m-3 pt-4">
                        <button type="submit" class="btn btn-primary btn-block">Anmelden</button>
                    </div>

                    <p class="text-center">

                    </p>

                    <?php if (setting('Auth.allowMagicLinkLogins')) : ?>
                        <p class="text-center"><a href="<?= url_to('magic-link') ?>">Passwort vergessen?</a>
                    <?php endif ?>

                    <?php if (setting('Auth.allowRegistration')) : ?>
                         | <a href="<?= url_to('register') ?>">Registrieren</a>
                    <?php endif ?>
                </p>

                </form>
            </div>
        </div>
</main>

<script>
document.addEventListener("DOMContentLoaded", () => {

  document.querySelectorAll(".btn-password-toggle").forEach(btn => {
    const group = btn.closest(".input-group");
    const input = group.querySelector("input");
    const icon = btn.querySelector("i");

    const showPassword = () => {
      input.type = "text";
      icon.classList.replace("bi-eye", "bi-eye-slash");
    };

    const hidePassword = () => {
      input.type = "password";
      icon.classList.replace("bi-eye-slash", "bi-eye");
    };

    // Desktop
    btn.addEventListener("mousedown", showPassword);
    btn.addEventListener("mouseup", hidePassword);
    btn.addEventListener("mouseleave", hidePassword);
    btn.addEventListener("blur", hidePassword);

    // Mobile
    btn.addEventListener("touchstart", showPassword);
    btn.addEventListener("touchend", hidePassword);
    btn.addEventListener("touchcancel", hidePassword);
  });

});
</script>



<?= $this->endSection() ?>
