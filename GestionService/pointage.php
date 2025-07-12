<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'db.php'; // Doit créer $pdo
require 'auth.php';
autoriser(['admin', 'manager', 'cuisinier']);

$role = $_SESSION['role'];
$email = $_SESSION['user'];
$success = false;

// 🔍 ID employé si cuisinier
$employe_id_connecte = null;
if ($role === 'cuisinier') {
    $stmt = $pdo->prepare("SELECT id FROM employes WHERE email = ?");
    $stmt->execute([$email]);
    $employe_id_connecte = $stmt->fetchColumn();
}

// 🔁 Formulaire pointage
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employe_id = ($role === 'cuisinier') ? $employe_id_connecte : $_POST['employe_id'];
    $action = $_POST['action'];
    $date = date('Y-m-d');
    $heure = date('H:i:s');

    if ($action === 'Arrivée') {
        $stmt = $pdo->prepare("INSERT INTO pointage (employe_id, date, heure_arrivee) VALUES (?, ?, ?)");
        $stmt->execute([$employe_id, $date, $heure]);
    } elseif ($action === 'Départ') {
        $stmt = $pdo->prepare("UPDATE pointage 
            SET heure_depart = ?, total_heures = TIMEDIFF(?, heure_arrivee) 
            WHERE employe_id = ? AND date = ? AND heure_depart IS NULL 
            ORDER BY id DESC LIMIT 1");
        $stmt->execute([$heure, $heure, $employe_id, $date]);
    }

    $success = true;
}

// 📋 Données
$employes = $pdo->query("SELECT id, nom, prenom FROM employes")->fetchAll(PDO::FETCH_ASSOC);
$pointages = $pdo->query("
    SELECT p.*, e.nom, e.prenom 
    FROM pointage p 
    JOIN employes e ON p.employe_id = e.id 
    ORDER BY p.date DESC, p.heure_arrivee DESC 
    LIMIT 20
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Pointage - SoulMade</title>
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
            margin-top: 50px;
            background: rgba(0,0,0,0.8);
            padding: 30px;
            border-radius: 10px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-block {
            background: rgba(255, 255, 255, 0.08);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .form-label {
            color: #f0f0f0;
        }

        .table-row-hover:hover {
            background-color: rgba(255, 255, 255, 0.1) !important;
            transform: scale(1.01);
            transition: transform 0.2s ease-in-out;
            cursor: pointer;
        }

        .card {
            border: 1px solid #444;
            border-radius: 12px;
            overflow: hidden;
            animation: fadeInUp 0.6s ease-in-out;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🕒 Pointage des Employés</h2>

        <?php if ($success): ?>
            <div class="alert alert-success text-center">✅ Pointage enregistré avec succès !</div>
        <?php endif; ?>

        <div class="form-block">
            <form method="POST" class="row g-3">
                <?php if ($role !== 'cuisinier'): ?>
                    <div class="col-md-6">
                        <label class="form-label">Employé</label>
                        <select name="employe_id" class="form-select" required>
                            <option value="">-- Sélectionner --</option>
                            <?php foreach ($employes as $emp): ?>
                                <option value="<?= $emp['id'] ?>">
                                    <?= htmlspecialchars($emp['nom'] . ' ' . $emp['prenom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php else: ?>
                    <input type="hidden" name="employe_id" value="<?= $employe_id_connecte ?>">
                <?php endif; ?>

                <div class="col-md-<?= ($role === 'cuisinier') ? '12' : '6' ?>">
                    <label class="form-label">Action</label>
                    <select name="action" class="form-select" required>
                        <option value="Arrivée">Arrivée</option>
                        <option value="Départ">Départ</option>
                    </select>
                </div>

                <div class="col-md-12 d-flex justify-content-center">
                    <button type="submit" class="btn btn-warning px-4">🔁 Enregistrer</button>
                </div>
            </form>
        </div>

        <?php if (!empty($pointages)): ?>
        <div class="card bg-dark text-white shadow-lg mt-4">
            <div class="card-header bg-info text-center fw-bold fs-5">
                Derniers pointages enregistrés
            </div>
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead class="text-uppercase text-center table-info">
                        <tr>
                            <th>👤 Employé</th>
                            <th>📅 Date</th>
                            <th>🕘 Arrivée</th>
                            <th>🕕 Départ</th>
                            <th>⏱️ Durée</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        <?php foreach ($pointages as $row): ?>
                            <tr class="table-row-hover">
                                <td><?= htmlspecialchars($row['nom'] . ' ' . $row['prenom']) ?></td>
                                <td><?= $row['date'] ?></td>
                                <td><?= $row['heure_arrivee'] ?? '-' ?></td>
                                <td><?= $row['heure_depart'] ?? '-' ?></td>
                                <td><?= $row['total_heures'] ?? '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php else: ?>
            <div class="alert alert-warning text-center">Aucun pointage enregistré.</div>
        <?php endif; ?>

        <div class="text-center mt-3">
            <a href="dashboard.php" class="btn btn-outline-light">🏠 Retour au Dashboard</a>
        </div>
    </div>
</body>
</html>
