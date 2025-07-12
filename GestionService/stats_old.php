<?php
session_start();

// Affiche les erreurs PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db.php';

// Vérifie que l'utilisateur est connecté et autorisé
if (!isset($_SESSION['user'], $_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'manager'])) {
    header("Location: login.php");
    exit();
}

// Total des ventes
$stmt = $pdo->query("SELECT SUM(prix_total) AS total FROM commandes");
$total = $stmt->fetchColumn() ?: 0;

// Nombre de commandes
$stmt = $pdo->query("SELECT COUNT(*) AS nb FROM commandes");
$nb_commandes = $stmt->fetchColumn() ?: 0;

// Ventes des 7 derniers jours
$stmt = $pdo->query("
    SELECT DATE(date_commande) AS jour, SUM(prix_total) AS total
    FROM commandes
    GROUP BY jour
    ORDER BY jour DESC
    LIMIT 7
");
$donnees_journalieres = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Statistiques des Ventes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-dark text-light">
<div class="container py-5">
    <h2 class="text-center mb-5">📊 Statistiques des Ventes</h2>

    <div class="text-center mb-4">
        <a href="dashboard.php" class="btn btn-outline-light">
            <i class="fas fa-arrow-left"></i> Retour au Dashboard
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="p-4 bg-secondary rounded text-center shadow">
                <h5>Total des Ventes</h5>
                <h2><?= number_format($total, 2, ',', ' ') ?> €</h2>
            </div>
        </div>
        <div class="col-md-6">
            <div class="p-4 bg-secondary rounded text-center shadow">
                <h5>Nombre de Commandes</h5>
                <h2><?= $nb_commandes ?></h2>
            </div>
        </div>
    </div>

    <div class="card bg-dark border-light shadow p-4">
        <h4 class="text-center">📈 Ventes sur les 7 derniers jours</h4>
        <canvas id="salesChart" height="120"></canvas>
    </div>
</div>

<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_column($donnees_journalieres, 'jour')) ?>,
            datasets: [{
                label: 'Total des ventes (€)',
                data: <?= json_encode(array_map('floatval', array_column($donnees_journalieres, 'total'))) ?>,
                borderColor: '#ffc107',
                backgroundColor: 'rgba(255,193,7,0.2)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
</body>
</html>
