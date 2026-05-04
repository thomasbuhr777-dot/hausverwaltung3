<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Beleg-Upload - BelegFix</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; }
        .upload-area { 
            border: 2px dashed #dee2e6; 
            border-radius: 1rem; 
            padding: 3rem; 
            text-align: center;
            background: white;
            transition: all 0.3s;
        }
        .upload-area:hover { border-color: #0d6efd; background: #f8f9ff; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="text-center mb-4 font-monospace fw-bold">Beleg<span class="text-primary">Fix</span></h2>
            
            <?php if(session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <div class="upload-area shadow-sm">
                <form action="<?= site_url('belege/analyze') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-cloud-upload text-primary mb-3" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M4.406 1.342A5.53 5.53 0 0 1 8 0c2.69 0 4.923 2 5.166 4.579C14.758 4.804 16 6.137 16 7.773 16 9.569 14.502 11 12.687 11H10a.5.5 0 0 1 0-1h2.688C13.979 10 15 8.988 15 7.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 2.825 10.328 1 8 1a4.53 4.53 0 0 0-2.941 1.1c-.757.652-1.153 1.438-1.153 2.055v.448l-.445.049C2.064 4.805 1 5.952 1 7.318 1 8.785 2.23 10 3.781 10H6a.5.5 0 0 1 0 1H3.781C1.708 11 0 9.366 0 7.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383z"/>
                            <path fill-rule="evenodd" d="M7.646 4.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 5.707V14.5a.5.5 0 0 1-1 0V5.707L5.354 7.854a.5.5 0 1 1-.708-.708l3-3z"/>
                        </svg>
                        <h5>Datei auswählen</h5>
                        <p class="text-muted small">Unterstützt JPG, PNG und PDF</p>
                    </div>
                    <input type="file" name="beleg" class="form-control mb-3" required accept=".pdf,image/*">
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Analyse starten</button>
                </form>
            </div>
            <p class="text-center mt-4 text-muted small">Powered by AI Studio & Gemini 1.5 Flash</p>
        </div>
    </div>
</div>
</body>
</html>