<?php

if (!isset($villes)) {
  $villes = [];
}
if (!isset($totaux)) {
  $totaux = [
    'total_recus' => 0,
    'total_attribues' => 0,
    'total_reste' => 0,
  ];
}

?>

<?php include __DIR__ . '/includes/header.php'; ?>

<?php if (isset($_GET['message']) && $_GET['message']): ?>
  <div class="alert alert-<?= htmlspecialchars($_GET['type'] ?? 'info') ?> alert-dismissible fade show" role="alert">
    <strong><?= ($_GET['type'] ?? 'info') === 'success' ? 'Succès!' : 'Erreur!' ?></strong>
    <?= htmlspecialchars($_GET['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<div class="page-header mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h1 class="display-5 fw-bold">
        <i class="bi bi-speedometer2"></i> Tableau de bord
      </h1>
      <p class="lead text-muted">Vue d'ensemble des dons et besoins par ville</p>
    </div>
    <div>
      <form method="POST" action="<?= BASE_URL ?>/reinitialiser"
        onsubmit="return confirm('⚠️ ATTENTION : Cette action va supprimer TOUS les dispatches et réinitialiser TOUTES les quantités restantes. Cette opération est IRRÉVERSIBLE. Voulez-vous vraiment continuer ?');">
        <button type="submit" class="btn btn-danger btn-lg">
          <i class="bi bi-arrow-clockwise"></i> Réinitialiser
        </button>
      </form>
    </div>
  </div>
</div>

<div class="container-fluid">
  <!-- Cartes de statistiques globales -->
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card border-0 shadow-sm h-100"
        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <div class="card-body text-center">
          <i class="bi bi-inbox-fill" style="font-size: 2.5rem;"></i>
          <div class="mt-2">
            <div class="text-white-50 small">Total dons reçus</div>
            <div class="fs-3 fw-bold"><?= (int) $totaux['total_recus'] ?></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm h-100"
        style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
        <div class="card-body text-center">
          <i class="bi bi-check-circle-fill" style="font-size: 2.5rem;"></i>
          <div class="mt-2">
            <div class="text-white-50 small">Total dons attribués</div>
            <div class="fs-3 fw-bold"><?= (int) $totaux['total_attribues'] ?></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm h-100"
        style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
        <div class="card-body text-center">
          <i class="bi bi-box-seam-fill" style="font-size: 2.5rem;"></i>
          <div class="mt-2">
            <div class="text-white-50 small">Total dons restants</div>
            <div class="fs-3 fw-bold"><?= (int) $totaux['total_reste'] ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Besoins et dons par ville -->
  <?php foreach ($villes as $ville): ?>
    <div class="card shadow-sm border-0 mb-4">
      <div class="card-header text-white" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
        <h4 class="mb-0">
          <i class="bi bi-geo-alt-fill"></i> <?= htmlspecialchars($ville['nom'], ENT_QUOTES) ?>
          <small class="ms-3 opacity-75">(<?= $ville['nombre_sinistres'] ?> sinistrés)</small>
        </h4>
      </div>
      <div class="card-body">
        <div class="row">
          <!-- Besoins -->
          <div class="col-md-6">
            <h5 class="text-primary mb-3">
              <i class="bi bi-exclamation-triangle-fill"></i> Besoins
            </h5>
            <?php if (empty($ville['besoins'])): ?>
              <p class="text-muted">Aucun besoin enregistré</p>
            <?php else: ?>
              <div class="list-group">
                <?php foreach ($ville['besoins'] as $besoin): ?>
                  <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between align-items-center">
                      <h6 class="mb-1">
                        <span class="badge bg-info"><?= htmlspecialchars($besoin['type_libelle'], ENT_QUOTES) ?></span>
                        <?= htmlspecialchars($besoin['libelle'] ?? '-', ENT_QUOTES) ?>
                      </h6>
                      <small
                        class="text-muted"><?= $besoin['prix_unitaire'] !== null ? number_format((float) $besoin['prix_unitaire'], 2) . ' Ar' : '-' ?></small>
                    </div>
                    <div class="progress mt-2" style="height: 20px;">
                      <?php
                      $total = (int) $besoin['quantity'];
                      $restant = (int) $besoin['quantity_restante'];
                      $satisfait = $total - $restant;
                      $pourcentage = $total > 0 ? ($satisfait / $total * 100) : 0;
                      ?>
                      <div class="progress-bar bg-success" role="progressbar" style="width: <?= $pourcentage ?>%"
                        aria-valuenow="<?= $pourcentage ?>" aria-valuemin="0" aria-valuemax="100">
                        <?= round($pourcentage) ?>%
                      </div>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                      <small class="text-success"><i class="bi bi-check-circle"></i> <?= $satisfait ?> / <?= $total ?></small>
                      <small class="text-danger"><i class="bi bi-x-circle"></i> Restant: <?= $restant ?></small>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>

          <!-- Dons -->
          <div class="col-md-6">
            <h5 class="text-success mb-3">
              <i class="bi bi-heart-fill"></i> Dons
            </h5>
            <?php if (empty($ville['dons'])): ?>
              <p class="text-muted">Aucun don enregistré</p>
            <?php else: ?>
              <div class="list-group">
                <?php foreach ($ville['dons'] as $don): ?>
                  <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between align-items-center">
                      <h6 class="mb-1">
                        <span class="badge bg-success"><?= htmlspecialchars($don['type_libelle'], ENT_QUOTES) ?></span>
                        <?= htmlspecialchars($don['besoin_libelle'] ?? '-', ENT_QUOTES) ?>
                      </h6>
                      <small class="text-muted"><?= date('d/m/Y', strtotime($don['date_saisie'])) ?></small>
                    </div>
                    <div class="progress mt-2" style="height: 20px;">
                      <?php
                      $total = (int) $don['quantity'];
                      $restant = (int) $don['quantity_restante'];
                      $utilise = $total - $restant;
                      $pourcentage = $total > 0 ? ($utilise / $total * 100) : 0;
                      ?>
                      <div class="progress-bar bg-info" role="progressbar" style="width: <?= $pourcentage ?>%"
                        aria-valuenow="<?= $pourcentage ?>" aria-valuemin="0" aria-valuemax="100">
                        <?= round($pourcentage) ?>%
                      </div>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                      <small class="text-info"><i class="bi bi-arrow-right-circle"></i> Utilisé: <?= $utilise ?> /
                        <?= $total ?></small>
                      <small class="text-primary"><i class="bi bi-box"></i> Disponible: <?= $restant ?></small>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>

  <?php if (empty($villes)): ?>
    <div class="alert alert-info">
      <i class="bi bi-info-circle"></i> Aucune donnée disponible
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

