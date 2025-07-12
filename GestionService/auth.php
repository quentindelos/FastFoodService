<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

// Fonction pour restreindre selon rôle
function autoriser($roles_permis = []) {
    if (!in_array($_SESSION['role'], $roles_permis)) {
        header("Location: dashboard.php");
        exit();
    }
}
?>
