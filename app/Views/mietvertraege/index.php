<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between mb-4">
    <div></div>
    <a href="<?= base_url('mietvertraege/neu') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Neuer Mietvertrag
    </a>
</div>

<?php if (!empty($auslaufend)): ?>
<div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
    <i class="bi bi-calendar-x-fill me-2 fs-5"></i>
    <div>
        <strong><?= count($auslaufend) ?> Vertrag/Verträge</strong> laufen in den nächsten 90 Tagen aus.
        <a href="#auslaufend" class="alert-link ms-1">Anzeigen</a>
    </div>
</div>
<?php endif; ?>

<!-- Filter-Tabs -->
<ul class="nav nav-tabs mb-3" id="statusTabs">
    <li class="nav-item"><a class="nav-link active" href="#" data-filter="all">Alle (<?= count($vertraege) ?>)</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-filter="aktiv">Aktiv</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-filter="gekuendigt">Gekündigt</a></li>
    <li class="nav-item"><a class="nav-link" href="#" data-filter="beendet">Beendet</a></li>
</ul>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0" id="vertraegeTable">
            <thead class="table-light">
                <tr>
                    <th>Vertragsnr.</th>
                    <th>Mieter</th>
                    <th>Objekt / Einheit</th>
                    <th class="text-end">Kaltmiete</th>
                    <th class="text-end">NK</th>
                    <th>Beginn</th>
                    <th>Ende</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($vertraege)): ?>
                <tr><td colspan="9" class="text-center text-muted py-4">Keine Mietverträge vorhanden.</td></tr>
                <?php else: ?>
                <?php foreach ($vertraege as $v): ?>
                <tr data-status="<?= $v['status'] ?>">
                    <td><code><?= esc($v['vertragsnummer'] ?? '–') ?></code></td>
                    <td>
                        <div class="fw-medium"><?= esc($v['mieter_name']) ?><?= $v['mieter_vorname'] ? ', ' . esc($v['mieter_vorname']) : '' ?></div>
                        <?php if ($v['mieter_email']): ?>
                            <small class="text-muted"><?= esc($v['mieter_email']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div><?= esc($v['objekt_bezeichnung']) ?></div>
                        <small class="text-muted"><?= esc($v['einheit_bezeichnung']) ?></small>
                    </td>
                    <td class="text-end"><?= number_format($v['kaltmiete'], 2, ',', '.') ?> €</td>
                    <td class="text-end"><?= number_format($v['nebenkosten'], 2, ',', '.') ?> €</td>
                    <td><?= date('d.m.Y', strtotime($v['beginn_datum'])) ?></td>
                    <td><?= $v['ende_datum'] ? date('d.m.Y', strtotime($v['ende_datum'])) : '<span class="text-muted">unbefristet</span>' ?></td>
                    <td><span class="badge badge-status-<?= $v['status'] ?>"><?= ucfirst($v['status']) ?></span></td>
                    <td>
                        <a href="<?= base_url("mietvertraege/{$v['id']}") ?>" class="btn btn-sm btn-outline-primary">Details</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.querySelectorAll('[data-filter]').forEach(tab => {
    tab.addEventListener('click', e => {
        e.preventDefault();
        document.querySelectorAll('[data-filter]').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        const filter = tab.dataset.filter;
        document.querySelectorAll('#vertraegeTable tbody tr[data-status]').forEach(row => {
            row.style.display = (filter === 'all' || row.dataset.status === filter) ? '' : 'none';
        });
    });
});
</script>
<?= $this->endSection() ?>
