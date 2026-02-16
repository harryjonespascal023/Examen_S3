<?php

use Ahc\Cli\Application;
use Ahc\Cli\Input\Command;

/**
 * Commande CLI pour tester et gérer le système de dispatch FIFO
 * 
 * Utilisation :
 *   php vendor/bin/runway dispatch:run     # Lancer le dispatch
 *   php vendor/bin/runway dispatch:report  # Afficher le rapport
 *   php vendor/bin/runway dispatch:add     # Ajouter un don
 */

// Commande pour lancer le dispatch
$dispatchRun = new Command('dispatch:run', 'Lance le dispatch FIFO des dons');
$dispatchRun->action(function() {
    echo "\n╔═══════════════════════════════════════════════════╗\n";
    echo "║     LANCEMENT DU DISPATCH FIFO DES DONS         ║\n";
    echo "╚═══════════════════════════════════════════════════╝\n\n";
    
    try {
        // Récupérer les services
        $db = Flight::db();
        $donRepository = new DonRepository($db);
        $donService = new DonService($donRepository);
        
        // État avant dispatch
        echo "📊 État AVANT le dispatch:\n";
        echo "──────────────────────────\n";
        $reportBefore = $donService->getReport();
        echo "  • Dons disponibles: " . $reportBefore['dons_non_utilises']['count'] . 
             " (" . $reportBefore['dons_non_utilises']['total_quantity'] . " unités)\n";
        echo "  • Besoins en attente: " . $reportBefore['besoins_non_satisfaits']['count'] . 
             " (" . $reportBefore['besoins_non_satisfaits']['total_quantity'] . " unités)\n\n";
        
        // Lancer le dispatch
        echo "🔄 Exécution du dispatch FIFO...\n\n";
        $stats = $donService->dispatchDons();
        
        // Afficher les résultats
        echo "✅ Dispatch terminé avec succès!\n";
        echo "──────────────────────────────\n";
        echo "  • Nombre de dispatches: " . $stats['total_dispatches'] . "\n";
        echo "  • Quantité totale dispatchée: " . $stats['total_quantity_dispatched'] . " unités\n";
        echo "  • Besoins satisfaits: " . $stats['besoins_satisfaits'] . "\n";
        echo "  • Dons totalement utilisés: " . $stats['dons_utilises'] . "\n\n";
        
        // Détails des dispatches
        if (!empty($stats['details'])) {
            echo "📋 Détails des dispatches:\n";
            echo "──────────────────────────\n";
            foreach ($stats['details'] as $i => $detail) {
                echo sprintf(
                    "  %d. Don #%d (%s, %s) → Besoin #%d (%s) : %d unités\n",
                    $i + 1,
                    $detail['don_id'],
                    $detail['type'],
                    $detail['don_date'],
                    $detail['besoin_id'],
                    $detail['ville'],
                    $detail['quantity']
                );
            }
            echo "\n";
        }
        
        // État après dispatch
        echo "📊 État APRÈS le dispatch:\n";
        echo "──────────────────────────\n";
        $reportAfter = $donService->getReport();
        echo "  • Dons disponibles: " . $reportAfter['dons_non_utilises']['count'] . 
             " (" . $reportAfter['dons_non_utilises']['total_quantity'] . " unités)\n";
        echo "  • Besoins en attente: " . $reportAfter['besoins_non_satisfaits']['count'] . 
             " (" . $reportAfter['besoins_non_satisfaits']['total_quantity'] . " unités)\n\n";
        
        echo "✨ Dispatch FIFO complété!\n\n";
        
    } catch (Exception $e) {
        echo "❌ Erreur lors du dispatch: " . $e->getMessage() . "\n\n";
        return 1;
    }
    
    return 0;
});

// Commande pour afficher le rapport
$dispatchReport = new Command('dispatch:report', 'Affiche le rapport de l\'état actuel des dons et besoins');
$dispatchReport->action(function() {
    echo "\n╔═══════════════════════════════════════════════════╗\n";
    echo "║          RAPPORT SYSTÈME DE DISPATCH            ║\n";
    echo "╚═══════════════════════════════════════════════════╝\n\n";
    
    try {
        $db = Flight::db();
        $donRepository = new DonRepository($db);
        $donService = new DonService($donRepository);
        
        $report = $donService->getReport();
        
        // Dons disponibles
        echo "📦 DONS NON UTILISÉS\n";
        echo "════════════════════\n";
        echo "Total: " . $report['dons_non_utilises']['count'] . " dons\n";
        echo "Quantité totale: " . $report['dons_non_utilises']['total_quantity'] . " unités\n\n";
        
        if (!empty($report['dons_non_utilises']['details'])) {
            $dons = $report['dons_non_utilises']['details'];
            foreach ($dons as $don) {
                echo sprintf(
                    "  #%-3d %-15s | %4d unités | %s\n",
                    $don['id'],
                    $don['type_libelle'],
                    $don['quantity_restante'],
                    $don['date_saisie']
                );
            }
        } else {
            echo "  Aucun don disponible\n";
        }
        
        echo "\n";
        
        // Besoins en attente
        echo "⚠️  BESOINS NON SATISFAITS\n";
        echo "═════════════════════════\n";
        echo "Total: " . $report['besoins_non_satisfaits']['count'] . " besoins\n";
        echo "Quantité totale: " . $report['besoins_non_satisfaits']['total_quantity'] . " unités\n\n";
        
        if (!empty($report['besoins_non_satisfaits']['details'])) {
            $besoins = $report['besoins_non_satisfaits']['details'];
            foreach ($besoins as $besoin) {
                echo sprintf(
                    "  #%-3d %-15s | %-15s | %4d unités | %.2f Ar\n",
                    $besoin['id'],
                    $besoin['type_libelle'],
                    $besoin['ville_nom'],
                    $besoin['quantity_restante'],
                    $besoin['prix_unitaire']
                );
            }
        } else {
            echo "  Tous les besoins sont satisfaits ✓\n";
        }
        
        echo "\n";
        
    } catch (Exception $e) {
        echo "❌ Erreur: " . $e->getMessage() . "\n\n";
        return 1;
    }
    
    return 0;
});

// Commande pour ajouter un don
$dispatchAdd = new Command('dispatch:add', 'Ajoute un nouveau don');
$dispatchAdd
    ->option('-t --type', 'Type de besoin (1=Eau, 2=Nourriture, 3=Médicaments, 4=Vêtements, 5=Abri)', null, true)
    ->option('-q --quantity', 'Quantité du don', null, true)
    ->option('-d --date', 'Date de saisie (YYYY-MM-DD)', date('Y-m-d'))
    ->action(function($type, $quantity, $date) {
        echo "\n╔═══════════════════════════════════════════════════╗\n";
        echo "║          AJOUT D'UN NOUVEAU DON                 ║\n";
        echo "╚═══════════════════════════════════════════════════╝\n\n";
        
        try {
            // Validation
            if (!$type || !$quantity) {
                echo "❌ Erreur: Type et quantité sont obligatoires\n";
                echo "\nUtilisation:\n";
                echo "  php vendor/bin/runway dispatch:add -t 1 -q 100\n";
                echo "\nTypes de besoins:\n";
                echo "  1 = Eau\n";
                echo "  2 = Nourriture\n";
                echo "  3 = Médicaments\n";
                echo "  4 = Vêtements\n";
                echo "  5 = Abri\n\n";
                return 1;
            }
            
            $types = [
                1 => 'Eau',
                2 => 'Nourriture',
                3 => 'Médicaments',
                4 => 'Vêtements',
                5 => 'Abri'
            ];
            
            if (!isset($types[$type])) {
                echo "❌ Erreur: Type invalide (doit être entre 1 et 5)\n\n";
                return 1;
            }
            
            if ($quantity <= 0) {
                echo "❌ Erreur: La quantité doit être supérieure à 0\n\n";
                return 1;
            }
            
            // Créer le don
            $db = Flight::db();
            $donRepository = new DonRepository($db);
            $donService = new DonService($donRepository);
            
            $donId = $donService->createDon($type, $quantity, $date);
            
            echo "✅ Don créé avec succès!\n";
            echo "────────────────────────\n";
            echo "  • ID: #" . $donId . "\n";
            echo "  • Type: " . $types[$type] . "\n";
            echo "  • Quantité: " . $quantity . " unités\n";
            echo "  • Date: " . $date . "\n\n";
            
            echo "💡 Vous pouvez maintenant lancer le dispatch avec:\n";
            echo "   php vendor/bin/runway dispatch:run\n\n";
            
        } catch (Exception $e) {
            echo "❌ Erreur: " . $e->getMessage() . "\n\n";
            return 1;
        }
        
        return 0;
    });

// Enregistrer les commandes
$app = new Application('Dispatch FIFO Manager', '1.0.0');
$app->add($dispatchRun);
$app->add($dispatchReport);
$app->add($dispatchAdd);

return $app;
