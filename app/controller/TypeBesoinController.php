<?php
namespace app\controller;

use app\repository\TypeBesoinRepository;
use app\services\TypeBesoinService;
use Flight;

class TypeBesoinController
{
  private function service(): TypeBesoinService
  {
    return new TypeBesoinService(new TypeBesoinRepository(Flight::db()));
  }

  public function index(): void
  {
    $types = $this->service()->getAll();
    Flight::render('ListTypeBesoin', ['types' => $types]);
  }

  public function createForm(): void
  {
    Flight::render('TypeBesoinForm', [
      'typeBesoin' => null,
      'action' => BASE_URL.'/types-besoin',
      'title' => 'Ajouter un type de besoin',
      'submitLabel' => 'Ajouter',
    ]);
  }

  public function store(): void
  {
    $request = Flight::request();
    $libelle = trim((string) ($request->data->libelle ?? ''));

    $this->service()->create($libelle);
    Flight::redirect('/types-besoin');
  }

  public function editForm($id): void
  {
    $typeBesoin = $this->service()->getById((int) $id);
    if ($typeBesoin === null) {
      Flight::redirect('/types-besoin');
      return;
    }

    Flight::render('TypeBesoinForm', [
      'typeBesoin' => $typeBesoin,
      'action' => '/types-besoin/' . (int) $id . '/update',
      'title' => 'Modifier un type de besoin',
      'submitLabel' => 'Modifier',
    ]);
  }

  public function update($id): void
  {
    $request = Flight::request();
    $libelle = trim((string) ($request->data->libelle ?? ''));

    $this->service()->update((int) $id, $libelle);
    Flight::redirect('/types-besoin');
  }

  public function delete($id): void
  {
    $this->service()->delete((int) $id);
    Flight::redirect('/types-besoin');
  }
}

