<?php
use app\controller\VilleController;

ini_set('display_errors', 1);
error_reporting(E_ALL);

Flight::route('GET /', function (): void {
	Flight::redirect('/villes');
});

Flight::route('GET /villes', [VilleController::class, 'index']);
Flight::route('GET /villes/create', [VilleController::class, 'createForm']);
Flight::route('POST /villes', [VilleController::class, 'store']);
Flight::route('GET /villes/@id/edit', [VilleController::class, 'editForm']);
Flight::route('POST /villes/@id/update', [VilleController::class, 'update']);
Flight::route('POST /villes/@id/delete', [VilleController::class, 'delete']);
