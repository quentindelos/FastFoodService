<?php
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=rapport_heures.xls");

require 'db.php'; // Connexion PDO

echo "Nom\tPrénom\tHeures Travaillées\n";

$sql = "SELECT e.nom, e.prenom, SUM(p.total_heures) AS heures_totales 
        FROM pointage p 
        JOIN employes e ON p.employe_id = e.id 
        GROUP BY e.id";

try {
    $stmt = $pdo->query($sql);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['nom'] . "\t" . $row['prenom'] . "\t" . $row['heures_totales'] . " h\n";
    }
} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
?>
