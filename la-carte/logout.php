<?php
session_start(); // Démarrer la session

// Détruire toutes les variables de session
$_SESSION = array();

// Si vous souhaitez détruire complètement la session, supprimez également le cookie de session.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"], $params["secure"], $params["httponly"]
    );
}

// Détruire la session
session_destroy();

// Ajouter des entêtes HTTP pour éviter la mise en cache de la page après la déconnexion
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Ne pas rediriger vers login.php, rester sur index.php en mode déconnecté
header('Location: index.php');
exit();
?>
