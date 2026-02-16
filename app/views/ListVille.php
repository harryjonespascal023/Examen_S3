<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des villes</title>
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/modern-theme.css">
</head>
<body>
    <div class="container py-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0">Liste des villes</h1>
            <a class="btn btn-primary" href="/villes/create">Ajouter ville</a>
        </div>

        <?php if (empty($villes)) : ?>
            <div class="alert alert-info">Aucune ville enregistree.</div>
        <?php else : ?>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Nom</th>
                            <th scope="col">Nombre de sinistres</th>
                            <th scope="col" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($villes as $ville) : ?>
                            <tr>
                                <td><?php echo (int)$ville->id; ?></td>
                                <td><?php echo htmlspecialchars((string)$ville->nom, ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo (int)$ville->nombre_sinistres; ?></td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-secondary" href="/villes/<?php echo (int)$ville->id; ?>/edit">Modifier</a>
                                    <form class="d-inline" method="post" action="/villes/<?php echo (int)$ville->id; ?>/delete" onsubmit="return confirm('Supprimer cette ville ?');">
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
