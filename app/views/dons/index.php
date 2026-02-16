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
          <form method="POST" action="/dons/dispatch" class="mt-2">
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
          <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Saisir un Don</h5>
        </div>
        <div class="card-body">
          <form method="POST" action="/dons/create">
            <div class="mb-3">
              <label for="id_besoin" class="form-label">
                <i class="bi bi-card-checklist"></i> Besoin à Satisfaire *
              </label>
              <select class="form-select" id="id_besoin" name="id_besoin" required>
                <option value="">Sélectionner un besoin...</option>
                <?php foreach ($besoins as $besoin): ?>
                  <option value="<?= $besoin['id'] ?>">
                    <?= htmlspecialchars($besoin['libelle']) ?> -
                    <?= htmlspecialchars($besoin['ville_nom']) ?>
                    (<?= htmlspecialchars($besoin['type_libelle']) ?>) -
                    Restant: <?= $besoin['quantity_restante'] ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <small class="form-text text-muted">
                Sélectionnez le besoin spécifique que vous souhaitez satisfaire
              </small>
            </div>
            <div class="mb-3">
              <label for="quantity" class="form-label">
                <i class="bi bi-box"></i> Quantité *
              </label>
              <input type="number" class="form-control" id="quantity" name="quantity" min="1" required
                placeholder="Entrer la quantité à donner">
            </div>
            <div class="mb-3">
              <label for="date_saisie" class="form-label">
                <i class="bi bi-calendar-event"></i> Date de Saisie *
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
                      <h6 class="mb-1 fw-bold"><?= htmlspecialchars($besoin['libelle']) ?></h6>
                      <p class="mb-1 text-muted small">
                        <i class="bi bi-geo-alt-fill text-danger"></i> <?= htmlspecialchars($besoin['ville_nom']) ?>
                      </p>
                      <small class="badge bg-secondary"><?= htmlspecialchars($besoin['type_libelle']) ?></small>
                      <small class="text-muted ms-2">Besoin #<?= $besoin['id'] ?></small>
                    </div>
                    <span class="badge rounded-pill fs-6"
                      style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
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
                      <h6 class="mb-1 fw-bold"><?= htmlspecialchars($don['besoin_libelle']) ?></h6>
                      <p class="mb-1 text-muted small">
                        <i class="bi bi-geo-alt-fill text-danger"></i> <?= htmlspecialchars($don['ville_nom']) ?>
                      </p>
                      <small class="badge bg-secondary"><?= htmlspecialchars($don['type_libelle']) ?></small>
                      <small class="text-muted ms-2"><i class="bi bi-calendar"></i>
                        <?= date('d/m/Y', strtotime($don['date_saisie'])) ?></small>
                    </div>
                    <span class="badge rounded-pill fs-6"
                      style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
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
                    <th><i class="bi bi-card-text"></i> Besoin</th>
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
                        <small class="fw-bold d-block"><?= htmlspecialchars($don['besoin_libelle']) ?></small>
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

