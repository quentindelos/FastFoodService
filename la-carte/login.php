<?php
session_start();
require 'config.php'; // Connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT id_client, mot_de_passe FROM clients WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['mot_de_passe'])) {
        $_SESSION['id_client'] = $user['id_client'];
        header("Location: index.php");
        exit();
    } else {
        $error = "Email ou mot de passe incorrect";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../source/assets/css/login.css?v=1.1">
</head>

<body>
    <div class="login-container">
        <h2>Connexion</h2>

        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

        <form action="login.php" method="POST">
            <label for="email">Email :</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Mot de passe :</label>
            <input type="password" name="password" id="password" required>

            <!-- ✅ Lien déplacé ici, comme sur le modèle -->
            <div class="forgot-password-link">
                <a href="renew_password.php">Mot de passe oublié ?</a>
            </div>

            <button type="submit">Se connecter</button>
        </form>

        <div class="register-link">
            <p>Pas encore de compte ? <a href="register.php">Inscrivez-vous</a></p>
        </div>
    </div>
</body>

</html>
