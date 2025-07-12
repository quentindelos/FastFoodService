<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

if (!isset($_SESSION['user']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

require 'auth.php';
autoriser(['admin', 'manager']);

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $email = $_POST['email'] ?? '';
    $poste = $_POST['poste'] ?? '';
    $salaire = $_POST['salaire'] ?? 0;
    $date_embauche = $_POST['date_embauche'] ?? '';
    $photoName = null;

    // 📸 Upload de la photo
    if (!empty($_FILES['photo']['name'])) {
        $targetDir = "uploads/";
        $photoName = uniqid() . '_' . basename($_FILES["photo"]["name"]);
        $targetFile = $targetDir . $photoName;

        if (!move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
            $message = "❌ Erreur lors de l'envoi de la photo.";
        }
    }

    // ✅ Création employé + utilisateur
    if ($nom && $prenom && $email && $poste && $date_embauche) {
        $stmt = $pdo->prepare("INSERT INTO employes (nom, prenom, email, poste, salaire, date_embauche, photo) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $success = $stmt->execute([$nom, $prenom, $email, $poste, $salaire, $date_embauche, $photoName]);

        if ($success) {
            $last_id = $pdo->lastInsertId();
            $role = strtolower($poste);
            $mot_de_passe = password_hash("password123", PASSWORD_DEFAULT);

            $stmt_user = $pdo->prepare("INSERT INTO utilisateurs (employe_id, email, mot_de_passe, role) VALUES (?, ?, ?, ?)");
            $success_user = $stmt_user->execute([$last_id, $email, $mot_de_passe, $role]);

            if ($success_user) {
                $message = "✅ Employé ajouté avec succès.";
            } else {
                $message = "❌ Employé ajouté, mais création du compte utilisateur échouée.";
            }
        } else {
            $message = "❌ Erreur lors de l'ajout de l'employé.";
        }
    } else {
        $message = "❌ Tous les champs doivent être remplis.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Employé</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-dark text-white">
<div class="container mt-5">
    <h2 class="mb-4">👨‍🍳 Ajouter un Employé</h2>
    <?php if ($message): ?>
        <div class="alert alert-info text-center"><?= $message ?></div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-6">
            <label>Nom</label>
            <input type="text" name="nom" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label>Prénom</label>
            <input type="text" name="prenom" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label>Poste</label>
            <select name="poste" class="form-select" required>
                <option value="">-- Choisir --</option>
                <option value="Admin">Admin</option>
                <option value="Manager">Manager</option>
                <option value="Cuisinier">Cuisinier</option>
            </select>
        </div>
        <div class="col-md-6">
            <label>Salaire (€)</label>
            <input type="number" step="0.01" name="salaire" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label>Date d'embauche</label>
            <input type="date" name="date_embauche" class="form-control" required>
        </div>
        <div class="col-md-12">
            <label>Photo de profil</label>
            <input type="file" name="photo" class="form-control" accept="image/*">
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-success w-100">Ajouter</button>
        </div>
    </form>
</div>
</body>
</html>
