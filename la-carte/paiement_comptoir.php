<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Dépendances
require '../source/model/phpmailer/PHPMailer-master/src/PHPMailer.php';
require '../source/model/phpmailer/PHPMailer-master/src/SMTP.php';
require '../source/model/phpmailer/PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Connexion à la BDD
$pdo = new PDO('mysql:host=fastfoqsmashmade.mysql.db;dbname=fastfoqsmashmade;charset=utf8', 'fastfoqsmashmade', 'rX6Lk7f8qytRoQXKHEbCki33k');

// Récupération des données
$email = $_POST['email'] ?? '';
$telephone = $_POST['telephone'] ?? '';
$cartJson = $_POST['cart'] ?? '';

if (!$email || !$cartJson) {
    die("Données manquantes.");
}

$cart = json_decode($cartJson, true);
if (!is_array($cart)) {
    die("Format de panier invalide.");
}

// Calcul du total + génération détails
$total = 0;
$commandeDetails = '';
foreach ($cart as $item) {
    $lineTotal = $item['price'] * $item['quantity'];
    $total += $lineTotal;

    $commandeDetails .= htmlspecialchars($item['name']) . ' x' . $item['quantity'] . ' = ' . number_format($lineTotal, 2, ',', ' ') . "€<br>";
}

// Générer le numéro de commande du jour
$dateDuJour = date('Y-m-d');
$stmt = $pdo->prepare("SELECT COUNT(*) FROM commandes WHERE DATE(date_commande) = ?");
$stmt->execute([$dateDuJour]);
$countToday = $stmt->fetchColumn();
$numeroCommande = $countToday + 1;

$nomCommande = (string) $numeroCommande;

// Enregistrement dans la base de données
$stmt = $pdo->prepare("INSERT INTO commandes (montant_total, statut, mail_clientFromStripe, tel_clientFromStripe, nom_commande, mode_paiement) 
                       VALUES (?, ?, ?, ?, ?, ?)");
$stmt->execute([
    $total,
    'En attente',
    $email,
    $telephone,
    $nomCommande,
    'comptoir'
]);

$commandeId = $pdo->lastInsertId();

// Insertion des produits dans la table details_commandes
foreach ($cart as $item) {
    $produit = $item['name'] ?? '';
    $sauce = $item['sauce'] ?? null; // null si absent
    $quantite = $item['quantity'] ?? 1;
    $prix_unitaire = $item['price'] ?? 0;
    $prix_total = $quantite * $prix_unitaire;

$stmt = $pdo->prepare("INSERT INTO details_commande (commande_id, produit, sauce, quantite, prix_unitaire, prix_total) 
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $commandeId,
        $produit,
        $sauce,
        $quantite,
        $prix_unitaire,
        $prix_total
    ]);
}


// Récupération du nom de la commande (numéro du jour)
$stmt = $pdo->prepare("SELECT nom_commande FROM commandes WHERE id = ?");
$stmt->execute([$commandeId]);
$nomCommande = $stmt->fetchColumn();

// Envoi de l'email de confirmation
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'smashmade.inscriptions@gmail.com';
    $mail->Password = 'aiqa magv wobj rxgi'; // ✅ garde ce mot de passe secret
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('smashmade.inscriptions@gmail.com', 'SmashMade');
    $mail->addAddress($email);

    $mail->CharSet = 'UTF-8';
    $mail->isHTML(true);
    $mail->Subject = "Votre commande SmashMade (à régler au comptoir)";
    $mail->Body = "
Bonjour,<br><br>
Merci pour votre commande chez SmashMade ! 🎉<br><br>
<strong>Numéro de commande :</strong> $nomCommande<br><br>
<strong>Détails de votre commande :</strong><br>
$commandeDetails
<br>
<strong>Total à payer :</strong> " . number_format($total, 2, ',', ' ') . " €<br><br>
Veuillez régler votre commande au comptoir dès que possible.<br><br>
L'équipe SmashMade 🍔
";



    $mail->send();
} catch (Exception $e) {
    error_log("Erreur d'envoi email : " . $mail->ErrorInfo);
}
$_SESSION['nom_commande'] = $nomCommande;


// Redirection vers une page de confirmation
header("Location: confirmation.php");
exit;
?>