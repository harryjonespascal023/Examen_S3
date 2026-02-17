<?php
// Force display of all errors for debugging
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$ds = DIRECTORY_SEPARATOR;
require_once __DIR__ . '/../vendor/autoload.php';
require(__DIR__ . $ds . '..' . $ds . 'app' . $ds . 'config' . $ds . 'bootstrap.php');

Flight::set('flight.debug', true);

Flight::start();
