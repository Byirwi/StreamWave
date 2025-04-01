<?php
// Fichier de vérification d'authentification
session_start();

function isUserLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function redirectToLogin() {
    header("Location: login.php");
    exit();
}

function requireLogin() {
    if (!isUserLoggedIn()) {
        redirectToLogin();
    }
}
?>