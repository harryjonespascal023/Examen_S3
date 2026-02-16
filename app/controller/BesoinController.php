<?php
namespace app\controller;

use app\repository\BesoinRepository;
use app\repository\TypeBesoinRepository;
use app\repository\VilleRepository;
use app\services\BesoinService;
use Flight;

class BesoinController
{
	private function service(): BesoinService
	{
		return new BesoinService(new BesoinRepository(Flight::db()));
	}

	private function villeRepository(): VilleRepository
	{
		return new VilleRepository(Flight::db());
	}

	private function typeRepository(): TypeBesoinRepository
	{
		return new TypeBesoinRepository(Flight::db());
	}

	public function index(): void
	{
		$besoins = $this->service()->getAll();
		Flight::render('ListBesoin', ['besoins' => $besoins]);
	}

	public function createForm(): void
	{
		$villes = $this->villeRepository()->all();
		$types = $this->typeRepository()->all();

		Flight::render('BesoinForm', [
			'besoin' => null,
			'villes' => $villes,
			'types' => $types,
			'action' => '/besoins',
			'title' => 'Ajouter un besoin',
			'submitLabel' => 'Ajouter',
		]);
	}

	public function store(): void
	{
		$request = Flight::request();
		$idVille = (int)($request->data->id_ville ?? 0);
		$idType = (int)($request->data->id_type_besoin ?? 0);
		$prixUnitaire = (float)($request->data->prix_unitaire ?? 0);
		$quantity = (int)($request->data->quantity ?? 0);
		$quantityRestante = (int)($request->data->quantity_restante ?? $quantity);

		$this->service()->create($idVille, $idType, $prixUnitaire, $quantity, $quantityRestante);
		Flight::redirect('/besoins');
	}

	public function editForm($id): void
	{
		$besoin = $this->service()->getById((int)$id);
		if ($besoin === null) {
			Flight::redirect('/besoins');
			return;
		}

		$villes = $this->villeRepository()->all();
		$types = $this->typeRepository()->all();

		Flight::render('BesoinForm', [
			'besoin' => $besoin,
			'villes' => $villes,
			'types' => $types,
			'action' => '/besoins/' . (int)$id . '/update',
			'title' => 'Modifier un besoin',
			'submitLabel' => 'Modifier',
		]);
	}

	public function update($id): void
	{
		$request = Flight::request();
		$idVille = (int)($request->data->id_ville ?? 0);
		$idType = (int)($request->data->id_type_besoin ?? 0);
		$prixUnitaire = (float)($request->data->prix_unitaire ?? 0);
		$quantity = (int)($request->data->quantity ?? 0);
		$quantityRestante = (int)($request->data->quantity_restante ?? $quantity);

		$this->service()->update((int)$id, $idVille, $idType, $prixUnitaire, $quantity, $quantityRestante);
		Flight::redirect('/besoins');
	}

	public function delete($id): void
	{
		$this->service()->delete((int)$id);
		Flight::redirect('/besoins');
	}
}

