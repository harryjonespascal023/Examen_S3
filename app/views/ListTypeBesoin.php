<?php
$pageTitle = 'Liste des Types de Besoins - BNGRC';
$currentPage = 'types';
include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div class="d-flex align-items-center justify-content-between">
    <h1><i class="bi bi-tag-fill text-info"></i> Liste des Types de Besoins</h1>
    <a class="btn btn-primary" href="/types-besoin/create">
      <i class="bi bi-plus-circle"></i> Ajouter un type
    </a>
  </div>
</div>

<?php if (empty($types)): ?>
  <div class="alert alert-info">
    <i class="bi bi-info-circle"></i> Aucun type de besoin enregistré.
  </div>
<?php else: ?>
  <div class="card">
    <div class="card-header">
      <i class="bi bi-list"></i> <?= count($types) ?> type(s) de besoin
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped align-middle mb-0">
          <thead>
            <tr>
              <th scope="col" style="width: 100px;"><i class="bi bi-hash"></i> ID</th>
              <th scope="col"><i class="bi bi-tag"></i> Libellé</th>
              <th scope="col" class="text-end" style="width: 250px;"><i class="bi bi-gear"></i> Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($types as $type): ?>
              <tr>
                <td><span class="badge bg-secondary"><?php echo (int) $type->id; ?></span></td>
                <td>
                  <i class="bi bi-tag-fill text-info"></i>
                  <strong><?php echo htmlspecialchars((string) $type->libelle, ENT_QUOTES, 'UTF-8'); ?></strong>
                </td>
                <td class="text-end">
                  <a class="btn btn-sm btn-info" href="/types-besoin/<?php echo (int) $type->id; ?>/edit">
                    <i class="bi bi-pencil"></i> Modifier
                  </a>
                  <form class="d-inline" method="post" action="/types-besoin/<?php echo (int) $type->id; ?>/delete">
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

