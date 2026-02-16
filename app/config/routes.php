<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

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