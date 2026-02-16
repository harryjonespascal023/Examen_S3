 
<body>
  <div class="container vh-100 d-flex align-items-center justify-content-center"
    style="background: var(--bg-secondary);">
    <div class="modern-card p-4 p-md-5" style="width:100%; max-width: 450px;">
      <div class="text-center mb-4">
        <div class="d-inline-flex align-items-center justify-content-center mb-3"
          style="width: 64px; height: 64px; background: var(--gradient-primary); border-radius: 16px;">
          <i class="bi bi-shop text-white" style="font-size: 2rem;"></i>
        </div>
        <h3 class="fw-bold gradient-text">Bienvenue sur TAKALO</h3>
        <p class="text-muted">Connectez-vous à votre compte</p>
      </div>

      <!-- Message global -->
      <div id="formStatus" class="mb-3"></div>

      <form id="formLogin" action="/login" method="POST" novalidate>

        <!-- Nom -->
        <div class="mb-3">
          <label for="nom" class="form-label fw-semibold">Nom d'utilisateur</label>
          <input type="text" class="form-control modern-input <?= !empty($erreurs['nom']) ? 'is-invalid' : '' ?>"
            id="nom" name="nom" value="<?= htmlspecialchars($admin['nom'] ?? '') ?>"
            placeholder="Entrez votre nom d'utilisateur">

          <div id="nomError" class="invalid-feedback">
            <?= $erreurs['nom'] ?? '' ?>
          </div>
        </div>

        <!-- Password -->
        <div class="mb-4">
          <label for="password" class="form-label fw-semibold">Mot de passe</label>
          <input type="password"
            class="form-control modern-input <?= !empty($erreurs['password']) ? 'is-invalid' : '' ?>" id="password"
            name="password" value="<?= htmlspecialchars($admin['password_hash'] ?? '') ?>"
            placeholder="Entrez votre mot de passe">

          <div id="passwordError" class="invalid-feedback">
            <?= $erreurs['password'] ?? '' ?>
          </div>
        </div>

        <button type="submit" id="btn_submit" class="modern-btn modern-btn-primary w-100 border-0 py-3">
          <i class="bi bi-box-arrow-in-right me-2"></i> Se connecter
        </button>

      </form>

      <div class="text-center mt-4">
        <small class="text-muted">Pas encore de compte ?
          <a href="/inscription" class="text-decoration-none fw-semibold"
            style="color: var(--primary-color);">Inscrivez-vous</a>
        </small>
      </div>

    </div>
  </div>

  <script src="<?= BASE_URL ?>/assets/script/theme.js"></script>
  <script src="<?= BASE_URL ?>/assets/script/LoginValidation.js"></script>
</body>

</html>
