<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';

// Connexion BDD
try {
    $pdo = new PDO('mysql:host=fastfoqsmashmade.mysql.db;dbname=fastfoqsmashmade;charset=utf8', 'fastfoqsmashmade', 'rX6Lk7f8qytRoQXKHEbCki33k');
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// MàJ statut "prête"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commande_id']) && isset($_POST['new_statut']) && $_POST['new_statut'] === 'Prête') {
    $stmt = $pdo->prepare("UPDATE commandes SET statut = 'Prête' WHERE id = ?");
    $stmt->execute([$_POST['commande_id']]);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Fonctions
function getDetailsCommande($pdo, $id_commande) {
    $stmt = $pdo->prepare("SELECT * FROM details_commande WHERE commande_id = ?");
    $stmt->execute([$id_commande]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function afficher_date_simplifiee($date_commande) {
    $dt = new DateTime($date_commande);
    $maintenant = new DateTime();
    if ($dt->format('Y-m-d') === $maintenant->format('Y-m-d')) {
        return $dt->format('H:i');
    } elseif ($dt->format('Y-m-d') === $maintenant->modify('-1 day')->format('Y-m-d')) {
        return 'Hier ' . $dt->format('H:i');
    } else {
        return $dt->format('d-m H:i');
    }
}

// Commandes "En cuisine" sur 24h
$commandes = $pdo->prepare(
    "SELECT * FROM commandes 
     WHERE statut = 'En cuisine'
       AND date_commande >= (NOW() - INTERVAL 24 HOUR)
     ORDER BY date_commande ASC"
);
$commandes->execute();
$commandes = $commandes->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="10">
    <title>Commandes en cours (cuisine)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f0f2f5; }
        .order-card { transition: all 0.3s ease; }
        .status-indicator { width: 12px; height: 12px; border-radius: 50%; background: #fbbf24; }
        .order-price { font-size: 1rem; font-weight: 600; color: #10b981; }
        .details-list li { margin-bottom: 0.3rem; }
    </style>
</head>
<body class="min-h-screen">
    <header class="bg-blue-800 text-white py-4 px-6 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl md:text-3xl font-bold flex items-center">
                Commandes en cours (cuisine)
            </h1>
            <div class="text-sm md:text-base" id="current-time"></div>
        </div>
    </header>
    <main class="container mx-auto py-6 px-4">
        <?php if (count($commandes) === 0): ?>
            <div class="mt-10 text-center p-8 bg-white rounded-lg shadow-md">
                <h2 class="mt-4 text-xl font-semibold text-gray-700">Aucune commande en cuisine</h2>
                <p class="mt-2 text-gray-500">Toutes les commandes ont été traitées</p>
            </div>
        <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($commandes as $commande): ?>
            <?php $details = getDetailsCommande($pdo, $commande['id']); ?>
            <div class="order-card bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gray-100 p-4 flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="status-indicator mr-2"></div>
                        <h2 class="text-xl font-bold">CMD&nbsp;#<?= htmlspecialchars($commande['id']) ?></h2>
                    </div>
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="elapsed-time font-medium"><?= afficher_date_simplifiee($commande['date_commande']) ?></span>
                    </div>
                </div>
                <div class="p-4">
                    <?php foreach ($details as $item): ?>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <div class="flex items-start">
                                <span class="font-bold text-lg mr-2"><?= intval($item['quantite']) ?>x</span>
                                <div>
                                    <p class="font-medium text-lg"><?= htmlspecialchars($item['produit']) ?></p>
                                    <?php if (!empty($item['sauce'])): ?>
                                        <p class="text-gray-600 italic"><?= htmlspecialchars($item['sauce']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="order-price mt-2 mb-3">
                        Total : <?= number_format($commande['montant_total'], 2, ',', ' ') ?> €
                    </div>
                    <form method="POST">
                        <input type="hidden" name="commande_id" value="<?= $commande['id'] ?>">
                        <input type="hidden" name="new_statut" value="Prête">
                        <button type="submit"
                            class="w-full py-3 bg-green-500 hover:bg-green-600 text-white font-bold rounded-md transition-colors flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Commande Prête
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </main>
    <script>
        function updateClock() {
            const now = new Date();
            document.getElementById('current-time').textContent = now.toLocaleTimeString('fr-FR');
        }
        updateClock();
        setInterval(updateClock, 1000);
    </script>
</body>
</html>
