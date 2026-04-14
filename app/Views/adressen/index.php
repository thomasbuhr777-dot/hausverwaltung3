<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
// Hilfsfunktion: Sortier-URL generieren
function sortUrl(string $col, string $currentSort, string $currentDir, string $base): string {
    $newDir = ($currentSort === $col && $currentDir === 'ASC') ? 'DESC' : 'ASC';
    $params = array_merge($_GET, ['sort' => $col, 'dir' => $newDir, 'page' => 1]);
    return $base . '?' . http_build_query($params);
}

function sortIcon(string $col, string $currentSort, string $currentDir): string {
    if ($currentSort !== $col) return '<i class="bi bi-chevron-expand text-muted ms-1" style="font-size:.7rem"></i>';
    return $currentDir === 'ASC'
        ? '<i class="bi bi-chevron-up text-primary ms-1" style="font-size:.7rem"></i>'
        : '<i class="bi bi-chevron-down text-primary ms-1" style="font-size:.7rem"></i>';
}

$baseUrl = base_url('adressen');
?>

<!-- Suche + Filter + Neu-Button -->
<form method="get" action="<?= $baseUrl ?>" id="filterForm">
    <input type="hidden" name="sort" value="<?= esc($sort) ?>">
    <input type="hidden" name="dir"  value="<?= esc($dir) ?>">
    <div class="d-flex gap-2 mb-4 flex-wrap">
        <div class="input-group" style="max-width:340px">
            <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
            <input type="text" name="q" class="form-control border-start-0"
                   placeholder="Name, E-Mail, Ort…"
                   value="<?= esc($suche) ?>"
                   id="sucheInput">
            <?php if ($suche): ?>
                <a href="<?= $baseUrl ?>?typ=<?= esc($typ) ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-x"></i>
                </a>
            <?php endif; ?>
        </div>
        <select name="typ" class="form-select" style="width:auto" onchange="this.form.submit()">
            <option value="">Alle Typen</option>
            <option value="person" <?= $typ === 'person' ? 'selected' : '' ?>>Person</option>
            <option value="firma"  <?= $typ === 'firma'  ? 'selected' : '' ?>>Firma</option>
        </select>
        <button type="submit" class="btn btn-outline-primary">
            <i class="bi bi-search me-1"></i>Suchen
        </button>
        <div class="ms-auto">
            <button type="button" class="btn btn-primary" onclick="openCreateModal()">
                <i class="bi bi-plus-lg me-1"></i> Neue Adresse
            </button>
        </div>
    </div>
</form>

<!-- Ergebnis-Info -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <small class="text-muted">
        <?= number_format($total, 0, ',', '.') ?> Einträge
        <?= $suche ? '– Suche: <strong>' . esc($suche) . '</strong>' : '' ?>
        <?= $typ   ? '– Typ: <strong>' . ucfirst(esc($typ)) . '</strong>' : '' ?>
    </small>
    <small class="text-muted">Seite <?= $page ?> von <?= max(1, $totalPages) ?></small>
</div>

<!-- Tabelle -->
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>
                        <a href="<?= sortUrl('anzeigename', $sort, $dir, $baseUrl) ?>"
                           class="text-dark text-decoration-none fw-semibold">
                            Name / Firma <?= sortIcon('anzeigename', $sort, $dir) ?>
                        </a>
                    </th>
                    <th>
                        <a href="<?= sortUrl('kontakt_typ', $sort, $dir, $baseUrl) ?>"
                           class="text-dark text-decoration-none fw-semibold">
                            Typ <?= sortIcon('kontakt_typ', $sort, $dir) ?>
                        </a>
                    </th>
                    <th>
                        <a href="<?= sortUrl('ort', $sort, $dir, $baseUrl) ?>"
                           class="text-dark text-decoration-none fw-semibold">
                            Ort <?= sortIcon('ort', $sort, $dir) ?>
                        </a>
                    </th>
                    <th>
                        <a href="<?= sortUrl('email', $sort, $dir, $baseUrl) ?>"
                           class="text-dark text-decoration-none fw-semibold">
                            E-Mail <?= sortIcon('email', $sort, $dir) ?>
                        </a>
                    </th>
                    <th>
                        <a href="<?= sortUrl('telefon1', $sort, $dir, $baseUrl) ?>"
                           class="text-dark text-decoration-none fw-semibold">
                            Telefon <?= sortIcon('telefon1', $sort, $dir) ?>
                        </a>
                    </th>
                    <th class="text-end">Aktionen</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($adressen)): ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                        <i class="bi bi-inbox display-6 d-block mb-2"></i>
                        Keine Einträge gefunden.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($adressen as $a): ?>
                <tr>
                    <td>
                        <div class="fw-medium">
                            <a href="<?= $baseUrl ?>/<?= $a['id'] ?>" class="text-decoration-none text-dark">
                                <?= esc($a['anzeigename']) ?>
                            </a>
                        </div>
                        <?php if ($a['strasse']): ?>
                            <small class="text-muted">
                                <?= esc($a['strasse']) ?> <?= esc($a['hsnr'] ?? '') ?>
                            </small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($a['kontakt_typ'] === 'firma'): ?>
                            <span class="badge bg-info text-dark">
                                <i class="bi bi-building me-1"></i>Firma
                            </span>
                        <?php else: ?>
                            <span class="badge bg-secondary">
                                <i class="bi bi-person me-1"></i>Person
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($a['ort']): ?>
                            <span><?= esc($a['plz'] ?? '') ?> <?= esc($a['ort']) ?></span>
                        <?php else: ?>
                            <span class="text-muted">–</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($a['email']): ?>
                            <a href="mailto:<?= esc($a['email']) ?>" class="text-truncate d-inline-block"
                               style="max-width:180px"><?= esc($a['email']) ?></a>
                        <?php else: ?>
                            <span class="text-muted">–</span>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($a['telefon1'] ?? '–') ?></td>
                    <td>
                        <div class="d-flex gap-1 justify-content-end">
                            <button class="btn btn-sm btn-outline-secondary"
                                    onclick="openEditModal(<?= $a['id'] ?>)"
                                    title="Bearbeiten">
                                <i class="bi bi-pencil"></i>
                            </button>
                      <form method="post"
      action="<?= $baseUrl ?>/<?= $a['id'] ?>/loeschen"
      onsubmit="return confirm('Adresse wirklich löschen?')">
    <?= csrf_field() ?>
    <input type="hidden" name="_q"    value="<?= esc($suche) ?>">
    <input type="hidden" name="_typ"  value="<?= esc($typ) ?>">
    <input type="hidden" name="_sort" value="<?= esc($sort) ?>">
    <input type="hidden" name="_dir"  value="<?= esc($dir) ?>">
    <input type="hidden" name="_page" value="<?= esc($page) ?>">
    <button class="btn btn-sm btn-outline-danger" title="Löschen">
        <i class="bi bi-trash"></i>
    </button>
</form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<?php
$queryBase = array_filter(['q' => $suche, 'typ' => $typ, 'sort' => $sort, 'dir' => $dir]);
$pageRange = function(int $page, int $total): array {
    $start = max(1, $page - 2);
    $end   = min($total, $page + 2);
    return range($start, $end);
};
?>
<nav class="mt-3">
    <ul class="pagination pagination-sm justify-content-center mb-0">
        <!-- Erste -->
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= $baseUrl ?>?<?= http_build_query(array_merge($queryBase, ['page' => 1])) ?>">
                <i class="bi bi-chevron-double-left"></i>
            </a>
        </li>
        <!-- Zurück -->
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= $baseUrl ?>?<?= http_build_query(array_merge($queryBase, ['page' => $page - 1])) ?>">
                <i class="bi bi-chevron-left"></i>
            </a>
        </li>

        <?php foreach ($pageRange($page, $totalPages) as $p): ?>
        <li class="page-item <?= $p === $page ? 'active' : '' ?>">
            <a class="page-link" href="<?= $baseUrl ?>?<?= http_build_query(array_merge($queryBase, ['page' => $p])) ?>">
                <?= $p ?>
            </a>
        </li>
        <?php endforeach; ?>

        <!-- Vor -->
        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= $baseUrl ?>?<?= http_build_query(array_merge($queryBase, ['page' => $page + 1])) ?>">
                <i class="bi bi-chevron-right"></i>
            </a>
        </li>
        <!-- Letzte -->
        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= $baseUrl ?>?<?= http_build_query(array_merge($queryBase, ['page' => $totalPages])) ?>">
                <i class="bi bi-chevron-double-right"></i>
            </a>
        </li>
    </ul>
</nav>
<?php endif; ?>

<!-- ================================================================
     Modal: Adresse anlegen / bearbeiten
     ================================================================ -->
<div class="modal fade" id="adresseModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Neue Adresse</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

        <form id="adresseForm" method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="_q"    value="<?= esc($suche) ?>">
    <input type="hidden" name="_typ"  value="<?= esc($typ) ?>">
    <input type="hidden" name="_sort" value="<?= esc($sort) ?>">
    <input type="hidden" name="_dir"  value="<?= esc($dir) ?>">
    <input type="hidden" name="_page" value="<?= esc($page) ?>">

                <div class="modal-body">
                    <div id="modal_alert" class="alert alert-danger d-none"></div>

                    <!-- Typ-Umschalter -->
                    <div class="mb-4">
                        <div class="btn-group w-100">
                            <input type="radio" class="btn-check" name="kontakt_typ"
                                   id="m_typ_person" value="person" checked>
                            <label class="btn btn-outline-primary" for="m_typ_person">
                                <i class="bi bi-person me-1"></i>Person
                            </label>
                            <input type="radio" class="btn-check" name="kontakt_typ"
                                   id="m_typ_firma" value="firma">
                            <label class="btn btn-outline-primary" for="m_typ_firma">
                                <i class="bi bi-building me-1"></i>Firma
                            </label>
                        </div>
                    </div>

                    <!-- Personen-Block -->
                    <div id="m_block_person">
                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Anrede</label>
                                <select name="anrede" id="m_anrede" class="form-select">
                                    <option value="">–</option>
                                    <option>Herr</option>
                                    <option>Frau</option>
                                    <option>Divers</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Titel</label>
                                <input type="text" name="titel" id="m_titel"
                                       class="form-control" placeholder="Dr., Prof.">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Vorname</label>
                                <input type="text" name="vorname" id="m_vorname" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Nachname *</label>
                                <input type="text" name="nachname" id="m_nachname" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Firmen-Block -->
                    <div id="m_block_firma" style="display:none">
                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Firmenname *</label>
                                <input type="text" name="firmenname" id="m_firmenname" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Umsatzsteuer-ID</label>
                                <input type="text" name="umsatzsteuer_id" id="m_umsatzsteuer_id"
                                       class="form-control" placeholder="DE123456789">
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <!-- Kontakt -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">E-Mail</label>
                            <input type="email" name="email" id="m_email" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Telefon 1</label>
                            <input type="text" name="telefon1" id="m_telefon1" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Telefon 2</label>
                            <input type="text" name="telefon2" id="m_telefon2" class="form-control">
                        </div>
                    </div>

                    <hr class="my-3">

                    <!-- Adresse -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-9">
                            <label class="form-label fw-semibold">Straße</label>
                            <input type="text" name="strasse" id="m_strasse" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Hausnr.</label>
                            <input type="text" name="hsnr" id="m_hsnr" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">PLZ</label>
                            <input type="text" name="plz" id="m_plz" class="form-control">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Ort</label>
                            <input type="text" name="ort" id="m_ort" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Land</label>
                            <input type="text" name="land" id="m_land"
                                   class="form-control" value="Deutschland">
                        </div>
                    </div>

                    <hr class="my-3">

                    <!-- Bankdaten -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">IBAN</label>
                            <input type="text" name="iban" id="m_iban"
                                   class="form-control" placeholder="DE00 0000…">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Bank</label>
                            <input type="text" name="bank" id="m_bank" class="form-control">
                        </div>
                    </div>

                    <div>
                        <label class="form-label fw-semibold">Bemerkungen</label>
                        <textarea name="bemerkungen" id="m_bemerkungen"
                                  class="form-control" rows="2"></textarea>
                    </div>
                </div><!-- /.modal-body -->

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Abbrechen</button>
                    <button type="submit" class="btn btn-primary" id="m_submit">
                        <i class="bi bi-check-lg me-1"></i>Speichern
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
    'use strict';

    const baseUrl    = '<?= base_url('adressen') ?>';
    const modal      = new bootstrap.Modal(document.getElementById('adresseModal'));
    const form       = document.getElementById('adresseForm');
    const alertBox   = document.getElementById('modal_alert');
    const modalTitle = document.getElementById('modalTitle');

    const fields = {
        typ         : document.querySelectorAll('input[name="kontakt_typ"]'),
        anrede      : document.getElementById('m_anrede'),
        titel       : document.getElementById('m_titel'),
        vorname     : document.getElementById('m_vorname'),
        nachname    : document.getElementById('m_nachname'),
        firmenname  : document.getElementById('m_firmenname'),
        ust         : document.getElementById('m_umsatzsteuer_id'),
        email       : document.getElementById('m_email'),
        telefon1    : document.getElementById('m_telefon1'),
        telefon2    : document.getElementById('m_telefon2'),
        strasse     : document.getElementById('m_strasse'),
        hsnr        : document.getElementById('m_hsnr'),
        plz         : document.getElementById('m_plz'),
        ort         : document.getElementById('m_ort'),
        land        : document.getElementById('m_land'),
        iban        : document.getElementById('m_iban'),
        bank        : document.getElementById('m_bank'),
        bemerkungen : document.getElementById('m_bemerkungen'),
    };

    // Typ-Toggle
    fields.typ.forEach(r => r.addEventListener('change', () => toggleTyp(r.value)));

    function toggleTyp(typ) {
        document.getElementById('m_block_person').style.display = typ === 'person' ? '' : 'none';
        document.getElementById('m_block_firma').style.display  = typ === 'firma'  ? '' : 'none';
    }

    function resetModal() {
        alertBox.classList.add('d-none');
        alertBox.textContent = '';
        form.querySelectorAll('input[type=text], input[type=email], textarea')
            .forEach(el => { el.value = ''; el.classList.remove('is-invalid'); });
        fields.anrede.value = '';
        fields.land.value   = 'Deutschland';
        document.getElementById('m_typ_person').checked = true;
        toggleTyp('person');
    }

    // ---- Neu anlegen ----
    window.openCreateModal = function () {
        resetModal();
        modalTitle.textContent = 'Neue Adresse anlegen';
        form.action = baseUrl;
        modal.show();
    };

    // ---- Bearbeiten ----
    window.openEditModal = async function (id) {
            const autoEditId = Number(new URLSearchParams(window.location.search).get('edit') || 0);

    if (autoEditId > 0) {
        const url = new URL(window.location.href);
        url.searchParams.delete('edit');
        window.history.replaceState({}, '', url.toString());
        window.openEditModal(autoEditId);
    }
        resetModal();
        modalTitle.textContent = 'Adresse bearbeiten…';
        form.action = `${baseUrl}/${id}`;

        try {
            const res  = await fetch(`${baseUrl}/${id}/bearbeiten`);
            const data = await res.json();

            if (! res.ok) {
                alertBox.textContent = data.error ?? 'Fehler beim Laden.';
                alertBox.classList.remove('d-none');
                modal.show();
                return;
            }

            modalTitle.textContent = 'Adresse bearbeiten';

            // Typ setzen
            const typ = data.kontakt_typ ?? 'person';
            document.getElementById(typ === 'firma' ? 'm_typ_firma' : 'm_typ_person').checked = true;
            toggleTyp(typ);

            // Felder befüllen
            const map = {
                anrede      : 'm_anrede',
                titel       : 'm_titel',
                vorname     : 'm_vorname',
                nachname    : 'm_nachname',
                firmenname  : 'm_firmenname',
                umsatzsteuer_id : 'm_umsatzsteuer_id',
                email       : 'm_email',
                telefon1    : 'm_telefon1',
                telefon2    : 'm_telefon2',
                strasse     : 'm_strasse',
                hsnr        : 'm_hsnr',
                plz         : 'm_plz',
                ort         : 'm_ort',
                land        : 'm_land',
                iban        : 'm_iban',
                bank        : 'm_bank',
                bemerkungen : 'm_bemerkungen',
            };

            Object.entries(map).forEach(([key, elId]) => {
                const el = document.getElementById(elId);
                if (el) el.value = data[key] ?? '';
            });

            // Anrede als select
            if (fields.anrede) fields.anrede.value = data.anrede ?? '';

        } catch (e) {
            alertBox.textContent = 'Netzwerkfehler. Bitte erneut versuchen.';
            alertBox.classList.remove('d-none');
        }

        modal.show();
    };

    // ---- Client-Validierung ----
    form.addEventListener('submit', function (e) {
        const typ = document.querySelector('input[name="kontakt_typ"]:checked').value;
        let ok = true;

        if (typ === 'person' && ! fields.nachname.value.trim()) {
            fields.nachname.classList.add('is-invalid');
            ok = false;
        }
        if (typ === 'firma' && ! fields.firmenname.value.trim()) {
            fields.firmenname.classList.add('is-invalid');
            ok = false;
        }

        if (! ok) {
            e.preventDefault();
            alertBox.textContent = 'Bitte Pflichtfelder ausfüllen.';
            alertBox.classList.remove('d-none');
        }
    });

    ['m_nachname', 'm_firmenname'].forEach(id => {
        document.getElementById(id)?.addEventListener('input', function () {
            this.classList.remove('is-invalid');
            alertBox.classList.add('d-none');
        });
    });

    // Modal nach Flash-Message automatisch schließen – nicht nötig da Redirect
}());
</script>
<?= $this->endSection() ?>
