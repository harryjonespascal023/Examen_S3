<?php

if (!isset($villes)) {
  $villes = [];
}
if (!isset($totaux)) {
  $totaux = [
    'total_recus' => 0,
    'total_attribues' => 0,
    'total_reste' => 0,
  ];
}

$types = [];
foreach ($villes as $ville) {
  if (!isset($ville['types']) || !is_array($ville['types'])) {
    continue;
  }
  foreach ($ville['types'] as $typeLibelle => $stats) {
    $types[$typeLibelle] = true;
  }
}
$types = array_keys($types);
sort($types);

?>

<?php include __DIR__ . '/includes/header.php'; ?>

    <div class="page-header mb-4">
        <h1 class="display-5 fw-bold">
            <i class="bi bi-speedometer2"></i> Dashboard
        </h1>
        <p class="lead text-muted">Vue d'ensemble des dons et besoins par ville</p>
    </div>

    <div class="container">
      <h1 class="h3 mb-4">Dashboard</h1>

      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div class="card-body text-center">
              <i class="bi bi-inbox-fill" style="font-size: 2.5rem;"></i>
              <div class="mt-2">
                <div class="text-white-50 small">Total dons reçus</div>
                <div class="fs-3 fw-bold"><?= (int) $totaux['total_recus'] ?></div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
            <div class="card-body text-center">
              <i class="bi bi-check-circle-fill" style="font-size: 2.5rem;"></i>
              <div class="mt-2">
                <div class="text-white-50 small">Total dons attribués</div>
                <div class="fs-3 fw-bold"><?= (int) $totaux['total_attribues'] ?></div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
            <div class="card-body text-center">
              <i class="bi bi-box-seam-fill" style="font-size: 2.5rem;"></i>
              <div class="mt-2">
                <div class="text-white-50 small">Total dons restants</div>
                <div class="fs-3 fw-bold"><?= (int) $totaux['total_reste'] ?></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card shadow-sm border-0">
        <div class="card-header text-white" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
          <h5 class="mb-0">
            <i class="bi bi-table"></i> Détail par Ville et Type
          </h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-sm table-hover table-bordered align-middle mb-0">
              <thead class="table-dark">
                <tr>
                  <th><i class="bi bi-geo-alt-fill"></i> Ville</th>
                  <?php foreach ($types as $t): ?>
                    <th class="text-end" colspan="3">
                      <span class="badge bg-secondary"><?= htmlspecialchars($t, ENT_QUOTES) ?></span>
                    </th>
                  <?php endforeach; ?>
                </tr>
                <tr class="table-light">
                  <th></th>
                  <?php foreach ($types as $t): ?>
                    <th class="text-end small">Besoin</th>
                    <th class="text-end small">Satisfait</th>
                    <th class="text-end small">Restant</th>
                  <?php endforeach; ?>
                </tr>
              </thead>
              <tbody>
                <?php if (count($villes) === 0): ?>
                  <tr>
                    <td colspan="<?= 1 + (count($types) * 3) ?>" class="text-center text-muted py-4">Aucune donnée</td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($villes as $ville): ?>
                    <tr>
                      <td class="fw-bold">
                        <i class="bi bi-geo-alt-fill text-danger"></i> 
                        <?= htmlspecialchars($ville['nom'] ?? '', ENT_QUOTES) ?>
                      </td>
                      <?php foreach ($types as $t):
                        $stats = $ville['types'][$t] ?? [
                          'besoin_qty' => 0,
                          'attribue_qty' => 0,
                          'restant_qty' => 0,
                        ];
                        $besoin = (int) ($stats['besoin_qty'] ?? 0);
                        $satisfait = (int) ($stats['attribue_qty'] ?? 0);
                        $restant = (int) ($stats['restant_qty'] ?? 0);
                        $pourcentage = $besoin > 0 ? ($satisfait / $besoin * 100) : 0;
                      ?>
                        <td class="text-end"><?= $besoin ?></td>
                        <td class="text-end">
                          <?php if ($satisfait > 0): ?>
                            <span class="badge bg-success"><?= $satisfait ?></span>
                          <?php else: ?>
                            <?= $satisfait ?>
                          <?php endif; ?>
                        </td>
                        <td class="text-end">
                          <?php if ($restant > 0): ?>
                            <span class="badge bg-warning text-dark"><?= $restant ?></span>
                          <?php else: ?>
                            <?= $restant ?>
                          <?php endif; ?>
                        </td>
                      <?php endforeach; ?>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

<?php include __DIR__ . '/includes/footer.php'; ?>
