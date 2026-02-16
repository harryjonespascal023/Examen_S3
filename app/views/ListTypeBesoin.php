<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Liste des types de besoin</title>
	<link rel="stylesheet" href="/assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="/assets/css/modern-theme.css">
</head>
<body>
	<div class="container py-4">
		<div class="d-flex align-items-center justify-content-between mb-4">
			<h1 class="h3 mb-0">Liste des types de besoin</h1>
			<a class="btn btn-primary" href="/types-besoin/create">Ajouter type</a>
		</div>

		<?php if (empty($types)) : ?>
			<div class="alert alert-info">Aucun type enregistre.</div>
		<?php else : ?>
			<div class="table-responsive">
				<table class="table table-striped align-middle">
					<thead>
						<tr>
							<th scope="col">ID</th>
							<th scope="col">Libelle</th>
							<th scope="col" class="text-end">Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($types as $type) : ?>
							<tr>
								<td><?php echo (int)$type->id; ?></td>
								<td><?php echo htmlspecialchars((string)$type->libelle, ENT_QUOTES, 'UTF-8'); ?></td>
								<td class="text-end">
									<a class="btn btn-sm btn-outline-secondary" href="/types-besoin/<?php echo (int)$type->id; ?>/edit">Modifier</a>
									<form class="d-inline" method="post" action="/types-besoin/<?php echo (int)$type->id; ?>/delete" onsubmit="return confirm('Supprimer ce type ?');">
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

