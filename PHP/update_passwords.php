<?php
// Script pour mettre à jour les mots de passe existants avec des versions hachées
// ATTENTION : Ce script ne doit être exécuté qu'une seule fois, puis supprimé

ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once(__DIR__ . DIRECTORY_SEPARATOR . "Config.php");

if (!isset($conn)) {
    die("Erreur interne : la connexion à la base n'est pas définie.");
}

// Récupérer tous les utilisateurs
$query = "SELECT id, username, password FROM login_streamwave";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo "<h1>Mise à jour des mots de passe</h1>";
    echo "<p>Début de la conversion des mots de passe...</p>";
    
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $plaintext_password = $row['password'];
        
        // Vérifier si le mot de passe est déjà haché (commence par $)
        if (substr($plaintext_password, 0, 1) === '$') {
            echo "<p>Utilisateur {$row['username']} : mot de passe déjà haché, ignoré.</p>";
            continue;
        }
        
        // Hacher le mot de passe
        $hashed_password = password_hash($plaintext_password, PASSWORD_DEFAULT);
        
        // Mettre à jour le mot de passe dans la base de données
        $updateQuery = "UPDATE login_streamwave SET password = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("si", $hashed_password, $id);
        
        if ($stmt->execute()) {
            echo "<p>Utilisateur {$row['username']} : mot de passe mis à jour avec succès.</p>";
        } else {
            echo "<p>Utilisateur {$row['username']} : erreur lors de la mise à jour du mot de passe - {$stmt->error}</p>";
        }
        
        $stmt->close();
    }
    
    echo "<p>Conversion des mots de passe terminée.</p>";
    echo "<p style='color:red;font-weight:bold;'>IMPORTANT : Supprimez ce fichier après utilisation pour des raisons de sécurité.</p>";
} else {
    echo "<p>Aucun utilisateur trouvé dans la base de données.</p>";
}

$conn->close();
?>
