<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['user'];

// Récupérer le nom complet de l’employé connecté
$stmt = $pdo->prepare("SELECT e.nom, e.prenom FROM utilisateurs u JOIN employes e ON u.employe_id = e.id WHERE u.email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$nom_complet = $user ? $user['nom'] . ' ' . $user['prenom'] : '';

$shifts = [];
if ($nom_complet) {
    $stmt = $pdo->prepare("
        SELECT * FROM shifts 
        WHERE employe = ? 
        ORDER BY FIELD(jour, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'), heure_debut
    ");
    $stmt->execute([$nom_complet]);
    $shifts = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Planning</title>
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
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.5);
            animation: fadeIn 0.8s ease-in-out;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #ffc107;
        }
        .table thead {
            background-color: #ffc107;
            color: #000;
            text-transform: uppercase;
        }
        .table tbody tr:hover {
            background-color: rgba(255,255,255,0.05);
        }
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .badge-jour {
            font-size: 0.9rem;
            background-color: #6c757d;
            padding: 6px 10px;
            border-radius: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🗓️ Mon Planning Hebdomadaire</h2>

        <?php if (!empty($shifts)): ?>
            <div class="card bg-dark border-light shadow">
                <div class="card-header text-center bg-warning text-dark fw-bold fs-5">
                    📅 Vos shifts à venir
                </div>
                <div class="table-responsive">
                    <table class="table table-dark table-hover text-center align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Jour</th>
                                <th>Heure Début</th>
                                <th>Heure Fin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($shifts as $row): ?>
                                <tr>
                                    <td><span class="badge badge-jour"><?= htmlspecialchars($row['jour']) ?></span></td>
                                    <td><?= htmlspecialchars($row['heure_debut']) ?></td>
                                    <td><?= htmlspecialchars($row['heure_fin']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center mt-4">⚠️ Aucun shift prévu pour vous pour l’instant.</div>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="dashboard.php" class="btn btn-outline-light">🏠 Retour au Dashboard</a>
        </div>
    </div>
</body>
</html>
