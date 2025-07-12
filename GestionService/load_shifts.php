<?php
require 'db.php';

try {
    $stmt = $pdo->query("SELECT p.*, e.nom, e.prenom FROM planning p JOIN employes e ON p.employe_id = e.id");
    $shifts = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $shifts[] = [
            "title" => $row["nom"] . " " . $row["prenom"] . " (" . $row["heure_debut"] . " - " . $row["heure_fin"] . ")",
            "start" => $row["date"] . "T" . $row["heure_debut"],
            "end"   => $row["date"] . "T" . $row["heure_fin"]
        ];
    }

    echo json_encode($shifts);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
