<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Liste des besoins</title>
	<link rel="stylesheet" href="/assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="/assets/css/modern-theme.css">
</head>
<body>
	<div class="container py-4">
		<div class="d-flex align-items-center justify-content-between mb-4">
			<h1 class="h3 mb-0">Liste des besoins</h1>
			<a class="btn btn-primary" href="/besoins/create">Ajouter besoin</a>
		</div>

		<?php if (empty($besoins)) : ?>
			<div class="alert alert-info">Aucun besoin enregistre.</div>
		<?php else : ?>
			<div class="table-responsive">
				<table class="table table-striped align-middle">
					<thead>
						<tr>
							<th scope="col">ID</th>
							<th scope="col">Ville</th>
							<th scope="col">Type</th>
							<th scope="col">Prix unitaire</th>
							<th scope="col">Quantite</th>
							<th scope="col">Restant</th>
							<th scope="col" class="text-end">Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($besoins as $besoin) : ?>
							<tr>
								<td><?php echo (int)$besoin->id; ?></td>
								<td><?php echo htmlspecialchars((string)$besoin->ville_nom, ENT_QUOTES, 'UTF-8'); ?></td>
								<td><?php echo htmlspecialchars((string)$besoin->type_libelle, ENT_QUOTES, 'UTF-8'); ?></td>
								<td><?php echo number_format((float)$besoin->prix_unitaire, 2, '.', ''); ?></td>
								<td><?php echo (int)$besoin->quantity; ?></td>
								<td><?php echo (int)$besoin->quantity_restante; ?></td>
								<td class="text-end">
									<a class="btn btn-sm btn-outline-secondary" href="/besoins/<?php echo (int)$besoin->id; ?>/edit">Modifier</a>
									<form class="d-inline" method="post" action="/besoins/<?php echo (int)$besoin->id; ?>/delete" onsubmit="return confirm('Supprimer ce besoin ?');">
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

