<?php

namespace App\repository;

class ObjetRepository
{
  private $db;

  public function __construct($db)
  {
    $this->db = $db;
  }

  public function getOthersObjets($user_id)
  {
    if ($user_id) {
      $sql = $this->db->prepare("SELECT o.*, c.libelle as category, u.nom as users
                 FROM objets o
                 JOIN categories_objet c ON c.id = o.categorie_id
                 JOIN users u ON u.id = o.user_id
                 WHERE o.user_id != ? ORDER BY o.date_publication DESC");
      $sql->execute([$user_id]);
    } else {
      $sql = $this->db->prepare("SELECT o.*, c.libelle as category, u.nom as users
                 FROM objets o
                 JOIN categories_objet c ON c.id = o.categorie_id
                 JOIN users u ON u.id = o.user_id
                 ORDER BY o.date_publication DESC");
      $sql->execute();
    }
    return $sql->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function getUserObjets($user_id)
  {
    $sql = $this->db->prepare("SELECT o.*, c.libelle as category, u.nom as username
             FROM objets o
             JOIN categories_objet c ON c.id = o.categorie_id
             JOIN users u ON u.id = o.user_id
             WHERE o.user_id = ? ORDER BY o.date_publication DESC");
    $sql->execute([$user_id]);
    return $sql->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function getObjetById($objet_id)
  {
    $sql = $this->db->prepare("SELECT o.*, c.libelle as category, u.nom as users
             FROM objets o
             JOIN categories_objet c ON c.id = o.categorie_id
             JOIN users u ON u.id = o.user_id
             WHERE o.id = ? LIMIT 1");
    $sql->execute([$objet_id]);
    return $sql->fetch(\PDO::FETCH_ASSOC);
  }

  public function getImagesObject($objet_id)
  {
    $sql = $this->db->prepare("SELECT * from images_objet WHERE objet_id = ?");
    $sql->execute([$objet_id]);
    return $sql->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function getOneImageObject($objet_id)
  {
    $sql = $this->db->prepare("SELECT * from images_objet WHERE objet_id = ? LIMIT 1");
    $sql->execute([$objet_id]);
    return $sql->fetch(\PDO::FETCH_ASSOC);
  }

  public function searchObjets($motCle, $categorieId, $excludeUserId = null)
  {
    $conditions = [];
    $params = [];

    if ($excludeUserId) {
      $conditions[] = 'o.user_id != ?';
      $params[] = $excludeUserId;
    }
    if ($motCle !== '') {
      $conditions[] = 'o.libelle LIKE ?';
      $params[] = '%' . $motCle . '%';
    }
    if (!empty($categorieId)) {
      $conditions[] = 'o.categorie_id = ?';
      $params[] = $categorieId;
    }

    $where = '';
    if (!empty($conditions)) {
      $where = 'WHERE ' . implode(' AND ', $conditions);
    }

    $sql = $this->db->prepare("SELECT o.*, c.libelle as category, u.nom as users
             FROM objets o
             JOIN categories_objet c ON c.id = o.categorie_id
             JOIN users u ON u.id = o.user_id
             $where
             ORDER BY o.date_publication DESC");
    $sql->execute($params);
    return $sql->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function getAllCategories()
  {
    $sql = $this->db->prepare("SELECT * FROM categories_objet ORDER BY libelle ASC");
    $sql->execute();
    return $sql->fetchAll(\PDO::FETCH_ASSOC);
  }

  function addCategory($libelle)
  {
    $sql = $this->db->prepare("INSERT INTO categories_objet (libelle) VALUES (?)");
    $sql->execute([$libelle]);
  }

  function removeCategory($category_id)
  {
    $sql = $this->db->prepare("DELETE FROM categories_objet WHERE id = ?");
    $sql->execute([$category_id]);
  }

  public function createObject($nom, $description, $categorie_id, $user_id, $prix)
  {
    $sql = $this->db->prepare("INSERT INTO objets (libelle, description, categorie_id, user_id, date_publication, prix) VALUES (?, ?, ?, ?, NOW(), ?)");
    $sql->execute([$nom, $description, $categorie_id, $user_id, $prix]);
    return $this->db->lastInsertId();
  }

  public function deleteObject($objet_id)
  {
    $sql = $this->db->prepare("DELETE FROM objets WHERE id = ?");
    $sql->execute([$objet_id]);
  }

  public function addImage($objet_id, $image)
  {
    $sql = $this->db->prepare("INSERT INTO images_objet (objet_id, fichier) VALUES (?, ?)");
    $sql->execute([$objet_id, $image]);
  }

  public function removeImage($image_id)
  {
    $sql = $this->db->prepare("DELETE FROM images_objet WHERE id = ?");
    $sql->execute([$image_id]);
  }

  public function removeAllImages($objet_id)
  {
    $sql = $this->db->prepare("DELETE FROM images_objet WHERE objet_id = ?");
    $sql->execute([$objet_id]);
  }

  public function removeObjet($objet_id)
  {
    $sql = $this->db->prepare("DELETE FROM objets WHERE id = ?");
    $sql->execute([$objet_id]);
  }

  public function removeHistoriqueObjet($objet_id)
  {
    $sql = $this->db->prepare("DELETE FROM historique_objet WHERE objet_id = ?");
    $sql->execute([$objet_id]);
  }

  public function updateObjetOwner($objet_id, $user_id)
  {
    $sql = $this->db->prepare("UPDATE objets SET user_id = ? WHERE id = ?");
    $sql->execute([$user_id, $objet_id]);
  }

  public function addHistoriqueObjet($objet_id, $user_id)
  {
    $sql = $this->db->prepare("INSERT INTO historique_objet (objet_id, user_id, date_changement) VALUES (?, ?, NOW())");
    $sql->execute([$objet_id, $user_id]);
  }

  public function getHistoriqueObjet($objet_id)
  {
    $sql = $this->db->prepare("SELECT h.*, u.nom as username
             FROM historique_objet h
             JOIN users u ON u.id = h.user_id
             WHERE h.objet_id = ?
             ORDER BY h.date_changement DESC");
    $sql->execute([$objet_id]);
    return $sql->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function removeCategorie($id)
  {
    $sql = $this->db->prepare("DELETE FROM categories_objet WHERE id = ?");
    $sql->execute([$id]);
  }

  public function updateObjet($objet_id, $nom, $categorie_id, $user_id)
  {
    $sql = $this->db->prepare("UPDATE objets SET libelle = ?, categorie_id = ?, user_id = ? WHERE id = ?");
    $sql->execute([$nom, $categorie_id, $user_id, $objet_id]);
  }

  public function updateCategory($id, $nom)
  {
    $sql = $this->db->prepare("UPDATE categories_objet SET libelle = ? WHERE id = ?");
    $sql->execute([$nom, $id]);
  }

  public function updateImage($id, $image)
  {
    $sql = $this->db->prepare("UPDATE images_objet SET fichier = ? WHERE id = ?");
    $sql->execute([$image, $id]);
  }

  public function getObjetsByPriceRange($prixMin, $prixMax, $excludeUserId = null, $excludeObjetId = null)
  {
    $conditions = ['o.prix BETWEEN ? AND ?'];
    $params = [$prixMin, $prixMax];

    if ($excludeUserId) {
      $conditions[] = 'o.user_id != ?';
      $params[] = $excludeUserId;
    }

    if ($excludeObjetId) {
      $conditions[] = 'o.id != ?';
      $params[] = $excludeObjetId;
    }

    $where = 'WHERE ' . implode(' AND ', $conditions);

    $sql = $this->db->prepare("SELECT o.*, c.libelle as category, u.nom as users
             FROM objets o
             JOIN categories_objet c ON c.id = o.categorie_id
             JOIN users u ON u.id = o.user_id
             $where
             ORDER BY o.prix ASC");
    $sql->execute($params);
    return $sql->fetchAll(\PDO::FETCH_ASSOC);
  }
}
