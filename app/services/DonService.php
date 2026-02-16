<?php

namespace app\services;

use app\repository\DonRepository;

class DonService
{
    private $donRepository;

    public function __construct(DonRepository $donRepository)
    {
        $this->donRepository = $donRepository;
    }

    public function getAllTypesBesoin()
    {
        return $this->donRepository->getAllTypesBesoin();
    }

    public function getBesoinsDisponibles()
    {
        return $this->donRepository->getBesoinsDisponibles();
    }

    public function dispatchDons()
    {
        $stats = [
            'total_dispatches' => 0,
            'total_quantity_dispatched' => 0,
            'besoins_satisfaits' => 0,
            'dons_utilises' => 0,
            'details' => []
        ];

        try {
            // 1. Récupérer les besoins non satisfaits
            $besoins = $this->donRepository->getBesoinsNonSatisfaits();
            
            // 2. Récupérer les dons non utilisés totalement (triés FIFO par date de saisie)
            $dons = $this->donRepository->getDonsNonUtilises();

            // 3. Grouper les dons par type de besoin pour un accès rapide
            $donsByType = [];
            foreach ($dons as $don) {
                $typeId = $don['id_type_besoin'];
                if (!isset($donsByType[$typeId])) {
                    $donsByType[$typeId] = [];
                }
                $donsByType[$typeId][] = $don;
            }

            // 4. Algorithme FIFO : traiter chaque besoin
            foreach ($besoins as $besoin) {
                $besoinId = $besoin['id'];
                $typeBesoin = $besoin['id_type_besoin'];
                $quantityNeed = $besoin['quantity_restante'];

                // Vérifier s'il existe des dons pour ce type de besoin
                if (!isset($donsByType[$typeBesoin]) || empty($donsByType[$typeBesoin])) {
                    continue;
                }

                // Variable pour suivre la progression
                $quantityRemaining = $quantityNeed;

                // 5. Dispatcher les dons selon FIFO jusqu'à satisfaction du besoin
                foreach ($donsByType[$typeBesoin] as &$don) {
                    if ($quantityRemaining <= 0) {
                        break; // Le besoin est complètement satisfait
                    }

                    if ($don['quantity_restante'] <= 0) {
                        continue; // Ce don est épuisé, passer au suivant
                    }

                    $donId = $don['id'];
                    $quantityAvailable = $don['quantity_restante'];

                    // Calculer la quantité à dispatcher
                    $quantityToDispatch = min($quantityAvailable, $quantityRemaining);

                    // 6. Insertion dans la table dispatch
                    $this->donRepository->insertDispatch($donId, $besoinId, $quantityToDispatch);

                    // 7. Mise à jour des quantités restantes
                    $this->donRepository->updateQuantityRestanteDon($donId, $quantityToDispatch);
                    $this->donRepository->updateQuantityRestanteBesoin($besoinId, $quantityToDispatch);

                    // Mettre à jour les variables locales
                    $don['quantity_restante'] -= $quantityToDispatch;
                    $quantityRemaining -= $quantityToDispatch;

                    // Enregistrer les statistiques
                    $stats['total_dispatches']++;
                    $stats['total_quantity_dispatched'] += $quantityToDispatch;
                    $stats['details'][] = [
                        'don_id' => $donId,
                        'besoin_id' => $besoinId,
                        'type' => $don['type_libelle'],
                        'ville' => $besoin['ville_nom'],
                        'quantity' => $quantityToDispatch,
                        'don_date' => $don['date_saisie']
                    ];

                    // Compter les dons totalement utilisés
                    if ($don['quantity_restante'] == 0) {
                        $stats['dons_utilises']++;
                    }
                }

                // Compter les besoins totalement satisfaits
                if ($quantityRemaining == 0) {
                    $stats['besoins_satisfaits']++;
                }
            }

            return $stats;

        } catch (Exception $e) {
            throw new Exception("Erreur lors du dispatch : " . $e->getMessage());
        }
    }

    public function createDon($idBesoin, $quantity, $dateSaisie = null)
    {
        if ($quantity <= 0) {
            throw new Exception("La quantité doit être supérieure à 0");
        }

        if ($dateSaisie === null) {
            $dateSaisie = date('Y-m-d');
        }

        return $this->donRepository->createDon($idBesoin, $quantity, $dateSaisie);
    }

    public function getAllDons()
    {
        return $this->donRepository->getAllDons();
    }

    public function getDispatchHistory()
    {
        return $this->donRepository->getDispatchHistory();
    }

    public function getReport()
    {
        $besoins = $this->donRepository->getBesoinsNonSatisfaits();
        $dons = $this->donRepository->getDonsNonUtilises();

        $report = [
            'besoins_non_satisfaits' => [
                'count' => count($besoins),
                'total_quantity' => array_sum(array_column($besoins, 'quantity_restante')),
                'details' => $besoins
            ],
            'dons_non_utilises' => [
                'count' => count($dons),
                'total_quantity' => array_sum(array_column($dons, 'quantity_restante')),
                'details' => $dons
            ]
        ];

        return $report;
    }

     public function getDashboardData(): array
    {
        return [
            'villes' => $this->donRepository->getVillesWithBesoinStats(),
            'totaux' => $this->donRepository->getGlobalDonTotals(),
        ];
    }
}