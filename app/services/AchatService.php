<?php
namespace app\services;

use app\repository\AchatRepository;
use Exception;

class AchatService
{
  private AchatRepository $achatRepository;

  public function __construct(AchatRepository $achatRepository)
  {
    $this->achatRepository = $achatRepository;
  }

  /**
   * Récupère tous les achats
   */
  public function getAll(): array
  {
    return $this->achatRepository->all();
  }

  /**
   * Récupère les achats par ville
   */
  public function getByVille(int $idVille): array
  {
    return $this->achatRepository->getByVille($idVille);
  }

  /**
   * Récupère les besoins restants
   */
  public function getBesoinsRestants(?int $idVille = null): array
  {
    return $this->achatRepository->getBesoinsRestants($idVille);
  }

  /**
   * Récupère le total des dons en argent restants
   */
  public function getTotalDonsArgentRestants(): float
  {
    return $this->achatRepository->getTotalDonsArgentRestants();
  }

  /**
   * Récupère le pourcentage de frais d'achat
   */
  public function getFraisAchatPourcentage(): float
  {
    return $this->achatRepository->getFraisAchatPourcentage();
  }

  /**
   * Crée un achat
   */
  public function createAchat(
    int $idBesoin,
    int $idVille,
    int $idTypeBesoin,
    string $libelle,
    int $quantity,
    float $prixUnitaire
  ): int {
    // Calculer le montant total
    $montantTotal = $quantity * $prixUnitaire;

    // Récupérer le pourcentage de frais d'achat
    $fraisPourcentage = $this->getFraisAchatPourcentage();

    // Calculer les frais d'achat
    $fraisAchat = $montantTotal * ($fraisPourcentage / 100);

    // Calculer le montant total avec frais
    $montantAvecFrais = $montantTotal + $fraisAchat;

    // Vérifier si on a assez de dons en argent
    $totalDonsArgent = $this->getTotalDonsArgentRestants();
    if ($montantAvecFrais > $totalDonsArgent) {
      throw new Exception("Erreur : fonds insuffisants. Disponible: " . number_format($totalDonsArgent, 2) . " Ar, Requis: " . number_format($montantAvecFrais, 2) . " Ar");
    }

    // Créer l'achat
    $dateAchat = date('Y-m-d');
    return $this->achatRepository->create(
      $idVille,
      $idTypeBesoin,
      $libelle,
      $quantity,
      $prixUnitaire,
      $montantTotal,
      $fraisAchat,
      $montantAvecFrais,
      $dateAchat,
      $idBesoin
    );
  }

  /**
   * Met à jour le pourcentage de frais d'achat
   */
  public function updateFraisAchatPourcentage(float $pourcentage): void
  {
    if ($pourcentage < 0 || $pourcentage > 100) {
      throw new Exception("Le pourcentage doit être entre 0 et 100.");
    }

    $this->achatRepository->updateFraisAchatPourcentage($pourcentage);
  }
}
