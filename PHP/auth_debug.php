<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once(__DIR__ . DIRECTORY_SEPARATOR . "Config.php");

echo "<h1>Diagnostic de connexion StreamWave</h1>";

if (!isset($conn)) {
    die("<p style='color:red;'>Erreur: Connexion à la base de données non établie.</p>");
}

// Vérifier si un test de connexion est demandé
if (isset($_POST['test_login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    echo "<h2>Test de connexion pour: " . htmlspecialchars($username) . "</h2>";
    
    // Vérifier l'existence de l'utilisateur
    $query = "SELECT id, username, password FROM login_streamwave WHERE username = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            echo "<p style='color:red;'>Utilisateur introuvable dans la base de données.</p>";
        } else {
            $user = $result->fetch_assoc();
            
            echo "<p>Utilisateur trouvé avec ID: " . $user['id'] . "</p>";
            
            // Information sur le mot de passe stocké
            echo "<p>Longueur du mot de passe stocké: " . strlen($user['password']) . " caractères</p>";
            echo "<p>Début du hash: " . substr($user['password'], 0, 10) . "...</p>";
            
            // Test de password_verify
            if (password_verify($password, $user['password'])) {
                echo "<p style='color:green;'>SUCCÈS: Le mot de passe est vérifié correctement!</p>";
            } else {
                echo "<p style='color:red;'>ÉCHEC: La vérification du mot de passe a échoué.</p>";
                
                // Vérifier si le hash est tronqué
                if (strlen($user['password']) < 60) {
                    echo "<p style='color:orange;'>ATTENTION: Le hash semble être tronqué (moins de 60 caractères).</p>";
                    echo "<p>Vous devriez réexécuter le script update_passwords.php après avoir agrandi le champ password.</p>";
                }
            }
        }
        $stmt->close();
    } else {
        echo "<p style='color:red;'>Erreur de préparation de la requête: " . $conn->error . "</p>";
    }
}

// Afficher quelques utilisateurs pour référence
$query = "SELECT id, username, SUBSTRING(password, 1, 15) AS password_start, LENGTH(password) AS password_length FROM login_streamwave LIMIT 5";
$result = $conn->query($query);

echo "<h2>Échantillon d'utilisateurs dans la base de données</h2>";

if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Nom d'utilisateur</th><th>Début du hash</th><th>Longueur</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['username'] . "</td>";
        echo "<td>" . $row['password_start'] . "...</td>";
        echo "<td>" . $row['password_length'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>Aucun utilisateur trouvé.</p>";
}

// Formulaire de test
echo "<h2>Tester la connexion d'un utilisateur</h2>";
echo "<form method='post'>";
echo "<div><label>Nom d'utilisateur: <input type='text' name='username' required></label></div>";
echo "<div><label>Mot de passe: <input type='password' name='password' required></label></div>";
echo "<div><button type='submit' name='test_login'>Tester la connexion</button></div>";
echo "</form>";

// Instructions à l'intention de l'utilisateur
echo "<h2>Si vous ne pouvez toujours pas vous connecter</h2>";
echo "<ol>";
echo "<li>Vérifiez si les mots de passe ont une longueur suffisante (60+ caractères)</li>";
echo "<li>Si les mots de passe sont tronqués, réexécutez le script update_passwords.php</li>";
echo "<li>Si vous avez créé votre compte après la mise à jour, essayez de récupérer le mot de passe</li>";
echo "<li>Vérifiez que les sessions fonctionnent correctement (permissions des cookies, etc.)</li>";
echo "</ol>";

$conn->close();
?>
