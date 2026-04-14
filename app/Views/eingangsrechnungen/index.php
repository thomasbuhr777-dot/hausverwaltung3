<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between mb-4">
    <div>
        <?php if (!empty($ueberfaellig)): ?>
        <span class="badge bg-danger fs-6">
            <i class="bi bi-exclamation-circle me-1"></i>
            <?= count($ueberfaellig) ?> überfällige Rechnung(en)
        </span>
        <?php endif; ?>
    </div>
    <a href="<?= base_url('eingangsrechnungen/neu') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Neue Rechnung
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Rechnungsnr.</th>
                    <th>Lieferant</th>
                    <th>Zuweisung</th>
                    <th>Kategorie</th>
                    <th class="text-end">Netto</th>
                    <th class="text-end">MwSt.</th>
                    <th class="text-end">Brutto</th>
                    <th>Datum</th>
                    <th>Fällig</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rechnungen)): ?>
                <tr><td colspan="11" class="text-center text-muted py-4">Keine Eingangsrechnungen vorhanden.</td></tr>
                <?php else: ?>
                <?php foreach ($rechnungen as $r): ?>
                <tr>
                    <td><code><?= esc($r['rechnungsnummer']) ?></code></td>
                    <td><?= esc($r['lieferant']) ?></td>
                    <td>
                        <?php if ($r['objekt_bezeichnung'] && !$r['einheit_bezeichnung']): ?>
                            <span class="badge bg-info text-dark">Objekt</span>
                            <?= esc($r['objekt_bezeichnung']) ?>
                        <?php elseif ($r['einheit_bezeichnung']): ?>
                            <span class="badge bg-secondary">Einheit</span>
                            <?= esc($r['einheit_bezeichnung']) ?>
                            <small class="text-muted">/ <?= esc($r['objekt_bezeichnung'] ?? '') ?></small>
                        <?php endif; ?>
                    </td>
                    <td class="text-capitalize"><?= esc($r['kategorie']) ?></td>
                    <td class="text-end"><?= number_format($r['nettobetrag'], 2, ',', '.') ?> €</td>
                    <td class="text-end text-muted"><?= number_format($r['steuerbetrag'], 2, ',', '.') ?> €</td>
                    <td class="text-end fw-semibold"><?= number_format($r['bruttobetrag'], 2, ',', '.') ?> €</td>
                    <td><?= date('d.m.Y', strtotime($r['rechnungsdatum'])) ?></td>
                    <td>
                        <?php if ($r['faellig_datum']): ?>
                            <?php $ueberfaelligClass = ($r['status'] === 'offen' && $r['faellig_datum'] < date('Y-m-d')) ? 'text-danger fw-semibold' : '' ?>
                            <span class="<?= $ueberfaelligClass ?>"><?= date('d.m.Y', strtotime($r['faellig_datum'])) ?></span>
                        <?php else: ?>
                            <span class="text-muted">–</span>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge badge-status-<?= $r['status'] ?>"><?= ucfirst($r['status']) ?></span></td>
                    <td class="d-flex gap-1">
                        <a href="<?= base_url("eingangsrechnungen/{$r['id']}") ?>" class="btn btn-sm btn-outline-primary">Details</a>
                        <?php if ($r['status'] === 'offen'): ?>
                        <form method="post" action="<?= base_url("eingangsrechnungen/{$r['id']}/bezahlt") ?>">
                            <?= csrf_field() ?>
                            <button class="btn btn-sm btn-success" title="Als bezahlt markieren"><i class="bi bi-check-lg"></i></button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <?php if (!empty($rechnungen)): ?>
            <tfoot class="table-light fw-bold">
                <tr>
                    <td colspan="4">Summe</td>
                    <td class="text-end"><?= number_format(array_sum(array_column($rechnungen, 'nettobetrag')), 2, ',', '.') ?> €</td>
                    <td class="text-end"><?= number_format(array_sum(array_column($rechnungen, 'steuerbetrag')), 2, ',', '.') ?> €</td>
                    <td class="text-end"><?= number_format(array_sum(array_column($rechnungen, 'bruttobetrag')), 2, ',', '.') ?> €</td>
                    <td colspan="4"></td>
                </tr>
            </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
