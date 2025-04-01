<?php
// Fonction pour charger les variables d'environnement
function loadEnv($path) {
    if (!file_exists($path)) {
        return false;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos(trim($line), '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            putenv("$key=$value");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
    return true;
}

// Charger le fichier .env depuis la racine du projet
// Note: __DIR__ . '/../../.env' remonte de deux niveaux depuis le dossier php
// Si votre fichier php/config.php est directement à un niveau en dessous de la racine,
// utilisez plutôt __DIR__ . '/../.env'
loadEnv(__DIR__ . '/../.env');

// Utiliser les variables d'environnement pour la connexion à la base de données
$servername = getenv('DB_HOST'); 
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');
$dbname = getenv('DB_NAME');

// Le reste de votre code de connexion reste inchangé
// Par exemple:
// $conn = new mysqli($servername, $username, $password, $dbname);
// ou
// $bdd = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);