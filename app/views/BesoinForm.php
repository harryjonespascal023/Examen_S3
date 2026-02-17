<?php
$pageTitle = ($title ?? 'Ajouter un besoin') . ' - BNGRC';
$currentPage = 'besoins';
$action = $action ?? '/besoins';
$title = $title ?? 'Ajouter un besoin';
$submitLabel = $submitLabel ?? 'Enregistrer';
$idVilleValue = 0;
$idTypeValue = 0;
$prixUnitaireValue = 0;
$quantityValue = 0;
$quantityRestanteValue = 0;
$libelleValue = '';
$dateBesoinValue = date('Y-m-d');

if (!empty($besoin)) {
	$idVilleValue = (int)$besoin->id_ville;
	$idTypeValue = (int)$besoin->id_type_besoin;
	$prixUnitaireValue = (float)$besoin->prix_unitaire;
	$quantityValue = (int)$besoin->quantity;
	$quantityRestanteValue = (int)$besoin->quantity_restante;
	$libelleValue = (string)$besoin->libelle;
} else {
	$quantityRestanteValue = $quantityValue;
}

include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
	<div class="d-flex align-items-center justify-content-between">
		<h1><i class="bi bi-exclamation-triangle-fill text-warning"></i> <?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>
		<a class="btn btn-secondary" href="/besoins">
			<i class="bi bi-arrow-left"></i> Retour
		</a>
	</div>
</div>

<div class="card">
	<div class="card-header">
		<i class="bi bi-pencil-square"></i> Informations du besoin
	</div>
	<div class="card-body">
		<form method="post" action="<?php echo htmlspecialchars($action, ENT_QUOTES, 'UTF-8'); ?>">
			<div class="row">
				<div class="col-md-6 mb-3">
					<label class="form-label" for="id_ville">
						<i class="bi bi-geo-alt"></i> Ville *
					</label>
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
				<div class="col-md-6 mb-3">
					<label class="form-label" for="id_type_besoin">
						<i class="bi bi-tag"></i> Type de besoin *
					</label>
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
			</div>
			<div class="mb-3">
				<label class="form-label" for="libelle">
					<i class="bi bi-journal-text"></i> Libellé *
				</label>
				<input class="form-control" id="libelle" name="libelle" type="text" required
					   value="<?php echo htmlspecialchars($libelleValue, ENT_QUOTES, 'UTF-8'); ?>"
					   placeholder="Description spécifique du besoin">
				<small class="form-text text-muted">
					Exemple: "Eau potable pour école primaire de Tanambao"
				</small>
			</div>
			<div class="row">
				<div class="col-md-4 mb-3">
					<label class="form-label" for="prix_unitaire">
						<i class="bi bi-currency-dollar"></i> Prix unitaire (Ar) *
					</label>
					<input class="form-control" id="prix_unitaire" name="prix_unitaire"
						   type="number" step="0.01" min="0" required
						   value="<?php echo htmlspecialchars((string)$prixUnitaireValue, ENT_QUOTES, 'UTF-8'); ?>"
						   placeholder="0.00">
				</div>
				<div class="col-md-4 mb-3">
					<label class="form-label" for="quantity">
						<i class="bi bi-box"></i> Quantité *
					</label>
					<input class="form-control" id="quantity" name="quantity" type="number" min="0" required
						   value="<?php echo (int)$quantityValue; ?>"
						   placeholder="0">
				</div>
				<div class="col-md-4 mb-3">
					<label class="form-label" for="quantity_restante">
						<i class="bi bi-boxes"></i> Quantité restante *
					</label>
					<input class="form-control" id="quantity_restante" name="quantity_restante"
						   type="number" min="0" required
						   value="<?php echo (int)$quantityRestanteValue; ?>"
						   placeholder="0">
					<small class="form-text text-muted">
						Quantité non encore satisfaite
					</small>
				</div>
			</div>
			<div class="d-grid gap-2 d-md-flex justify-content-md-end">
				<a href="/besoins" class="btn btn-secondary">
					<i class="bi bi-x-circle"></i> Annuler
				</a>
				<button class="btn btn-primary" type="submit">
					<i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($submitLabel, ENT_QUOTES, 'UTF-8'); ?>
				</button>
			</div>
		</form>
	</div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const typeSelect = document.getElementById('id_type_besoin');
    const libelleInput = document.getElementById('libelle');
    const libelleRequired = document.getElementById('libelleRequired');
    const libelleHelp = document.getElementById('libelleHelp');
    const prixInput = document.getElementById('prix_unitaire');
    const prixContainer = document.getElementById('prix_unitaire').closest('.col-md-4');
    const prixRequired = document.querySelector('.prix-required');

    function updateFields() {
      const selectedOption = typeSelect.options[typeSelect.selectedIndex];
      const typeLibelle = selectedOption ? selectedOption.getAttribute('data-libelle') : '';

      if (typeLibelle === 'argent') {
        // Masquer libellé et prix pour argent
        libelleInput.required = false;
        libelleRequired.style.display = 'none';
        libelleInput.placeholder = 'Pas de libellé pour l\'argent';
        libelleHelp.textContent = 'Les besoins en argent n\'ont pas de libellé';
        libelleInput.value = '';

        prixInput.required = false;
        prixInput.value = '';
        prixContainer.style.display = 'none';
        if (prixRequired) prixRequired.style.display = 'none';
      } else {
        // Afficher libellé et prix pour nature/matériaux
        libelleInput.required = true;
        libelleRequired.style.display = 'inline';
        libelleInput.placeholder = 'Description spécifique du besoin';
        libelleHelp.textContent = 'Exemple: "Eau potable pour école primaire de Tanambao"';

        prixInput.required = true;
        prixContainer.style.display = '';
        if (prixRequired) prixRequired.style.display = 'inline';
      }
    }

    typeSelect.addEventListener('change', updateFields);
    updateFields();
  });
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

