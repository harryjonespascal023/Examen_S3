<?php
$libelleValue = '';
$action = $action ?? '/types-besoin';
$title = $title ?? 'Ajouter un type de besoin';
$submitLabel = $submitLabel ?? 'Enregistrer';

if (!empty($typeBesoin)) {
	$libelleValue = (string)$typeBesoin->libelle;
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
			<a class="btn btn-outline-secondary" href="/types-besoin">Retour</a>
		</div>

		<div class="card">
			<div class="card-body">
				<form method="post" action="<?php echo htmlspecialchars($action, ENT_QUOTES, 'UTF-8'); ?>">
					<div class="mb-3">
						<label class="form-label" for="libelle">Libelle</label>
						<input class="form-control" id="libelle" name="libelle" type="text" required value="<?php echo htmlspecialchars($libelleValue, ENT_QUOTES, 'UTF-8'); ?>">
					</div>
					<button class="btn btn-primary" type="submit"><?php echo htmlspecialchars($submitLabel, ENT_QUOTES, 'UTF-8'); ?></button>
				</form>
			</div>
		</div>
	</div>
</body>
</html>

