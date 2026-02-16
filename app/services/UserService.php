<?php

namespace App\services;

use app\repository\UserRepository;
use Flight;

class UserService
{
    private UserRepository $repository;

    public function __construct(){
        $this->repository = new UserRepository(Flight::db());
    }

    public function getUserByName($name)
    {
        return $this->repository->getUserByName($name);
    }

    public function validateFormInscription(array $inputs)
    {
        $erreurs = [
            'nom' => '',
            'password' => '',
            'confirm' => ''
        ];

        $nom = trim($inputs['nom'] ?? '');
        $password = $inputs['password'] ?? '';
        $confirm = $inputs['confirm'] ?? '';

        if (mb_strlen($nom) < 2) {
            $erreurs['nom'] = "Le nom doit contenir au moins 2 caractères.";
        }
        else if ($this->getUserByName($nom) != null) {
            $erreurs['nom'] = "Un utilisateur avec ce nom existe déjà.";
        }

        if (strlen($password) < 8) {
            $erreurs['password'] = "Le mot de passe doit contenir au moins 8 caractères.";
        }

        if ($password !== $confirm) {
            $erreurs['confirm'] = "Les mots de passe ne correspondent pas.";
        }

        $ok = true;
        foreach ($erreurs as $m) {
            if ($m !== '') {
                $ok = false;
                break;
            }
        }

        return [
            'ok' => $ok,
            'erreurs' => $erreurs,
            'nom' => $nom
        ];
    }

    public function validateFormLogin(array $inputs)
    {
        $erreurs = [
            'nom' => '',
            'password' => ''
        ];

        $nom = trim($inputs['nom'] ?? '');
        $password = $inputs['password'] ?? '';

       if ($this->getUserByName($nom) == null) {
            $erreurs['nom'] = "Aucun utilisateur avec ce nom.";
        }

        if (strlen($password) < 8) {
            $erreurs['password'] = "Le mot de passe doit contenir au moins 8 caractères.";
        }

        $ok = true;
        foreach ($erreurs as $m) {
            if ($m !== '') {
                $ok = false;
                break;
            }
        }

        return [
            'ok' => $ok,
            'erreurs' => $erreurs,
            'nom' => $nom
        ];
    }

    public function getUserAdmin()
    {
        return $this->repository->getUserAdmin();
    }

    public function creerUser($nom, $password){
        $hash = password_hash((string)$password, PASSWORD_DEFAULT);
        $id = $this->repository->creerUser($nom, $hash);
        return $this->repository->getUserById($id);
    }
}