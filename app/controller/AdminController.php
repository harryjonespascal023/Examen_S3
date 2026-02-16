<?php

namespace App\controller;

use app\repository\EchangeRepository;
use app\repository\UserRepository;
use Flight;

class AdminController
{
    private function ensureAdmin(): void
    {
        if (($_SESSION['statut'] ?? '') !== 'admin') {
            Flight::redirect('/login');
            exit;
        }
    }

    public function stats(): void
    {
        $this->ensureAdmin();
        $userRepository = new UserRepository(Flight::db());
        $echangeRepository = new EchangeRepository(Flight::db());

        $totalUsers = $userRepository->countUsers();
        $totalEchanges = $echangeRepository->countEchangesAcceptes();

        Flight::render('admin/stats', [
            'totalUsers' => $totalUsers,
            'totalEchanges' => $totalEchanges,
        ]);
    }
}
