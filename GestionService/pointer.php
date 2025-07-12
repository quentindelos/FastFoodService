<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $employe_id = $_POST['employe_id'];
    $action = $_POST['action'];
    $date = date("Y-m-d");
    $heure = date("H:i:s");

    if ($action === "arrivee") {
        // Vérifier si l'employé a déjà pointé aujourd'hui
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM pointage WHERE employe_id = ? AND date = ?");
        $stmt->execute([$employe_id, $date]);
        $deja_pointe = $stmt->fetchColumn();

        if ($deja_pointe == 0) {
            $stmt = $pdo->prepare("INSERT INTO pointage (employe_id, date, heure_arrivee) VALUES (?, ?, ?)");
            $stmt->execute([$employe_id, $date, $heure]);
            echo "✅ Pointage d'arrivée enregistré.";
        } else {
            echo "⚠️ Vous avez déjà pointé votre arrivée aujourd'hui.";
        }

    } elseif ($action === "depart") {
        $stmt = $pdo->prepare("
            UPDATE pointage 
            SET heure_depart = ?, total_heures = TIMEDIFF(?, heure_arrivee) 
            WHERE employe_id = ? AND date = ? AND heure_depart IS NULL
        ");
        $stmt->execute([$heure, $heure, $employe_id, $date]);
        echo "✅ Pointage de départ enregistré.";
    }
}
?>
