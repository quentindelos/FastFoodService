<?php
// Connexion à la base distante OVH
$host = 'fastfoqsmashmade.mysql.db';  // nom d’hôte OVH
$dbname = 'fastfoqsmashmade';         // nom de ta base
$user = 'fastfoqsmashmade';           // identifiant fourni par OVH
$pass = 'rX6Lk7f8qytRoQXKHEbCki33k';   // mot de passe

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erreur : ' . $e->getMessage());
}
?>
