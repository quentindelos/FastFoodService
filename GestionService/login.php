<?php
session_start();
include('db.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$error = ""; // Empêche l'affichage de message par défaut

if (isset($_SESSION['user']) && isset($_SESSION['role'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if (password_verify($password, $user['mot_de_passe'])) {
            $_SESSION['user'] = $email;
            $_SESSION['role'] = $user['role'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Mot de passe incorrect.";
        }
    } else {
        $error = "Email non trouvé.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - SoulMade</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Pacifico&display=swap');

        body {
            background: url('photo/img1.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Poppins', sans-serif;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background: rgba(0, 0, 0, 0.7);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-container h2 {
            font-family: 'Pacifico', cursive;
            font-size: 28px;
            margin-bottom: 20px;
            background: linear-gradient(45deg, #ffb347, #ffcc33);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .form-control {
            border-radius: 30px;
            padding: 12px 20px;
            margin-bottom: 15px;
            border: none;
        }

        .btn-login {
            border-radius: 30px;
            padding: 10px 25px;
            border: 1px solid white;
            background-color: transparent;
            color: white;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background-color: white;
            color: black;
        }

        .error-msg {
            color: #ff6b6b;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>SoulMade Manager</h2>

        <?php if (!empty($error))
            echo "<p class='error-msg'>$error</p>"; ?>

        <form method="POST" action="">
            <input type="email" name="email" placeholder="Email" class="form-control" required>
            <input type="password" name="password" placeholder="Mot de passe" class="form-control" required>
            <button type="submit" class="btn btn-login">Connexion</button>
        </form>
    </div>
</body>

</html>