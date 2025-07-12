<?php
session_start();
require 'db.php'; // Ce fichier doit fournir un objet PDO nommé $pdo

// 🔐 Vérification connexion
if (!isset($_SESSION['user']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['user'];
$role = $_SESSION['role'];
$success = $error = "";

// ✅ Ajout de shift
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($role, ['admin', 'manager'])) {
    $employe = $_POST['employe'] ?? '';
    $jour = $_POST['jour'] ?? '';
    $debut = $_POST['heure_debut'] ?? '';
    $fin = $_POST['heure_fin'] ?? '';

    if ($employe && $jour && $debut && $fin) {
        $stmt = $pdo->prepare("INSERT INTO shifts (employe, jour, heure_debut, heure_fin) VALUES (?, ?, ?, ?)");
        $stmt->execute([$employe, $jour, $debut, $fin]);
        $success = "✅ Shift ajouté.";
    }
}

// 🗑️ Suppression d’un shift
if (isset($_GET['delete']) && in_array($role, ['admin', 'manager'])) {
    $id = (int) $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM shifts WHERE id = ?");
    $stmt->execute([$id]);
    $success = "✅ Shift supprimé.";
}

// 📋 Liste des employés
$employes = $pdo->query("SELECT nom, prenom FROM employes")->fetchAll(PDO::FETCH_ASSOC);

// 📆 Liste des shifts selon rôle
if ($role === 'cuisinier') {
    $stmt = $pdo->prepare("SELECT e.nom, e.prenom FROM utilisateurs u JOIN employes e ON u.employe_id = e.id WHERE u.email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $fullName = $user ? $user['nom'] . ' ' . $user['prenom'] : '';

    $stmt = $pdo->prepare("SELECT * FROM shifts WHERE employe = ? ORDER BY FIELD(jour, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'), heure_debut");
    $stmt->execute([$fullName]);
    $shifts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $shifts = $pdo->query("SELECT * FROM shifts ORDER BY FIELD(jour, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'), heure_debut")->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Planning - SoulMade</title>
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
            background: rgba(0,0,0,0.8);
            padding: 30px;
            border-radius: 10px;
        }
        .form-block {
            background: rgba(255,255,255,0.05);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .table thead {
            background-color: #ffc107;
            color: black;
        }
        .btn-retour {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>🗓️ Planning des Shifts</h2>

    <?php if ($success): ?>
        <div class="alert alert-success text-center"><?= $success ?></div>
    <?php endif; ?>

    <?php if (in_array($role, ['admin', 'manager'])): ?>
        <div class="form-block">
            <form method="POST" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Employé</label>
                    <select name="employe" class="form-select" required>
                        <option value="">-- Sélectionner --</option>
                        <?php foreach ($employes as $emp): ?>
                            <?php $nomComplet = $emp['nom'] . ' ' . $emp['prenom']; ?>
                            <option value="<?= htmlspecialchars($nomComplet) ?>">
                                <?= htmlspecialchars($nomComplet) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Jour</label>
                    <select name="jour" class="form-select" required>
                        <?php foreach (['Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche'] as $j): ?>
                            <option><?= $j ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Heure début</label>
                    <input type="time" name="heure_debut" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Heure fin</label>
                    <input type="time" name="heure_fin" class="form-control" required>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-success w-100">Ajouter un Shift</button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <?php if (!empty($shifts)): ?>
        <div class="card bg-dark border-light shadow-lg mt-4">
            <div class="card-header bg-warning text-dark fw-bold text-center fs-5">
                📋 Planning des Shifts
            </div>
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle text-center">
                    <thead class="table-warning text-dark">
                        <tr>
                            <th>👤 Employé</th>
                            <th>📅 Jour</th>
                            <th>🕒 Début</th>
                            <th>🕕 Fin</th>
                            <?php if (in_array($role, ['admin', 'manager'])): ?>
                                <th>🗑️ Action</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($shifts as $row): ?>
                            <tr style="transition: 0.2s;" onmouseover="this.style.backgroundColor='rgba(255,255,255,0.05)'" onmouseout="this.style.backgroundColor=''">
                                <td class="fw-bold text-info"><?= htmlspecialchars($row['employe']) ?></td>
                                <td><span class="badge bg-secondary"><?= $row['jour'] ?></span></td>
                                <td><?= $row['heure_debut'] ?></td>
                                <td><?= $row['heure_fin'] ?></td>
                                <?php if (in_array($role, ['admin', 'manager'])): ?>
                                    <td>
                                        <a href="?delete=<?= $row['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Supprimer ce shift ?')">
                                            Supprimer
                                        </a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center">Aucun shift planifié pour le moment.</div>
    <?php endif; ?>

    <div class="text-center">
        <a href="dashboard.php" class="btn btn-light btn-retour">🏠 Retour au Dashboard</a>
    </div>
</div>
</body>
</html>
