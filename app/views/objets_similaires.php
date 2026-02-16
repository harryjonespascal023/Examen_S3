<?php
Flight::render('includes/header', ['titre' => 'Objets similaires']);
?>

<main class="py-5" style="min-height: calc(100vh - 200px);">
    <div class="container">

        <!-- Objet de référence -->
        <div class="mb-4">
            <a href="/mes-objets" class="btn btn-outline-secondary mb-3">
                <i class="bi bi-arrow-left"></i> Retour à mes objets
            </a>

            <div class="modern-card">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-box-seam me-2"></i>
                        Votre objet de référence
                    </h5>
                    <div class="d-flex align-items-center">
                        <div class="fw-bold text-dark me-3">
                            <?= htmlspecialchars($objet_base['libelle']) ?>
                        </div>
                        <span class="badge bg-primary fs-6">
                            <?= number_format($objet_base['prix'], 0, ',', ' ') ?> Ar
                        </span>
                    </div>
                    <p class="text-muted mt-2 mb-0">
                        Affichage des objets avec un prix de ±<?= $pourcentage ?>%
                        (entre <?= number_format($objet_base['prix'] * (1 - $pourcentage/100), 0, ',', ' ') ?> Ar
                        et <?= number_format($objet_base['prix'] * (1 + $pourcentage/100), 0, ',', ' ') ?> Ar)
                    </p>
                </div>
            </div>
        </div>

        <h3 class="fw-bold mb-4 gradient-text">
            Objets disponibles pour l'échange (±<?= $pourcentage ?>%)
        </h3>

        <!-- Liste des objets similaires -->
        <?php if (empty($objets)): ?>
            <div class="text-center py-5">
                <i class="bi bi-search fs-1 text-muted"></i>
                <p class="mt-3 text-muted">Aucun objet trouvé dans cette fourchette de prix.</p>
                <a href="/mes-objets" class="btn btn-primary mt-3">
                    Retour à mes objets
                </a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($objets as $objet): ?>
                    <div class="col-md-4 col-lg-3">
                        <div class="modern-card h-100 hover-lift">

                            <!-- Image -->
                            <div class="position-relative">
                                <a href="/objet/<?= $objet['id'] ?>" class="text-decoration-none">
                                    <?php if (!empty($objet['image']['fichier'])): ?>
                                        <img src="<?= BASE_URL ?>/assets/uploads/<?= htmlspecialchars($objet['image']['fichier']) ?>"
                                             class="card-img-top"
                                             style="height:220px; object-fit:cover;"
                                             alt="objet">
                                    <?php else: ?>
                                        <img src="<?= BASE_URL ?>/assets/images/avatar-placeholder.svg"
                                             class="card-img-top"
                                             style="height:220px; object-fit:cover;"
                                             alt="objet">
                                    <?php endif; ?>
                                </a>

                                <span class="badge-modern position-absolute top-0 end-0 m-2">
                                    <?= htmlspecialchars($objet['category']) ?>
                                </span>

                                <!-- Badge de différence en % -->
                                <?php
                                    $diff = $objet['difference_pourcentage'];
                                    $badgeClass = $diff > 0 ? 'bg-success' : ($diff < 0 ? 'bg-danger' : 'bg-secondary');
                                    $diffText = $diff > 0 ? '+' . $diff : $diff;
                                ?>
                                <span class="badge <?= $badgeClass ?> position-absolute top-0 start-0 m-2">
                                    <?= $diffText ?>%
                                </span>
                            </div>

                            <!-- Contenu -->
                            <div class="card-body d-flex flex-column p-3">
                                <a href="/objet/<?= $objet['id'] ?>" class="text-decoration-none">
                                    <h6 class="fw-bold mb-1 text-dark">
                                        <?= htmlspecialchars($objet['libelle']) ?>
                                    </h6>
                                </a>

                                <small class="text-muted mb-2">
                                    Par <?= htmlspecialchars($objet['users']) ?>
                                </small>

                                <div class="mt-auto">
                                    <div class="fw-bold fs-5 text-dark mb-2">
                                        <?= number_format($objet['prix'], 0, ',', ' ') ?> Ar
                                    </div>
                                    <small class="text-muted d-block mb-3">
                                        <?= date('d M Y', strtotime($objet['date_publication'])) ?>
                                    </small>

                                    <!-- Bouton de proposition d'échange -->
                                    <form action="/echange/proposer" method="post" class="d-grid">
                                        <input type="hidden" name="objet_propose_id" value="<?= $objet_base['id'] ?>">
                                        <input type="hidden" name="objet_demande_id" value="<?= $objet['id'] ?>">
                                        <button type="submit" class="modern-btn modern-btn-primary border-0">
                                            <i class="bi bi-arrow-left-right me-1"></i>
                                            Proposer un échange
                                        </button>
                                    </form>
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
