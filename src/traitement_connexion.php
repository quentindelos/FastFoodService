<?php
session_start();

// Vérification si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Vérification des champs vides
    if (empty($email) || empty($password)) {
        header("Location: index.php?error=empty_fields");
        exit();
    }

    // Connexion à la base de données
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=fastfoodservice", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        header("Location: index.php?error=db_error");
        exit();
    }

    // Préparation de la requête selon le rôle
    $query = "SELECT * FROM utilisateurs WHERE email = :email AND role = :role";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['email' => $email, 'role' => $role]);
    $user = $stmt->fetch();

    // Vérification des identifiants
    if ($user && password_verify($password, $user['password'])) {
        // Création de la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['nom'];
        $_SESSION['restaurant_id'] = $user['restaurant_id'];

        // Redirection selon le rôle
        if ($role === 'gerant') {
            header("Location: dashboard_gerant.php");
        } else {
            header("Location: espace_employe.php");
        }
        exit();
    } else {
        header("Location: index.php?error=invalid_credentials");
        exit();
    }
} else {
    // Si quelqu'un accède directement à ce fichier
    header("Location: index.php");
    exit();
}
?> 