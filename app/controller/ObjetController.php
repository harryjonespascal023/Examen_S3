<?php

namespace App\controller;

use app\repository\ObjetRepository;
use app\services\ObjetService;
use Flight;

class ObjetController
{
  public function listeObjets()
  {
    $service = new ObjetService();
    $objets = $service->getPublications($_SESSION['user_id'] ?? null);
    Flight::render('publications', ['objets' => $objets]);
  }

  public function mesObjets()
  {
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
      Flight::redirect('/login');
      return;
    }
    $service = new ObjetService();
    $objets = $service->getUserObjects($userId);
    Flight::render('mes_objets', ['objets' => $objets]);
  }

  public function listeObjetsByUser($user_id)
  {
    $service = new ObjetService();
    $objets = $service->getUserObjects($user_id);
    Flight::render('pageUser', ['objets' => $objets]);
  }

  public function getFicheObjet($objet_id)
  {
    $repository = new ObjetRepository(Flight::db());
    $objet = $repository->getObjetById($objet_id);
    if (!$objet) {
      Flight::redirect('/accueil');
      return;
    }
    $images = $repository->getImagesObject($objet_id);
    $objet['images'] = $images;
    $historique = $repository->getHistoriqueObjet($objet_id);
    $mesObjets = [];
    if (!empty($_SESSION['user_id'])) {
      $mesObjets = $repository->getUserObjets($_SESSION['user_id']);
    }
    Flight::render('ficheObjet', [
      'objet' => $objet,
      'historique' => $historique,
      'mesObjets' => $mesObjets
    ]);
  }

  public function add()
  {
    $request = Flight::request();
    $service = new ObjetService();
    $nom = $request->data['nom_objet'] ?? '';
    $description = $request->data['description'] ?? '';
    $prix = $request->data['prix'] ?? 0;
    $images = [];
    $categorie_id = $request->data['categorie_id'] ?? null;
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id) {
      Flight::redirect('/login');
      return;
    }

    if (isset($_FILES['image']) && !empty($_FILES['image']['name'][0])) {
      foreach ($_FILES['image']['name'] as $key => $name) {
        $tmp_name = $_FILES['image']['tmp_name'][$key];
        if ($tmp_name) {
          $images[] = [
            'name' => $name,
            'tmp_name' => $tmp_name,
            'type' => $_FILES['image']['type'][$key],
            'error' => $_FILES['image']['error'][$key],
            'size' => $_FILES['image']['size'][$key]
          ];
        }
      }
    }

    $service->createObjet($nom, $description, $prix, $categorie_id, $user_id, $images);
    Flight::redirect('/mes-objets');
  }

  public function delete()
  {
    $user_id = $_SESSION['user_id'] ?? null;
    if (!$user_id) {
      Flight::redirect('/login');
      return;
    }
    $objet_id = Flight::request()->data['objet_id'] ?? null;
    if (!$objet_id) {
      Flight::redirect('/mes-objets');
      return;
    }
    $repository = new ObjetRepository(Flight::db());
    $objet = $repository->getObjetById($objet_id);
    if (!$objet || (int) $objet['user_id'] !== (int) $user_id) {
      Flight::redirect('/mes-objets');
      return;
    }
    $service = new ObjetService();
    $service->deleteObjet($objet_id);
    Flight::redirect('/mes-objets');
  }

  public function recherche()
  {
    $motCle = trim(Flight::request()->query['mot_cle'] ?? '');
    $categorieId = Flight::request()->query['categorie'] ?? null;
    $repository = new ObjetRepository(Flight::db());
    $objets = $repository->searchObjets($motCle, $categorieId, $_SESSION['user_id'] ?? null);
    Flight::render('publications', ['objets' => $objets, 'motCle' => $motCle, 'categorieId' => $categorieId]);
  }

  private function ensureAdmin(): void
  {
    if (($_SESSION['statut'] ?? '') !== 'admin') {
      Flight::redirect('/login');
      exit;
    }
  }

  public function categories()
  {
    $this->ensureAdmin();
    $repository = new ObjetRepository(Flight::db());
    $categories = $repository->getAllCategories();
    Flight::render('admin/categories', ['categories' => $categories]);
  }

  public function addCategory()
  {
    $this->ensureAdmin();
    $libelle = trim(Flight::request()->data['libelle'] ?? '');
    if ($libelle !== '') {
      $repository = new ObjetRepository(Flight::db());
      $repository->addCategory($libelle);
    }
    Flight::redirect('/admin/categories');
  }

  public function updateCategory()
  {
    $this->ensureAdmin();
    $id = Flight::request()->data['id'] ?? null;
    $libelle = trim(Flight::request()->data['libelle'] ?? '');
    if ($id && $libelle !== '') {
      $repository = new ObjetRepository(Flight::db());
      $repository->updateCategory($id, $libelle);
    }
    Flight::redirect('/admin/categories');
  }

  public function deleteCategory()
  {
    $this->ensureAdmin();
    $id = Flight::request()->data['id'] ?? null;
    if ($id) {
      $repository = new ObjetRepository(Flight::db());
      $repository->removeCategory($id);
    }
    Flight::redirect('/admin/categories');
  }

  public function objetsSimilaires($objet_id)
  {
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
      Flight::redirect('/login');
      return;
    }

    $percentage = Flight::request()->query['percentage'] ?? 10;
    if (!in_array($percentage, [10, 20])) {
      $percentage = 10;
    }

    $service = new ObjetService();
    $result = $service->getObjetsBySimilarPrice($objet_id, $userId, $percentage);

    if (!$result) {
      Flight::redirect('/mes-objets');
      return;
    }

    Flight::render('objets_similaires', $result);
  }
}
