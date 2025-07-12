<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['user'];
$success = $error = "";

// 🔍 Récupérer infos actuelles
$stmt = $pdo->prepare("
    SELECT u.email, u.mot_de_passe, u.employe_id, e.nom, e.prenom, e.photo 
    FROM utilisateurs u 
    JOIN employes e ON u.employe_id = e.id 
    WHERE u.email = ?
");
$stmt->execute([$email]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);
$mot_de_passe_actuel = $userData['mot_de_passe'] ?? '';
$employe_id = $userData['employe_id'];

// 📸 Suppression de la photo
if (isset($_POST['delete_photo'])) {
    if (!empty($userData['photo']) && file_exists("uploads/" . $userData['photo'])) {
        unlink("uploads/" . $userData['photo']);
    }
    $stmt = $pdo->prepare("UPDATE employes SET photo = NULL WHERE id = ?");
    $stmt->execute([$employe_id]);
    $userData['photo'] = null;
    $success = "✅ Photo supprimée.";
}

// 📸 Mise à jour de la photo
if (isset($_POST['update_photo']) && isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $targetDir = "uploads/";
    $photoName = uniqid() . '_' . basename($_FILES["photo"]["name"]);
    $targetFile = $targetDir . $photoName;

    if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
        $stmt = $pdo->prepare("UPDATE employes SET photo = ? WHERE id = ?");
        $stmt->execute([$photoName, $employe_id]);
        $userData['photo'] = $photoName;
        $success = "✅ Photo mise à jour.";
    } else {
        $error = "❌ Erreur lors de l'envoi de la photo.";
    }
}

// 🔧 Modifier nom, prénom, email
if (isset($_POST['update_info'])) {
    $new_nom = $_POST['nom'] ?? '';
    $new_prenom = $_POST['prenom'] ?? '';
    $new_email = $_POST['email'] ?? '';
    $current_password = $_POST['current_password_info'] ?? '';

    if ($new_nom && $new_prenom && $new_email && $current_password) {
        if (password_verify($current_password, $mot_de_passe_actuel)) {
            $update = $pdo->prepare("
                UPDATE employes e 
                JOIN utilisateurs u ON e.id = u.employe_id 
                SET e.nom = ?, e.prenom = ?, e.email = ?, u.email = ?
                WHERE u.email = ?
            ");
            $update->execute([$new_nom, $new_prenom, $new_email, $new_email, $email]);

            $_SESSION['user'] = $new_email;
            $email = $new_email;
            $success = "✅ Informations mises à jour.";
        } else {
            $error = "❌ Mot de passe incorrect.";
        }
    } else {
        $error = "❌ Tous les champs sont requis.";
    }
}

// 🔐 Modifier mot de passe
if (isset($_POST['update_password'])) {
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($old_password && $new_password && $confirm_password) {
        if (password_verify($old_password, $mot_de_passe_actuel)) {
            if ($new_password === $confirm_password) {
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE email = ?");
                $stmt->execute([$hashed, $email]);
                $success = "✅ Mot de passe mis à jour.";
            } else {
                $error = "❌ Les nouveaux mots de passe ne correspondent pas.";
            }
        } else {
            $error = "❌ Ancien mot de passe incorrect.";
        }
    } else {
        $error = "❌ Remplissez tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">
    <div class="container mt-5">

        <h2 class="mb-4"><i class="fa-solid fa-user"></i> Mon Profil</h2>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <!-- ✅ Affichage photo -->
        <?php if (!empty($userData['photo']) && file_exists("uploads/" . $userData['photo'])): ?>
            <div class="text-center mb-3">
                <img src="uploads/<?= htmlspecialchars($userData['photo']) ?>" width="100" height="100" class="rounded-circle shadow">
            </div>
        <?php endif; ?>

        <!-- 📸 Formulaire mise à jour/suppression -->
        <form method="POST" enctype="multipart/form-data" class="text-center mb-4 d-flex justify-content-center gap-3">
            <input type="hidden" name="update_photo" value="1">
            <input type="file" name="photo" accept="image/*" class="form-control w-50" required>
            <button type="submit" class="btn btn-warning">📸 Mettre à jour la photo</button>
        </form>

        <?php if (!empty($userData['photo'])): ?>
            <form method="POST" class="text-center mb-4">
                <input type="hidden" name="delete_photo" value="1">
                <button type="submit" class="btn btn-danger">🗑️ Supprimer la photo</button>
            </form>
        <?php endif; ?>

        <div class="row">
            <!-- 👤 Modifier infos -->
            <div class="col-md-6">
                <h4>Modifier mes informations</h4>
                <form method="POST">
                    <input type="hidden" name="update_info" value="1">
                    <div class="mb-3">
                        <label>Nom</label>
                        <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($userData['nom']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Prénom</label>
                        <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($userData['prenom']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($userData['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Mot de passe actuel</label>
                        <input type="password" name="current_password_info" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-warning">Mettre à jour</button>
                </form>
                 
            </div>
            <!-- 🔐 Changer mot de passe -->
            <div class="col-md-6">
                <h4>Changer mon mot de passe</h4>
                <form method="POST">
                    <input type="hidden" name="update_password" value="1">
                    <div class="mb-3">
                        <label>Ancien mot de passe</label>
                        <input type="password" name="old_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Nouveau mot de passe</label>
                        <input type="password" name="new_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Confirmer le mot de passe</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-light">Changer le mot de passe</button>
                </form>
            </div>
        </div>
    </div>
    <div class="text-center mt-4">
    <a href="dashboard.php" class="btn btn-outline-light">🏠 Retour au Dashboard</a>
</div>
</body>
</html>
