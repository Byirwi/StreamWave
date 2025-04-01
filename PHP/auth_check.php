<?php
// Fichier de vérification d'authentification
session_start();

function isUserLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function redirectToLogin() {
    // Ajustez l'URL selon l'emplacement de votre page de login
    header("Location: https://streamwave.ldpa-tech.fr/HTML_PHP/Login.php");
    exit();
}

function requireLogin() {
    if (!isUserLoggedIn()) {
        redirectToLogin();
    }
}
?>