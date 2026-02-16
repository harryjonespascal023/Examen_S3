CREATE DATABASE IF NOT EXISTS BNGRC;
USE BNGRC;

CREATE TABLE IF NOT EXISTS BNR_ville(
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255) NOT NULL,
    nombre_sinistres INT NOT NULL
);
CREATE TABLE IF NOT EXISTS BNR_type_besoin(
    id INT PRIMARY KEY AUTO_INCREMENT,
    libelle VARCHAR(255) NOT NULL
);
CREATE TABLE IF NOT EXISTS BNR_besoin(
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_ville INT NOT NULL,
    id_type_besoin INT NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    quantity_restante INT NOT NULL,
    FOREIGN KEY (id_ville) REFERENCES BNR_ville(id),
    FOREIGN KEY (id_type_besoin) REFERENCES BNR_type_besoin(id)
);
CREATE TABLE IF NOT EXISTS BNR_don(
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_type_besoin INT NOT NULL,
    quantity INT NOT NULL,
    quantity_restante INT NOT NULL,
    date_saisie DATE NOT NULL,
    FOREIGN KEY (id_type_besoin) REFERENCES BNR_type_besoin(id)
);
CREATE TABLE IF NOT EXISTS BNR_dispatch(
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_don INT NOT NULL,
    id_besoin INT NOT NULL,
    quantity INT NOT NULL,
    date_dispatch DATE NOT NULL,
    FOREIGN KEY (id_don) REFERENCES BNR_don(id),
    FOREIGN KEY (id_besoin) REFERENCES BNR_besoin(id)
);
