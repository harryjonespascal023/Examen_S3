<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="page-header mb-4">
  <h1 class="display-5 fw-bold">
    <i class="bi bi-play-circle"></i> Simulation de Dispatch
  </h1>
  <p class="lead text-muted">Simulez le dispatch des dons avant validation</p>
</div>

<div class="container">
  <?php if (isset($message) && $message): ?>
    <div class="alert alert-<?= htmlspecialchars($messageType) ?> alert-dismissible fade show" role="alert">
      <strong><?= $messageType === 'success' ? 'Succès!' : ($messageType === 'info' ? 'Info' : 'Erreur!') ?></strong>
      <?= htmlspecialchars($message) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <div class="row mb-4">
    <div class="col-md-12">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0">
            <i class="bi bi-info-circle"></i> À propos de la Simulation
          </h5>
        </div>
        <div class="card-body">
          <p class="mb-2">
            <strong>La simulation</strong> permet de visualiser les résultats du dispatch sans modifier la base de
            données.
          </p>
          <ul class="mb-3">
            <li><strong>Simuler :</strong> Affiche les résultats du dispatch en mode lecture seule (aucune modification
              dans la base de données).</li>
            <li><strong>Valider :</strong> Exécute réellement le dispatch et enregistre les modifications dans la base
              de données.</li>
          </ul>
          <div class="alert alert-info mb-0">
            <i class="bi bi-lightbulb"></i> <strong>Modes de dispatch :</strong>
            <ul class="mb-0 mt-2">
              <li><strong>Par date (FIFO) :</strong> Les besoins les plus anciens sont satisfaits en premier, avec les
                dons les plus anciens</li>
              <li><strong>Par quantité croissante :</strong> Les petits besoins ET les petits dons sont prioritaires
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Sélecteur de mode de dispatch -->
  <div class="row mb-4">
    <div class="col-md-12">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-secondary text-white">
          <h5 class="mb-0">
            <i class="bi bi-gear"></i> Mode de Dispatch
          </h5>
        </div>
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col-md-8">
              <label for="dispatch_mode" class="form-label fw-bold">Choisissez le mode de dispatch :</label>
              <select class="form-select form-select-lg" id="dispatch_mode" name="mode">
                <option value="date" selected>
                  📅 Par date (FIFO) - Besoins les plus anciens en premier
                </option>
                <option value="quantity">
                  📊 Par quantité croissante - Petits besoins en premier
                </option>
                <option value="proportional">
                  ⚖️ Proportionnel - Distribution équitable selon les besoins
                </option>
              </select>
            </div>
            <div class="col-md-4 text-center">
              <div class="badge bg-primary fs-6 py-3 px-4">
                <i class="bi bi-arrow-down-up"></i>
                <span id="mode_label">Mode FIFO</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-md-6">
      <form method="POST" action="<?= BASE_URL ?>/dons/simulation/simulate" class="h-100">
        <input type="hidden" name="mode" id="simulate_mode" value="date">
        <div class="card shadow-sm border-0 h-100"
          style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
          <div class="card-body text-center d-flex flex-column justify-content-center">
            <i class="bi bi-eye" style="font-size: 5rem;"></i>
            <h3 class="mt-3 mb-3">Mode Simulation</h3>
            <p class="mb-4">Visualisez le résultat du dispatch sans modifier les données</p>
            <div class="mb-3 text-start">
              <label for="mode_simulation" class="form-label mb-1">Mode de répartition</label>
              <select id="mode_simulation" name="mode" class="form-select">
                <option value="fifo" selected>FIFO</option>
                <option value="proportionnel">Proportionnel</option>
              </select>
            </div>
            <button type="submit" class="btn btn-light btn-lg w-100">
              <i class="bi bi-play-circle"></i> Simuler le Dispatch
            </button>
          </div>
        </div>
      </form>
    </div>

    <div class="col-md-6">
      <form method="POST" action="<?= BASE_URL ?>/dons/dispatch" class="h-100">
        <input type="hidden" name="mode" id="validate_mode" value="date">
        <div class="card shadow-sm border-0 h-100"
          style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
          <div class="card-body text-center d-flex flex-column justify-content-center">
            <i class="bi bi-check-circle-fill" style="font-size: 5rem;"></i>
            <h3 class="mt-3 mb-3">Mode Validation</h3>
            <p class="mb-4">Exécutez réellement le dispatch et enregistrez les modifications</p>
            <div class="mb-3 text-start">
              <label for="mode_validation" class="form-label mb-1">Mode de répartition</label>
              <select id="mode_validation" name="mode" class="form-select">
                <option value="fifo" selected>FIFO</option>
                <option value="proportionnel">Proportionnel</option>
              </select>
            </div>
            <button type="submit" class="btn btn-light btn-lg w-100">
              <i class="bi bi-check2-all"></i> Valider le Dispatch
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <?php if (isset($simulationResult) && $simulationResult !== null): ?>
    <div class="row mb-4">
      <div class="col-md-12">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
              <i class="bi bi-clipboard-check"></i> Résultats de la Simulation
            </h5>
            <span class="badge bg-light text-dark fs-6">
              <?php
              $mode = $simulationResult['mode'] ?? 'date';
              if ($mode === 'quantity') {
                $modeLabel = '📊 Mode: Quantité Croissante';
              } elseif ($mode === 'proportional') {
                $modeLabel = '⚖️ Mode: Proportionnel';
              } else {
                $modeLabel = '📅 Mode: Date (FIFO)';
              }
              echo $modeLabel;
              ?>
            </span>
          </div>
          <div class="card-body">
            <div class="row g-3 mb-4">
              <div class="col-md-3">
                <div class="card text-center border h-100">
                  <div class="card-body">
                    <h2 class="text-primary"><?= $simulationResult['total_dispatches'] ?></h2>
                    <p class="mb-0 small text-muted">Dispatches</p>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card text-center border h-100">
                  <div class="card-body">
                    <h2 class="text-info"><?= $simulationResult['total_quantity_dispatched'] ?></h2>
                    <p class="mb-0 small text-muted">Unités dispatchées</p>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card text-center border h-100">
                  <div class="card-body">
                    <h2 class="text-success"><?= $simulationResult['besoins_satisfaits'] ?></h2>
                    <p class="mb-0 small text-muted">Besoins satisfaits</p>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card text-center border h-100">
                  <div class="card-body">
                    <h2 class="text-warning"><?= $simulationResult['dons_utilises'] ?></h2>
                    <p class="mb-0 small text-muted">Dons utilisés</p>
                  </div>
                </div>
              </div>
            </div>

            <?php if (!empty($simulationResult['details'])): ?>
              <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> <strong>Mode Simulation :</strong> Ces données sont indicatives.
                Aucune modification n'a été enregistrée dans la base de données.
              </div>

              <h6 class="mb-3">Détails des Dispatches Simulés</h6>
              <div class="table-responsive">
                <table class="table table-striped table-hover">
                  <thead class="table-dark">
                    <tr>
                      <th>Type</th>
                      <th>Don (Libellé)</th>
                      <th>Ville</th>
                      <th>Besoin (Libellé)</th>
                      <th class="text-center">Quantité</th>
                      <th>Date Don</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($simulationResult['details'] as $detail): ?>
                      <tr>
                        <td>
                          <span class="badge bg-secondary"><?= htmlspecialchars($detail['type']) ?></span>
                        </td>
                        <td>
                          <strong><?= htmlspecialchars($detail['libelle']) ?></strong>
                          <br>
                          <small class="text-muted">Don #<?= $detail['don_id'] ?></small>
                        </td>
                        <td>
                          <i class="bi bi-geo-alt-fill text-danger"></i>
                          <?= htmlspecialchars($detail['ville']) ?>
                        </td>
                        <td>
                          <?= htmlspecialchars($detail['besoin_libelle']) ?>
                          <br>
                          <small class="text-muted">
                            <i class="bi bi-calendar-event"></i>
                            Besoin du <?= date('d/m/Y', strtotime($detail['besoin_date'])) ?>
                          </small>
                        </td>
                        <td class="text-center">
                          <span class="badge bg-primary"><?= $detail['quantity'] ?>
                            <?= $detail['libelle'] === 'Argent' ? 'Ar' : 'unités' ?></span>
                        </td>
                        <td class="text-muted small"><?= date('d/m/Y', strtotime($detail['don_date'])) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>

              <div class="mt-4">
                <p class="text-muted">
                  <i class="bi bi-lightbulb"></i>
                  <strong>Conseil :</strong> Si les résultats de la simulation vous conviennent,
                  cliquez sur le bouton <strong>"Valider le Dispatch"</strong> pour enregistrer ces modifications.
                </p>
              </div>
            <?php else: ?>
              <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i>
                Aucun dispatch n'a pu être simulé. Vérifiez qu'il y a des dons disponibles et des besoins non satisfaits.
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <div class="row">
    <div class="col-md-12">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <h6 class="card-title">
            <i class="bi bi-question-circle"></i> Aide
          </h6>
          <p class="mb-2"><strong>Qu'est-ce qu'un dispatch ?</strong></p>
          <p class="mb-3">
            Le dispatch consiste à attribuer automatiquement les dons disponibles aux besoins non satisfaits
            selon le mode choisi.
          </p>
          <p class="mb-2"><strong>Comment ça fonctionne ?</strong></p>
          <ol class="mb-2">
            <li>Les dons sont enregistrés par type (eau, riz, argent, etc.) sans préciser la destination</li>
            <li><strong>Mode par date (FIFO) :</strong> Besoins triés par date (plus anciens en premier), dons triés par
              date de saisie</li>
            <li><strong>Mode par quantité croissante :</strong> Besoins ET dons triés par quantité restante (plus petits
              en premier)</li>
            <li>Le dispatch distribue les dons selon le mode sélectionné</li>
            <li><strong>Exemple mode date :</strong> 200 kg de riz → Ville B (besoin du 10/02) reçoit le don du 05/02 en
              priorité</li>
            <li><strong>Exemple mode quantité :</strong> 200 kg de riz → Ville A (50 kg restants) reçoit un petit don
              (30 kg) en priorité</li>
          </ol>
          <p class="mb-0 text-primary">
            <i class="bi bi-star-fill"></i> <strong>Conseil :</strong> Utilisez le mode <strong>par date</strong> pour
            respecter l'ordre chronologique (équitable),
            ou le mode <strong>par quantité</strong> pour maximiser le nombre de besoins complètement satisfaits
            (efficace).
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Synchronisation du mode de dispatch entre le sélecteur et les formulaires
  document.addEventListener('DOMContentLoaded', function () {
    const modeSelect = document.getElementById('dispatch_mode');
    const modeLabel = document.getElementById('mode_label');
    const simulateMode = document.getElementById('simulate_mode');
    const validateMode = document.getElementById('validate_mode');

    function updateMode() {
      const selectedMode = modeSelect.value;

      // Mettre à jour les champs cachés des formulaires
      simulateMode.value = selectedMode;
      validateMode.value = selectedMode;

      // Mettre à jour le badge d'affichage
      if (selectedMode === 'date') {
        modeLabel.textContent = 'Mode FIFO';
      } else if (selectedMode === 'quantity') {
        modeLabel.textContent = 'Mode Quantité Croissante';
      } else if (selectedMode === 'proportional') {
        modeLabel.textContent = 'Mode Proportionnel';
      }

      console.log('Mode mis à jour:', selectedMode, '| Simulate:', simulateMode.value, '| Validate:', validateMode.value);
    }

    // Initialiser au chargement de la page
    updateMode();

    // Mettre à jour quand l'utilisateur change le mode
    modeSelect.addEventListener('change', updateMode);
  });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>

