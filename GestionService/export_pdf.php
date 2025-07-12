<?php
require('fpdf/fpdf.php');
require 'db.php'; // Connexion via PDO

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont("Arial", "B", 16);
$pdf->Cell(0, 10, "Rapport des Heures Travaillées", 0, 1, "C");
$pdf->Ln(10);

// En-tête du tableau
$pdf->SetFont("Arial", "B", 12);
$pdf->Cell(60, 10, "Nom", 1);
$pdf->Cell(60, 10, "Prénom", 1);
$pdf->Cell(60, 10, "Heures Travaillées", 1);
$pdf->Ln();

// Récupérer les heures travaillées
$sql = "SELECT e.nom, e.prenom, SUM(p.total_heures) AS heures_totales 
        FROM pointage p 
        JOIN employes e ON p.employe_id = e.id 
        GROUP BY e.id";

try {
    $stmt = $pdo->query($sql);
    $pdf->SetFont("Arial", "", 12);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $pdf->Cell(60, 10, $row['nom'], 1);
        $pdf->Cell(60, 10, $row['prenom'], 1);
        $pdf->Cell(60, 10, $row['heures_totales'] . " h", 1);
        $pdf->Ln();
    }

    $pdf->Output("D", "rapport_heures.pdf");

} catch (PDOException $e) {
    die("❌ Erreur : " . $e->getMessage());
}
?>
