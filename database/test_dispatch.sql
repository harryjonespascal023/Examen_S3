-- Script de test pour le système de dispatch FIFO
-- Ce script insère des données de test et montre comment utiliser le système

-- 1. Insertion des villes
INSERT INTO BNR_ville (nom, nombre_sinistres) VALUES
('Toamasina', 500),
('Antananarivo', 300),
('Mahajanga', 200),
('Fianarantsoa', 150);

-- 2. Insertion des types de besoins
INSERT INTO BNR_type_besoin (libelle) VALUES
('Eau'),
('Nourriture'),
('Médicaments'),
('Vêtements'),
('Abri');

-- 3. Insertion des besoins (avec quantity_restante = quantity au départ)
INSERT INTO BNR_besoin (id_ville, id_type_besoin, prix_unitaire, quantity, quantity_restante) VALUES
-- Toamasina a besoin de beaucoup d'eau et nourriture
(1, 1, 1.50, 200, 200),    -- Eau : 200 unités
(1, 2, 5.00, 150, 150),    -- Nourriture : 150 unités
(1, 3, 15.00, 50, 50),     -- Médicaments : 50 unités

-- Antananarivo a besoin de vêtements et abri
(2, 4, 10.00, 100, 100),   -- Vêtements : 100 unités
(2, 5, 50.00, 30, 30),     -- Abri : 30 unités

-- Mahajanga a besoin d'eau et médicaments
(3, 1, 1.50, 100, 100),    -- Eau : 100 unités
(3, 3, 15.00, 75, 75),     -- Médicaments : 75 unités

-- Fianarantsoa a besoin de nourriture
(4, 2, 5.00, 80, 80);      -- Nourriture : 80 unités

-- 4. Insertion des dons (avec quantity_restante = quantity au départ)
-- IMPORTANT : Les dons seront utilisés par ordre de date_saisie (FIFO)

-- Dons du 14 février 2026
INSERT INTO BNR_don (id_type_besoin, quantity, quantity_restante, date_saisie) VALUES
(1, 100, 100, '2026-02-14'), -- Don 1 : Eau - 100 unités (le plus ancien)
(2, 50, 50, '2026-02-14');   -- Don 2 : Nourriture - 50 unités

-- Dons du 15 février 2026
INSERT INTO BNR_don (id_type_besoin, quantity, quantity_restante, date_saisie) VALUES
(1, 250, 250, '2026-02-15'), -- Don 3 : Eau - 250 unités
(3, 100, 100, '2026-02-15'), -- Don 4 : Médicaments - 100 unités
(2, 200, 200, '2026-02-15'); -- Don 5 : Nourriture - 200 unités

-- Dons du 16 février 2026
INSERT INTO BNR_don (id_type_besoin, quantity, quantity_restante, date_saisie) VALUES
(4, 120, 120, '2026-02-16'), -- Don 6 : Vêtements - 120 unités
(5, 40, 40, '2026-02-16'),   -- Don 7 : Abri - 40 unités
(3, 50, 50, '2026-02-16');   -- Don 8 : Médicaments - 50 unités (le plus récent)

-- 5. Vérification des données avant dispatch
SELECT 'BESOINS NON SATISFAITS AVANT DISPATCH' as Info;
SELECT 
    b.id,
    v.nom as ville,
    t.libelle as type,
    b.quantity as quantite_totale,
    b.quantity_restante,
    b.prix_unitaire,
    (b.quantity_restante * b.prix_unitaire) as valeur_restante
FROM BNR_besoin b
JOIN BNR_ville v ON b.id_ville = v.id
JOIN BNR_type_besoin t ON b.id_type_besoin = t.id
WHERE b.quantity_restante > 0
ORDER BY b.id;

SELECT 'DONS NON UTILISES AVANT DISPATCH' as Info;
SELECT 
    d.id,
    t.libelle as type,
    d.quantity as quantite_totale,
    d.quantity_restante,
    d.date_saisie
FROM BNR_don d
JOIN BNR_type_besoin t ON d.id_type_besoin = t.id
WHERE d.quantity_restante > 0
ORDER BY d.date_saisie, d.id;

-- ==========================================
-- APRES LE DISPATCH (via POST /api/dons/dispatch)
-- ==========================================

-- 6. Requêtes pour vérifier les résultats après dispatch

-- Voir tous les dispatches effectués
SELECT 'HISTORIQUE DES DISPATCHES' as Info;
SELECT 
    dp.id,
    dp.date_dispatch,
    d.id as don_id,
    d.date_saisie as don_date,
    t.libelle as type,
    dp.quantity as quantite_dispatchee,
    b.id as besoin_id,
    v.nom as ville
FROM BNR_dispatch dp
JOIN BNR_don d ON dp.id_don = d.id
JOIN BNR_besoin b ON dp.id_besoin = b.id
JOIN BNR_type_besoin t ON d.id_type_besoin = t.id
JOIN BNR_ville v ON b.id_ville = v.id
ORDER BY dp.date_dispatch DESC, dp.id DESC;

-- Voir l'état des besoins après dispatch
SELECT 'ETAT DES BESOINS APRES DISPATCH' as Info;
SELECT 
    b.id,
    v.nom as ville,
    t.libelle as type,
    b.quantity as quantite_totale,
    b.quantity_restante,
    CASE 
        WHEN b.quantity_restante = 0 THEN '✅ Satisfait'
        WHEN b.quantity_restante < b.quantity THEN '⚠️ Partiellement satisfait'
        ELSE '❌ Non satisfait'
    END as statut,
    ROUND((b.quantity - b.quantity_restante) / b.quantity * 100, 2) as pourcentage_satisfait
FROM BNR_besoin b
JOIN BNR_ville v ON b.id_ville = v.id
JOIN BNR_type_besoin t ON b.id_type_besoin = t.id
ORDER BY b.id;

-- Voir l'état des dons après dispatch
SELECT 'ETAT DES DONS APRES DISPATCH' as Info;
SELECT 
    d.id,
    t.libelle as type,
    d.quantity as quantite_totale,
    d.quantity_restante,
    d.date_saisie,
    CASE 
        WHEN d.quantity_restante = 0 THEN '✅ Totalement utilisé'
        WHEN d.quantity_restante < d.quantity THEN '⚠️ Partiellement utilisé'
        ELSE '❌ Non utilisé'
    END as statut,
    ROUND((d.quantity - d.quantity_restante) / d.quantity * 100, 2) as pourcentage_utilise
FROM BNR_don d
JOIN BNR_type_besoin t ON d.id_type_besoin = t.id
ORDER BY d.date_saisie, d.id;

-- Statistiques globales
SELECT 'STATISTIQUES GLOBALES' as Info;
SELECT 
    (SELECT COUNT(*) FROM BNR_don WHERE quantity_restante = 0) as dons_totalement_utilises,
    (SELECT COUNT(*) FROM BNR_don WHERE quantity_restante > 0 AND quantity_restante < quantity) as dons_partiellement_utilises,
    (SELECT COUNT(*) FROM BNR_don WHERE quantity_restante = quantity) as dons_non_utilises,
    (SELECT COUNT(*) FROM BNR_besoin WHERE quantity_restante = 0) as besoins_satisfaits,
    (SELECT COUNT(*) FROM BNR_besoin WHERE quantity_restante > 0 AND quantity_restante < quantity) as besoins_partiels,
    (SELECT COUNT(*) FROM BNR_besoin WHERE quantity_restante = quantity) as besoins_non_satisfaits,
    (SELECT COUNT(*) FROM BNR_dispatch) as total_dispatches,
    (SELECT SUM(quantity) FROM BNR_dispatch) as quantite_totale_dispatchee;

-- Détail par type de besoin
SELECT 'BILAN PAR TYPE DE BESOIN' as Info;
SELECT 
    t.libelle as type,
    COALESCE(SUM(d.quantity), 0) as total_dons,
    COALESCE(SUM(d.quantity_restante), 0) as dons_restants,
    COALESCE(SUM(b.quantity), 0) as total_besoins,
    COALESCE(SUM(b.quantity_restante), 0) as besoins_restants,
    COALESCE(SUM(d.quantity), 0) - COALESCE(SUM(d.quantity_restante), 0) as quantite_dispatchee,
    CASE 
        WHEN COALESCE(SUM(d.quantity), 0) > COALESCE(SUM(b.quantity), 0) THEN '✅ Excédent'
        WHEN COALESCE(SUM(d.quantity), 0) = COALESCE(SUM(b.quantity), 0) THEN '✅ Équilibré'
        ELSE '⚠️ Déficit'
    END as statut
FROM BNR_type_besoin t
LEFT JOIN BNR_don d ON t.id = d.id_type_besoin
LEFT JOIN BNR_besoin b ON t.id = b.id_type_besoin
GROUP BY t.id, t.libelle
ORDER BY t.libelle;
