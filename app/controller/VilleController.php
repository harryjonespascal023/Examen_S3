<?php
namespace app\controller;

use app\repository\VilleRepository;
use app\services\VilleService;
use Flight;

class VilleController
{
	private function service(): VilleService
	{
		return new VilleService(new VilleRepository(Flight::db()));
	}

	public function index(): void
	{
		$villes = $this->service()->getAll();
		Flight::render('ListVille', ['villes' => $villes]);
	}

	public function createForm(): void
	{
		Flight::render('VilleForm', [
			'ville' => null,
			'action' =>  BASE_URL.'/villes',
			'title' => 'Ajouter une ville',
			'submitLabel' => 'Ajouter',
		]);
	}

	public function store(): void
	{
		$request = Flight::request();
		$nom = trim((string)($request->data->nom ?? ''));
		$nombreSinistres = (int)($request->data->nombre_sinistres ?? 0);

		$this->service()->create($nom, $nombreSinistres);
		Flight::redirect('/villes');
	}

	public function editForm($id): void
	{
		$ville = $this->service()->getById((int)$id);
		if ($ville === null) {
			Flight::redirect('/villes');
			return;
		}

		Flight::render('VilleForm', [
			'ville' => $ville,
			'action' => '/villes/' . (int)$id . '/update',
			'title' => 'Modifier une ville',
			'submitLabel' => 'Modifier',
		]);
	}

	public function update($id): void
	{
		$request = Flight::request();
		$nom = trim((string)($request->data->nom ?? ''));
		$nombreSinistres = (int)($request->data->nombre_sinistres ?? 0);

		$this->service()->update((int)$id, $nom, $nombreSinistres);
		Flight::redirect('/villes');
	}

	public function delete($id): void
	{
		$this->service()->delete((int)$id);
		Flight::redirect('/villes');
	}
}
