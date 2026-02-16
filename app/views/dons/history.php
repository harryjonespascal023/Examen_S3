<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Historique des Dispatches - BNGRC</title>
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
            <a class="nav-link" href="/dons">
              <i class="bi bi-house-door"></i> Accueil
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="/dons/history">
              <i class="bi bi-clock-history"></i> Historique
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container">
    <div class="row mb-4">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
          <h2><i class="bi bi-clock-history"></i> Historique des Dispatches</h2>
          <a href="/dons" class="btn btn-outline-danger">
            <i class="bi bi-arrow-left"></i> Retour
          </a>
        </div>
        <hr>
      </div>
    </div>

    <?php if (empty($history)): ?>
      <div class="row">
        <div class="col-12">
          <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Aucun dispatch n'a encore été effectué
          </div>
        </div>
      </div>
    <?php else: ?>
      <div class="row mb-3">
        <div class="col-12">
          <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
              <h5 class="mb-0">
                <i class="bi bi-graph-up"></i> Statistiques
              </h5>
            </div>
            <div class="card-body">
              <div class="row text-center">
                <div class="col-md-3">
                  <h3 class="text-primary"><?= count($history) ?></h3>
                  <p class="text-muted">Total Dispatches</p>
                </div>
                <div class="col-md-3">
                  <h3 class="text-success"><?= array_sum(array_column($history, 'quantity')) ?></h3>
                  <p class="text-muted">Unités Distribuées</p>
                </div>
                <div class="col-md-3">
                  <h3 class="text-info"><?= count(array_unique(array_column($history, 'id_don'))) ?></h3>
                  <p class="text-muted">Dons Utilisés</p>
                </div>
                <div class="col-md-3">
                  <h3 class="text-warning"><?= count(array_unique(array_column($history, 'id_besoin'))) ?></h3>
                  <p class="text-muted">Besoins Concernés</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
              <h5 class="mb-0">
                <i class="bi bi-list-ul"></i> Détail des Dispatches
              </h5>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                  <thead class="table-dark">
                    <tr>
                      <th>ID</th>
                      <th>Date Dispatch</th>
                      <th>Don</th>
                      <th>Date Don</th>
                      <th>Besoin</th>
                      <th>Type</th>
                      <th>Ville</th>
                      <th class="text-end">Quantité</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $currentDate = '';
                    foreach ($history as $dispatch):
                      $dispatchDate = date('d/m/Y', strtotime($dispatch['date_dispatch']));
                      $showDateSeparator = $dispatchDate !== $currentDate;
                      $currentDate = $dispatchDate;
                      ?>
                      <?php if ($showDateSeparator): ?>
                        <tr class="table-secondary">
                          <td colspan="8" class="fw-bold">
                            <i class="bi bi-calendar-event"></i> <?= $dispatchDate ?>
                          </td>
                        </tr>
                      <?php endif; ?>
                      <tr>
                        <td><span class="badge bg-primary">#<?= $dispatch['id'] ?></span></td>
                        <td><?= date('H:i', strtotime($dispatch['date_dispatch'])) ?></td>
                        <td>
                          <span class="badge bg-info">Don #<?= $dispatch['id_don'] ?></span>
                        </td>
                        <td>
                          <small class="text-muted">
                            <i class="bi bi-calendar"></i> <?= date('d/m/Y', strtotime($dispatch['don_date'])) ?>
                          </small>
                        </td>
                        <td>
                          <strong><?= htmlspecialchars($dispatch['besoin_libelle']) ?></strong>
                          <br>
                          <small class="text-muted">Besoin #<?= $dispatch['id_besoin'] ?></small>
                        </td>
                        <td>
                          <span class="badge bg-secondary">
                            <?= htmlspecialchars($dispatch['type_libelle']) ?>
                          </span>
                        </td>
                        <td>
                          <i class="bi bi-geo-alt-fill text-danger"></i>
                          <?= htmlspecialchars($dispatch['ville_nom']) ?>
                        </td>
                        <td class="text-end">
                          <span class="badge bg-success fs-6"><?= $dispatch['quantity'] ?></span>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                  <tfoot class="table-light">
                    <tr>
                      <th colspan="7" class="text-end">Total distribué:</th>
                      <th class="text-end">
                        <span class="badge bg-success fs-5">
                          <?= array_sum(array_column($history, 'quantity')) ?> unités
                        </span>
                      </th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row mt-4">
        <div class="col-md-6">
          <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
              <h6 class="mb-0"><i class="bi bi-box-seam"></i> Par Type de Besoin</h6>
            </div>
            <div class="card-body">
              <?php
              $byType = [];
              foreach ($history as $dispatch) {
                $type = $dispatch['type_libelle'];
                if (!isset($byType[$type])) {
                  $byType[$type] = 0;
                }
                $byType[$type] += $dispatch['quantity'];
              }
              arsort($byType);
              ?>
              <ul class="list-group">
                <?php foreach ($byType as $type => $quantity): ?>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= htmlspecialchars($type) ?>
                    <span class="badge bg-info rounded-pill"><?= $quantity ?> unités</span>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
              <h6 class="mb-0"><i class="bi bi-geo-alt"></i> Par Ville</h6>
            </div>
            <div class="card-body">
              <?php
              $byVille = [];
              foreach ($history as $dispatch) {
                $ville = $dispatch['ville_nom'];
                if (!isset($byVille[$ville])) {
                  $byVille[$ville] = 0;
                }
                $byVille[$ville] += $dispatch['quantity'];
              }
              arsort($byVille);
              ?>
              <ul class="list-group">
                <?php foreach ($byVille as $ville => $quantity): ?>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <i class="bi bi-geo-alt-fill text-danger"></i> <?= htmlspecialchars($ville) ?>
                    <span class="badge bg-warning text-dark rounded-pill"><?= $quantity ?> unités</span>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <footer class="bg-dark text-white text-center py-3 mt-5">
    <div class="container">
      <p class="mb-0">Bureau National de Gestion des Risques et des Catastrophes - BNGRC &copy; <?= date('Y') ?></p>
    </div>
  </footer>

  <script src="/assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>
