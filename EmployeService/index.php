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

// Passage automatique "Paiement validé" -> "En cuisine" sur les commandes des 24 dernières heures
$pdo->query("UPDATE commandes SET statut = 'En cuisine' WHERE statut = 'Paiement validé' AND date_commande >= (NOW() - INTERVAL 24 HOUR)");

// POST - Mise à jour statuts manuelle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commande_id'])) {
    if (isset($_POST['new_statut'])) {
        $new_statut = $_POST['new_statut'];
        $stmt = $pdo->prepare("UPDATE commandes SET statut = ? WHERE id = ?");
        $stmt->execute([$new_statut, $_POST['commande_id']]);
    }
    if (isset($_POST['receptionnee'])) {
        $stmt = $pdo->prepare("UPDATE commandes SET statut = 'Terminée' WHERE id = ?");
        $stmt->execute([$_POST['commande_id']]);
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Fonctions utilitaires
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
function getClientNameByEmail($pdo, $email) {
    if (!$email) return null;
    $stmt = $pdo->prepare("SELECT nom FROM clients WHERE email = ?");
    $stmt->execute([$email]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row['nom'] : null;
}

// Récupération de l'ID commande pour affichage détail (popup)
$details_commande_id = isset($_GET['details']) ? intval($_GET['details']) : null;
if ($details_commande_id) {
    $stmt = $pdo->prepare("SELECT id FROM commandes WHERE id = ?");
    $stmt->execute([$details_commande_id]);
    $cmd = $stmt->fetch(PDO::FETCH_ASSOC);
    $num = $cmd ? $cmd['id'] : '';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="10">
    <title>
        <?php if ($details_commande_id): ?>
            Commande #<?= htmlspecialchars($num) ?> – SmashMade
        <?php else: ?>
            Commandes – SmashMade
        <?php endif; ?>
    </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f0f2f5; }
        .order-card { transition: all 0.3s ease; }
        .status-indicator { width: 12px; height: 12px; border-radius: 50%; }
        .status-in-progress { background-color: #fbbf24; }
        .status-ready { background-color: #10b981; }
        .status-waiting { background-color: #d1d5db; }
        .badge-retard { background: #fee2e2; color: #b91c1c; font-weight: bold; padding: 2px 8px; border-radius: 10px; margin-left: 8px; }
        .order-price { font-size: 1rem; font-weight: 600; color: #10b981; }
        .details-list li { margin-bottom: 0.3rem; }
    </style>
</head>
<body class="min-h-screen">
    <header class="bg-blue-800 text-white py-4 px-6 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl md:text-3xl font-bold flex items-center">
                Commandes en cours
                <?php
                if ($details_commande_id) {
                    echo '<span class="ml-2 text-blue-200 font-bold text-2xl">CMD #'.htmlspecialchars($details_commande_id).'</span>';
                }
                ?>
            </h1>
            <div class="flex items-center gap-3">
                <div class="text-sm md:text-base">
                    <span id="current-time" class="font-medium">00:00:00</span>
                </div>
                <?php if(!$details_commande_id): ?>
                <div class="flex items-center gap-2">
                    <span class="text-sm md:text-base">Commandes actives:</span>
                    <span id="active-orders-count" class="bg-white text-blue-800 font-bold px-2 py-1 rounded-md">
                        <?php
                        $commandes_count = $pdo->query(
                            "SELECT COUNT(*) FROM commandes 
                             WHERE statut IN ('En attente', 'En cuisine', 'Prête')
                               AND date_commande >= (NOW() - INTERVAL 24 HOUR)"
                        )->fetchColumn();
                        echo $commandes_count;
                        ?>
                    </span>
                </div>
                <?php endif; ?>
                <a href="/EmployeService/cuisine"
                    class="ml-8 px-4 py-2 bg-white text-blue-800 rounded-md font-semibold shadow hover:bg-blue-100 transition"
                    target="_blank" rel="noopener">
                    Accès cuisine
                </a>
            </div>
        </div>
    </header>
    <main class="container mx-auto py-6 px-4">
    <?php
    // ---- PAGE DETAILS (POPUP) -----
    if ($details_commande_id) {
        $stmt = $pdo->prepare("SELECT * FROM commandes WHERE id = ?");
        $stmt->execute([$details_commande_id]);
        $commande = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($commande) {
            $details = getDetailsCommande($pdo, $commande['id']);
            $nom_client = null;
            if ($commande['mail_clientFromStripe']) {
                $nom_client = getClientNameByEmail($pdo, $commande['mail_clientFromStripe']);
            }
            echo '<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-md p-8">';
            echo '<div class="flex items-center justify-between mb-6">';
            echo '<h2 class="text-2xl font-bold">Détail de la commande <span class="text-blue-700">CMD #'.$commande['id'].'</span></h2>';
            echo '<a href="?" class="inline-block px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 font-semibold text-gray-700 transition">Fermer</a>';
            echo '</div>';
            echo '<div class="mb-4">';
            echo '<span class="text-gray-500">Statut :</span> <span class="font-semibold">';
            echo $commande['statut'] === 'En attente' ? 'En attente de paiement' : htmlspecialchars($commande['statut']);
            echo '</span><br>';
            echo '<span class="text-gray-500">Date :</span> <span class="font-semibold">'.afficher_date_simplifiee($commande['date_commande']).'</span><br>';
            echo '<span class="text-gray-500">Client :</span> <span class="font-semibold">';
            if ($nom_client) {
                echo htmlspecialchars($nom_client);
            } elseif ($commande['mail_clientFromStripe']) {
                echo htmlspecialchars($commande['mail_clientFromStripe']);
            } elseif ($commande['tel_clientFromStripe']) {
                echo htmlspecialchars($commande['tel_clientFromStripe']);
            } else {
                echo "Non renseigné";
            }
            echo '</span>';
            echo '</div>';
            echo '<div class="mb-4">';
            echo '<span class="text-gray-500 font-semibold">Articles :</span>';
            echo '<ul class="mt-2 details-list">';
            foreach ($details as $item) {
                $nom = htmlspecialchars($item['produit']);
                $qte = intval($item['quantite']);
                $sauce = !empty($item['sauce']) ? ' <span class="text-gray-500 italic">('.htmlspecialchars($item['sauce']).')</span>' : '';
                $prix = number_format($item['prix_total'], 2, ',', ' ');
                echo "<li><span class='font-bold'>{$qte}x</span> {$nom}{$sauce} <span class='text-green-700 font-semibold ml-2'>{$prix} €</span></li>";
            }
            echo '</ul>';
            echo '</div>';
            echo '<div class="order-price mb-3 text-lg">Total à payer : '.number_format($commande['montant_total'], 2, ',', ' ').' €</div>';
            if ($commande['statut'] === 'En attente') {
                // bouton manuel "Mettre en cuisine"
                echo '
                <form method="POST">
                    <input type="hidden" name="commande_id" value="'.$commande['id'].'">
                    <input type="hidden" name="new_statut" value="En cuisine">
                    <button type="submit"
                        class="w-full py-3 mt-2 bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-md transition-colors flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Mettre en cuisine
                    </button>
                </form>';
            }
            if ($commande['statut'] == 'En cuisine') {
                echo '
                <form method="POST">
                    <input type="hidden" name="commande_id" value="'.$commande['id'].'">
                    <input type="hidden" name="new_statut" value="Prête">
                    <button type="submit"
                        class="w-full py-3 mt-2 bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-md transition-colors flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Commande prête
                    </button>
                </form>';
            }
            if ($commande['statut'] == 'Prête') {
                echo '
                <form method="POST">
                    <input type="hidden" name="commande_id" value="'.$commande['id'].'">
                    <input type="hidden" name="receptionnee" value="1">
                    <button type="submit"
                        class="w-full py-3 mt-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-md transition-colors flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Réceptionnée
                    </button>
                </form>';
            }
            echo '</div>';
        } else {
            echo '<div class="text-center text-red-600 font-semibold text-lg">Commande introuvable !</div>';
        }
        echo '</main></body></html>';
        exit;
    }

    // ---- COMMANDES 24H ----
    $commandes = $pdo->prepare(
        "SELECT * FROM commandes 
         WHERE statut IN ('En attente', 'En cuisine', 'Prête') 
           AND date_commande >= (NOW() - INTERVAL 24 HOUR)
         ORDER BY date_commande ASC"
    );
    $commandes->execute();
    $commandes = $commandes->fetchAll(PDO::FETCH_ASSOC);

    $en_attente = array_filter($commandes, fn($c) => $c['statut'] === 'En attente');
    $en_cuisine = array_filter($commandes, fn($c) => $c['statut'] === 'En cuisine');
    $prete = array_filter($commandes, fn($c) => $c['statut'] === 'Prête');

    // -------- AFFICHAGE DES CARTES PAR STATUT --------
    function renderCommandesSection($titre, $commandes, $statut_couleur, $pdo = null) {
        if (count($commandes) === 0) return;
        echo '<h2 class="text-xl font-bold mb-4 mt-8">'.$titre.'</h2>';
        echo '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">';
        foreach ($commandes as $commande) {
            $details_link = '<a href="?details='.$commande['id'].'" class="text-blue-600 hover:underline">Voir détails</a>';
            $prix = number_format($commande['montant_total'], 2, ',', ' ');
            echo '
            <div class="order-card bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gray-100 p-4 flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="status-indicator '.$statut_couleur.' mr-2"></div>
                        <h2 class="text-xl font-bold">CMD&nbsp;#'.$commande['id'].'</h2>';
            if (in_array($commande['statut'], ['En attente', 'En cuisine']) && est_en_retard($commande['date_commande'])) {
                echo '<span class="badge-retard ml-2">Retard !</span>';
            }
            echo '
                    </div>
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span class="elapsed-time font-medium">'.afficher_date_simplifiee($commande['date_commande']).'</span>
                    </div>
                </div>
                <div class="p-4">
                    <div class="mb-2">'.$details_link.'</div>
                    <div class="order-price mb-3">Total : '.$prix.' €</div>';

            // --- Bouton "Mettre en cuisine" si EN ATTENTE
            if ($commande['statut'] === 'En attente') {
                echo '
                <form method="POST">
                    <input type="hidden" name="commande_id" value="'.$commande['id'].'">
                    <input type="hidden" name="new_statut" value="En cuisine">
                    <button type="submit"
                        class="w-full py-3 bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-md transition-colors flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Mettre en cuisine
                    </button>
                </form>';
            }

            // --- Bouton "Commande prête" si EN CUISINE
            if ($commande['statut'] === 'En cuisine') {
                echo '
                <form method="POST">
                    <input type="hidden" name="commande_id" value="'.$commande['id'].'">
                    <input type="hidden" name="new_statut" value="Prête">
                    <button type="submit"
                        class="w-full py-3 bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-md transition-colors flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Commande prête
                    </button>
                </form>';
            }

            // --- Bouton "Réceptionnée" si PRETE
            if ($commande['statut'] === 'Prête') {
                echo '
                <form method="POST">
                    <input type="hidden" name="commande_id" value="'.$commande['id'].'">
                    <input type="hidden" name="receptionnee" value="1">
                    <button type="submit"
                        class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-md transition-colors flex items-center justify-center mt-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Réceptionnée
                    </button>
                </form>';
            }

            echo '
                </div>
            </div>';
        }
        echo '</div>';
    }

    if (empty($en_attente) && empty($en_cuisine) && empty($prete)) {
        echo '<div class="mt-10 text-center p-8 bg-white rounded-lg shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <h2 class="mt-4 text-xl font-semibold text-gray-700">Aucune commande</h2>
                <p class="mt-2 text-gray-500">Aucune commande à afficher.</p>
            </div>';
    } else {
        renderCommandesSection('En attente de paiement', $en_attente, 'status-waiting', $pdo);
        renderCommandesSection('En cuisine', $en_cuisine, 'status-in-progress', $pdo);
        renderCommandesSection('Prêtes', $prete, 'status-ready', $pdo);
    }
    ?>
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
