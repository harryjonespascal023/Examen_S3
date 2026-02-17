</div>
</div>

<footer>
  <div class="container-fluid py-4">
    <div class="row">
      <div class="col-md-4 mb-3 mb-md-0">
        <h5 class="fw-bold mb-3">
          <i class="bi bi-heart-fill text-danger"></i> BNGRC
        </h5>
        <p class="text-muted mb-2">
          Bureau National de Gestion des Risques et des Catastrophes
        </p>
        <p class="text-muted small">
          Système de gestion et de distribution des dons humanitaires selon l'algorithme FIFO.
        </p>
      </div>
      <div class="col-md-4 mb-3 mb-md-0">
        <h6 class="fw-bold mb-3">Liens rapides</h6>
        <ul class="list-unstyled">
          <li class="mb-2">
            <a href="<?= BASE_URL ?>/dons" class="text-decoration-none text-muted hover-text-white">
              <i class="bi bi-chevron-right"></i> Accueil
            </a>
          </li>
          <li class="mb-2">
            <a href="<?= BASE_URL ?>/dons/history" class="text-decoration-none text-muted hover-text-white">
              <i class="bi bi-chevron-right"></i> Historique des dispatches
            </a>
          </li>
          <li class="mb-2">
            <a href="<?= BASE_URL ?>/villes" class="text-decoration-none text-muted hover-text-white">
              <i class="bi bi-chevron-right"></i> Gestion des villes
            </a>
          </li>
          <li class="mb-2">
            <a href="<?= BASE_URL ?>/besoins" class="text-decoration-none text-muted hover-text-white">
              <i class="bi bi-chevron-right"></i> Gestion des besoins
            </a>
          </li>
          <li class="mb-2">
            <a href="<?= BASE_URL ?>/types-besoin" class="text-decoration-none text-muted hover-text-white">
              <i class="bi bi-chevron-right"></i> Types de besoins
            </a>
          </li>
        </ul>
      </div>
      <div class="col-md-4">
        <h6 class="fw-bold mb-3">Participants</h6>
        <div class="d-flex align-items-center">
          <i class="bi bi-people-fill text-primary me-2"></i>
          <span class="text-muted small">Harry Jones ETU3922</span>
        </div>
        <div class="d-flex align-items-center">
          <i class="bi bi-people-fill text-primary me-2"></i>
          <span class="text-muted small">Mihaja Nasandratriniavo ETU4167</span>
        </div>
        <div class="d-flex align-items-center">
          <i class="bi bi-people-fill text-primary me-2"></i>
          <span class="text-muted small">Juan Albertino ETU4094</span>
        </div>
      </div>
    </div>
    <hr class="my-4 bg-secondary">
    <div class="row">
      <div class="col-12 text-center">
        <p class="mb-0 text-muted small">
          &copy; <?= date('Y') ?> BNGRC - Tous droits réservés |
          Système de gestion des dons v2.0 |
          <i class="bi bi-heart-fill text-danger"></i> Développé avec passion
        </p>
      </div>
    </div>
  </div>
</footer>

<script src="<?= BASE_URL ?>/assets/js/bootstrap.bundle.min.js"></script>
<script>
  // Animation au scroll
  document.addEventListener('DOMContentLoaded', function () {
    // Animation des cartes
    const cards = document.querySelectorAll('.card');
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = '0';
          entry.target.style.transform = 'translateY(20px)';
          setTimeout(() => {
            entry.target.style.transition = 'all 0.6s ease';
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
          }, 100);
        }
      });
    }, { threshold: 0.1 });

    cards.forEach(card => observer.observe(card));

    // Confirmation pour les suppressions
    const deleteForms = document.querySelectorAll('form[action*="/delete"]');
    deleteForms.forEach(form => {
      form.addEventListener('submit', function (e) {
        if (!confirm('⚠️ Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.')) {
          e.preventDefault();
        }
      });
    });

    // Auto-hide des alertes après 5 secondes
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
      if (alert.classList.contains('alert-dismissible')) {
        setTimeout(() => {
          const bsAlert = new bootstrap.Alert(alert);
          bsAlert.close();
        }, 5000);
      }
    });

    // Effet hover sur liens footer
    const footerLinks = document.querySelectorAll('.hover-text-white');
    footerLinks.forEach(link => {
      link.addEventListener('mouseenter', function () {
        this.style.color = 'white';
        this.style.transform = 'translateX(5px)';
        this.style.transition = 'all 0.3s ease';
      });
      link.addEventListener('mouseleave', function () {
        this.style.color = '';
        this.style.transform = 'translateX(0)';
      });
    });
  });
</script>
</body>

</html>
