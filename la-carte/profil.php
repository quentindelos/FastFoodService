<?php
session_start();

if (!isset($_SESSION['id_client'])) {
    header('Location: login.php');
    exit();
}

require 'config.php';

$id_client = $_SESSION['id_client'];

$query = $pdo->prepare("SELECT nom, email, date_creation, points_fidelite FROM clients WHERE id_client = ?");
$query->execute([$id_client]);
$user = $query->fetch();

if (!$user) {
    header('Location: login.php');
    exit();
}

$nom = $user['nom'];
$email = $user['email'];
$date_creation = $user['date_creation'];
$points = $user['points_fidelite'];

$message = $_SESSION['message'] ?? null;
unset($_SESSION['message']);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Mon Profil</title>
    <link rel="stylesheet" href="css/profil.css">
</head>

<body>
    <div class="container">
        <h1>Mon Profil</h1>

        <?php if ($message): ?>
            <p style="color: green; text-align: center;"><strong><?php echo htmlspecialchars($message); ?></strong></p>
        <?php endif; ?>

        <div class="profile-info">
            <h2>Informations personnelles</h2>
            <p><strong>Nom :</strong> <?php echo htmlspecialchars($nom); ?></p>
            <p><strong>Email :</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Date de création :</strong> <?php echo htmlspecialchars($date_creation); ?></p>
        </div>

        <div class="password-change">
            <h2>Modifier mon mot de passe</h2>
            <form action="renew_password.php" method="GET">
                <button type="submit">Modifier mon mot de passe</button>
            </form>
        </div>

        <div class="loyalty-system">
            <h2>Mon compte fidélité</h2>
            <p><strong>Points de fidélité :</strong> <span
                    id="loyalty-points"><?php echo htmlspecialchars($points); ?></span></p>
            <form method="POST" action="activer_reduction.php" id="reduction-form">
                <button id="redeem-points" type="submit">Utiliser mes points</button>
            </form>

            <script>
                document.getElementById('reduction-form').addEventListener('submit', function (e) {
                    const confirmation = confirm("Confirmer l'utilisation de 100 points pour obtenir une réduction de 10% sur votre prochaine commande ?");
                    if (!confirmation) {
                        e.preventDefault();
                    }
                });
            </script>
        </div>
    </div>

    <div class="back-btn-container">
        <button id="back-btn" onclick="window.location.href='index.php'">Retour à l'accueil</button>
    </div>
</body>

</html>