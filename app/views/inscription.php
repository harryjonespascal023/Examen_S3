<!doctype html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inscription - TAKALO</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/modern-theme.css">
    <script src="<?= BASE_URL ?>/assets/js/bootstrap.bundle.js"></script>
</head>

<body>
<div class="container vh-100 d-flex align-items-center justify-content-center" style="background: var(--bg-secondary);">
    <div class="modern-card p-4 p-md-5" style="width: 100%; max-width: 500px;">
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; background: var(--gradient-primary); border-radius: 16px;">
                <i class="bi bi-person-plus text-white" style="font-size: 2rem;"></i>
            </div>
            <h3 class="fw-bold gradient-text">Créer un compte</h3>
            <p class="text-muted">Rejoignez TAKALO dès maintenant</p>
        </div>

        <!-- Message global -->
        <div id="formStatus" class="mb-3"></div>

        <form id="formInscription" action="/inscription" method="POST" novalidate>

            <!-- Nom -->
            <div class="mb-3">
                <label for="nom" class="form-label fw-semibold">Nom d'utilisateur</label>
                <input
                        type="text"
                        class="form-control modern-input <?= !empty($erreurs['nom']) ? 'is-invalid' : '' ?>"
                        id="nom"
                        name="nom"
                        value="<?= htmlspecialchars($nom ?? '') ?>"
                        placeholder="Minimum 2 caractères">

                <div id="nomError" class="invalid-feedback">
                    <?= $erreurs['nom'] ?? '' ?>
                </div>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">Mot de passe</label>
                <input
                        type="password"
                        class="form-control modern-input <?= !empty($erreurs['password']) ? 'is-invalid' : '' ?>"
                        id="password"
                        name="password"
                        placeholder="Minimum 8 caractères">

                <div id="passwordError" class="invalid-feedback">
                    <?= $erreurs['password'] ?? '' ?>
                </div>
            </div>

            <!-- Confirm -->
            <div class="mb-4">
                <label for="confirm" class="form-label fw-semibold">Confirmez votre mot de passe</label>
                <input
                        type="password"
                        class="form-control modern-input <?= !empty($erreurs['confirm']) ? 'is-invalid' : '' ?>"
                        id="confirm"
                        name="confirm"
                        placeholder="Retapez votre mot de passe">

                <div id="confirmError" class="invalid-feedback">
                    <?= $erreurs['confirm'] ?? '' ?>
                </div>
            </div>

            <button type="submit" id="btn_submit" class="modern-btn modern-btn-primary w-100 border-0 py-3">
                <i class="bi bi-person-check me-2"></i> S'inscrire
            </button>
        </form>

        <div class="text-center mt-4">
            <small class="text-muted">Déjà un compte ?
                <a href="/login" class="text-decoration-none fw-semibold" style="color: var(--primary-color);">Connectez-vous</a>
            </small>
        </div>

    </div>
</div>

<script src="<?= BASE_URL ?>/assets/script/theme.js"></script>
<script src="<?= BASE_URL ?>/assets/script/InscriptionValidation.js"></script>
</body>
</html>
