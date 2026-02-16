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

$totalRecus = (int) ($totaux['total_recus'] ?? 0);
$totalAttribues = (int) ($totaux['total_attribues'] ?? 0);
$totalReste = (int) ($totaux['total_reste'] ?? 0);
$globalPct = $totalRecus > 0 ? (int) round(($totalAttribues / $totalRecus) * 100) : 0;

function format_number($n): string {
  if ($n === null) {
    return '0';
  }
  if (is_float($n)) {
    return number_format($n, 2, '.', ' ');
  }
  return number_format((float)$n, 0, '.', ' ');
}

?>

<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Dons</title>
    <link rel="stylesheet" href="<?= htmlspecialchars(BASE_URL, ENT_QUOTES) ?>/assets/css/bootstrap.min.css">
    <style>
      body { background: radial-gradient(1200px circle at 10% 10%, rgba(13,110,253,.08), transparent 40%), radial-gradient(900px circle at 90% 20%, rgba(25,135,84,.08), transparent 35%), #f8f9fa; }
      .stat-card { border: 0; box-shadow: 0 8px 24px rgba(0,0,0,.06); }
      .city-card { border: 0; box-shadow: 0 8px 24px rgba(0,0,0,.06); }
      .kpi { font-variant-numeric: tabular-nums; }
      .city-title { letter-spacing: .2px; }
      .type-row { border-radius: .75rem; background: rgba(33,37,41,.02); }
      .progress { height: .6rem; border-radius: 999px; }
      .progress-bar { border-radius: 999px; }
      .badge-soft { background: rgba(13,110,253,.08); color: #0d6efd; }
      .badge-soft-success { background: rgba(25,135,84,.10); color: #198754; }
      .badge-soft-warning { background: rgba(255,193,7,.16); color: #8a6d00; }
    </style>
  </head>
  <body class="bg-light">
    <div class="container py-4">
      <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-2 mb-4">
        <div>
          <h1 class="h3 mb-1">Dashboard des dons</h1>
          <div class="text-muted">Suivi des besoins par ville, dons attribués et restants.</div>
        </div>
        <div class="text-muted small">Dernière mise à jour: <?= date('Y-m-d H:i') ?></div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <div class="card stat-card">
            <div class="card-body">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="text-muted">Total dons reçus</div>
                <span class="badge rounded-pill badge-soft">Global</span>
              </div>
              <div class="fs-3 fw-semibold kpi"><?= format_number($totalRecus) ?></div>
              <div class="mt-3">
                <div class="d-flex justify-content-between small text-muted mb-1">
                  <span>Attribués</span>
                  <span><?= $globalPct ?>%</span>
                </div>
                <div class="progress" role="progressbar" aria-label="Progression globale" aria-valuenow="<?= $globalPct ?>" aria-valuemin="0" aria-valuemax="100">
                  <div class="progress-bar bg-success" style="width: <?= $globalPct ?>%"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card stat-card">
            <div class="card-body">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="text-muted">Total attribués</div>
                <span class="badge rounded-pill badge-soft-success">Dispatch</span>
              </div>
              <div class="fs-3 fw-semibold kpi"><?= format_number($totalAttribues) ?></div>
              <div class="text-muted small mt-2">Somme des quantités dispatchées vers les besoins.</div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card stat-card">
            <div class="card-body">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="text-muted">Total restants</div>
                <span class="badge rounded-pill badge-soft-warning">À attribuer</span>
              </div>
              <div class="fs-3 fw-semibold kpi"><?= format_number($totalReste) ?></div>
              <div class="text-muted small mt-2">Quantité de dons non encore attribuée globalement.</div>
            </div>
          </div>
        </div>
      </div>

      <?php if (count($villes) === 0): ?>
        <div class="card city-card">
          <div class="card-body text-center text-muted py-5">Aucune donnée à afficher.</div>
        </div>
      <?php else: ?>
        <div class="row g-3">
          <?php foreach ($villes as $ville): ?>
            <?php
              $villeNom = (string)($ville['nom'] ?? '');
              $villeTypes = (is_array($ville['types'] ?? null)) ? $ville['types'] : [];
              $villeBesoin = 0;
              $villeAttribue = 0;
              $villeRestant = 0;
              $villeBesoinValeur = 0.0;
              $villeAttribueValeur = 0.0;
              $villeRestantValeur = 0.0;
              foreach ($types as $t) {
                $s = $villeTypes[$t] ?? [];
                $villeBesoin += (int)($s['besoin_qty'] ?? 0);
                $villeAttribue += (int)($s['attribue_qty'] ?? 0);
                $villeRestant += (int)($s['restant_qty'] ?? 0);
                $villeBesoinValeur += (float)($s['besoin_valeur'] ?? 0);
                $villeAttribueValeur += (float)($s['attribue_valeur'] ?? 0);
                $villeRestantValeur += (float)($s['restant_valeur'] ?? 0);
              }
              $pct = $villeBesoin > 0 ? (int)round(($villeAttribue / $villeBesoin) * 100) : 0;
              $pct = max(0, min(100, $pct));
            ?>

            <div class="col-12">
              <div class="card city-card">
                <div class="card-body">
                  <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                    <div>
                      <div class="d-flex align-items-center gap-2 mb-1">
                        <h2 class="h5 mb-0 city-title"><?= htmlspecialchars($villeNom, ENT_QUOTES) ?></h2>
                        <span class="badge text-bg-light">Ville</span>
                      </div>
                      <div class="text-muted small">Besoin total: <span class="fw-semibold kpi"><?= format_number($villeBesoin) ?></span> | Attribué: <span class="fw-semibold kpi"><?= format_number($villeAttribue) ?></span> | Restant: <span class="fw-semibold kpi"><?= format_number($villeRestant) ?></span></div>
                      <div class="text-muted small">Valeur: <span class="fw-semibold kpi"><?= format_number($villeAttribueValeur) ?></span> / <span class="fw-semibold kpi"><?= format_number($villeBesoinValeur) ?></span></div>
                    </div>
                    <div class="text-lg-end">
                      <div class="text-muted small mb-1">Taux de satisfaction</div>
                      <div class="d-flex align-items-center gap-2 justify-content-lg-end">
                        <div style="min-width:220px" class="w-100 w-lg-auto">
                          <div class="progress" role="progressbar" aria-label="Satisfaction" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar bg-success" style="width: <?= $pct ?>%"></div>
                          </div>
                        </div>
                        <div class="fw-semibold kpi"><?= $pct ?>%</div>
                      </div>
                    </div>
                  </div>

                  <div class="mt-3">
                    <div class="row g-2">
                      <?php foreach ($types as $t): ?>
                        <?php
                          $s = $villeTypes[$t] ?? [
                            'besoin_qty' => 0,
                            'attribue_qty' => 0,
                            'restant_qty' => 0,
                            'besoin_valeur' => 0.0,
                            'attribue_valeur' => 0.0,
                            'restant_valeur' => 0.0,
                          ];
                          $bq = (int)($s['besoin_qty'] ?? 0);
                          $aq = (int)($s['attribue_qty'] ?? 0);
                          $rq = (int)($s['restant_qty'] ?? 0);
                          $bv = (float)($s['besoin_valeur'] ?? 0);
                          $av = (float)($s['attribue_valeur'] ?? 0);
                          $rv = (float)($s['restant_valeur'] ?? 0);
                          $tpct = $bq > 0 ? (int)round(($aq / $bq) * 100) : 0;
                          $tpct = max(0, min(100, $tpct));
                        ?>

                        <div class="col-12 col-lg-4">
                          <div class="p-3 type-row">
                            <div class="d-flex align-items-start justify-content-between">
                              <div>
                                <div class="fw-semibold mb-1"><?= htmlspecialchars($t, ENT_QUOTES) ?></div>
                                <div class="small text-muted">Besoin: <span class="fw-semibold kpi"><?= format_number($bq) ?></span> | Attribué: <span class="fw-semibold kpi"><?= format_number($aq) ?></span> | Restant: <span class="fw-semibold kpi"><?= format_number($rq) ?></span></div>
                                <div class="small text-muted">Valeur: <span class="fw-semibold kpi"><?= format_number($av) ?></span> / <span class="fw-semibold kpi"><?= format_number($bv) ?></span></div>
                              </div>
                              <span class="badge rounded-pill text-bg-success"><?= $tpct ?>%</span>
                            </div>
                            <div class="mt-2">
                              <div class="progress" role="progressbar" aria-label="Satisfaction par type" aria-valuenow="<?= $tpct ?>" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar bg-success" style="width: <?= $tpct ?>%"></div>
                              </div>
                            </div>
                            <div class="mt-2 small text-muted">Reste (valeur): <span class="fw-semibold kpi"><?= format_number($rv) ?></span></div>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <script src="<?= htmlspecialchars(BASE_URL, ENT_QUOTES) ?>/assets/js/bootstrap.bundle.min.js"></script>
  </body>
</html>

