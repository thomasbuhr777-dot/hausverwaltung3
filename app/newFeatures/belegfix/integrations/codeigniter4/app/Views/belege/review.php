<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beleg Review - BelegFix CI4</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .review-card { border-radius: 15px; border: none; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .input-group-text { background-color: transparent; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card review-card">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h5 class="mb-0 text-primary">Human-in-the-Loop: Review & Freigabe</h5>
                </div>
                <div class="card-body p-4">
                    <form action="<?= site_url('belege/store') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <!-- Hidden Reference -->
                        <input type="hidden" name="filename" value="<?= $filename ?>">

                        <div class="row g-4">
                            <!-- Linke Seite: Formular -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">VERKÄUFER</label>
                                    <input type="text" name="vendor" class="form-control" value="<?= esc($data['vendor'] ?? '') ?>" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small fw-bold text-muted">RECHNUNGS-NR.</label>
                                        <input type="text" name="invoice_number" class="form-control" value="<?= esc($data['invoiceNumber'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small fw-bold text-muted">DATUM</label>
                                        <input type="date" name="date" class="form-control" value="<?= esc($data['date'] ?? '') ?>">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label small fw-bold text-muted">NETTO</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" name="net_amount" class="form-control" value="<?= esc($data['netAmount'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label small fw-bold text-muted">MWST</label>
                                        <input type="number" step="0.01" name="tax_amount" class="form-control" value="<?= esc($data['taxAmount'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label small fw-bold text-muted">BRUTTO</label>
                                        <input type="number" step="0.01" name="total_amount" class="form-control fw-bold border-primary" value="<?= esc($data['totalAmount'] ?? '') ?>">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">IBAN (ZAHLUNGSEMPFÄNGER)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">IBAN</span>
                                        <input type="text" name="iban" class="form-control font-monospace" value="<?= esc($data['iban'] ?? '') ?>">
                                    </div>
                                    <div id="ibanFeedback" class="form-text">KI-Validierung: Prüfen Sie das Format manuell.</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">WÄHRUNG</label>
                                    <select name="currency" class="form-select">
                                        <option value="EUR" <?= ($data['currency'] ?? 'EUR') == 'EUR' ? 'selected' : '' ?>>EUR</option>
                                        <option value="USD" <?= ($data['currency'] ?? '') == 'USD' ? 'selected' : '' ?>>USD</option>
                                        <option value="CHF" <?= ($data['currency'] ?? '') == 'CHF' ? 'selected' : '' ?>>CHF</option>
                                    </select>
                                </div>

                                <hr class="my-4">

                                <button type="submit" class="btn btn-primary w-100 py-3 shadow-sm fw-bold">
                                    Freigeben & in Datenbank speichern
                                </button>
                                <a href="<?= site_url('belege') ?>" class="btn btn-link w-100 mt-2 text-muted">Abbrechen</a>
                            </div>

                            <!-- Rechte Seite: Vorschau (Falls Bild) -->
                            <div class="col-md-6 border-start d-flex flex-column align-items-center justify-content-center bg-light rounded-end">
                                <p class="text-muted small mb-2">DATEI-REFERENZ: <?= esc($filename) ?></p>
                                <div class="alert alert-info py-2 small">
                                    Datei wurde gesichert unter: <br>
                                    <code>WRITEPATH/uploads/belege/</code>
                                </div>
                                <div class="text-center opacity-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-file-earmark-text" viewBox="0 0 16 16">
                                        <path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z"/>
                                        <path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5L9.5 0zm0 1v3.5A1.5 1.5 0 0 0 11 6h3.5L9.5 1zM4 1h4.5v3.5A2.5 2.5 0 0 0 11 7h3.5v7a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
