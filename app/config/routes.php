<?php
use app\controller\BesoinController;
use app\controller\DonController;
use app\controller\TypeBesoinController;
use app\controller\VilleController;

ini_set('display_errors', 1);
error_reporting(E_ALL);

Flight::route('GET /villes', [VilleController::class, 'index']);
Flight::route('GET /villes/create', [VilleController::class, 'createForm']);
Flight::route('POST /villes', [VilleController::class, 'store']);
Flight::route('GET /villes/@id/edit', [VilleController::class, 'editForm']);
Flight::route('POST /villes/@id/update', [VilleController::class, 'update']);
Flight::route('POST /villes/@id/delete', [VilleController::class, 'delete']);

Flight::route('GET /types-besoin', [TypeBesoinController::class, 'index']);
Flight::route('GET /types-besoin/create', [TypeBesoinController::class, 'createForm']);
Flight::route('POST /types-besoin', [TypeBesoinController::class, 'store']);
Flight::route('GET /types-besoin/@id/edit', [TypeBesoinController::class, 'editForm']);
Flight::route('POST /types-besoin/@id/update', [TypeBesoinController::class, 'update']);
Flight::route('POST /types-besoin/@id/delete', [TypeBesoinController::class, 'delete']);
Flight::route('GET /besoins', [BesoinController::class, 'index']);
Flight::route('GET /besoins/create', [BesoinController::class, 'createForm']);
Flight::route('POST /besoins', [BesoinController::class, 'store']);
Flight::route('GET /besoins/@id/edit', [BesoinController::class, 'editForm']);
Flight::route('POST /besoins/@id/update', [BesoinController::class, 'update']);
Flight::route('POST /besoins/@id/delete', [BesoinController::class, 'delete']);
Flight::route('GET /dons', [DonController::class, 'index']);
Flight::route('POST /dons/create', [DonController::class, 'createForm']);
Flight::route('POST /dons/dispatch', [DonController::class, 'dispatchForm']);
Flight::route('GET /dons/history', [DonController::class, 'history']);
Flight::route('/', [DonController::class, 'dashboard']);
