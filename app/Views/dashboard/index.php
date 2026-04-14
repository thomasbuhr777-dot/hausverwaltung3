<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="value"><?= $stats['objekte_gesamt'] ?></div>
            <div class="label"><i class="bi bi-building-fill me-1"></i>Objekte</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="value"><?= $stats['einheiten_vermietet'] ?> / <?= $stats['einheiten_gesamt'] ?></div>
            <div class="label"><i class="bi bi-door-open-fill me-1"></i>Einheiten vermietet</div>
            <div class="progress mt-2" style="height:6px">
                <?php $pct = $stats['einheiten_gesamt'] > 0 ? round($stats['einheiten_vermietet'] / $stats['einheiten_gesamt'] * 100) : 0 ?>
                <div class="progress-bar bg-success" style="width:<?= $pct ?>%"></div>
            </div>
            <small class="text-muted"><?= $pct ?>% Auslastung</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="value <?= $stats['zahlungen_offen'] > 0 ? 'text-warning' : 'text-success' ?>">
                <?= $stats['zahlungen_offen'] ?>
            </div>
            <div class="label"><i class="bi bi-cash-coin me-1"></i>Offene Zahlungen</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="value <?= $stats['rechnungen_offen'] > 0 ? 'text-danger' : 'text-success' ?>">
                <?= $stats['rechnungen_offen'] ?>
            </div>
            <div class="label"><i class="bi bi-receipt me-1"></i>Offene Rechnungen</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Einnahmen-Chart -->
    <div class="col-lg-7">
        <div class="card border-0">
            <div class="card-header border-bottom-0 pt-3 bg-primary">
                <h6 class="fw-semibold mb-0"><i class="bi bi-bar-chart-fill me-2"></i>Einnahmen <?= date('Y') ?></h6>
            </div>
            <div class="card-body">
                <canvas id="einnahmenChart" height="120"></canvas>
            </div>
        </div>
    </div>

    <!-- Auslaufende Verträge -->
    <div class="col-lg-5">
        <div class="card border-0  h-100">
            <div class="card-header border-bottom-0 pt-3 bg-primary">
                <h6 class="fw-semibold mb-0"><i class="bi bi-calendar-x  me-2"></i>Auslaufende Verträge (90 Tage)</h6>
            </div>
            <div class="card-body p-0">
                <?php if (empty($auslaufend)): ?>
                    <p class="text-muted p-3 mb-0"><i class="bi bi-check-circle me-1"></i>Keine auslaufenden Verträge.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($auslaufend as $v): ?>
                            <li class="list-group-item px-3 py-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-medium"><?= esc($v['mieter_name']) ?></div>
                                        <small class="text-muted"><?= esc($v['objekt_bezeichnung']) ?> – <?= esc($v['einheit_bezeichnung']) ?></small>
                                    </div>
                                    <span class="badge bg-warning "><?= date('d.m.Y', strtotime($v['ende_datum'])) ?></span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Überfällige Rechnungen -->
    <?php if (! empty($ueberfaellig_rechnungen)): ?>
    <div class="col-12">
        <div class="card border-0 shadow-sm border-start border-danger border-4">
            <div class="card-header bg-white border-bottom-0 pt-3">
                <h6 class="fw-semibold mb-0 text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Überfällige Eingangsrechnungen</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Rechnungsnr.</th><th>Lieferant</th><th>Objekt / Einheit</th>
                            <th class="text-end">Brutto</th><th>Fällig</th><th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ueberfaellig_rechnungen as $r): ?>
                        <tr>
                            <td><code><?= esc($r['rechnungsnummer']) ?></code></td>
                            <td><?= esc($r['lieferant']) ?></td>
                            <td>
                                <?= esc($r['objekt_bezeichnung'] ?? '') ?>
                                <?php if ($r['einheit_bezeichnung']): ?>
                                    <small class="text-muted">/ <?= esc($r['einheit_bezeichnung']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td class="text-end fw-semibold"><?= number_format($r['bruttobetrag'], 2, ',', '.') ?> €</td>
                            <td><span class="text-danger"><?= date('d.m.Y', strtotime($r['faellig_datum'])) ?></span></td>
                            <td>
                                <a href="<?= base_url("eingangsrechnungen/{$r['id']}") ?>" class="btn btn-sm btn-outline-primary">Details</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const monate = ['Jan','Feb','Mär','Apr','Mai','Jun','Jul','Aug','Sep','Okt','Nov','Dez'];
const einnahmenData = <?= json_encode($einnahmen) ?>;

// Arrys mit 12 Monaten (0 = kein Wert)
const labels  = monate;
const miete   = new Array(12).fill(0);
const nk      = new Array(12).fill(0);

einnahmenData.forEach(e => {
    const idx = parseInt(e.monat) - 1;
    miete[idx] = parseFloat(e.miete || 0);
    nk[idx]    = parseFloat(e.nebenkosten || 0);
});

new Chart(document.getElementById('einnahmenChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            { label: 'Kaltmiete', data: miete, backgroundColor: '#3b82f6' },
            { label: 'Nebenkosten', data: nk,  backgroundColor: '#93c5fd' },
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } },
        scales: {
            x: { stacked: true },
            y: { stacked: true, ticks: { callback: v => v.toLocaleString('de') + ' €' } }
        }
    }
});
</script>
<?= $this->endSection() ?>
