<?php
Flight::render('includes/header', ['titre' => 'Publications']);
?>

<main class="py-5" style="min-height: calc(100vh - 200px);">
  <div class="container">

    <!-- ================= LISTE OBJETS ================= -->

    <?php if (empty($objets)): ?>

      <div class="text-center py-5">
        <i class="bi bi-box fs-1 text-muted"></i>
        <p class="mt-3 text-muted">Aucune publication pour le moment.</p>
      </div>

    <?php else: ?>

      <div class="row g-4 d-flex flex-wrap">
        <?php foreach ($objets as $objet): ?>
          <div class="col-md-4 col-lg-3">
            <a href="/objet/<?= $objet['id'] ?>" class="text-decoration-none">
              <div class="modern-card h-100 hover-lift">

                <!-- Image -->
                <div class="position-relative">
                  <?php if (!empty($objet['image']['fichier'])): ?>
                    <img src="<?= BASE_URL ?>/assets/uploads/<?= htmlspecialchars($objet['image']['fichier']) ?>"
                      class="card-img-top" style="height:220px; object-fit:cover;" alt="objet">
                  <?php else: ?>
                    <img src="<?= BASE_URL ?>/assets/images/avatar-placeholder.svg" class="card-img-top"
                      style="height:220px; object-fit:cover;" alt="objet">
                  <?php endif; ?>

                  <span class="badge-modern position-absolute top-0 end-0 m-2 bg-dark" style="color: #ce12af;">
                    <?= htmlspecialchars($objet['category']) ?>
                  </span>
                </div>

                <!-- Contenu -->
                <div class="card-body d-flex flex-column p-3">

                  <h6 class="fw-bold mb-1" style="color: #ce12af;">
                    <?= htmlspecialchars($objet['libelle']) ?>
                  </h6>

                  <small class="text-muted mb-2">
                    Par <?= htmlspecialchars($objet['users']) ?>
                  </small>

                  <div class="mt-auto">
                    <div class="fw-bold fs-5 text-dark">
                      <?= number_format($objet['prix'], 0, ',', ' ') ?> Ar
                    </div>
                    <small class="text-muted">
                      <?= date('d M Y', strtotime($objet['date_publication'])) ?>
                    </small>
                  </div>

                </div>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>

    <?php endif; ?>

  </div>
</main>

<?php Flight::render('includes/footer'); ?>

