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
                            <button type="submit" class="btn btn-success btn-lg" 
                                    <?= $report['dons_non_utilises']['count'] == 0 || $report['besoins_non_satisfaits']['count'] == 0 ? 'disabled' : '' ?>>
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
                                <label for="id_type_besoin" class="form-label">Type de Besoin *</label>
                                <select class="form-select" id="id_type_besoin" name="id_type_besoin" required>
                                    <option value="">Sélectionner un type...</option>
                                    <?php foreach ($typesBesoin as $type): ?>
                                        <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['libelle']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantité *</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" 
                                       min="1" required placeholder="Entrer la quantité">
                            </div>
                            <div class="mb-3">
                                <label for="date_saisie" class="form-label">Date de Saisie *</label>
                                <input type="date" class="form-control" id="date_saisie" name="date_saisie" 
                                       value="<?= date('Y-m-d') ?>" required>
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
                                                <h6 class="mb-1"><?= htmlspecialchars($besoin['type_libelle']) ?></h6>
                                                <p class="mb-1 text-muted small">
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
                                                <h6 class="mb-1"><?= htmlspecialchars($don['type_libelle']) ?></h6>
                                                <p class="mb-1 text-muted small">
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
                                            <th>Type</th>
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
                                                <td><?= htmlspecialchars($don['type_libelle']) ?></td>
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
