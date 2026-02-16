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

<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link rel="stylesheet" href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES) ?>/assets/css/bootstrap.min.css">
  </head>
  <body class="bg-light">
    <div class="container py-4">
      <h1 class="h3 mb-4">Dashboard</h1>

      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <div class="text-muted">Total dons reçus</div>
              <div class="fs-4 fw-semibold"><?= (int) $totaux['total_recus'] ?></div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <div class="text-muted">Total dons attribués</div>
              <div class="fs-4 fw-semibold"><?= (int) $totaux['total_attribues'] ?></div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <div class="text-muted">Total dons restants</div>
              <div class="fs-4 fw-semibold"><?= (int) $totaux['total_reste'] ?></div>
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-sm table-striped align-middle mb-0">
              <thead>
                <tr>
                  <th>Ville</th>
                  <?php foreach ($types as $t): ?>
                    <th class="text-end">Besoin (<?= htmlspecialchars($t, ENT_QUOTES) ?>)</th>
                    <th class="text-end">Satisfait (<?= htmlspecialchars($t, ENT_QUOTES) ?>)</th>
                    <th class="text-end">Restant (<?= htmlspecialchars($t, ENT_QUOTES) ?>)</th>
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
                      <td><?= htmlspecialchars($ville['nom'] ?? '', ENT_QUOTES) ?></td>
                      <?php foreach ($types as $t):
                        $stats = $ville['types'][$t] ?? [
                          'besoin_qty' => 0,
                          'attribue_qty' => 0,
                          'restant_qty' => 0,
                        ];
                      ?>
                        <td class="text-end"><?= (int) ($stats['besoin_qty'] ?? 0) ?></td>
                        <td class="text-end"><?= (int) ($stats['attribue_qty'] ?? 0) ?></td>
                        <td class="text-end"><?= (int) ($stats['restant_qty'] ?? 0) ?></td>
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
  </body>
</html>
