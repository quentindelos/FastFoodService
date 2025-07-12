<?php
// ⚠️ Active l'affichage des erreurs pour le debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'config.php';

if (!isset($_SESSION['id_client'])) {
    header('Location: login.php');
    exit();
}

$id_client = $_SESSION['id_client'];

// Vérifie les points actuels
$stmt = $pdo->prepare("SELECT points_fidelite FROM clients WHERE id_client = ?");
$stmt->execute([$id_client]);
$points = $stmt->fetchColumn();

if ($points === false) {
    $_SESSION['message'] = "Erreur : client introuvable.";
    header("Location: profil.php");
    exit();
}

if ($points >= 100) {
    // Déduit les points et active la réduction
    $stmt = $pdo->prepare("UPDATE clients SET points_fidelite = points_fidelite - 100, reduction_active = 1 WHERE id_client = ?");
    $stmt->execute([$id_client]);

    $_SESSION['message'] = "🎉 Vous avez activé une réduction de 10% pour votre prochaine commande.";
} else {
    $_SESSION['message'] = "❌ Vous devez avoir au moins 100 points pour activer une réduction.";
}

header("Location: profil.php");
exit();
