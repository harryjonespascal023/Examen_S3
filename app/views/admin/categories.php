<?php
Flight::render('includes/header', ['titre' => 'Catégories']);
?>

<main class="py-5" style="min-height: calc(100vh - 200px);">
    <div class="container">
        <div class="modern-card mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">Ajouter une catégorie</h5>
                <form action="/admin/categories/add" method="post" class="d-flex gap-2">
                    <input type="text" name="libelle" class="form-control modern-input" placeholder="Nom de la catégorie" required>
                    <button type="submit" class="modern-btn modern-btn-primary border-0"><i class="bi bi-plus-circle me-1"></i> Ajouter</button>
                </form>
            </div>
        </div>

        <div class="modern-card">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">Liste des catégories</h5>
                <?php if (empty($categories)): ?>
                    <p class="text-muted">Aucune catégorie.</p>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($categories as $cat): ?>
                            <div class="list-group-item d-flex align-items-center justify-content-between">
                                <form action="/admin/categories/update" method="post" class="d-flex align-items-center gap-2 flex-grow-1 me-2">
                                    <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                    <input type="text" name="libelle" class="form-control modern-input" value="<?= htmlspecialchars($cat['libelle']) ?>" required>
                                    <button type="submit" class="modern-btn"><i class="bi bi-pencil me-1"></i> Modifier</button>
                                </form>
                                <form action="/admin/categories/delete" method="post" onsubmit="return confirm('Supprimer cette catégorie ?');">
                                    <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php Flight::render('includes/footer'); ?>
