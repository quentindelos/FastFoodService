<?php
session_start();

$pdo = new PDO('mysql:host=fastfoqsmashmade.mysql.db;dbname=fastfoqsmashmade;charset=utf8', 'fastfoqsmashmade', 'rX6Lk7f8qytRoQXKHEbCki33k');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_SESSION['last_commande_id'])) {
    $id = $_SESSION['last_commande_id'];


    // ✅ Enregistre le paiement si tu veux garder une trace
    $stmt = $pdo->prepare("INSERT INTO paiements (id_commande, montant_paye, statut_paiement, transaction_id) VALUES (?, ?, 'payé', ?)");
    $stmt->execute([$id, 0, uniqid("txn_")]);

    unset($_SESSION['last_commande_id']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paiement Réussi</title>
    <link rel="stylesheet" href="css/success.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="success-container">
        <div class="success-icon">✔</div>
        <h1>Merci pour votre commande !</h1>
        <p>Votre paiement a été effectué avec succès.</p>
        <a href="index.php" class="btn-home">Retour à l'accueil</a>
    </div>
</body>
</html>
