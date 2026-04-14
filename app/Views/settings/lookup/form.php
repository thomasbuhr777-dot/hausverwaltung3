<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
    $isEdit = isset($item['id']);
    $errors = session('errors') ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0"><?= esc($title) ?></h1>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">

                <?php if (! empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($errors as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post"
                      action="<?= $isEdit
                          ? base_url("settings/lookup/{$table}/update/{$item['id']}")
                          : base_url("settings/lookup/{$table}/store") ?>">

                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="bezeichnung" class="form-label fw-semibold">Bezeichnung</label>
                        <input type="text"
                               id="bezeichnung"
                               name="bezeichnung"
                               class="form-control<?= isset($errors['bezeichnung']) ? ' is-invalid' : '' ?>"
                               value="<?= esc(old('bezeichnung', $item['bezeichnung'] ?? '')) ?>"
                               maxlength="100"
                               required
                               autofocus>

                        <?php if (isset($errors['bezeichnung'])): ?>
                            <div class="invalid-feedback">
                                <?= esc($errors['bezeichnung']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($isEdit): ?>
                        <div class="row g-3 mb-2">
                            <div class="col-md-6">
                                <div class="small text-muted">
                                    Status:
                                    <?php if ((int) ($item['aktiv'] ?? 0) === 1): ?>
                                        <span class="badge text-bg-success">Aktiv</span>
                                    <?php else: ?>
                                        <span class="badge text-bg-secondary">Inaktiv</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <div class="small text-muted">
                                    Sortierung: <?= (int) ($item['sortierung'] ?? 0) ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url("settings/lookup/{$table}") ?>" class="btn btn-outline-secondary">
                            Abbrechen
                        </a>

                        <button type="submit" class="btn btn-primary">
                            <i class="fa-light fa-check me-1"></i>
                            <?= $isEdit ? 'Änderungen speichern' : 'Speichern' ?>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>