<?php
$pageTitle = 'Liste des Villes - BNGRC';
$currentPage = 'villes';
include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div class="d-flex align-items-center justify-content-between">
        <h1><i class="bi bi-geo-alt-fill text-danger"></i> Liste des Villes</h1>
        <a class="btn btn-primary" href="/villes/create">
            <i class="bi bi-plus-circle"></i> Ajouter une ville
        </a>
    </div>
</div>

<?php if (empty($villes)) : ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Aucune ville enregistrée.
    </div>
<?php else : ?>
    <div class="card">
        <div class="card-header">
            <i class="bi bi-list"></i> <?= count($villes) ?> ville(s) enregistrée(s)
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col"><i class="bi bi-hash"></i> ID</th>
                            <th scope="col"><i class="bi bi-geo-alt"></i> Nom</th>
                            <th scope="col"><i class="bi bi-exclamation-triangle"></i> Nombre de sinistres</th>
                            <th scope="col" class="text-end"><i class="bi bi-gear"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($villes as $ville) : ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?php echo (int)$ville->id; ?></span></td>
                                <td><strong><?php echo htmlspecialchars((string)$ville->nom, ENT_QUOTES, 'UTF-8'); ?></strong></td>
                                <td>
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-exclamation-triangle"></i> <?php echo (int)$ville->nombre_sinistres; ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-info" href="/villes/<?php echo (int)$ville->id; ?>/edit">
                                        <i class="bi bi-pencil"></i> Modifier
                                    </a>
                                    <form class="d-inline" method="post" action="/villes/<?php echo (int)$ville->id; ?>/delete">
                                        <button class="btn btn-sm btn-danger" type="submit">
                                            <i class="bi bi-trash"></i> Supprimer
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
