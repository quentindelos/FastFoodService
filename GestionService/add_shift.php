<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employe_id = $_POST['employe_id'];
    $date = $_POST['date'];
    $heure_debut = $_POST['heure_debut'];
    $heure_fin = $_POST['heure_fin'];
    $commentaire = $_POST['commentaire'];

    $stmt = $pdo->prepare("INSERT INTO planning (employe_id, date, heure_debut, heure_fin, commentaire) VALUES (?, ?, ?, ?, ?)");
    $success = $stmt->execute([$employe_id, $date, $heure_debut, $heure_fin, $commentaire]);

    if ($success) {
        echo "✅ Shift ajouté avec succès";
    } else {
        echo "❌ Erreur lors de l'ajout du shift.";
    }
}
?>
