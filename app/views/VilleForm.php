<?php
$pageTitle = ($title ?? 'Ajouter une ville') . ' - BNGRC';
$currentPage = 'villes';
$nomValue = '';
$nombreValue = 0;
$action = $action ?? '/villes';
$title = $title ?? 'Ajouter une ville';
$submitLabel = $submitLabel ?? 'Enregistrer';

if (!empty($ville)) {
  $nomValue = (string) $ville->nom;
  $nombreValue = (int) $ville->nombre_sinistres;
}

include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div class="d-flex align-items-center justify-content-between">
    <h1><i class="bi bi-geo-alt-fill text-danger"></i> <?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>
    <a class="btn btn-secondary" href="<?= BASE_URL ?>/villes">
      <i class="bi bi-arrow-left"></i> Retour
    </a>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <i class="bi bi-pencil-square"></i> Informations de la ville
  </div>
  <div class="card-body">
    <form method="post" action="<?php echo htmlspecialchars($action, ENT_QUOTES, 'UTF-8'); ?>">
      <div class="mb-3">
        <label class="form-label" for="nom">
          <i class="bi bi-geo-alt"></i> Nom de la ville *
        </label>
        <input class="form-control" id="nom" name="nom" type="text" required
          value="<?php echo htmlspecialchars($nomValue, ENT_QUOTES, 'UTF-8'); ?>"
          placeholder="Entrez le nom de la ville">
      </div>
      <div class="mb-3">
        <label class="form-label" for="nombre_sinistres">
          <i class="bi bi-exclamation-triangle"></i> Nombre de sinistres *
        </label>
        <input class="form-control" id="nombre_sinistres" name="nombre_sinistres" type="number" min="0" required
          value="<?php echo (int) $nombreValue; ?>" placeholder="Entrez le nombre de sinistres">
        <small class="form-text text-muted">
          Nombre de catastrophes ou situations d'urgence dans cette ville
        </small>
      </div>
      <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <a href="<?= BASE_URL ?>/villes" class="btn btn-secondary">
          <i class="bi bi-x-circle"></i> Annuler
        </a>
        <button class="btn btn-primary" type="submit">
          <i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($submitLabel, ENT_QUOTES, 'UTF-8'); ?>
        </button>
      </div>
    </form>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

