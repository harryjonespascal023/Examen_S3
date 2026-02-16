<?php
$action = $action ?? '/besoins';
$title = $title ?? 'Ajouter un besoin';
$submitLabel = $submitLabel ?? 'Enregistrer';
$idVilleValue = 0;
$idTypeValue = 0;
$prixUnitaireValue = 0;
$quantityValue = 0;
$quantityRestanteValue = 0;

if (!empty($besoin)) {
	$idVilleValue = (int)$besoin->id_ville;
	$idTypeValue = (int)$besoin->id_type_besoin;
	$prixUnitaireValue = (float)$besoin->prix_unitaire;
	$quantityValue = (int)$besoin->quantity;
	$quantityRestanteValue = (int)$besoin->quantity_restante;
} else {
	$quantityRestanteValue = $quantityValue;
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
			<a class="btn btn-outline-secondary" href="/besoins">Retour</a>
		</div>

		<div class="card">
			<div class="card-body">
				<form method="post" action="<?php echo htmlspecialchars($action, ENT_QUOTES, 'UTF-8'); ?>">
					<div class="mb-3">
						<label class="form-label" for="id_ville">Ville</label>
						<select class="form-select" id="id_ville" name="id_ville" required>
							<option value="">Choisir une ville</option>
							<?php foreach (($villes ?? []) as $ville) : ?>
								<?php $selected = (int)$ville->id === $idVilleValue ? 'selected' : ''; ?>
								<option value="<?php echo (int)$ville->id; ?>" <?php echo $selected; ?>>
									<?php echo htmlspecialchars((string)$ville->nom, ENT_QUOTES, 'UTF-8'); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="mb-3">
						<label class="form-label" for="id_type_besoin">Type de besoin</label>
						<select class="form-select" id="id_type_besoin" name="id_type_besoin" required>
							<option value="">Choisir un type</option>
							<?php foreach (($types ?? []) as $type) : ?>
								<?php $selected = (int)$type->id === $idTypeValue ? 'selected' : ''; ?>
								<option value="<?php echo (int)$type->id; ?>" <?php echo $selected; ?>>
									<?php echo htmlspecialchars((string)$type->libelle, ENT_QUOTES, 'UTF-8'); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="mb-3">
						<label class="form-label" for="prix_unitaire">Prix unitaire</label>
						<input class="form-control" id="prix_unitaire" name="prix_unitaire" type="number" step="0.01" min="0" required value="<?php echo htmlspecialchars((string)$prixUnitaireValue, ENT_QUOTES, 'UTF-8'); ?>">
					</div>
					<div class="mb-3">
						<label class="form-label" for="quantity">Quantite</label>
						<input class="form-control" id="quantity" name="quantity" type="number" min="0" required value="<?php echo (int)$quantityValue; ?>">
					</div>
					<div class="mb-3">
						<label class="form-label" for="quantity_restante">Quantite restante</label>
						<input class="form-control" id="quantity_restante" name="quantity_restante" type="number" min="0" required value="<?php echo (int)$quantityRestanteValue; ?>">
					</div>
					<button class="btn btn-primary" type="submit"><?php echo htmlspecialchars($submitLabel, ENT_QUOTES, 'UTF-8'); ?></button>
				</form>
			</div>
		</div>
	</div>
</body>
</html>

