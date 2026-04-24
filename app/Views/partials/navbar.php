 <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-primary" data-bs-theme="dark">
        <div class="container">
            <a class="navbar-brand d-flex gap-2" href="<?= base_url() ?>">
                <img src="<?= base_url('img/g_logo_vs6.png') ?>" alt="Logo">
            </a>

           <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Navigation umschalten">
                <span class="navbar-toggler-icon"></span>
            </button>

             <div class="collapse navbar-collapse pt-2 pt-lg-3 align-items-center" id="mainNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-lg-center">
                     <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Verwaltung
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="/objekte">Objekte</a></li>
            <li><a class="dropdown-item" href="/einheiten">Einheiten</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="/adressen">Adressen</a></li>
          </ul>
        </li>
                                   <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Vermietungen
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="/mietvertraege">Mietvertäge</a></li>
            <li><a class="dropdown-item" href="/zahlungen">Zahlungen</a></li>
          
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Nebenkosten
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="/mietvertraege">Abrechnungen</a></li>
            <li><a class="dropdown-item" href="/zahlungen">Eingangsrechnungen</a></li>
          
          </ul>
        </li>
                </ul>


                <!-- Right tools -->
                <div class="navbar-tools d-flex align-items-center gap-2 mt-3 mt-lg-0">
                    <div class="vr d-none d-lg-block"></div>

                    <!-- Theme toggle -->
                    <div class="dropdown">
                        <button class="btn btn-icon btn-sm btn-outline-light border-1" id="themeToggle" data-bs-toggle="dropdown">
                            <i class="bi bi-circle-half"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="themeToggle">
                            <li><button class="dropdown-item" data-theme="light"><i class="bi bi-sun-fill"></i> Light</button></li>
                            <li><button class="dropdown-item" data-theme="dark"><i class="bi bi-moon-fill"></i> Dark</button></li>
                        </ul>
                    </div>

<?php
$avatar = auth()->user()->avatar
    ? base_url('uploads/avatars/' . auth()->user()->avatar)
    : base_url('img/profile.png');
?>


                    <!-- Profile -->
                    <div class="dropdown">
                        <button class="btn btn-link p-0 border-0" data-bs-toggle="dropdown">
                            <img class="avatar border rounded-circle"
     src="<?= $avatar ?>"
     width="40"
     height="40"
     style="object-fit: cover;"
     alt="Profilbild">
                        </button>
                       <ul class="dropdown-menu dropdown-menu-end text-bg-primary">
       <li class="dropdown-header text-white text-uppercase small">
        <i class="fa-light fa-arrow-right"></i> Account
    </li>
    <li>
        <a class="dropdown-item" href="/profile">Profile</a>
    </li>

    <li>
        <a class="dropdown-item" href="/logout">Logout</a>
    </li>
<?php if (auth()->loggedIn() && auth()->user()->inGroup('admin')): ?>
    <li><hr class="dropdown-divider"></li>

    <li class="dropdown-header text-white text-uppercase small">
        <i class="fa-light fa-arrow-right"></i> Settings
    </li>

    <li>
        <a class="dropdown-item" href="<?= base_url('settings/lookup/objektarten') ?>">
            Objektarten
        </a>
    </li>
    <li>
        <a class="dropdown-item" href="<?= base_url('settings/lookup/einheitenlage') ?>">
            Einheitenlage
        </a>
    </li>
   
    <li>
        <a class="dropdown-item" href="<?= base_url('settings/lookup/einheitengeschoss') ?>">
            Einheitengeschoss
        </a>
    </li>
     <!--
    <li>
        <a class="dropdown-item" href="<?= base_url('settings/lookup/ausstattungen') ?>">
            Ausstattungen
        </a>
    </li>
    <li>
        <a class="dropdown-item" href="<?= base_url('settings/lookup/heizungsarten') ?>">
            Heizungsarten
        </a>
    </li>
    <li>
        <a class="dropdown-item" href="<?= base_url('settings/lookup/energieausweis_typen') ?>">
            Energieausweisarten
        </a>
    </li>
    -->
    <?php endif; ?>
</ul>

                    </div>
                </div>
            </div>
        </div>
    </nav>
