<?php

namespace app\services;
use app\repository\ObjetRepository;
use Flight;

class ObjetService
{

  private ObjetRepository $objetRepository;

  public function __construct()
  {
    $this->objetRepository = new ObjetRepository(Flight::db());
  }

  public function getPublications($user_id)
  {
    $objets = $this->objetRepository->getOthersObjets($user_id);
    for ($i = 0; $i < count($objets); $i++) {
      $image = $this->objetRepository->getOneImageObject($objets[$i]['id']);
      $objets[$i]['image'] = $image;
    }
    return $objets;
  }

  public function getUserObjects($user_id)
  {
    $objets = $this->objetRepository->getUserObjets($user_id);
    for ($i = 0; $i < count($objets); $i++) {
      $image = $this->objetRepository->getOneImageObject($objets[$i]['id']);
      $objets[$i]['image'] = $image;
    }
    return $objets;
  }

  public function createObjet($nom, $description, $prix, $categorie_id, $user_id, array $images)
  {
    $object_id = $this->objetRepository->createObject($nom, $description, $categorie_id, $user_id, $prix);
    $this->objetRepository->addHistoriqueObjet($object_id, $user_id);
    foreach ($images as $img) {
      $fichier = $this->upload_image($img);
      if ($fichier == null || $fichier == "")
        return;
      $this->objetRepository->addImage($object_id, $fichier);
    }
  }

  public function deleteObjet($objet_id)
  {
    $images = $this->objetRepository->getImagesObject($objet_id);
    foreach ($images as $img) {
      $this->objetRepository->removeImage($img['id']);
    }
    $this->objetRepository->removeHistoriqueObjet($objet_id);
    $this->objetRepository->removeObjet($objet_id);
  }

  function upload_image($file)
  {
    $uploadDir = dirname(__DIR__, 2) . '/public/assets/uploads/';
    $maxSize = 20 * 1024 * 1024; // 20 Mo
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/avif', 'image/webp', 'image/svg'];

    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
      die('Aucun fichier n’a été téléchargé.');
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
      die('Erreur lors de l’upload : ' . $file['error']);
    }
    if ($file['size'] > $maxSize) {
      die('Le fichier est trop volumineux.');
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (!in_array($mime, $allowedMimeTypes)) {
      die('Type de fichier non autorisé : ' . $mime);
    }

    $originalName = pathinfo($file['name'], PATHINFO_FILENAME);
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newName = $originalName . '_' . uniqid() . '.' . $extension;

    if (move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) {
      return $newName;
    } else {
      die("Échec du déplacement du fichier.");
    }
  }

  public function getObjetsBySimilarPrice($objetId, $userId, $percentage)
  {
    $objet = $this->objetRepository->getObjetById($objetId);
    if (!$objet || (int) $objet['user_id'] !== (int) $userId) {
      return null;
    }

    $prixBase = (float) $objet['prix'];
    $pourcentage = (float) $percentage;
    $prixMin = $prixBase * (1 - $pourcentage / 100);
    $prixMax = $prixBase * (1 + $pourcentage / 100);

    $objets = $this->objetRepository->getObjetsByPriceRange($prixMin, $prixMax, $userId, $objetId);

    // Ajouter l'image et calculer la différence en %
    for ($i = 0; $i < count($objets); $i++) {
      $image = $this->objetRepository->getOneImageObject($objets[$i]['id']);
      $objets[$i]['image'] = $image;

      // Calculer la différence en pourcentage
      $prixObjet = (float) $objets[$i]['prix'];
      $difference = (($prixObjet - $prixBase) / $prixBase) * 100;
      $objets[$i]['difference_pourcentage'] = round($difference, 1);
    }

    return [
      'objet_base' => $objet,
      'objets' => $objets,
      'pourcentage' => $pourcentage
    ];
  }
}
