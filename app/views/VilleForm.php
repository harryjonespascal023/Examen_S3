<?php
$nomValue = '';
$nombreValue = 0;
$action = $action ?? '/villes';
$title = $title ?? 'Ajouter une ville';
$submitLabel = $submitLabel ?? 'Enregistrer';

if (!empty($ville)) {
    $nomValue = (string)$ville->nom;
    $nombreValue = (int)$ville->nombre_sinistres;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/modern-theme.css">
</head>
<body>
    <div class="container py-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>
            <a class="btn btn-outline-secondary" href="/villes">Retour</a>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="post" action="<?php echo htmlspecialchars($action, ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="mb-3">
                        <label class="form-label" for="nom">Nom</label>
                        <input class="form-control" id="nom" name="nom" type="text" required value="<?php echo htmlspecialchars($nomValue, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="nombre_sinistres">Nombre de sinistres</label>
                        <input class="form-control" id="nombre_sinistres" name="nombre_sinistres" type="number" min="0" required value="<?php echo (int)$nombreValue; ?>">
                    </div>
                    <button class="btn btn-primary" type="submit"><?php echo htmlspecialchars($submitLabel, ENT_QUOTES, 'UTF-8'); ?></button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
