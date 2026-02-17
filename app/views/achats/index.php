<?php
if (!isset($achats)) {
  $achats = [];
}
if (!isset($villes)) {
  $villes = [];
}
if (!isset($villeSelectionnee)) {
  $villeSelectionnee = null;
}
if (!isset($message)) {
  $message = null;
}
if (!isset($messageType)) {
  $messageType = 'info';
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="page-header mb-4">
  <h1 class="display-5 fw-bold">
    <i class="bi bi-cart-check"></i> Liste des Achats
  </h1>
  <p class="lead text-muted">Historique des achats effectués via les dons en argent</p>
</div>

<div class="container">
  <?php if ($message): ?>
    <div class="alert alert-<?= htmlspecialchars($messageType, ENT_QUOTES) ?> alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($message, ENT_QUOTES) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <form method="GET" action="/achats" class="row g-3">
        <div class="col-md-6">
          <label for="ville" class="form-label">Filtrer par ville</label>
          <select class="form-select" id="ville" name="ville">
            <option value="">Toutes les villes</option>
            <?php foreach ($villes as $ville): ?>
              <option value="<?= (int) $ville['id'] ?>" <?= $villeSelectionnee == (int) $ville['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($ville['nom'], ENT_QUOTES) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6 d-flex align-items-end">
          <button type="submit" class="btn btn-primary me-2">
            <i class="bi bi-filter"></i> Filtrer
          </button>
          <a href="/achats" class="btn btn-secondary me-2">
            <i class="bi bi-x-circle"></i> Réinitialiser
          </a>
          <a href="/achats/besoins-restants" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Nouvel Achat
          </a>
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0">
        <i class="bi bi-list-ul"></i> Achats
        <?php if ($villeSelectionnee): ?>
          <?php
          $nomVille = '';
          foreach ($villes as $v) {
            if ((int) $v['id'] == $villeSelectionnee) {
              $nomVille = $v['nom'];
              break;
            }
          }
          ?>
          - <?= htmlspecialchars($nomVille, ENT_QUOTES) ?>
        <?php endif; ?>
      </h5>
    </div>
    <div class="card-body p-0">
      <?php if (count($achats) === 0): ?>
        <div class="p-4 text-center text-muted">
          <i class="bi bi-inbox" style="font-size: 3rem;"></i>
          <p class="mt-2">Aucun achat enregistré</p>
        </div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-hover table-striped align-middle mb-0">
            <thead class="table-dark">
              <tr>
                <th>Date</th>
                <th>Ville</th>
                <th>Type</th>
                <th>Libellé</th>
                <th class="text-end">Quantité</th>
                <th class="text-end">Prix Unitaire</th>
                <th class="text-end">Montant Total</th>
                <th class="text-end">Frais</th>
                <th class="text-end">Total avec Frais</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($achats as $achat): ?>
                <tr>
                  <td><?= htmlspecialchars(date('d/m/Y', strtotime($achat['date_achat'])), ENT_QUOTES) ?></td>
                  <td>
                    <i class="bi bi-geo-alt-fill text-danger"></i>
                    <?= htmlspecialchars($achat['ville_nom'], ENT_QUOTES) ?>
                  </td>
                  <td>
                    <?php
                    $badgeClass = 'bg-secondary';
                    $typeLibelleLower = strtolower($achat['type_libelle']);
                    if ($typeLibelleLower === 'nature') {
                      $badgeClass = 'bg-success';
                    } elseif ($typeLibelleLower === 'materiaux') {
                      $badgeClass = 'bg-info';
                    }
                    ?>
                    <span class="badge <?= $badgeClass ?>">
                      <?= htmlspecialchars($achat['type_libelle'], ENT_QUOTES) ?>
                    </span>
                  </td>
                  <td><?= htmlspecialchars($achat['libelle'], ENT_QUOTES) ?></td>
                  <td class="text-end"><?= (int) $achat['quantity'] ?></td>
                  <td class="text-end"><?= number_format((float) $achat['prix_unitaire'], 2) ?> Ar</td>
                  <td class="text-end"><?= number_format((float) $achat['montant_total'], 2) ?> Ar</td>
                  <td class="text-end">
                    <span class="text-danger">
                      +<?= number_format((float) $achat['frais_achat'], 2) ?> Ar
                    </span>
                  </td>
                  <td class="text-end">
                    <strong><?= number_format((float) $achat['montant_avec_frais'], 2) ?> Ar</strong>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot class="table-light">
              <tr>
                <td colspan="6" class="text-end"><strong>Total:</strong></td>
                <td class="text-end">
                  <strong><?= number_format(array_sum(array_column($achats, 'montant_total')), 2) ?> Ar</strong>
                </td>
                <td class="text-end">
                  <strong class="text-danger">
                    +<?= number_format(array_sum(array_column($achats, 'frais_achat')), 2) ?> Ar
                  </strong>
                </td>
                <td class="text-end">
                  <strong><?= number_format(array_sum(array_column($achats, 'montant_avec_frais')), 2) ?> Ar</strong>
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

