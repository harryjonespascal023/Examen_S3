<?php
Flight::render('includes/header', ['titre' => 'Fiche objet']);
?>

<main class="py-5" style="min-height: calc(100vh - 200px);">
    <div class="container">

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="modern-card">
                    <div class="card-body p-4">
                        <h3 class="fw-bold mb-1"><?= htmlspecialchars($objet['libelle']) ?></h3>
                        <div class="text-muted mb-3">
                            <?= htmlspecialchars($objet['category']) ?> · Par <?= htmlspecialchars($objet['users']) ?>
                        </div>
                        <div class="row g-2 mb-3">
                            <?php if (!empty($objet['images'])): ?>
                                <?php foreach ($objet['images'] as $img): ?>
                                    <div class="col-6 col-md-4">
                                        <img src="<?= BASE_URL ?>/assets/uploads/<?= htmlspecialchars($img['fichier']) ?>"
                                             class="img-fluid rounded" alt="image">
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-12">
                                    <img src="<?= BASE_URL ?>/assets/images/avatar-placeholder.svg"
                                         class="img-fluid rounded" alt="image">
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="fs-4 fw-bold text-dark mb-3">
                            <?= number_format($objet['prix'], 0, ',', ' ') ?> Ar
                        </div>

                        <p class="mb-0">
                            <?= nl2br(htmlspecialchars($objet['description'])) ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="modern-card mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Proposer un échange</h5>

                        <?php if (empty($_SESSION['user_id'])): ?>
                            <p class="text-muted">Connectez-vous pour proposer un échange.</p>
                            <a href="/login" class="btn btn-outline-primary">Se connecter</a>
                        <?php elseif ((int)$_SESSION['user_id'] === (int)$objet['user_id']): ?>
                            <p class="text-muted">Vous êtes le propriétaire de cet objet.</p>
                        <?php elseif (empty($mesObjets)): ?>
                            <p class="text-muted">Ajoutez d’abord un objet pour proposer un échange.</p>
                            <a href="/mes-objets" class="btn btn-outline-primary">Ajouter un objet</a>
                        <?php else: ?>
                            <form action="/echange/proposer" method="post">
                                <input type="hidden" name="objet_demande_id" value="<?= $objet['id'] ?>">
                                <div class="mb-3">
                                    <label for="objet_propose_id" class="form-label fw-semibold">Choisir votre objet</label>
                                    <select name="objet_propose_id" id="objet_propose_id" class="form-select modern-select" required>
                                        <option value="">Sélectionner</option>
                                        <?php foreach ($mesObjets as $o): ?>
                                            <option value="<?= $o['id'] ?>">
                                                <?= htmlspecialchars($o['libelle']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" class="modern-btn modern-btn-primary w-100 border-0">
                                    <i class="bi bi-arrow-left-right me-1"></i> Envoyer la proposition
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Historique d’appartenance</h5>
                        <?php if (empty($historique)): ?>
                            <p class="text-muted">Aucun historique disponible.</p>
                        <?php else: ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($historique as $h): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><?= htmlspecialchars($h['username']) ?></span>
                                        <small class="text-muted">
                                            <?= date('d M Y H:i', strtotime($h['date_changement'])) ?>
                                        </small>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<?php Flight::render('includes/footer'); ?>
