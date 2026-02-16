<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Système de Dispatch FIFO - BNGRC</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/bootstrap-icons/font/bootstrap-icons.min.css">
  <style>
    body {
      background-color: #f8f9fa;
      padding: 20px;
    }

    .card {
      margin-bottom: 20px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .stat-card {
      text-align: center;
      padding: 20px;
    }

    .stat-number {
      font-size: 2.5rem;
      font-weight: bold;
      color: #0d6efd;
    }

    .dispatch-btn {
      font-size: 1.2rem;
      padding: 15px 30px;
    }

    .log-entry {
      padding: 10px;
      margin: 5px 0;
      border-radius: 5px;
      background-color: #f8f9fa;
    }

    .success {
      border-left: 4px solid #198754;
    }

    .info {
      border-left: 4px solid #0d6efd;
    }

    .warning {
      border-left: 4px solid #ffc107;
    }
  </style>
</head>

<body>
  <div class="container-fluid">
    <div class="row mb-4">
      <div class="col-12">
        <h1 class="display-4">
          <i class="bi bi-heart-fill text-danger"></i>
          Système de Dispatch FIFO des Dons
        </h1>
        <p class="lead">Bureau National de Gestion des Risques et des Catastrophes</p>
      </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="card stat-card">
          <i class="bi bi-box-seam text-primary" style="font-size: 3rem;"></i>
          <div class="stat-number" id="totalDons">0</div>
          <div>Dons Non Utilisés</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card stat-card">
          <i class="bi bi-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
          <div class="stat-number" id="totalBesoins">0</div>
          <div>Besoins Non Satisfaits</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card stat-card">
          <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
          <div class="stat-number" id="totalDispatches">-</div>
          <div>Total Dispatches</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card stat-card">
          <i class="bi bi-bag-fill text-info" style="font-size: 3rem;"></i>
          <div class="stat-number" id="totalQuantity">-</div>
          <div>Quantité Dispatchée</div>
        </div>
      </div>
    </div>

    <!-- Actions principales -->
    <div class="row mb-4">
      <div class="col-md-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">
              <i class="bi bi-plus-circle"></i> Saisir un Don
            </h5>
            <form id="donForm">
              <div class="mb-3">
                <label class="form-label">Type de Besoin</label>
                <select class="form-select" id="typeBesoin" required>
                  <option value="">Sélectionner...</option>
                  <option value="1">Eau</option>
                  <option value="2">Nourriture</option>
                  <option value="3">Médicaments</option>
                  <option value="4">Vêtements</option>
                  <option value="5">Abri</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Quantité</label>
                <input type="number" class="form-control" id="quantity" min="1" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Date de Saisie</label>
                <input type="date" class="form-control" id="dateSaisie" required>
              </div>
              <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-save"></i> Enregistrer le Don
              </button>
            </form>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card">
          <div class="card-body text-center">
            <h5 class="card-title">
              <i class="bi bi-arrow-repeat"></i> Lancer le Dispatch FIFO
            </h5>
            <p class="text-muted">
              Le dispatch va distribuer automatiquement les dons aux besoins selon l'algorithme FIFO
              (First In First Out - Premier Arrivé Premier Servi).
            </p>
            <button id="dispatchBtn" class="btn btn-success dispatch-btn">
              <i class="bi bi-play-circle"></i> Démarrer le Dispatch
            </button>
            <div id="dispatchLoading" class="mt-3" style="display: none;">
              <div class="spinner-border text-success" role="status">
                <span class="visually-hidden">Chargement...</span>
              </div>
              <p class="mt-2">Dispatch en cours...</p>
            </div>
          </div>
        </div>

        <div class="card mt-3">
          <div class="card-body">
            <button id="refreshBtn" class="btn btn-outline-secondary w-100">
              <i class="bi bi-arrow-clockwise"></i> Actualiser les Données
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Logs et historique -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h5><i class="bi bi-clock-history"></i> Logs et Historique</h5>
          </div>
          <div class="card-body" id="logs" style="max-height: 400px; overflow-y: auto;">
            <div class="log-entry info">
              <i class="bi bi-info-circle"></i>
              <strong>Système prêt</strong> - En attente d'actions...
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Détails des dispatches -->
    <div class="row mt-4">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h5><i class="bi bi-box-seam"></i> Dons Disponibles</h5>
          </div>
          <div class="card-body" id="donsDetails" style="max-height: 300px; overflow-y: auto;">
            <p class="text-muted">Chargement...</p>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h5><i class="bi bi-exclamation-triangle"></i> Besoins en Attente</h5>
          </div>
          <div class="card-body" id="besoinsDetails" style="max-height: 300px; overflow-y: auto;">
            <p class="text-muted">Chargement...</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="/assets/js/bootstrap.bundle.min.js"></script>
  <script>
    // Configuration
    const API_BASE = '/api/dons';

    // Initialisation
    document.addEventListener('DOMContentLoaded', function () {
      // Définir la date d'aujourd'hui par défaut
      document.getElementById('dateSaisie').valueAsDate = new Date();

      // Charger les données initiales
      loadReport();

      // Event listeners
      document.getElementById('donForm').addEventListener('submit', handleDonSubmit);
      document.getElementById('dispatchBtn').addEventListener('click', handleDispatch);
      document.getElementById('refreshBtn').addEventListener('click', loadReport);
    });

    // Soumettre un don
    async function handleDonSubmit(e) {
      e.preventDefault();

      const data = {
        id_type_besoin: document.getElementById('typeBesoin').value,
        quantity: parseInt(document.getElementById('quantity').value),
        date_saisie: document.getElementById('dateSaisie').value
      };

      try {
        const response = await fetch(`${API_BASE}`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
          addLog('success', 'Don créé',
            `Don #${result.don_id} enregistré avec succès : ${data.quantity} unités`);
          document.getElementById('donForm').reset();
          document.getElementById('dateSaisie').valueAsDate = new Date();
          loadReport();
        } else {
          addLog('warning', 'Erreur', result.message);
        }
      } catch (error) {
        addLog('warning', 'Erreur', 'Impossible de créer le don : ' + error.message);
      }
    }

    // Lancer le dispatch
    async function handleDispatch() {
      const btn = document.getElementById('dispatchBtn');
      const loading = document.getElementById('dispatchLoading');

      btn.disabled = true;
      loading.style.display = 'block';

      try {
        const response = await fetch(`${API_BASE}/dispatch`, {
          method: 'POST'
        });

        const result = await response.json();

        if (result.success) {
          const stats = result.stats;
          addLog('success', 'Dispatch réussi',
            `${stats.total_dispatches} dispatches effectués, ` +
            `${stats.total_quantity_dispatched} unités dispatchées, ` +
            `${stats.besoins_satisfaits} besoins satisfaits, ` +
            `${stats.dons_utilises} dons épuisés`);

          // Afficher les détails
          if (stats.details && stats.details.length > 0) {
            stats.details.forEach(detail => {
              addLog('info', 'Dispatch détail',
                `Don #${detail.don_id} (${detail.type}, ${detail.don_date}) → ` +
                `Besoin #${detail.besoin_id} (${detail.besoin_libelle} - ${detail.type}, ${detail.ville}) : ${detail.quantity} unités`);
            });
          }

          loadReport();
        } else {
          addLog('warning', 'Erreur dispatch', result.message);
        }
      } catch (error) {
        addLog('warning', 'Erreur', 'Impossible de lancer le dispatch : ' + error.message);
      } finally {
        btn.disabled = false;
        loading.style.display = 'none';
      }
    }

    // Charger le rapport
    async function loadReport() {
      try {
        const response = await fetch(`${API_BASE}/report`);
        const result = await response.json();

        if (result.success) {
          const report = result.report;

          // Mettre à jour les statistiques
          document.getElementById('totalDons').textContent = report.dons_non_utilises.count;
          document.getElementById('totalBesoins').textContent = report.besoins_non_satisfaits.count;

          // Afficher les détails des dons
          displayDons(report.dons_non_utilises.details);

          // Afficher les détails des besoins
          displayBesoins(report.besoins_non_satisfaits.details);
        }
      } catch (error) {
        console.error('Erreur chargement rapport:', error);
      }
    }

    // Afficher les dons
    function displayDons(dons) {
      const container = document.getElementById('donsDetails');

      if (!dons || dons.length === 0) {
        container.innerHTML = '<p class="text-muted">Aucun don disponible</p>';
        return;
      }

      let html = '<div class="list-group">';
      dons.forEach(don => {
        html += `
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${don.besoin_libelle}</strong>
                                <br>
                                <small class="text-muted">${don.type_libelle} - ${don.ville_nom}</small>
                                <br>
                                <small class="text-muted">Don #${don.id} - ${don.date_saisie}</small>
                            </div>
                            <span class="badge bg-primary rounded-pill">${don.quantity_restante} unités</span>
                        </div>
                    </div>
                `;
      });
      html += '</div>';
      container.innerHTML = html;
    }

    // Afficher les besoins
    function displayBesoins(besoins) {
      const container = document.getElementById('besoinsDetails');

      if (!besoins || besoins.length === 0) {
        container.innerHTML = '<p class="text-muted">Aucun besoin en attente</p>';
        return;
      }

      let html = '<div class="list-group">';
      besoins.forEach(besoin => {
        html += `
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${besoin.libelle}</strong>
                                <br>
                                <small class="text-muted">${besoin.type_libelle} - ${besoin.ville_nom}</small>
                                <br>
                                <small class="text-muted">Besoin #${besoin.id}</small>
                            </div>
                            <span class="badge bg-warning rounded-pill">${besoin.quantity_restante} unités</span>
                        </div>
                    </div>
                `;
      });
      html += '</div>';
      container.innerHTML = html;
    }

    // Ajouter un log
    function addLog(type, title, message) {
      const logsContainer = document.getElementById('logs');
      const timestamp = new Date().toLocaleTimeString();

      const logEntry = document.createElement('div');
      logEntry.className = `log-entry ${type}`;

      let icon = 'info-circle';
      if (type === 'success') icon = 'check-circle';
      if (type === 'warning') icon = 'exclamation-triangle';

      logEntry.innerHTML = `
                <i class="bi bi-${icon}"></i>
                <strong>${title}</strong> <small class="text-muted">(${timestamp})</small>
                <br>
                ${message}
            `;

      logsContainer.insertBefore(logEntry, logsContainer.firstChild);

      // Garder seulement les 20 derniers logs
      while (logsContainer.children.length > 20) {
        logsContainer.removeChild(logsContainer.lastChild);
      }
    }
  </script>
</body>

</html>
