<?php

namespace App\repository;

class UserRepository
{
  private $db;

  public function __construct($db)
  {
    $this->db = $db;
  }

  public function getTypeUser($type)
  {
    $sql = $this->db->prepare("SELECT * FROM type_user WHERE type = ?");
    $sql->execute([$type]);
    return $sql->fetch();
  }

  public function getTypeUserById($id)
  {
    $sql = $this->db->prepare("SELECT * FROM type_user WHERE id = ?");
    $sql->execute([$id]);
    return $sql->fetch();
  }

  public function getUserByName($name)
  {
    $sql = $this->db->prepare("SELECT * FROM users WHERE nom = ?");
    $sql->execute([$name]);
    return $sql->fetch();
  }

  public function creerUser($nom, $password_hash)
  {
    $type = $this->getTypeUser('user');
    $sql = $this->db->prepare("INSERT INTO users (nom, password_hash, type_id, creation) VALUES (?, ?, ?, NOW())");
    $sql->execute([$nom, $password_hash, $type['id']]);
    return $this->db->lastInsertId();
  }

  public function getUser($nom, $password_hash)
  {
    $sql = $this->db->prepare("SELECT * FROM users WHERE nom = ? AND password_hash = ?");
    $sql->execute([$nom, $password_hash]);
    return $sql->fetch();
  }

  public function getUserAdmin()
  {
    $sql = $this->db->prepare("SELECT * FROM users u JOIN type_user t ON u.type_id = t.id WHERE t.type = 'admin' LIMIT 1");
    $sql->execute();
    return $sql->fetch();
  }

  public function getUserById($id)
  {
    $sql = $this->db->prepare("SELECT * FROM users WHERE id = ?");
    $sql->execute([$id]);
    return $sql->fetch();
  }

  public function countUsers()
  {
    $sql = $this->db->prepare("SELECT COUNT(*) as total FROM users");
    $sql->execute();
    return (int) $sql->fetch(\PDO::FETCH_ASSOC)['total'];
  }
}
