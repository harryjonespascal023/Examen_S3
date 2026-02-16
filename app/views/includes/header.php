<?php
$user = (new \app\repository\UserRepository(Flight::db()))->getUserById($_SESSION['user_id'] ?? null);
$statut = $_SESSION['statut'] ?? null;
$categories = (new \app\repository\ObjetRepository(Flight::db()))->getAllCategories();
?>
<!doctype html>
<html lang="fr" data-theme="light">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $titre ?? 'Page' ?> - TAKALO</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/bootstrap-icons/font/bootstrap-icons.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/modern-theme.css">
  <script src="<?= BASE_URL ?>/assets/js/bootstrap.bundle.js"></script>
</head>

<body>
  <header class="modern-header">
    <div class="container-fluid">
      <div class="d-flex align-items-center justify-content-between gap-3">

        <!-- Logo -->
        <a href="/accueil" class="d-flex align-items-center text-decoration-none">
          <i class="bi bi-shop logo-icon me-2"></i>
          <span class="logo-brand">TAKALO</span>
        </a>

        <!-- Recherche -->
        <div class="flex-grow-1 mx-3 position-relative" style="max-width: 600px;">
          <button class="search-btn w-100 d-flex align-items-center justify-content-start border-0" type="button"
            data-bs-toggle="collapse" data-bs-target="#searchCollapse" aria-expanded="false"
            aria-controls="searchCollapse">
            <i class="bi bi-search me-2"></i>
            <span>Rechercher des objets...</span>
          </button>

          <div class="collapse position-absolute start-0 w-100 mt-2" id="searchCollapse" style="z-index: 1000;">
            <div class="modern-card p-3">
              <form class="d-flex flex-column flex-md-row gap-2" method="GET" action="/recherche">
                <input type="search" name="mot_cle" id="mot_cle" class="form-control modern-input flex-grow-1"
                  placeholder="Mot clé...">
                <select name="categorie" id="categorie" class="form-select modern-select flex-grow-1">
                  <option value="">Toutes les catégories</option>
                  <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['libelle']) ?></option>
                  <?php endforeach; ?>
                </select>
                <button type="submit" class="modern-btn modern-btn-primary flex-shrink-0 border-0">
                  <i class="bi bi-search me-1"></i> Rechercher
                </button>
              </form>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="d-flex align-items-center gap-2">
          <!-- Bouton Theme -->
          <button id="themeToggle" class="theme-toggle border-0">
            <i class="bi bi-moon-fill"></i>
          </button>

          <?php if ($user): ?>
            <a href="/mes-objets" class="modern-btn d-none d-lg-inline-flex align-items-center">
              <i class="bi bi-box-seam me-2"></i> Mes objets
            </a>
            <a href="/echanges" class="modern-btn d-none d-lg-inline-flex align-items-center">
              <i class="bi bi-arrow-left-right me-2"></i> Échanges
            </a>
          <?php endif; ?>

          <!-- Admin Dropdown -->
          <?php if ($statut === "admin"): ?>
            <div class="dropdown">
              <button class="modern-btn d-flex align-items-center" type="button" id="adminDropdown"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-gear-fill me-2"></i>
                <span class="d-none d-md-inline">Admin</span>
              </button>
              <ul class="dropdown-menu dropdown-menu-end modern-card border-0 shadow" aria-labelledby="adminDropdown">
                <li><a class="dropdown-item" href="/admin/categories"><i class="bi bi-tags me-2"></i> Catégories</a></li>
                <li><a class="dropdown-item" href="/admin/stats"><i class="bi bi-graph-up me-2"></i> Statistiques</a></li>
              </ul>
            </div>
          <?php endif; ?>

          <!-- Utilisateur Dropdown -->
          <div class="dropdown">
            <button class="modern-btn d-flex align-items-center" type="button" id="userDropdown"
              data-bs-toggle="dropdown" aria-expanded="false">
              <?php if ($statut === "admin"): ?>
                <i class="bi bi-star-fill text-warning me-2"></i>
              <?php else: ?>
                <i class="bi bi-person-circle me-2"></i>
              <?php endif; ?>
              <span class="d-none d-md-inline"><?= htmlspecialchars($user['nom'] ?? "Connexion") ?></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end modern-card border-0 shadow" aria-labelledby="userDropdown">
              <?php if ($user): ?>
                <li class="px-3 py-2">
                  <small class="text-muted">Connecté en tant que</small><br>
                  <strong><?= htmlspecialchars($user['nom']) ?></strong>
                </li>
                <li>
                  <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="/logout"><i class="bi bi-box-arrow-right me-2"></i> Se déconnecter</a>
                </li>
              <?php else: ?>
                <li><a class="dropdown-item" href="/login"><i class="bi bi-box-arrow-in-right me-2"></i> Se connecter</a>
                </li>
                <li><a class="dropdown-item" href="/inscription"><i class="bi bi-person-plus me-2"></i> S'inscrire</a>
                </li>
              <?php endif; ?>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </header>

  <script src="<?= BASE_URL ?>/assets/script/theme.js"></script>
