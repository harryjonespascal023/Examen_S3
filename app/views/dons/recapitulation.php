<?php
if (!isset($stats)) {
  $stats = [
    'total_besoins_montant' => 0,
    'total_satisfaits_montant' => 0,
    'total_restants_montant' => 0,
    'pourcentage_satisfait' => 0
  ];
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="page-header mb-4">
  <h1 class="display-5 fw-bold">
    <i class="bi bi-clipboard-data"></i> Récapitulation
  </h1>
  <p class="lead text-muted">Vue d'ensemble des besoins totaux, satisfaits et restants en montant</p>
</div>

<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Statistiques Financières</h3>
    <button type="button" class="btn btn-primary" id="btnActualiser">
      <i class="bi bi-arrow-clockwise"></i> Actualiser
    </button>
  </div>

  <div id="loadingIndicator" style="display: none;" class="text-center mb-3">
    <div class="spinner-border text-primary" role="status">
      <span class="visually-hidden">Chargement...</span>
    </div>
    <p class="mt-2 text-muted">Actualisation en cours...</p>
  </div>

  <div id="statsContainer">
    <div class="row g-4 mb-4">
      <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100"
          style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
          <div class="card-body text-center">
            <i class="bi bi-cash-stack" style="font-size: 3rem;"></i>
            <div class="mt-3">
              <div class="text-white-50 small">Besoins Totaux</div>
              <div class="fs-2 fw-bold" id="totalBesoins">
                <?= number_format($stats['total_besoins_montant'], 2) ?> Ar
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100"
          style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
          <div class="card-body text-center">
            <i class="bi bi-check-circle-fill" style="font-size: 3rem;"></i>
            <div class="mt-3">
              <div class="text-white-50 small">Besoins Satisfaits</div>
              <div class="fs-2 fw-bold" id="totalSatisfaits">
                <?= number_format($stats['total_satisfaits_montant'], 2) ?> Ar
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100"
          style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
          <div class="card-body text-center">
            <i class="bi bi-exclamation-triangle-fill" style="font-size: 3rem;"></i>
            <div class="mt-3">
              <div class="text-white-50 small">Besoins Restants</div>
              <div class="fs-2 fw-bold" id="totalRestants">
                <?= number_format($stats['total_restants_montant'], 2) ?> Ar
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card shadow-sm">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
          <i class="bi bi-graph-up"></i> Progression de Satisfaction
        </h5>
      </div>
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-md-8">
            <div class="progress" style="height: 40px;">
              <div
                class="progress-bar progress-bar-striped progress-bar-animated <?= $stats['pourcentage_satisfait'] >= 100 ? 'bg-success' : 'bg-info' ?>"
                role="progressbar" id="progressBar" style="width: <?= min(100, $stats['pourcentage_satisfait']) ?>%;"
                aria-valuenow="<?= $stats['pourcentage_satisfait'] ?>" aria-valuemin="0" aria-valuemax="100">
                <span class="fs-6 fw-bold" id="progressText">
                  <?= number_format($stats['pourcentage_satisfait'], 2) ?>%
                </span>
              </div>
            </div>
          </div>
          <div class="col-md-4 text-center">
            <div class="mt-3 mt-md-0">
              <span
                class="badge bg-<?= $stats['pourcentage_satisfait'] >= 100 ? 'success' : ($stats['pourcentage_satisfait'] >= 50 ? 'warning' : 'danger') ?> fs-5 px-4 py-2"
                id="statusBadge">
                <?php if ($stats['pourcentage_satisfait'] >= 100): ?>
                  <i class="bi bi-check-circle"></i> Complet
                <?php elseif ($stats['pourcentage_satisfait'] >= 50): ?>
                  <i class="bi bi-hourglass-split"></i> En cours
                <?php else: ?>
                  <i class="bi bi-exclamation-triangle"></i> Critique
                <?php endif; ?>
              </span>
            </div>
          </div>
        </div>

        <div class="mt-4">
          <h6>Détails des montants</h6>
          <div class="table-responsive">
            <table class="table table-sm table-bordered">
              <tbody>
                <tr>
                  <td class="fw-bold">Besoins Totaux</td>
                  <td class="text-end" id="detailBesoins">
                    <?= number_format($stats['total_besoins_montant'], 2) ?> Ar
                  </td>
                </tr>
                <tr class="table-success">
                  <td class="fw-bold">Besoins Satisfaits</td>
                  <td class="text-end" id="detailSatisfaits">
                    <?= number_format($stats['total_satisfaits_montant'], 2) ?> Ar
                  </td>
                </tr>
                <tr class="table-danger">
                  <td class="fw-bold">Besoins Restants</td>
                  <td class="text-end" id="detailRestants">
                    <?= number_format($stats['total_restants_montant'], 2) ?> Ar
                  </td>
                </tr>
                <tr class="table-info">
                  <td class="fw-bold">Pourcentage de Satisfaction</td>
                  <td class="text-end" id="detailPourcentage">
                    <?= number_format($stats['pourcentage_satisfait'], 2) ?> %
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="card-footer text-muted text-center">
        <small>Dernière actualisation: <span id="lastUpdate"><?= date('d/m/Y H:i:s') ?></span></small>
      </div>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col-md-12">
      <div class="card shadow-sm">
        <div class="card-body">
          <h6 class="card-title">
            <i class="bi bi-info-circle"></i> Information
          </h6>
          <ul class="mb-0">
            <li><strong>Besoins Totaux</strong> : Montant total de tous les besoins enregistrés (quantité × prix
              unitaire).</li>
            <li><strong>Besoins Satisfaits</strong> : Montant total des besoins qui ont été satisfaits par les dons
              dispatchés.</li>
            <li><strong>Besoins Restants</strong> : Montant total des besoins non encore satisfaits (quantité restante ×
              prix unitaire).</li>
            <li><strong>Actualiser</strong> : Cliquez sur le bouton pour rafraîchir les données en temps réel via Ajax.
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const btnActualiser = document.getElementById('btnActualiser');
    const loadingIndicator = document.getElementById('loadingIndicator');

    btnActualiser.addEventListener('click', function () {
      // Désactiver le bouton et afficher le loading
      btnActualiser.disabled = true;
      loadingIndicator.style.display = 'block';

      // Appel Ajax
      fetch('/api/recapitulation', {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json'
        }
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            updateStats(data.data);
          } else {
            alert('Erreur lors de l\'actualisation : ' + (data.message || 'Erreur inconnue'));
          }
        })
        .catch(error => {
          console.error('Erreur:', error);
          alert('Erreur lors de l\'actualisation des données');
        })
        .finally(() => {
          // Réactiver le bouton et masquer le loading
          btnActualiser.disabled = false;
          loadingIndicator.style.display = 'none';
        });
    });

    function updateStats(stats) {
      // Mettre à jour les valeurs
      document.getElementById('totalBesoins').textContent = formatNumber(stats.total_besoins_montant) + ' Ar';
      document.getElementById('totalSatisfaits').textContent = formatNumber(stats.total_satisfaits_montant) + ' Ar';
      document.getElementById('totalRestants').textContent = formatNumber(stats.total_restants_montant) + ' Ar';

      document.getElementById('detailBesoins').textContent = formatNumber(stats.total_besoins_montant) + ' Ar';
      document.getElementById('detailSatisfaits').textContent = formatNumber(stats.total_satisfaits_montant) + ' Ar';
      document.getElementById('detailRestants').textContent = formatNumber(stats.total_restants_montant) + ' Ar';
      document.getElementById('detailPourcentage').textContent = formatNumber(stats.pourcentage_satisfait) + ' %';

      // Mettre à jour la barre de progression
      const progressBar = document.getElementById('progressBar');
      const progressText = document.getElementById('progressText');
      const pourcentage = Math.min(100, stats.pourcentage_satisfait);

      progressBar.style.width = pourcentage + '%';
      progressBar.setAttribute('aria-valuenow', pourcentage);
      progressText.textContent = formatNumber(stats.pourcentage_satisfait) + '%';

      // Mettre à jour les classes de la barre de progression
      if (stats.pourcentage_satisfait >= 100) {
        progressBar.className = 'progress-bar progress-bar-striped progress-bar-animated bg-success';
      } else {
        progressBar.className = 'progress-bar progress-bar-striped progress-bar-animated bg-info';
      }

      // Mettre à jour le badge de statut
      const statusBadge = document.getElementById('statusBadge');
      if (stats.pourcentage_satisfait >= 100) {
        statusBadge.className = 'badge bg-success fs-5 px-4 py-2';
        statusBadge.innerHTML = '<i class="bi bi-check-circle"></i> Complet';
      } else if (stats.pourcentage_satisfait >= 50) {
        statusBadge.className = 'badge bg-warning fs-5 px-4 py-2';
        statusBadge.innerHTML = '<i class="bi bi-hourglass-split"></i> En cours';
      } else {
        statusBadge.className = 'badge bg-danger fs-5 px-4 py-2';
        statusBadge.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Critique';
      }

      // Mettre à jour l'heure de dernière actualisation
      const now = new Date();
      document.getElementById('lastUpdate').textContent = now.toLocaleString('fr-FR');
    }

    function formatNumber(num) {
      return parseFloat(num).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    }
  });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>

