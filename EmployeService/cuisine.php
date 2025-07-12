<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';
use Ovh\Api;

// Connexion BDD
try {
    $pdo = new PDO('mysql:host=fastfoqsmashmade.mysql.db;dbname=fastfoqsmashmade;charset=utf8', 'fastfoqsmashmade', 'rX6Lk7f8qytRoQXKHEbCki33k');
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// ---- OVH SMS ----
$ovh = new Api(
    'e50da5206b6a2662',
    'b71175e94cc92c8d6ff38ed5e76a8f71',
    'ovh-eu',
    '5ab647ffc4d5d4006ff040d584342e7f'
);

function enleverAccents($texte) {
    return iconv('UTF-8', 'ASCII//TRANSLIT', $texte);
}
function envoyerSMS($numero, $message) {
    global $ovh;
    $serviceName = 'sms-dq32673-1';
    try {
        $message = enleverAccents($message); // Retire si tu veux garder les accents
        $ovh->post("/sms/$serviceName/jobs", [
            'receivers' => [$numero],
            'message' => $message,
            'priority' => 'high',
            'senderForResponse' => false,
            'noStopClause' => true,
            'sender' => 'SmashMade'
        ]);
    } catch (Exception $e) {
        error_log('Erreur envoi SMS : ' . $e->getMessage());
    }
}
function envoyerMail($email, $message) {
    $sujet = "Commande prête - SmashMade";
    $headers = "From: no-reply@smashmade.fr\r\n" .
               "Reply-To: no-reply@smashmade.fr\r\n" .
               "Content-Type: text/plain; charset=UTF-8\r\n";
    mail($email, $sujet, $message, $headers);
}
function getDetailsCommande($pdo, $id_commande) {
    $stmt = $pdo->prepare("SELECT * FROM details_commande WHERE commande_id = ?");
    $stmt->execute([$id_commande]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function est_en_retard($date_commande) {
    $commande_time = strtotime($date_commande);
    return (time() - $commande_time) > (15 * 60);
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
function temps_ecoule($date_commande) {
    $seconds = time() - strtotime($date_commande);
    $minutes = floor($seconds / 60);
    $rest_sec = $seconds % 60;
    return "{$minutes}m {$rest_sec}s";
}

// Passage automatique "Paiement validé" -> "En cuisine" sur les commandes des 24 dernières heures
$pdo->query("UPDATE commandes SET statut = 'En cuisine' WHERE statut = 'Paiement validé' AND date_commande >= (NOW() - INTERVAL 24 HOUR)");

// POST - Mise à jour statuts manuelle + NOTIF
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commande_id'])) {
    $commande_id = $_POST['commande_id'];
    $wasReady = false;
    if (isset($_POST['new_statut'])) {
        $new_statut = $_POST['new_statut'];
        if ($new_statut === "Prête") $wasReady = true;
        $stmt = $pdo->prepare("UPDATE commandes SET statut = ? WHERE id = ?");
        $stmt->execute([$new_statut, $commande_id]);
    }
    if ($wasReady) {
        $stmt = $pdo->prepare("SELECT * FROM commandes WHERE id = ?");
        $stmt->execute([$commande_id]);
        $commande = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($commande) {
            $numeroCommande = $commande['nom_commande'] ? $commande['nom_commande'] : $commande['id'];
            //$message = "Bonjour ! Votre commande n°{$numeroCommande} est prête à être récupérée. Merci pour votre commande et bon appétit !";//2 crédit
            $message = "Bonjour ! Votre commande n°{$numeroCommande} est prête. Bon appétit !"; //1 crédit
            if (!empty($commande['tel_clientFromStripe'])) {
                envoyerSMS($commande['tel_clientFromStripe'], $message);
            } elseif (!empty($commande['mail_clientFromStripe'])) {
                envoyerMail($commande['mail_clientFromStripe'], $message);
            }
        }
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Récupération des commandes à afficher (EN CUISINE)
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
    <title>Commandes en cours (cuisine) – SmashMade</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background: #f3f5f9; }
        .status-dot { width: 15px; height: 15px; border-radius: 9999px; display:inline-block; background:#fbbf24; margin-right:10px; vertical-align:middle; }
        .badge-retard { background: #fee2e2; color: #b91c1c; font-weight: bold; padding: 2px 8px; border-radius: 10px; margin-left: 8px; font-size:1rem;}
    </style>
</head>
<body class="min-h-screen">
<header class="bg-blue-800 text-white py-4 px-6 shadow-md">
    <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-2xl md:text-3xl font-bold flex items-center">
            Commandes en cours <span class="text-base font-normal text-blue-100 ml-2">(cuisine)</span>
        </h1>
        <div class="flex items-center gap-6">
            <span id="current-time" class="text-lg"></span>
        </div>
    </div>
</header>

<main class="container mx-auto py-6 px-3">
    <?php if (empty($commandes)): ?>
        <div class="mt-10 text-center p-8 bg-white rounded-lg shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <h2 class="mt-4 text-xl font-semibold text-gray-700">Aucune commande en cuisine</h2>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6">
            <?php foreach ($commandes as $commande): 
                $details = getDetailsCommande($pdo, $commande['id']);
                $num_cmd = $commande['nom_commande'] ? $commande['nom_commande'] : $commande['id'];
                ?>
                <div class="bg-white rounded-lg shadow-md p-4 flex flex-col">
                    <div class="flex justify-between items-center mb-2">
                        <div>
                            <span class="status-dot"></span>
                            <span class="text-xl font-bold"><?= htmlspecialchars($num_cmd) ?></span>
                            <?php if (est_en_retard($commande['date_commande'])): ?>
                                <span class="badge-retard">Retard&nbsp;!</span>
                            <?php endif; ?>
                        </div>
                        <div class="flex items-center text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span><?= temps_ecoule($commande['date_commande']) ?></span>
                        </div>
                    </div>
                    <!-- DETAILS PRODUITS -->
                    <div class="mb-3">
                        <?php foreach ($details as $item): ?>
                            <div class="py-1 flex flex-col border-b last:border-b-0">
                                <span class="font-bold"><?= intval($item['quantite']) ?>x <?= htmlspecialchars($item['produit']) ?></span>
                                <?php if(!empty($item['options']) || !empty($item['sauce'])): ?>
                                    <span class="text-gray-500 italic text-sm">
                                        <?php
                                            $arr = [];
                                            if (!empty($item['options'])) $arr[] = htmlspecialchars($item['options']);
                                            if (!empty($item['sauce'])) $arr[] = htmlspecialchars($item['sauce']);
                                            echo implode(', ', $arr);
                                        ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <form method="POST" class="mt-auto">
                        <input type="hidden" name="commande_id" value="<?= $commande['id'] ?>">
                        <input type="hidden" name="new_statut" value="Prête">
                        <button type="submit" class="w-full py-3 bg-green-500 hover:bg-green-600 text-white font-bold rounded-md transition-colors flex items-center justify-center mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Commande Prête
                        </button>
                    </form>
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
updateClock(); setInterval(updateClock, 1000);
</script>
</body>
</html>
