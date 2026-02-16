<?php
$pageTitle = ($title ?? 'Ajouter un type de besoin') . ' - BNGRC';
$currentPage = 'types';
$libelleValue = '';
$action = $action ?? '/types-besoin';
$title = $title ?? 'Ajouter un type de besoin';
$submitLabel = $submitLabel ?? 'Enregistrer';

if (!empty($typeBesoin)) {
  $libelleValue = (string) $typeBesoin->libelle;
}

include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div class="d-flex align-items-center justify-content-between">
    <h1><i class="bi bi-tag-fill text-info"></i> <?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>
    <a class="btn btn-secondary" href="/types-besoin">
      <i class="bi bi-arrow-left"></i> Retour
    </a>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <i class="bi bi-pencil-square"></i> Informations du type de besoin
  </div>
  <div class="card-body">
    <form method="post" action="<?php echo htmlspecialchars($action, ENT_QUOTES, 'UTF-8'); ?>">
      <div class="mb-3">
        <label class="form-label" for="libelle">
          <i class="bi bi-tag"></i> Libellé du type de besoin *
        </label>
        <input class="form-control" id="libelle" name="libelle" type="text" required
          value="<?php echo htmlspecialchars($libelleValue, ENT_QUOTES, 'UTF-8'); ?>"
          placeholder="Ex: Eau, Nourriture, Vêtements, Médicaments...">
        <small class="form-text text-muted">
          Le type de besoin sera utilisé pour catégoriser les besoins des différentes villes
        </small>
      </div>
      <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <a href="/types-besoin" class="btn btn-secondary">
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

