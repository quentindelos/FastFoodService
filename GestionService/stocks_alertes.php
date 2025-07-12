<?php
session_start();
require 'db.php';
require 'auth.php';
autoriser(['admin', 'manager']);

if (!isset($_SESSION['user']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

// ✅ Requête : produits dont la quantité est inférieure ou égale au seuil
$stmt = $pdo->query("SELECT * FROM stock WHERE quantite_disponible <= seuil_minimum");
$produits_alertes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Alertes Stock</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: url('photo/img1.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Poppins', sans-serif;
            color: white;
        }
        .container {
            margin-top: 50px;
            background: rgba(0,0,0,0.85);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(255, 0, 0, 0.4);
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #ff4d4d;
        }
        .table thead {
            background-color: #dc3545;
            color: white;
        }
        .table tbody tr:hover {
            background-color: rgba(255, 0, 0, 0.1);
        }
        .btn-retour {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🚨 Alertes de Stock Critique</h2>

        <?php if (count($produits_alertes) > 0): ?>
            <div class="card bg-dark border-danger shadow-lg">
                <div class="card-header bg-danger text-white fw-bold text-center fs-5">
                    📋 Produits en-dessous du seuil
                </div>
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle mb-0 text-center">
                        <thead class="text-uppercase table-danger">
                            <tr>
                                <th>🛒 Produit</th>
                                <th>📦 Quantité</th>
                                <th>📉 Seuil</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($produits_alertes as $row): ?>
                                <tr>
                                    <td class="fw-bold text-warning"><?= htmlspecialchars($row['nom_produit']) ?></td>
                                    <td><?= $row['quantite_disponible'] ?></td>
                                    <td><span class="badge bg-danger"><?= $row['seuil_minimum'] ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-success text-center mt-4">
                ✅ Tous les niveaux de stock sont corrects.
            </div>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="dashboard.php" class="btn btn-outline-light btn-retour">🏠 Retour au Dashboard</a>
        </div>
    </div>
</body>
</html>
