<?php
Flight::render('includes/header', ['titre' => 'Statistiques']);
?>

<main class="py-5" style="min-height: calc(100vh - 200px);">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="modern-card hover-lift">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-people fs-1 me-3 text-primary"></i>
                            <div>
                                <div class="text-muted">Utilisateurs inscrits</div>
                                <div class="fs-3 fw-bold"><?= (int)$totalUsers ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="modern-card hover-lift">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-arrow-left-right fs-1 me-3 text-success"></i>
                            <div>
                                <div class="text-muted">Échanges effectués</div>
                                <div class="fs-3 fw-bold"><?= (int)$totalEchanges ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php Flight::render('includes/footer'); ?>
