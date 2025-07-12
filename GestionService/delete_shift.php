<?php
session_start();
require 'db.php';

// Sécurité : seuls les admins peuvent supprimer un shift
if (!isset($_SESSION['user']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    try {
        $stmt = $pdo->prepare("DELETE FROM shifts WHERE id = ?");
        $stmt->execute([$id]);
    } catch (PDOException $e) {
        // Optionnel : afficher une erreur ou logger
        die("❌ Erreur lors de la suppression : " . $e->getMessage());
    }
}

header("Location: planning.php?deleted=1");
exit();
?>
