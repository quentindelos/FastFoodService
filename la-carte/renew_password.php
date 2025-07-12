<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mot de passe oublié</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../source/assets/css/auth.css?v=1.2"> <!-- forcer rechargement -->
</head>
<body>
    <div class="register-container">
        <h2>Modifier mon mot de passe</h2>

        <form action="renew_password_traitement.php" method="post">
            <label for="email">Email :</label>
            <input type="email" name="email" id="email" required>

            <button type="submit">Recevoir un mot de passe temporaire</button>
        </form>

        <div class="login-link">
            <p>Déjà un compte ? <a href="login.php">Connectez-vous</a></p>
        </div>
    </div>
</body>
</html>
