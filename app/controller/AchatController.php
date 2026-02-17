<?php
namespace app\controller;

use app\repository\AchatRepository;
use app\repository\VilleRepository;
use app\services\AchatService;
use Exception;
use Flight;

class AchatController
{
  private function service(): AchatService
  {
    return new AchatService(new AchatRepository(Flight::db()));
  }

  private function villeRepository(): VilleRepository
  {
    return new VilleRepository(Flight::db());
  }

  /**
   * Page de liste des achats (filtrable par ville)
   */
  public function index(): void
  {
    $request = Flight::request();
    $idVille = isset($request->query->ville) ? (int) $request->query->ville : null;

    $achats = $idVille ? $this->service()->getByVille($idVille) : $this->service()->getAll();
    $villes = $this->villeRepository()->all();
    $message = $request->query->message ?? null;
    $messageType = $request->query->type ?? 'info';

    Flight::render('achats/index', [
      'achats' => $achats,
      'villes' => $villes,
      'villeSelectionnee' => $idVille,
      'message' => $message,
      'messageType' => $messageType
    ]);
  }

  /**
   * Page des besoins restants pour faire les achats
   */
  public function besoinsRestants(): void
  {
    $request = Flight::request();
    $idVille = isset($request->query->ville) ? (int) $request->query->ville : null;

    $besoins = $this->service()->getBesoinsRestants($idVille);
    $villes = $this->villeRepository()->all();
    $totalDonsArgent = $this->service()->getTotalDonsArgentRestants();
    $fraisPourcentage = $this->service()->getFraisAchatPourcentage();
    $message = $request->query->message ?? null;
    $messageType = $request->query->type ?? 'info';

    Flight::render('achats/besoins_restants', [
      'besoins' => $besoins,
      'villes' => $villes,
      'villeSelectionnee' => $idVille,
      'totalDonsArgent' => $totalDonsArgent,
      'fraisPourcentage' => $fraisPourcentage,
      'message' => $message,
      'messageType' => $messageType
    ]);
  }

  /**
   * Crée un achat
   */
  public function create(): void
  {
    try {
      $request = Flight::request();
      $idBesoin = (int) ($request->data->id_besoin ?? 0);
      $quantity = (int) ($request->data->quantity ?? 0);

      if (!$idBesoin || !$quantity) {
        Flight::redirect('/achats/besoins-restants?message=' . urlencode('Besoin et quantité requis') . '&type=danger');
        return;
      }

      if ($quantity <= 0) {
        Flight::redirect('/achats/besoins-restants?message=' . urlencode('La quantité doit être supérieure à 0') . '&type=danger');
        return;
      }

      // Récupérer les informations du besoin
      $besoins = $this->service()->getBesoinsRestants();
      $besoin = null;
      foreach ($besoins as $b) {
        if ((int) $b['id'] === $idBesoin) {
          $besoin = $b;
          break;
        }
      }

      if (!$besoin) {
        Flight::redirect('/achats/besoins-restants?message=' . urlencode('Besoin introuvable') . '&type=danger');
        return;
      }

      // Vérifier que la quantité ne dépasse pas la quantité restante
      if ($quantity > (int) $besoin['quantity_restante']) {
        Flight::redirect('/achats/besoins-restants?message=' . urlencode('Quantité supérieure au besoin restant') . '&type=danger');
        return;
      }

      // Créer l'achat
      $achatId = $this->service()->createAchat(
        $idBesoin,
        (int) $besoin['id_ville'],
        (int) $besoin['id_type_besoin'],
        $besoin['libelle'],
        $quantity,
        (float) $besoin['prix_unitaire']
      );

      Flight::redirect('/achats/besoins-restants?message=' . urlencode('Achat créé avec succès (ID: ' . $achatId . ')') . '&type=success');

    } catch (Exception $e) {
      Flight::redirect('/achats/besoins-restants?message=' . urlencode('Erreur: ' . $e->getMessage()) . '&type=danger');
    }
  }

  /**
   * Met à jour le pourcentage de frais d'achat
   */
  public function updateFrais(): void
  {
    try {
      $request = Flight::request();
      $pourcentage = (float) ($request->data->frais_pourcentage ?? 0);

      $this->service()->updateFraisAchatPourcentage($pourcentage);

      Flight::redirect('/achats/besoins-restants?message=' . urlencode('Frais d\'achat mis à jour: ' . $pourcentage . '%') . '&type=success');

    } catch (Exception $e) {
      Flight::redirect('/achats/besoins-restants?message=' . urlencode('Erreur: ' . $e->getMessage()) . '&type=danger');
    }
  }
}
