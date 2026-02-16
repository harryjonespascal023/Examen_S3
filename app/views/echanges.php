<?php
Flight::render('includes/header', ['titre' => 'Mes échanges']);
?>

<main class="py-5" style="min-height: calc(100vh - 200px);">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Propositions reçues</h5>
                        <?php if (empty($incoming)): ?>
                            <p class="text-muted">Aucune proposition reçue.</p>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach ($incoming as $e): ?>
                                    <div class="list-group-item">
                                        <div class="fw-semibold">
                                            <?= htmlspecialchars($e['demandeur']) ?> propose
                                            <?= htmlspecialchars($e['objet_propose']) ?>
                                            contre
                                            <?= htmlspecialchars($e['objet_demande']) ?>
                                        </div>
                                        <small class="text-muted">Demandé le <?= date('d M Y H:i', strtotime($e['date_demande'])) ?></small>

                                        <?php if ((int)$e['statut'] === 0): ?>
                                            <form action="/echange/decision" method="post" class="mt-2 d-flex gap-2">
                                                <input type="hidden" name="echange_id" value="<?= $e['id'] ?>">
                                                <button type="submit" name="action" value="accept" class="modern-btn modern-btn-primary border-0">
                                                    <i class="bi bi-check-circle me-1"></i> Accepter
                                                </button>
                                                <button type="submit" name="action" value="reject" class="modern-btn border-0">
                                                    <i class="bi bi-x-circle me-1"></i> Refuser
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="badge bg-<?= (int)$e['statut'] === 1 ? 'success' : 'secondary' ?> mt-2">
                                                <?= (int)$e['statut'] === 1 ? 'Acceptée' : 'Refusée' ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Mes propositions</h5>
                        <?php if (empty($outgoing)): ?>
                            <p class="text-muted">Aucune proposition envoyée.</p>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach ($outgoing as $e): ?>
                                    <div class="list-group-item">
                                        <div class="fw-semibold">
                                            Vous proposez <?= htmlspecialchars($e['objet_propose']) ?>
                                            à <?= htmlspecialchars($e['proprietaire']) ?>
                                            contre <?= htmlspecialchars($e['objet_demande']) ?>
                                        </div>
                                        <small class="text-muted">Envoyée le <?= date('d M Y H:i', strtotime($e['date_demande'])) ?></small>
                                        <div class="mt-2">
                                            <span class="badge bg-<?= (int)$e['statut'] === 0 ? 'warning' : ((int)$e['statut'] === 1 ? 'success' : 'secondary') ?>">
                                                <?= (int)$e['statut'] === 0 ? 'En attente' : ((int)$e['statut'] === 1 ? 'Acceptée' : 'Refusée') ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php Flight::render('includes/footer'); ?>
