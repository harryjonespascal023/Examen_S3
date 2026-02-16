CREATE DATABASE Takalo;
USE Takalo;

CREATE TABLE type_user(
    id INT PRIMARY KEY AUTO_INCREMENT,
    type VARCHAR(200)
);

CREATE TABLE users(
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255) NOT NULL ,
    password_hash VARCHAR(255) NOT NULL ,
    type_id INT NOT NULL ,
    creation DATETIME,
    FOREIGN KEY (type_id) REFERENCES type_user(id)
);

CREATE TABLE categories_objet(
    id INT PRIMARY KEY AUTO_INCREMENT,
    libelle VARCHAR(255) NOT NULL
);

CREATE TABLE objets(
    id INT PRIMARY KEY AUTO_INCREMENT,
    libelle VARCHAR(200) NOT NULL ,
    description TEXT,
    categorie_id INT,
    user_id INT NOT NULL ,
    prix DECIMAL NOT NULL DEFAULT 0,
    date_publication DATETIME,
    FOREIGN KEY (categorie_id) REFERENCES categories_objet(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE images_objet(
    id INT PRIMARY KEY AUTO_INCREMENT,
    objet_id INT NOT NULL ,
    fichier VARCHAR(255) NOT NULL ,
    FOREIGN KEY (objet_id) REFERENCES objets(id)
);

CREATE TABLE echanges(
    id INT PRIMARY KEY AUTO_INCREMENT,
    objet_id INT NOT NULL ,
    objet_propose_id INT NOT NULL ,
    user1_id INT NOT NULL ,
    user2_id INT NOT NULL ,
    statut TINYINT NOT NULL DEFAULT 0,
    date_demande DATETIME,
    date_accept DATETIME,
    FOREIGN KEY (objet_id) REFERENCES objets(id),
    FOREIGN KEY (objet_propose_id) REFERENCES objets(id),
    FOREIGN KEY (user1_id) REFERENCES users(id),
    FOREIGN KEY (user2_id) REFERENCES users(id)
);

CREATE TABLE historique_objet(
    id INT PRIMARY KEY AUTO_INCREMENT,
    objet_id INT NOT NULL ,
    user_id INT NOT NULL ,
    date_changement DATETIME NOT NULL,
    FOREIGN KEY (objet_id) REFERENCES objets(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
