<?php
if (!isset($besoins)) {
  $besoins = [];
}
if (!isset($villes)) {
  $villes = [];
}
if (!isset($villeSelectionnee)) {
  $villeSelectionnee = null;
}
if (!isset($totalDonsArgent)) {
  $totalDonsArgent = 0;
}
if (!isset($fraisPourcentage)) {
  $fraisPourcentage = 10;
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
    <i class="bi bi-cart-plus"></i> Achats - Besoins Restants
  </h1>
  <p class="lead text-muted">Utilisez les dons en argent pour acheter les besoins en nature et en matériaux</p>
</div>

<div class="container">
  <?php if ($message): ?>
    <div class="alert alert-<?= htmlspecialchars($messageType, ENT_QUOTES) ?> alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($message, ENT_QUOTES) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <!-- Carte des informations financières -->
  <div class="row g-3 mb-4">
    <div class="col-md-6">
      <div class="card border-0 shadow-sm h-100"
        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <div class="card-body text-center">
          <i class="bi bi-cash-stack" style="font-size: 2.5rem;"></i>
          <div class="mt-2">
            <div class="text-white-50 small">Dons en argent disponibles</div>
            <div class="fs-3 fw-bold"><?= number_format($totalDonsArgent, 2) ?> Ar</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <h6 class="card-title">
            <i class="bi bi-percent"></i> Frais d'achat
          </h6>
          <form method="POST" action="/achats/update-frais" class="d-flex align-items-end gap-2">
            <div class="flex-grow-1">
              <label for="frais_pourcentage" class="form-label small">Pourcentage (%)</label>
              <input type="number" class="form-control" id="frais_pourcentage" name="frais_pourcentage"
                value="<?= htmlspecialchars($fraisPourcentage, ENT_QUOTES) ?>" min="0" max="100" step="0.1" required>
            </div>
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-check-circle"></i> Mettre à jour
            </button>
          </form>
          <small class="text-muted mt-2 d-block">
            Actuellement: <?= htmlspecialchars($fraisPourcentage, ENT_QUOTES) ?>% de frais sur chaque achat
          </small>
        </div>
      </div>
    </div>
  </div>

  <!-- Filtres -->
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <form method="GET" action="/achats/besoins-restants" class="row g-3">
        <div class="col-md-8">
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
        <div class="col-md-4 d-flex align-items-end">
          <button type="submit" class="btn btn-primary me-2">
            <i class="bi bi-filter"></i> Filtrer
          </button>
          <a href="/achats/besoins-restants" class="btn btn-secondary me-2">
            <i class="bi bi-x-circle"></i> Réinitialiser
          </a>
          <a href="/achats" class="btn btn-info">
            <i class="bi bi-list-ul"></i> Historique
          </a>
        </div>
      </form>
    </div>
  </div>

  <!-- Liste des besoins restants -->
  <div class="card shadow-sm">
    <div class="card-header bg-success text-white">
      <h5 class="mb-0">
        <i class="bi bi-bag-check"></i> Besoins Restants (Nature & Matériaux)
      </h5>
    </div>
    <div class="card-body p-0">
      <?php if (count($besoins) === 0): ?>
        <div class="p-4 text-center text-muted">
          <i class="bi bi-check-circle" style="font-size: 3rem;"></i>
          <p class="mt-2">Tous les besoins en nature et matériaux sont satisfaits !</p>
        </div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-hover table-striped align-middle mb-0">
            <thead class="table-dark">
              <tr>
                <th>Ville</th>
                <th>Type</th>
                <th>Libellé</th>
                <th class="text-end">Prix Unitaire</th>
                <th class="text-end">Quantité Restante</th>
                <th class="text-end">Montant Restant</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($besoins as $besoin): ?>
                <tr>
                  <td>
                    <i class="bi bi-geo-alt-fill text-danger"></i>
                    <?= htmlspecialchars($besoin['ville_nom'], ENT_QUOTES) ?>
                  </td>
                  <td>
                    <?php
                    $badgeClass = 'bg-secondary';
                    $typeLibelleLower = strtolower($besoin['type_libelle']);
                    if ($typeLibelleLower === 'nature') {
                      $badgeClass = 'bg-success';
                    } elseif ($typeLibelleLower === 'materiaux') {
                      $badgeClass = 'bg-info';
                    }
                    ?>
                    <span class="badge <?= $badgeClass ?>">
                      <?= htmlspecialchars($besoin['type_libelle'], ENT_QUOTES) ?>
                    </span>
                  </td>
                  <td><?= htmlspecialchars($besoin['libelle'], ENT_QUOTES) ?></td>
                  <td class="text-end"><?= number_format((float) $besoin['prix_unitaire'], 2) ?> Ar</td>
                  <td class="text-end">
                    <span class="badge bg-warning text-dark">
                      <?= (int) $besoin['quantity_restante'] ?>
                    </span>
                  </td>
                  <td class="text-end">
                    <strong><?= number_format((float) $besoin['montant_restant'], 2) ?> Ar</strong>
                  </td>
                  <td class="text-center">
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#achatModal"
                      data-besoin-id="<?= (int) $besoin['id'] ?>"
                      data-besoin-libelle="<?= htmlspecialchars($besoin['libelle'], ENT_QUOTES) ?>"
                      data-besoin-ville="<?= htmlspecialchars($besoin['ville_nom'], ENT_QUOTES) ?>"
                      data-besoin-type="<?= htmlspecialchars($besoin['type_libelle'], ENT_QUOTES) ?>"
                      data-besoin-prix="<?= (float) $besoin['prix_unitaire'] ?>"
                      data-besoin-qty="<?= (int) $besoin['quantity_restante'] ?>">
                      <i class="bi bi-cart-plus"></i> Acheter
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Modal d'achat -->
<div class="modal fade" id="achatModal" tabindex="-1" aria-labelledby="achatModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="/achats/create" id="achatForm">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="achatModalLabel">
            <i class="bi bi-cart-plus"></i> Effectuer un Achat
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id_besoin" id="modal_id_besoin">

          <div class="mb-3">
            <label class="form-label fw-bold">Détails du besoin</label>
            <div class="card bg-light">
              <div class="card-body">
                <p class="mb-1"><strong>Ville:</strong> <span id="modal_ville"></span></p>
                <p class="mb-1"><strong>Type:</strong> <span id="modal_type"></span></p>
                <p class="mb-1"><strong>Libellé:</strong> <span id="modal_libelle"></span></p>
                <p class="mb-1"><strong>Prix unitaire:</strong> <span id="modal_prix"></span> Ar</p>
                <p class="mb-0"><strong>Quantité restante:</strong> <span id="modal_qty_max"></span></p>
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label for="modal_quantity" class="form-label">Quantité à acheter <span class="text-danger">*</span></label>
            <input type="number" class="form-control" id="modal_quantity" name="quantity" min="1" required>
            <small class="form-text text-muted">Maximum: <span id="modal_qty_max_text"></span></small>
          </div>

          <div class="mb-3">
            <label class="form-label">Calcul du coût</label>
            <div class="card bg-light">
              <div class="card-body">
                <p class="mb-1">Montant: <span id="modal_montant">0.00</span> Ar</p>
                <p class="mb-1 text-danger">Frais (<?= htmlspecialchars($fraisPourcentage, ENT_QUOTES) ?>%): +<span
                    id="modal_frais">0.00</span> Ar</p>
                <p class="mb-0"><strong>Total avec frais: <span id="modal_total">0.00</span> Ar</strong></p>
              </div>
            </div>
          </div>

          <div class="alert alert-info mb-0">
            <i class="bi bi-info-circle"></i> Budget disponible: <strong><?= number_format($totalDonsArgent, 2) ?>
              Ar</strong>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle"></i> Annuler
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle"></i> Confirmer l'Achat
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('achatModal');
    const fraisPourcentage = <?= $fraisPourcentage ?>;

    modal.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      const besoinId = button.getAttribute('data-besoin-id');
      const besoinLibelle = button.getAttribute('data-besoin-libelle');
      const besoinVille = button.getAttribute('data-besoin-ville');
      const besoinType = button.getAttribute('data-besoin-type');
      const besoinPrix = parseFloat(button.getAttribute('data-besoin-prix'));
      const besoinQty = parseInt(button.getAttribute('data-besoin-qty'));

      document.getElementById('modal_id_besoin').value = besoinId;
      document.getElementById('modal_ville').textContent = besoinVille;
      document.getElementById('modal_type').textContent = besoinType;
      document.getElementById('modal_libelle').textContent = besoinLibelle;
      document.getElementById('modal_prix').textContent = besoinPrix.toFixed(2);
      document.getElementById('modal_qty_max').textContent = besoinQty;
      document.getElementById('modal_qty_max_text').textContent = besoinQty;

      const quantityInput = document.getElementById('modal_quantity');
      quantityInput.max = besoinQty;
      quantityInput.value = 1;

      // Fonction de calcul
      function updateCalcul() {
        const qty = parseInt(quantityInput.value) || 0;
        const montant = qty * besoinPrix;
        const frais = montant * (fraisPourcentage / 100);
        const total = montant + frais;

        document.getElementById('modal_montant').textContent = montant.toFixed(2);
        document.getElementById('modal_frais').textContent = frais.toFixed(2);
        document.getElementById('modal_total').textContent = total.toFixed(2);
      }

      quantityInput.addEventListener('input', updateCalcul);
      updateCalcul();
    });
  });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>

