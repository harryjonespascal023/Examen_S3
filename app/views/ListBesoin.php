<?php
$pageTitle = 'Liste des Besoins - BNGRC';
$currentPage = 'besoins';
include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div class="d-flex align-items-center justify-content-between">
    <h1><i class="bi bi-exclamation-triangle-fill text-warning"></i> Liste des Besoins</h1>
    <a class="btn btn-primary" href="/besoins/create">
      <i class="bi bi-plus-circle"></i> Ajouter un besoin
    </a>
  </div>
</div>

<?php if (empty($besoins)): ?>
  <div class="alert alert-info">
    <i class="bi bi-info-circle"></i> Aucun besoin enregistré.
  </div>
<?php else: ?>
  <div class="card">
    <div class="card-header">
      <i class="bi bi-list"></i> <?= count($besoins) ?> besoin(s) enregistré(s)
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped align-middle mb-0">
          <thead>
            <tr>
              <th scope="col"><i class="bi bi-hash"></i> ID</th>
              <th scope="col"><i class="bi bi-geo-alt"></i> Ville</th>
              <th scope="col"><i class="bi bi-tag"></i> Type</th>
              <th scope="col"><i class="bi bi-journal-text"></i> Libellé</th>
              <th scope="col"><i class="bi bi-currency-dollar"></i> Prix unitaire</th>
              <th scope="col"><i class="bi bi-box"></i> Quantité</th>
              <th scope="col"><i class="bi bi-boxes"></i> Restant</th>
              <th scope="col" class="text-end"><i class="bi bi-gear"></i> Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($besoins as $besoin):
              $pourcentage = $besoin->quantity > 0 ? (($besoin->quantity - $besoin->quantity_restante) / $besoin->quantity * 100) : 0;
              ?>
              <tr>
                <td><span class="badge bg-secondary"><?php echo (int) $besoin->id; ?></span></td>
                <td>
                  <i class="bi bi-geo-alt-fill text-danger"></i>
                  <?php echo htmlspecialchars((string) $besoin->ville_nom, ENT_QUOTES, 'UTF-8'); ?>
                </td>
                <td><span
                    class="badge bg-info"><?php echo htmlspecialchars((string) $besoin->type_libelle, ENT_QUOTES, 'UTF-8'); ?></span>
                </td>
                <td><strong><?php echo htmlspecialchars((string) ($besoin->libelle ?? ''), ENT_QUOTES, 'UTF-8'); ?></strong>
                </td>
                <td><?php echo number_format((float) $besoin->prix_unitaire, 2, ',', ' '); ?> Ar</td>
                <td><span class="badge bg-success"><?php echo (int) $besoin->quantity; ?></span></td>
                <td>
                  <?php if ($besoin->quantity_restante == 0): ?>
                    <span class="badge bg-secondary">Satisfait</span>
                  <?php elseif ($besoin->quantity_restante < $besoin->quantity): ?>
                    <span class="badge bg-warning text-dark"><?php echo (int) $besoin->quantity_restante; ?>
                      (<?= round($pourcentage) ?>%)</span>
                  <?php else: ?>
                    <span class="badge bg-danger"><?php echo (int) $besoin->quantity_restante; ?></span>
                  <?php endif; ?>
                </td>
                <td class="text-end">
                  <a class="btn btn-sm btn-info" href="/besoins/<?php echo (int) $besoin->id; ?>/edit">
                    <i class="bi bi-pencil"></i> Modifier
                  </a>
                  <form class="d-inline" method="post" action="/besoins/<?php echo (int) $besoin->id; ?>/delete">
                    <button class="btn btn-sm btn-danger" type="submit">
                      <i class="bi bi-trash"></i> Supprimer
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>

