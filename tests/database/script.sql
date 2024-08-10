-- Active: 1710571967719@@127.0.0.1@3306@immobilier
CREATE DATABASE pet_friend;

USE pet_friend;
--Must

CREATE TABLE pet___sessions___ (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_session CHAR(32),
    id_session_php VARCHAR(32),
    csrf_token VARCHAR(32),
    created_at TIMESTAMP DEFAULT(CURRENT_TIMESTAMP),
    last_activity TIMESTAMP DEFAULT(CURRENT_TIMESTAMP) ON UPDATE CURRENT_TIMESTAMP
);

--EndMust

CREATE TABLE petadmins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50),
    password VARCHAR(100)
);

INSERT INTO
    petadmins VALUE (
        DEFAULT,
        'admin',
        '$2y$10$G8O7VLUkLKyR6YkfBo3nd.9Qt7APkVL/UvLBjn4ZpXtEwowdXm9ya'
    );
-- Password : "messi_is_the_goat"

CREATE TABLE petfiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ownerId INT DEFAULT(NULL),
    name VARCHAR(50) NOT NULL UNIQUE,
    mime VARCHAR(20) NOT NULL
);

CREATE TABLE petimages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_file INT,
    id_animal INT
);

-- Optional Pre-existant sql script
CREATE TABLE petusers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_file INT DEFAULT(NULL),
    pseudo VARCHAR(20),
    email VARCHAR(50),
    password VARCHAR(100),
    province VARCHAR(100),
    surface_disponible INT,
    budget_mois INT,
    date_naissance DATE
);

CREATE TABLE petespeces (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50),
    surface INT,
    depense INT
);

CREATE TABLE petanimals (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_espece INT,
    sexe CHAR(1),
    nom VARCHAR(20),
    id_image INT,
    id_age INT,
    province VARCHAR(100),
    adopte INT DEFAULT(0)
);

CREATE TABLE petages (
    id INT PRIMARY KEY AUTO_INCREMENT, -- 1 -15% : junior, 2 0% : adulte, 3 10% : senior
    age VARCHAR(20),
    pourcentage INT
);

INSERT INTO petages VALUE (DEFAULT, "Junior", -15);

INSERT INTO petages VALUE (DEFAULT, "Adulte", -15);

INSERT INTO petages VALUE (DEFAULT, "Senior", 10);