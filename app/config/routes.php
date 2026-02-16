<?php
use app\controller\VilleController;

ini_set('display_errors', 1);
error_reporting(E_ALL);

Flight::route('GET /villes', [VilleController::class, 'index']);
Flight::route('GET /villes/create', [VilleController::class, 'createForm']);
Flight::route('POST /villes', [VilleController::class, 'store']);
Flight::route('GET /villes/@id/edit', [VilleController::class, 'editForm']);
Flight::route('POST /villes/@id/update', [VilleController::class, 'update']);
Flight::route('POST /villes/@id/delete', [VilleController::class, 'delete']);
use app\controller\DonController;

Flight::route('GET /', [DonController::class, 'index']);
Flight::route('GET /dons', [DonController::class, 'index']);

Flight::route('POST /dons/create', [DonController::class, 'createForm']);
Flight::route('POST /dons/dispatch', [DonController::class, 'dispatchForm']);

Flight::route('GET /dons/history', [DonController::class, 'history']);

Flight::route('GET /api/dons', [DonController::class, 'list']);
Flight::route('POST /api/dons', [DonController::class, 'create']);
Flight::route('POST /api/dons/dispatch', [DonController::class, 'dispatch']);
Flight::route('GET /api/dons/report', [DonController::class, 'report']);
