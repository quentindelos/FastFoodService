<?php
require 'db.php'; // Connexion via PDO

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom_produit = $_POST['nom_produit'];
    $quantite_disponible = floatval($_POST['quantite_disponible']);
    $unite = $_POST['unite'];
    $seuil_minimum = floatval($_POST['seuil_minimum']);

    // Vérifier si le produit existe déjà
    $stmt = $pdo->prepare("SELECT id FROM stock WHERE nom_produit = ?");
    $stmt->execute([$nom_produit]);
    $existe = $stmt->fetch();

    if ($existe) {
        // Mise à jour du produit existant
        $stmt = $pdo->prepare("UPDATE stock SET quantite_disponible = ?, unite = ?, seuil_minimum = ?, date_mise_a_jour = NOW() WHERE nom_produit = ?");
        $stmt->execute([$quantite_disponible, $unite, $seuil_minimum, $nom_produit]);
    } else {
        // Insertion d’un nouveau produit
        $stmt = $pdo->prepare("INSERT INTO stock (nom_produit, quantite_disponible, unite, seuil_minimum) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nom_produit, $quantite_disponible, $unite, $seuil_minimum]);
    }

    header("Location: gestion_stock.php");
    exit();
}
