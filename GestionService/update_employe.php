<?php
require 'db.php';

$employe = null;
$erreur = "";
$photoName = "";

// 🔄 Récupération employé existant
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM employes WHERE id = ?");
    $stmt->execute([$id]);
    $employe = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$employe) {
        $erreur = "Aucun employé trouvé avec cet ID.";
    }
}

// 📝 Traitement de mise à jour
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST['id']);
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $poste = trim($_POST['poste']);
    $salaire = floatval($_POST['salaire']);
    $photoName = null;

    // 📂 Crée le dossier uploads s'il n'existe pas
    if (!is_dir("uploads")) {
        mkdir("uploads", 0777, true);
    }

    // 📸 Traitement image
    if (!empty($_FILES['photo']['name'])) {
        $photoName = uniqid() . '_' . basename($_FILES["photo"]["name"]);
        $targetFile = "uploads/" . $photoName;

        if (!move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
            $erreur = "Erreur lors de l'envoi de la photo (code " . $_FILES['photo']['error'] . ").";
        }
    }

    // 📤 Requête SQL avec ou sans image
    if (empty($erreur)) {
        if ($photoName) {
            $stmt = $pdo->prepare("UPDATE employes SET nom = ?, prenom = ?, email = ?, poste = ?, salaire = ?, photo = ? WHERE id = ?");
            $ok = $stmt->execute([$nom, $prenom, $email, $poste, $salaire, $photoName, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE employes SET nom = ?, prenom = ?, email = ?, poste = ?, salaire = ? WHERE id = ?");
            $ok = $stmt->execute([$nom, $prenom, $email, $poste, $salaire, $id]);
        }

        if ($ok) {
            header("Location: employes.php?updated=1");
            exit();
        } else {
            $erreur = "Erreur lors de la mise à jour.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Employé</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-dark text-light">

<nav class="navbar navbar-dark bg-dark p-3">
    <a class="navbar-brand" href="employes.php">← Retour</a>
</nav>

<div class="container mt-4">
    <h2 class="text-center mb-4">✏️ Modifier un Employé</h2>

    <?php if ($erreur): ?>
        <div class="alert alert-danger text-center"><?= $erreur ?></div>
    <?php elseif ($employe): ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= htmlspecialchars($employe['id']) ?>">

            <?php if (!empty($employe['photo']) && file_exists('uploads/' . $employe['photo'])): ?>
                <div class="text-center mb-3">
                    <img src="uploads/<?= htmlspecialchars($employe['photo']) ?>" width="100" height="100" class="rounded-circle shadow" alt="Photo">
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <label>Photo de profil :</label>
                <input type="file" name="photo" class="form-control" accept="image/*">
            </div>

            <div class="mb-3">
                <label>Nom :</label>
                <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($employe['nom']) ?>" required>
            </div>
            <div class="mb-3">
                <label>Prénom :</label>
                <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($employe['prenom']) ?>" required>
            </div>
            <div class="mb-3">
                <label>Email :</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($employe['email']) ?>" required>
            </div>
            <div class="mb-3">
                <label>Poste :</label>
                <input type="text" name="poste" class="form-control" value="<?= htmlspecialchars($employe['poste']) ?>" required>
            </div>
            <div class="mb-3">
                <label>Salaire (€) :</label>
                <input type="number" step="0.01" name="salaire" class="form-control" value="<?= htmlspecialchars($employe['salaire']) ?>" required>
            </div>

            <button type="submit" class="btn btn-warning w-100">💾 Enregistrer les modifications</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
