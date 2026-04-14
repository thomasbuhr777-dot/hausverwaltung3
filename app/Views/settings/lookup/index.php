<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0"><?= esc($title) ?></h1>

    <a href="<?= base_url("settings/lookup/{$table}/create") ?>" class="btn btn-primary btn-sm">
        <i class="fa-light fa-plus me-1"></i> Neu
    </a>
</div>

<div class="d-flex flex-wrap gap-2 mb-3">
    <a href="<?= base_url("settings/lookup/{$table}?status=all") ?>"
       class="btn btn-sm <?= $status === 'all' ? 'btn-primary' : 'btn-outline-primary' ?>">
        Alle <span class="badge text-bg-light ms-1"><?= (int) ($stats['all'] ?? 0) ?></span>
    </a>

    <a href="<?= base_url("settings/lookup/{$table}?status=active") ?>"
       class="btn btn-sm <?= $status === 'active' ? 'btn-success' : 'btn-outline-success' ?>">
        Aktiv <span class="badge text-bg-light ms-1"><?= (int) ($stats['active'] ?? 0) ?></span>
    </a>

    <a href="<?= base_url("settings/lookup/{$table}?status=inactive") ?>"
       class="btn btn-sm <?= $status === 'inactive' ? 'btn-secondary' : 'btn-outline-secondary' ?>">
        Inaktiv <span class="badge text-bg-light ms-1"><?= (int) ($stats['inactive'] ?? 0) ?></span>
    </a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

<?php $errors = session()->getFlashdata('errors'); ?>
<?php if (! empty($errors)): ?>
    <div class="alert alert-danger">
        <?php if (is_array($errors)): ?>
            <ul class="mb-0 ps-3">
                <?php foreach ($errors as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <?= esc((string) $errors) ?>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if (empty($items)): ?>
    <div class="alert alert-light border">
        Keine Einträge vorhanden.
    </div>
<?php else: ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="lookupSortTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">Sort.</th>
                            <th>Bezeichnung</th>
                            <th style="width: 120px;">Status</th>
                            <th class="text-end" style="width: 170px;">Aktionen</th>
                        </tr>
                    </thead>
                    <tbody id="lookup-sortable-body">
                        <?php foreach ($items as $item): ?>
                            <tr draggable="true" data-id="<?= (int) $item['id'] ?>">
                                <td class="text-muted">
                                    <span class="drag-handle" title="Ziehen zum Sortieren" style="cursor:grab;">
                                        <i class="fa-light fa-grip-dots-vertical"></i>
                                    </span>
                                    <span class="ms-2"><?= (int) ($item['sortierung'] ?? 0) ?></span>
                                </td>
                                <td><?= esc($item['bezeichnung']) ?></td>
                                <td>
                                    <?php if ((int) ($item['aktiv'] ?? 0) === 1): ?>
                                        <span class="badge text-bg-success">Aktiv</span>
                                    <?php else: ?>
                                        <span class="badge text-bg-secondary">Inaktiv</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a href="<?= base_url("settings/lookup/{$table}/edit/{$item['id']}") ?>"
                                       class="btn btn-sm btn-outline-secondary"
                                       title="Bearbeiten">
                                        <i class="fa-light fa-pen-to-square"></i>
                                    </a>

                                    <form method="post"
                                          action="<?= base_url("settings/lookup/{$table}/toggle/{$item['id']}") ?>"
                                          class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit"
                                                class="btn btn-sm <?= (int) ($item['aktiv'] ?? 0) === 1 ? 'btn-outline-warning' : 'btn-outline-success' ?>"
                                                title="<?= (int) ($item['aktiv'] ?? 0) === 1 ? 'Deaktivieren' : 'Aktivieren' ?>">
                                            <i class="fa-light <?= (int) ($item['aktiv'] ?? 0) === 1 ? 'fa-toggle-off' : 'fa-toggle-on' ?>"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="small text-muted mt-2">
        Reihenfolge per Drag & Drop ändern. Die Sortierung wird automatisch gespeichert.
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.getElementById('lookup-sortable-body');
    if (!tbody) {
        return;
    }

    let draggedRow = null;
    let csrfName = '<?= csrf_token() ?>';
    let csrfHash = '<?= csrf_hash() ?>';

    function getDragAfterElement(container, y) {
        const rows = [...container.querySelectorAll('tr:not(.dragging)')];

        return rows.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;

            if (offset < 0 && offset > closest.offset) {
                return { offset, element: child };
            }

            return closest;
        }, { offset: Number.NEGATIVE_INFINITY, element: null }).element;
    }

    async function persistSortOrder() {
        const ids = [...tbody.querySelectorAll('tr[data-id]')].map(row => row.dataset.id);

        const body = new URLSearchParams();
        body.append(csrfName, csrfHash);
        ids.forEach(id => body.append('ids[]', id));

        try {
            const response = await fetch('<?= base_url("settings/lookup/{$table}/sort") ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                },
                body: body.toString()
            });

            const data = await response.json();

            if (data.csrfName && data.csrfHash) {
                csrfName = data.csrfName;
                csrfHash = data.csrfHash;
            }

            if (!response.ok || !data.success) {
                console.error(data.message || 'Sortierung konnte nicht gespeichert werden.');
            }
        } catch (error) {
            console.error('Fehler beim Speichern der Sortierung:', error);
        }
    }

    tbody.querySelectorAll('tr').forEach(row => {
        row.addEventListener('dragstart', () => {
            draggedRow = row;
            row.classList.add('dragging');
        });

        row.addEventListener('dragend', async () => {
            row.classList.remove('dragging');
            draggedRow = null;
            await persistSortOrder();
        });
    });

    tbody.addEventListener('dragover', (event) => {
        event.preventDefault();

        if (!draggedRow) {
            return;
        }

        const afterElement = getDragAfterElement(tbody, event.clientY);

        if (afterElement == null) {
            tbody.appendChild(draggedRow);
        } else {
            tbody.insertBefore(draggedRow, afterElement);
        }
    });
});
</script>

<?= $this->endSection() ?>