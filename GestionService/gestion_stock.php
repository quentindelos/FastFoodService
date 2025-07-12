<?php
session_start();
require 'db.php'; // ce fichier doit fournir $pdo

// 🔒 Vérifie l'authentification
if (!isset($_SESSION['user']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];
if (!in_array($role, ['admin', 'manager', 'cuisinier'])) {
    header("Location: dashboard.php");
    exit();
}

$message = "";

// ✅ Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom_produit'] ?? '';
    $quantite = $_POST['quantite_disponible'] ?? null;
    $unite = $_POST['unite'] ?? '';
    $seuil = $_POST['seuil_minimum'] ?? null;
    $now = date('Y-m-d H:i:s');

    if ($nom && $quantite !== '' && $seuil !== '') {
        $stmt = $pdo->prepare("INSERT INTO stock (nom_produit, quantite_disponible, unite, seuil_minimum, date_mise_a_jour) 
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nom, $quantite, $unite, $seuil, $now]);
        $message = "✅ Produit ajouté avec succès.";
    } else {
        $message = "❌ Tous les champs obligatoires doivent être remplis.";
    }
}

// 📦 Récupération des stocks
$result = $pdo->query("SELECT * FROM stock")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion du Stock</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-dark text-light">
<div class="container mt-5">
    <h2 class="mb-4">📦 Gestion du Stock</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <div class="mb-3">
        <a href="dashboard.php" class="btn btn-outline-light">
            <i class="fas fa-arrow-left"></i> Retour au Dashboard
        </a>
    </div>

    <form method="POST" class="row g-3 mb-5">
        <div class="col-md-4">
            <input type="text" name="nom_produit" class="form-control" placeholder="Nom du produit" required>
        </div>
        <div class="col-md-2">
            <input type="number" step="0.01" name="quantite_disponible" class="form-control" placeholder="Quantité" required>
        </div>
        <div class="col-md-2">
            <input type="text" name="unite" class="form-control" placeholder="Unité (ex: kg, L)">
        </div>
        <div class="col-md-2">
            <input type="number" name="seuil_minimum" class="form-control" placeholder="Seuil Minimum" required>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-success w-100">Ajouter</button>
        </div>
    </form>

    <div class="card bg-dark border-secondary shadow-lg">
        <div class="card-header bg-success text-white text-center fw-bold fs-5">
            📋 Stock actuel des produits
        </div>
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0 text-center">
                <thead class="table-success text-uppercase">
                    <tr>
                        <th>#</th>
                        <th>🛒 Produit</th>
                        <th>📦 Quantité</th>
                        <th>🔢 Unité</th>
                        <th>📉 Seuil</th>
                        <th>🕒 Mis à jour</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result as $row): ?>
                        <tr class="table-row-hover">
                            <td><span class="badge bg-secondary"><?= $row['id'] ?></span></td>
                            <td class="fw-bold text-info"><?= htmlspecialchars($row['nom_produit']) ?></td>
                            <td><?= $row['quantite_disponible'] ?></td>
                            <td><?= $row['unite'] ?: '-' ?></td>
                            <td><span class="badge bg-danger"><?= $row['seuil_minimum'] ?></span></td>
                            <td><i class="fas fa-clock"></i> <?= $row['date_mise_a_jour'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
