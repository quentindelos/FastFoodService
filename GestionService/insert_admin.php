<?php
require 'db.php'; // connexion via $pdo (PDO)

try {
    // Supprimer l'ancien admin s'il existe déjà
    $pdo->prepare("DELETE FROM utilisateurs WHERE username = ?")->execute(['admin']);

    // Création d'un nouvel admin avec un mot de passe hashé
    $username = 'admin';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $role = 'admin';

    $stmt = $pdo->prepare("INSERT INTO utilisateurs (username, password, role) VALUES (?, ?, ?)");
    $stmt->execute([$username, $password, $role]);

    echo "✅ Administrateur ajouté avec succès.";

} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
?>
