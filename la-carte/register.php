<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../source/assets/css/auth.css?v=1.1">
</head>

<body>
    <div class="register-container"> <!-- structure identique à login-container -->
        <h2>Créer un compte</h2>

        <form action="register_traitement.php" method="post">
            <label for="name">Nom :</label>
            <input type="text" name="name" id="name" required>

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
