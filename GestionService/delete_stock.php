<?php
require 'db.php'; // Connexion via PDO

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        $stmt = $pdo->prepare("DELETE FROM stock WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: gestion_stock.php");
        exit();
    } catch (PDOException $e) {
        echo "❌ Erreur : " . $e->getMessage();
    }
} else {
    echo "❌ ID invalide.";
}
?>
