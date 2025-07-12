<?php
require 'db.php'; // Connexion via PDO

if (isset($_GET['employe_id']) && is_numeric($_GET['employe_id'])) {
    $employe_id = intval($_GET['employe_id']);

    $stmt = $pdo->prepare("SELECT * FROM pointage WHERE employe_id = ? ORDER BY date DESC");
    $stmt->execute([$employe_id]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as $row) {
        echo "<tr>
                <td>{$row['date']}</td>
                <td>{$row['heure_arrivee']}</td>
                <td>{$row['heure_depart']}</td>
                <td>{$row['total_heures']} h</td>
              </tr>";
    }
}
?>
