<?php include __DIR__ . '/../includes/header.php'; ?>

    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="display-5 fw-bold">
                    <i class="bi bi-clock-history"></i> Historique des Dispatches
                </h1>
                <p class="lead text-muted">Suivi complet des distributions effectuées</p>
            </div>
            <a href="/dons" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="container">

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
                    <div class="card shadow-sm border-0">
                        <div class="card-header text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <h5 class="mb-0">
                                <i class="bi bi-graph-up"></i> Statistiques Globales
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <div class="p-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; color: white;">
                                        <h3 class="mb-0"><?= count($history) ?></h3>
                                        <p class="mb-0 small">Total Dispatches</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-3" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); border-radius: 10px; color: white;">
                                        <h3 class="mb-0"><?= array_sum(array_column($history, 'quantity')) ?></h3>
                                        <p class="mb-0 small">Unités Distribuées</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-3" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 10px; color: white;">
                                        <h3 class="mb-0"><?= count(array_unique(array_column($history, 'id_don'))) ?></h3>
                                        <p class="mb-0 small">Dons Utilisés</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-3" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 10px; color: white;">
                                        <h3 class="mb-0"><?= count(array_unique(array_column($history, 'id_besoin'))) ?></h3>
                                        <p class="mb-0 small">Besoins Concernés</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header text-white" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <h5 class="mb-0">
                                <i class="bi bi-list-ul"></i> Détail des Dispatches
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th><i class="bi bi-hash"></i> ID</th>
                                            <th><i class="bi bi-calendar-event"></i> Date Dispatch</th>
                                            <th><i class="bi bi-box"></i> Don</th>
                                            <th><i class="bi bi-calendar"></i> Date Don</th>
                                            <th><i class="bi bi-card-text"></i> Besoin</th>
                                            <th><i class="bi bi-tag"></i> Type</th>
                                            <th><i class="bi bi-geo-alt"></i> Ville</th>
                                            <th class="text-end"><i class="bi bi-box-seam"></i> Quantité</th>
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
                                                    <strong class="text-primary"><?= htmlspecialchars($dispatch['besoin_libelle']) ?></strong>
                                                    <br><small class="text-muted">Besoin #<?= $dispatch['id_besoin'] ?></small>
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
                    <div class="card shadow-sm border-0">
                        <div class="card-header text-white" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
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
                    <div class="card shadow-sm border-0">
                        <div class="card-header text-white" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
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

<?php include __DIR__ . '/../includes/footer.php'; ?>
