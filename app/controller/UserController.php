<?php

namespace App\controller;
use app\repository\UserRepository;
use app\services\UserService;
use Flight;
use Throwable;

class UserController
{
    public function afficherLogin(){
        $services = new UserService();
        $admin = $services->getUserAdmin();
        Flight::render('login', ['admin' => $admin]);
    }

    public function afficherInscription(){
        Flight::render('inscription');
    }

    public function logout()
    {
        session_destroy();
        session_start();
        Flight::redirect('/login');
    }

    public function inscription(){
        $req = Flight::request();

        $input = [
            'nom' => $req->data->nom,
            'password' => $req->data->password,
            'confirm' => $req->data->confirm,
        ];
        $service = new UserService();
        $res = $service->validateFormInscription($input);
        if ($res['ok']) {
            $user = $service->creerUser($res['nom'], $input['password']);
            $rp = new UserRepository(Flight::db());
            $statut = $rp->getTypeUserById($user['type_id']);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['statut'] = $statut['type'];
            Flight::redirect('/accueil');
        }

        Flight::render('inscription', [
            'nom' => $res['nom'],
            'erreurs' => $res['erreurs'],
            'success' => false
        ]);
    }

    public function login()
    {
        $req = Flight::request();

        $nom = trim($req->data->nom ?? '');
        $password = $req->data->password ?? '';

        $service = new UserService();
        $user = $service->getUserByName($nom);
        if ($user) {
            $rp = new UserRepository(Flight::db());
            $statut = $rp->getTypeUserById($user['type_id']);
            if ($user['id'] == $rp->getUserAdmin()['id']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['statut'] = $statut['type'];
                Flight::redirect('/accueil');
                return;
            }

            if (password_verify($password, $user['password_hash'])) {
                // login utilisateur normal
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['statut'] = $statut['type'];
                Flight::redirect('/accueil');
                return;
            }
        }

        Flight::redirect("/login");
    }

    public function validationInscription(){
        header('Content-Type: application/json; charset=utf-8');
        try {
            $req = Flight::request();

            $input = [
                'nom' => $req->data->nom,
                'password' => $req->data->password,
                'confirm' => $req->data->confirm,
            ];

            $res = (new UserService())->validateFormInscription($input);
            Flight::json([
                'ok' => $res['ok'],
                'erreurs' => $res['erreurs'],
                'nom' => $res['nom'],
            ]);
        } catch (Throwable $e) {
            error_log('[validateFormInscription] ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            http_response_code(500);
            Flight::json([
                'ok' => false,
                'erreurs' => ['_global' => 'Erreur serveur lors de la validation.'],
                'nom' => ''
            ]);
        }
    }

    public function validationLogin(){
        header('Content-Type: application/json; charset=utf-8');
        try {
            $req = Flight::request();

            $input = [
                'nom' => $req->data->nom,
                'password' => $req->data->password
            ];

            $res = (new UserService())->validateFormLogin($input);
            Flight::json([
                'ok' => $res['ok'],
                'erreurs' => $res['erreurs'],
                'nom' => $res['nom'],
            ]);
        } catch (Throwable $e) {
            error_log('[validateFormLogin] ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            http_response_code(500);
            Flight::json([
                'ok' => false,
                'erreurs' => ['_global' => 'Erreur serveur lors de la validation.'],
                'nom' => ''
            ]);
        }
    }
}