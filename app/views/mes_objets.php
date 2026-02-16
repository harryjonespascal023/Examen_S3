<?php
Flight::render('includes/header', ['titre' => 'Mes objets']);
$categories = (new \app\repository\ObjetRepository(Flight::db()))->getAllCategories();
?>

<main class="py-5" style="min-height: calc(100vh - 200px);">
  <div class="container">

    <div class="modern-card mb-5">
      <div class="card-body p-4">
        <h4 class="fw-bold mb-4">
          <i class="bi bi-plus-circle me-2"></i>
          Ajouter un nouvel objet
        </h4>

        <form action="/object/add" method="post" enctype="multipart/form-data" class="row g-3">

          <div class="col-md-4">
            <input type="text" name="nom_objet" class="form-control modern-input" placeholder="Nom de l'objet" required>
          </div>

          <div class="col-md-4">
            <input type="number" name="prix" class="form-control modern-input" placeholder="Prix estimatif" min="0"
              step="0.01" required>
          </div>

          <div class="col-md-4">
            <select name="categorie_id" class="form-select modern-select" required>
              <option value="">Catégorie</option>
              <?php foreach ($categories as $categorie): ?>
                <option value="<?= $categorie['id'] ?>">
                  <?= htmlspecialchars($categorie['libelle']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-6">
            <input type="file" name="image[]" class="form-control modern-input" accept="image/*" multiple>
            <small class="text-muted">
              Plusieurs images possibles
            </small>
          </div>

          <div class="col-12">
            <label for="description" class="form-label fw-semibold">
              Description
            </label>
            <textarea name="description" id="description" class="form-control modern-input" rows="5"
              placeholder="Décrivez votre objet (état, détails, informations importantes...)" style="resize: none;"
              required></textarea>
          </div>

          <div class="col-md-2 d-grid">
            <button type="submit" class="modern-btn modern-btn-primary border-0">
              <i class="bi bi-plus-circle me-1"></i> Publier
            </button>
          </div>

        </form>
      </div>
    </div>

    <h3 class="fw-bold mb-4 gradient-text">Mes objets</h3>

    <?php if (empty($objets)): ?>
      <div class="text-center py-5">
        <i class="bi bi-box fs-1 text-muted"></i>
        <p class="mt-3 text-muted">Vous n'avez pas encore d'objets.</p>
      </div>
    <?php else: ?>
      <div class="row g-4">
        <?php foreach ($objets as $objet): ?>
          <div class="col-md-4 col-lg-3">
            <div class="modern-card h-100 hover-lift">
              <div class="position-relative">
                <?php if (!empty($objet['image']['fichier'])): ?>
                  <img src="<?= BASE_URL ?>/assets/uploads/<?= htmlspecialchars($objet['image']['fichier']) ?>"
                    class="card-img-top" style="height:220px; object-fit:cover;" alt="objet">
                <?php else: ?>
                  <img src="<?= BASE_URL ?>/assets/images/avatar-placeholder.svg" class="card-img-top"
                    style="height:220px; object-fit:cover;" alt="objet">
                <?php endif; ?>
              </div>
              <div class="card-body d-flex flex-column p-3">
                <h6 class="fw-bold mb-1">
                  <?= htmlspecialchars($objet['libelle']) ?>
                </h6>
                <small class="text-muted mb-2">
                  <?= htmlspecialchars($objet['category']) ?>
                </small>
                <div class="mt-auto d-flex justify-content-between align-items-center">
                  <div class="fw-bold fs-6 text-dark">
                    <?= number_format($objet['prix'], 0, ',', ' ') ?> Ar
                  </div>
                  <form action="/object/delete" method="post" onsubmit="return confirm('Supprimer cet objet ?');">
                    <input type="hidden" name="objet_id" value="<?= $objet['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </div>

                <div class="mt-2 d-flex justify-content-between align-items-center">
                  <a href="/objets-similaires/<?= $objet['id'] ?>?percentage=10"
                    class="btn btn-sm btn-outline-primary flex-fill me-1">
                    <i class="bi bi-percent"></i> +/- 10%
                  </a>
                  <a href="/objets-similaires/<?= $objet['id'] ?>?percentage=20"
                    class="btn btn-sm btn-outline-primary flex-fill ms-1">
                    <i class="bi bi-percent"></i> +/- 20%
                  </a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </div>
</main>

<?php Flight::render('includes/footer'); ?>

