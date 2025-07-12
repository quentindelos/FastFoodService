<?php
require 'db.php';

$email = 'an.senouci13@gmail.com';

// Requête sécurisée avec requête préparée
$stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "✅ Utilisateur trouvé";
} else {
    echo "❌ Aucun utilisateur trouvé";
}

$stmt->close();
?>
