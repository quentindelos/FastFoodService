<?php
require 'db.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        $stmt = $pdo->prepare("DELETE FROM employes WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: employes.php?deleted=1");
        exit();
    } catch (PDOException $e) {
        die("❌ Erreur lors de la suppression : " . $e->getMessage());
    }
} else {
    echo "❌ ID invalide.";
}
?>
