<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

use app\controller\UserController;
use app\controller\ObjetController;
use app\controller\EchangeController;
use app\controller\AdminController;
use flight\Engine;
use flight\net\Router;

/**
 * @var Router $router
 * @var Engine $app
 */

Flight::route('/', function () {
  Flight::redirect('/login');
});

Flight::route('/login', [UserController::class, 'afficherLogin']);
Flight::route('/inscription', [UserController::class, 'afficherInscription']);
Flight::route('POST /inscription', [UserController::class, 'inscription']);
Flight::route('POST /api/validation/inscription', [UserController::class, 'validationInscription']);
Flight::route('POST /login', [UserController::class, 'login']);
Flight::route('POST /api/validation/login', [UserController::class, 'validationLogin']);
Flight::route('GET /logout', [UserController::class, 'logout']);

Flight::route('GET /accueil', [ObjetController::class, 'listeObjets']);
Flight::route('GET /mes-objets', [ObjetController::class, 'mesObjets']);
Flight::route('GET /objet/@id', [ObjetController::class, 'getFicheObjet']);
Flight::route('GET /objets-similaires/@id', [ObjetController::class, 'objetsSimilaires']);
Flight::route('POST /object/add', [ObjetController::class, 'add']);
Flight::route('POST /object/delete', [ObjetController::class, 'delete']);
Flight::route('GET /recherche', [ObjetController::class, 'recherche']);

Flight::route('GET /echanges', [EchangeController::class, 'liste']);
Flight::route('POST /echange/proposer', [EchangeController::class, 'proposer']);
Flight::route('POST /echange/decision', [EchangeController::class, 'decision']);

Flight::route('GET /admin/categories', [ObjetController::class, 'categories']);
Flight::route('POST /admin/categories/add', [ObjetController::class, 'addCategory']);
Flight::route('POST /admin/categories/update', [ObjetController::class, 'updateCategory']);
Flight::route('POST /admin/categories/delete', [ObjetController::class, 'deleteCategory']);
Flight::route('GET /admin/stats', [AdminController::class, 'stats']);
