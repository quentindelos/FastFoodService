<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

require 'auth.php';
autoriser(['admin', 'manager', 'cuisinier']);

if (!in_array($_SESSION['role'], ['admin', 'manager'])) {
    header("Location: dashboard.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM employes");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Employés</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('photo/img1.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Poppins', sans-serif;
            color: white;
        }

        .container {
            margin-top: 60px;
            background: rgba(0,0,0,0.85);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
            animation: fadeIn 0.7s ease-in-out;
        }

        h2 {
            color: #ffc107;
            text-align: center;
            margin-bottom: 30px;
        }

        .table thead {
            background-color: #ffc107;
            color: #000;
            text-transform: uppercase;
        }

        .table tbody tr:hover {
            background-color: rgba(255,255,255,0.05);
        }

        .btn-action {
            margin: 0 2px;
        }

        .alert {
            font-weight: bold;
        }

        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
<div class="container">
    <h2>👥 Gestion des Employés</h2>



    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success text-center">
            ✅ Employé supprimé avec succès.
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-dark table-hover table-bordered text-center align-middle">
            <thead class="table-warning text-dark">
                <tr>
                    <th>📸 Photo</th>
                    <th>#</th>
                    <th>🧑 Nom</th>
                    <th>👤 Prénom</th>
                    <th>📧 Email</th>
                    <th>💼 Poste</th>
                    <th>💰 Salaire</th>
                    <th>⚙️ Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td>
    <?php if ($row['photo']): ?>
        <img src="uploads/<?= $row['photo'] ?>" width="60" height="60" class="rounded-circle shadow">
    <?php else: ?>
        <i class="fas fa-user-circle fa-2x text-secondary"></i>
    <?php endif; ?>
</td>

                        <td><span class="badge bg-secondary"><?= $row['id'] ?></span></td>
                        <td><?= htmlspecialchars($row['nom']) ?></td>
                        <td><?= htmlspecialchars($row['prenom']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><span class="badge bg-info text-dark"><?= htmlspecialchars($row['poste']) ?></span></td>
                        <td><?= number_format($row['salaire'], 2) ?> €</td>
                        <td>
                            <a href="update_employe.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning btn-action">
                                ✏️ Modifier
                            </a>
                            <a href="delete_employe.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger btn-action" onclick="return confirm('Confirmer la suppression de cet employé ?')">
                                🗑️ Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="text-center mt-4">
        <a href="add_employe.php" class="btn btn-success me-2">➕ Ajouter un Employé</a>
        <a href="dashboard.php" class="btn btn-outline-light">🏠 Retour au Dashboard</a>
    </div>
</div>

</body>
</html>
