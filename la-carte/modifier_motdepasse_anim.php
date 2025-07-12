<?php
require 'config.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $check = $pdo->prepare("SELECT * FROM clients WHERE reset_password_token = ?");
    $check->execute([$token]);

    if ($check->rowCount() === 1) {
        if (!empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
            $new_password = $_POST['new_password'];
            $confirm = $_POST['confirm_password'];

            if ($new_password !== $confirm) {
                $error = "Les mots de passe ne correspondent pas.";
            } else {
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $update = $pdo->prepare("UPDATE clients SET mot_de_passe = ?, reset_password_token = NULL WHERE reset_password_token = ?");
                $update->execute([$hashed, $token]);
                $success = "Mot de passe modifié avec succès. Vous pouvez maintenant vous connecter.";
            }
        }
    } else {
        $error = "Token invalide ou expiré.";
    }
} else {
    $error = "Aucun token fourni.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le mot de passe</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../source/assets/css/auth.css?v=1.1">
</head>
<body>
    <div class="register-container auth-long">
        <h2>Modifier votre mot de passe 🔐</h2>

        <?php if (isset($error)): ?>
            <div class="animated-msg error"><?= $error ?></div>
        <?php elseif (isset($success)): ?>
            <div class="animated-msg success"><?= $success ?></div>
            <div class="login-link">
                <a href="login.php">Se connecter</a>
            </div>
        <?php elseif (isset($token)): ?>
            <form method="post">
                <label for="new_password">Nouveau mot de passe :</label>
                <input type="password" name="new_password" id="new_password" required>
                <div id="passwordFeedback" class="live-feedback">Veuillez saisir un mot de passe conforme aux critères.</div>

                <ul class="password-criteria">
                    <li id="length">12 caractères minimum</li>
                    <li id="uppercase">1 lettre majuscule</li>
                    <li id="lowercase">1 lettre minuscule</li>
                    <li id="digit">1 chiffre</li>
                    <li id="special">1 caractère spécial</li>
                </ul>

                <label for="confirm_password">Confirmer le mot de passe :</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
                <div id="matchFeedback" class="live-feedback"></div>

                <button type="submit">Modifier le mot de passe</button>
            </form>
        <?php endif; ?>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const passwordField = document.getElementById("new_password");
        const confirmField = document.getElementById("confirm_password");
        const feedback = document.getElementById("passwordFeedback");
        const matchFeedback = document.getElementById("matchFeedback");

        passwordField.addEventListener("input", function () {
            const value = passwordField.value;

            const lengthValid = value.length >= 12;
            const upperValid = /[A-Z]/.test(value);
            const lowerValid = /[a-z]/.test(value);
            const digitValid = /[0-9]/.test(value);
            const specialValid = /[^A-Za-z0-9]/.test(value);

            toggleClass("length", lengthValid);
            toggleClass("uppercase", upperValid);
            toggleClass("lowercase", lowerValid);
            toggleClass("digit", digitValid);
            toggleClass("special", specialValid);

            const allValid = lengthValid && upperValid && lowerValid && digitValid && specialValid;
            feedback.textContent = allValid ? "Mot de passe sécurisé ✅" : "Le mot de passe ne respecte pas tous les critères.";
            feedback.className = "live-feedback " + (allValid ? "valid" : "invalid");
        });

        confirmField.addEventListener("input", function () {
            const match = passwordField.value === confirmField.value;
            matchFeedback.textContent = match ? "Les mots de passe correspondent ✅" : "Les mots de passe ne correspondent pas.";
            matchFeedback.className = "live-feedback " + (match ? "valid" : "invalid");
        });

        function toggleClass(id, condition) {
            const el = document.getElementById(id);
            if (condition) {
                el.classList.add("valid");
            } else {
                el.classList.remove("valid");
            }
        }
    });
    </script>
</body>
</html>
