<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once(__DIR__ . DIRECTORY_SEPARATOR . "Config.php");

if (!isset($conn)) {
    die("Erreur interne : la connexion à la base n'est pas définie.");
}

echo "<h1>Mise à jour du schéma de la base de données</h1>";

// Modification de la table pour augmenter la taille du champ password
$alterQuery = "ALTER TABLE login_streamwave MODIFY password VARCHAR(255) NOT NULL";
if ($conn->query($alterQuery)) {
    echo "<p style='color:green;'>La taille du champ password a été augmentée avec succès à VARCHAR(255).</p>";
} else {
    echo "<p style='color:red;'>Erreur lors de la modification du champ password : " . $conn->error . "</p>";
}

// Vérifier si tous les mots de passe sont correctement hachés
$checkQuery = "SELECT id, username, password FROM login_streamwave";
$result = $conn->query($checkQuery);

echo "<h2>État des mots de passe</h2>";

if ($result->num_rows > 0) {
    $hashedCount = 0;
    $totalUsers = $result->num_rows;
    
    while ($row = $result->fetch_assoc()) {
        // Vérifier si le mot de passe est haché (commence par $)
        if (substr($row['password'], 0, 1) === '$') {
            echo "<p>Utilisateur {$row['username']} : mot de passe correctement haché.</p>";
            $hashedCount++;
        } else {
            echo "<p style='color:orange;'>Utilisateur {$row['username']} : mot de passe NON haché. Utilisez update_passwords.php pour le mettre à jour.</p>";
        }
    }
    
    echo "<p>$hashedCount sur $totalUsers utilisateurs ont des mots de passe correctement hachés.</p>";
} else {
    echo "<p>Aucun utilisateur trouvé dans la base de données.</p>";
}

$conn->close();
?>
