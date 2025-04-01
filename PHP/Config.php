<?php

// Paramètres de connexion à la base de données

$servername = "localhost"; // Sur o2switch, le serveur de base de données est généralement "localhost"

$username   = "delo5366_StreamWave_Account"; // Votre nom d'utilisateur de base de données

$password   = "X2E_n!o9pjCriYnuKwT@rgaQ"; // Votre mot de passe

$dbname     = "delo5366_StreamWave"; // Nom de votre base de données



// Création de la connexion à la base de données

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {

    die("Échec de la connexion : " . $conn->connect_error);

}



// Création de la base de données si elle n'existe pas encore

$dbQuery = "CREATE DATABASE IF NOT EXISTS $dbname";

if (!$conn->query($dbQuery)) {

    die("Erreur de création de la base de données : " . $conn->error);

}



// Sélection de la base de données pour l'utiliser

$conn->select_db($dbname);



// Création de la table des utilisateurs si elle n'existe pas encore

$tableQuery = "CREATE TABLE IF NOT EXISTS login_streamwave (

    id SERIAL PRIMARY KEY,

    username VARCHAR(50) NOT NULL,

    password VARCHAR(50) NOT NULL

)";

if (!$conn->query($tableQuery)) {

    die("Erreur de création de la table : " . $conn->error);

}

?>