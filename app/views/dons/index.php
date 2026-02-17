<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestion des Dons - BNGRC</title>
  <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="/assets/bootstrap-icons/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">
  <nav class="navbar navbar-expand-lg navbar-dark bg-danger mb-4">
    <div class="container-fluid">
      <a class="navbar-brand" href="/dons">
        <i class="bi bi-heart-fill"></i> BNGRC - Gestion des Dons
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link active" href="/dons">
              <i class="bi bi-house-door"></i> Accueil
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/dons/history">
              <i class="bi bi-clock-history"></i> Historique
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container">
    <?php if (isset($message) && $message): ?>
      <div class="alert alert-<?= htmlspecialchars($messageType) ?> alert-dismissible fade show" role="alert">
        <strong><?= $messageType === 'success' ? 'Succès!' : 'Erreur!' ?></strong>
        <?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <div class="row mb-4">
      <div class="col-md-3">
        <div class="card text-center border-primary">
          <div class="card-body">
            <i class="bi bi-box-seam text-primary" style="font-size: 3rem;"></i>
            <h3 class="mt-2"><?= $report['dons_non_utilises']['count'] ?></h3>
            <p class="text-muted mb-0">Dons Disponibles</p>
            <small class="text-muted"><?= $report['dons_non_utilises']['total_quantity'] ?> unités</small>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center border-warning">
          <div class="card-body">
            <i class="bi bi-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
            <h3 class="mt-2"><?= $report['besoins_non_satisfaits']['count'] ?></h3>
            <p class="text-muted mb-0">Besoins en Attente</p>
            <small class="text-muted"><?= $report['besoins_non_satisfaits']['total_quantity'] ?> unités</small>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center border-info">
          <div class="card-body">
            <i class="bi bi-list-check text-info" style="font-size: 3rem;"></i>
            <h3 class="mt-2"><?= count($dons) ?></h3>
            <p class="text-muted mb-0">Total Dons</p>
            <small class="text-muted">Tous les dons enregistrés</small>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center border-success">
          <div class="card-body">
            <i class="bi bi-arrow-repeat text-success" style="font-size: 3rem;"></i>
            <form method="POST" action="/dons/dispatch" class="mt-2">
              <button type="submit" class="btn btn-success btn-lg" <?= $report['dons_non_utilises']['count'] == 0 || $report['besoins_non_satisfaits']['count'] == 0 ? 'disabled' : '' ?>>
                Dispatch FIFO
              </button>
            </form>
            <small class="text-muted">Lancer la distribution</small>
          </div>
        </div>
      </div>
    </div>

    <div class="row mb-4">
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Saisir un Don</h5>
          </div>
          <div class="card-body">
            <form method="POST" action="/dons/create">
              <div class="mb-3">
                <label for="id_besoin" class="form-label">Besoin à satisfaire *</label>
                <select class="form-select" id="id_besoin" name="id_besoin" required>
                  <option value="">Électionner un besoin...</option>
                  <?php foreach ($besoinsDisponibles as $besoin): ?>
                    <option value="<?= $besoin['id'] ?>">
                      <?= htmlspecialchars($besoin['libelle']) ?>
                      (<?= htmlspecialchars($besoin['type_libelle']) ?>) -
                      <?= htmlspecialchars($besoin['ville_nom']) ?> -
                      <?= $besoin['quantity_restante'] ?> unités restantes
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="mb-3">
                <label for="quantity" class="form-label">Quantité *</label>
                <input type="number" class="form-control" id="quantity" name="quantity" min="1" required
                  placeholder="Entrer la quantité">
              </div>
              <div class="mb-3">
                <label for="date_saisie" class="form-label">Date de Saisie *</label>
                <input type="date" class="form-control" id="date_saisie" name="date_saisie" value="<?= date('Y-m-d') ?>"
                  required>
              </div>
              <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-save"></i> Enregistrer le Don
              </button>
            </form>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Besoins Non Satisfaits</h5>
          </div>
          <div class="card-body" style="max-height: 400px; overflow-y: auto;">
            <?php if (empty($report['besoins_non_satisfaits']['details'])): ?>
              <div class="alert alert-success mb-0">
                <i class="bi bi-check-circle"></i> Tous les besoins sont satisfaits!
              </div>
            <?php else: ?>
              <div class="list-group">
                <?php foreach ($report['besoins_non_satisfaits']['details'] as $besoin): ?>
                  <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-start">
                      <div>
                        <h6 class="mb-1"><?= htmlspecialchars($besoin['libelle']) ?></h6>
                        <p class="mb-1 text-muted small">
                          <i class="bi bi-tag"></i> <?= htmlspecialchars($besoin['type_libelle']) ?> |
                          <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($besoin['ville_nom']) ?>
                        </p>
                        <small class="text-muted">Besoin #<?= $besoin['id'] ?></small>
                      </div>
                      <span class="badge bg-warning text-dark rounded-pill fs-6">
                        <?= $besoin['quantity_restante'] ?> unités
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

    <div class="row mb-4">
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-box-seam"></i> Dons Disponibles</h5>
          </div>
          <div class="card-body" style="max-height: 400px; overflow-y: auto;">
            <?php if (empty($report['dons_non_utilises']['details'])): ?>
              <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle"></i> Aucun don disponible actuellement
              </div>
            <?php else: ?>
              <div class="list-group">
                <?php foreach ($report['dons_non_utilises']['details'] as $don): ?>
                  <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-start">
                      <div>
                        <h6 class="mb-1"><?= htmlspecialchars($don['besoin_libelle']) ?></h6>
                        <p class="mb-1 text-muted small">
                          <i class="bi bi-tag"></i> <?= htmlspecialchars($don['type_libelle']) ?> |
                          <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($don['ville_nom']) ?> |
                          <i class="bi bi-calendar"></i> <?= date('d/m/Y', strtotime($don['date_saisie'])) ?>
                        </p>
                        <small class="text-muted">Don #<?= $don['id'] ?></small>
                      </div>
                      <span class="badge bg-success rounded-pill fs-6">
                        <?= $don['quantity_restante'] ?> / <?= $don['quantity'] ?>
                      </span>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="bi bi-list-ul"></i> Tous les Dons Enregistrés</h5>
          </div>
          <div class="card-body" style="max-height: 400px; overflow-y: auto;">
            <?php if (empty($dons)): ?>
              <div class="alert alert-secondary mb-0">
                <i class="bi bi-inbox"></i> Aucun don enregistré
              </div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-hover table-sm">
                  <thead class="table-light">
                    <tr>
                      <th>ID</th>
                      <th>Besoin</th>
                      <th>Type</th>
                      <th>Ville</th>
                      <th>Quantité</th>
                      <th>Restant</th>
                      <th>Date</th>
                      <th>Statut</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($dons as $don):
                      $pourcentage = $don['quantity'] > 0 ? (($don['quantity'] - $don['quantity_restante']) / $don['quantity'] * 100) : 0;
                      ?>
                      <tr>
                        <td><?= $don['id'] ?></td>
                        <td><?= htmlspecialchars($don['besoin_libelle']) ?></td>
                        <td><?= htmlspecialchars($don['type_libelle']) ?></td>
                        <td><?= htmlspecialchars($don['ville_nom']) ?></td>
                        <td><?= $don['quantity'] ?></td>
                        <td><?= $don['quantity_restante'] ?></td>
                        <td><?= date('d/m/Y', strtotime($don['date_saisie'])) ?></td>
                        <td>
                          <?php if ($don['quantity_restante'] == 0): ?>
                            <span class="badge bg-secondary">Épuisé</span>
                          <?php elseif ($don['quantity_restante'] < $don['quantity']): ?>
                            <span class="badge bg-warning text-dark"><?= round($pourcentage) ?>%</span>
                          <?php else: ?>
                            <span class="badge bg-success">Complet</span>
                          <?php endif; ?>
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
    </div>
  </div>

  <footer class="bg-dark text-white text-center py-3 mt-5">
    <div class="container">
      <p class="mb-0">Bureau National de Gestion des Risques et des Catastrophes - BNGRC &copy; <?= date('Y') ?></p>
    </div>
  </footer>

  <script src="/assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="page-header mb-4">
  <h1 class="display-5 fw-bold">
    <i class="bi bi-heart-fill"></i> Gestion des Dons
  </h1>
  <p class="lead text-muted">Distribution FIFO et suivi des besoins</p>
</div>

<div class="container">
  <?php if (isset($message) && $message): ?>
    <div class="alert alert-<?= htmlspecialchars($messageType) ?> alert-dismissible fade show" role="alert">
      <strong><?= $messageType === 'success' ? 'Succès!' : 'Erreur!' ?></strong>
      <?= htmlspecialchars($message) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card text-center border-0 shadow-sm h-100"
        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <div class="card-body">
          <i class="bi bi-box-seam" style="font-size: 3rem;"></i>
          <h3 class="mt-2"><?= $report['dons_non_utilises']['count'] ?></h3>
          <p class="mb-0 fw-semibold">Dons Disponibles</p>
          <small class="opacity-75"><?= $report['dons_non_utilises']['total_quantity'] ?> unités</small>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-center border-0 shadow-sm h-100"
        style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
        <div class="card-body">
          <i class="bi bi-exclamation-triangle" style="font-size: 3rem;"></i>
          <h3 class="mt-2"><?= $report['besoins_non_satisfaits']['count'] ?></h3>
          <p class="mb-0 fw-semibold">Besoins en Attente</p>
          <small class="opacity-75"><?= $report['besoins_non_satisfaits']['total_quantity'] ?> unités</small>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-center border-0 shadow-sm h-100"
        style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
        <div class="card-body">
          <i class="bi bi-list-check" style="font-size: 3rem;"></i>
          <h3 class="mt-2"><?= count($dons) ?></h3>
          <p class="mb-0 fw-semibold">Total Dons</p>
          <small class="opacity-75">Tous les dons enregistrés</small>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-center border-0 shadow-sm h-100"
        style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
        <div class="card-body">
          <i class="bi bi-arrow-repeat" style="font-size: 3rem;"></i>
          <form method="POST" action="<?= BASE_URL ?>/dons/dispatch" class="mt-2">
            <button type="submit" class="btn btn-light btn-lg" <?= $report['dons_non_utilises']['count'] == 0 || $report['besoins_non_satisfaits']['count'] == 0 ? 'disabled' : '' ?>>
              Dispatch
            </button>
          </form>
          <small class="opacity-75">Lancer la distribution</small>
        </div>
      </div>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-md-6">
      <div class="card shadow-sm border-0">
        <div class="card-header text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
          <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Faire un Don</h5>
        </div>
        <div class="card-body">
          <form method="POST" action="<?= BASE_URL ?>/dons/create">
            <div class="mb-3">
              <label for="id_type_besoin" class="form-label">
                <i class="bi bi-tag"></i> Catégorie *
              </label>
              <select class="form-select" id="id_type_besoin" name="id_type_besoin" required>
                <option value="">Sélectionner une catégorie...</option>
                <?php foreach ($typesBesoin as $type): ?>
                  <option value="<?= $type['id'] ?>" data-libelle="<?= htmlspecialchars(strtolower($type['libelle'])) ?>">
                    <?= htmlspecialchars($type['libelle']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <small class="form-text text-muted">
                Nature (nourriture), Matériaux (construction), ou Argent (financier)
              </small>
            </div>
            <div class="mb-3" id="libelleContainer">
              <label for="libelle" class="form-label">
                <i class="bi bi-box-seam"></i> Ce que vous donnez <span id="libelleRequired">*</span>
              </label>
              <input type="text" class="form-control" id="libelle" name="libelle"
                placeholder="Ex: Riz, Eau, Huile, Ciment, etc.">
              <small class="form-text text-muted" id="libelleHelp">
                Précisez ce que vous donnez : Riz, Eau, Huile pour Nature ; Ciment, Tôles pour Matériaux
              </small>
            </div>
            <div class="mb-3">
              <label for="quantity" class="form-label">
                <i class="bi bi-123"></i> Quantité *
              </label>
              <input type="number" class="form-control" id="quantity" name="quantity" min="1" required
                placeholder="Entrer la quantité à donner">
              <small class="form-text text-muted">
                Pour l'argent, entrez le montant. Pour Nature/Matériaux, entrez la quantité (kg, L, unités, etc.)
              </small>
            </div>
            <div class="mb-3">
              <label for="date_saisie" class="form-label">
                <i class="bi bi-calendar-event"></i> Date de Don *
              </label>
              <input type="date" class="form-control" id="date_saisie" name="date_saisie" value="<?= date('Y-m-d') ?>"
                required>
            </div>
            <div class="d-grid">
              <button type="submit" class="btn btn-lg text-white"
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <i class="bi bi-save"></i> Enregistrer le Don
              </button>
            </div>
          </form>
        </div>
      </div>

      <script>
        document.addEventListener('DOMContentLoaded', function () {
          const typeSelect = document.getElementById('id_type_besoin');
          const libelleInput = document.getElementById('libelle');
          const libelleContainer = document.getElementById('libelleContainer');
          const libelleRequired = document.getElementById('libelleRequired');
          const libelleHelp = document.getElementById('libelleHelp');

          function updateFields() {
            const selectedOption = typeSelect.options[typeSelect.selectedIndex];
            const typeLibelle = selectedOption ? selectedOption.getAttribute('data-libelle') : '';

            if (typeLibelle === 'argent') {
              // Pour l'argent, masquer le libellé
              libelleInput.required = false;
              libelleContainer.style.display = 'none';
            } else {
              // Pour Nature et Matériaux, afficher le libellé
              libelleInput.required = true;
              libelleContainer.style.display = '';

              if (typeLibelle === 'nature') {
                libelleInput.placeholder = 'Ex: Riz, Eau, Huile, Légumes, etc.';
                libelleHelp.textContent = 'Précisez le produit alimentaire que vous donnez';
              } else if (typeLibelle === 'matériaux' || typeLibelle === 'materiaux') {
                libelleInput.placeholder = 'Ex: Ciment, Tôles, Bois, Clous, etc.';
                libelleHelp.textContent = 'Précisez le matériau de construction que vous donnez';
              } else {
                libelleInput.placeholder = 'Ex: Riz, Eau, Huile, Ciment, etc.';
                libelleHelp.textContent = 'Précisez ce que vous donnez';
              }
            }
          }

          typeSelect.addEventListener('change', updateFields);
          updateFields();
        });
      </script>
    </div>

    <div class="col-md-6">
      <div class="card shadow-sm border-0">
        <div class="card-header text-white" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
          <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Besoins Non Satisfaits</h5>
        </div>
        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
          <?php if (empty($report['besoins_non_satisfaits']['details'])): ?>
            <div class="alert alert-success mb-0">
              <i class="bi bi-check-circle"></i> Tous les besoins sont satisfaits!
            </div>
          <?php else: ?>
            <div class="list-group list-group-flush">
              <?php foreach ($report['besoins_non_satisfaits']['details'] as $besoin): ?>
                <div class="list-group-item border-0 border-bottom">
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <h6 class="mb-1 fw-bold">
                        <?= $besoin['libelle'] == null ? "💰 Besoin en Argent" : htmlspecialchars($besoin['libelle']) ?>
                      </h6>
                      <p class="mb-1 text-muted small">
                        <i class="bi bi-geo-alt-fill text-danger"></i> <?= htmlspecialchars($besoin['ville_nom']) ?>
                      </p>
                      <small class="badge bg-secondary"><?= htmlspecialchars($besoin['type_libelle']) ?></small>
                      <small class="text-muted ms-2">Besoin #<?= $besoin['id'] ?></small>
                      <br>
                      <small class="text-info">
                        <i class="bi bi-calendar-event"></i>
                        Depuis: <?= date('d/m/Y', strtotime($besoin['date_besoin'])) ?>
                      </small>
                    </div>
                    <span class="badge rounded-pill fs-6"
                      style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                      <?= $besoin['quantity_restante'] ?>     <?= $besoin['prix_unitaire'] === null ? 'Ar' : 'unités' ?>
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

  <div class="row mb-4">
    <div class="col-md-6">
      <div class="card shadow-sm border-0">
        <div class="card-header text-white" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
          <h5 class="mb-0"><i class="bi bi-box-seam"></i> Dons Disponibles</h5>
        </div>
        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
          <?php if (empty($report['dons_non_utilises']['details'])): ?>
            <div class="alert alert-info mb-0">
              <i class="bi bi-info-circle"></i> Aucun don disponible actuellement
            </div>
          <?php else: ?>
            <div class="list-group list-group-flush">
              <?php foreach ($report['dons_non_utilises']['details'] as $don): ?>
                <div class="list-group-item border-0 border-bottom">
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <h6 class="mb-1 fw-bold">
                        <?= $don['libelle'] ? htmlspecialchars($don['libelle']) : '💰 Argent' ?>
                      </h6>
                      <small class="badge bg-success"><?= htmlspecialchars($don['type_libelle']) ?></small>
                      <small class="text-muted ms-2"><i class="bi bi-calendar"></i>
                        <?= date('d/m/Y', strtotime($don['date_saisie'])) ?></small>
                      <small class="text-muted ms-2">Don #<?= $don['id'] ?></small>
                    </div>
                    <span class="badge rounded-pill fs-6"
                      style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                      <?= $don['quantity_restante'] ?>     <?= $don['libelle'] ? 'unités' : 'Ar' ?>
                    </span>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card shadow-sm border-0">
        <div class="card-header text-white" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
          <h5 class="mb-0"><i class="bi bi-list-ul"></i> Tous les Dons Enregistrés</h5>
        </div>
        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
          <?php if (empty($dons)): ?>
            <div class="alert alert-secondary mb-0">
              <i class="bi bi-inbox"></i> Aucun don enregistré
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover table-sm mb-0">
                <thead class="table-light">
                  <tr>
                    <th><i class="bi bi-hash"></i></th>
                    <th><i class="bi bi-tag"></i> Type</th>
                    <th><i class="bi bi-box"></i> Qté</th>
                    <th><i class="bi bi-box-arrow-left"></i> Rest.</th>
                    <th><i class="bi bi-calendar"></i> Date</th>
                    <th>Statut</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($dons as $don):
                    $pourcentage = $don['quantity'] > 0 ? (($don['quantity'] - $don['quantity_restante']) / $don['quantity'] * 100) : 0;
                    ?>
                    <tr>
                      <td><span class="badge bg-primary"><?= $don['id'] ?></span></td>
                      <td>
                        <small class="badge bg-secondary"><?= htmlspecialchars($don['type_libelle']) ?></small>
                      </td>
                      <td><?= $don['quantity'] ?></td>
                      <td><span class="fw-bold text-success"><?= $don['quantity_restante'] ?></span></td>
                      <td><small><?= date('d/m/Y', strtotime($don['date_saisie'])) ?></small></td>
                      <td>
                        <?php if ($don['quantity_restante'] == 0): ?>
                          <span class="badge bg-secondary">Épuisé</span>
                        <?php elseif ($don['quantity_restante'] < $don['quantity']): ?>
                          <span class="badge bg-warning text-dark"><?= round($pourcentage) ?>%</span>
                        <?php else: ?>
                          <span class="badge bg-success">Complet</span>
                        <?php endif; ?>
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
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

