<?php
if (isset($_GET['json'])) {
    try {
        $pdo = new PDO('mysql:host=fastfoqsmashmade.mysql.db;dbname=fastfoqsmashmade;charset=utf8', 'fastfoqsmashmade', 'rX6Lk7f8qytRoQXKHEbCki33k');
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]); exit;
    }
    function getCmdNumbers($pdo, $statut) {
        $stmt = $pdo->prepare(
            "SELECT nom_commande FROM commandes
             WHERE statut = ? AND date_commande >= (NOW() - INTERVAL 24 HOUR)
             ORDER BY date_commande ASC"
        );
        $stmt->execute([$statut]);
        // On supprime les 'CMD' éventuels au début, pour l’annonce vocale
        return array_map(function($row) {
            $num = $row['nom_commande'] ?? '';
            // Si vide ou non trouvé, on renvoie quand même
            return preg_replace('/^CMD[-#\s]*/i', '', $num);
        }, $stmt->fetchAll(PDO::FETCH_ASSOC));
    }
    $en_attente = getCmdNumbers($pdo, 'En attente');
    $en_cuisine = getCmdNumbers($pdo, 'En cuisine');
    $prete = getCmdNumbers($pdo, 'Prête');
    echo json_encode([
        'en_attente' => $en_attente,
        'en_cuisine' => $en_cuisine,
        'prete' => $prete,
    ]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Suivi des Commandes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; background: #f0f2f5;}
        .order-card { transition: all 0.3s ease; font-size: 2.2rem;}
        .order-pending { background-color: #f59e0b;}
        .order-processing { background-color: #3b82f6;}
        .order-ready { background-color: #10b981; animation: pulse 2s infinite;}
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);}
            70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);}
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);}
        }
        .order-new { animation: fadeIn 0.5s ease-out; }
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(-10px);}
            100% { opacity: 1; transform: translateY(0);}
        }
        .status-column { display: flex; flex-direction: column; height: 100vh;}
        .orders-container { flex: 1; overflow-y: auto; padding: 1.7rem;}
        /* Pour écran large, rend les colonnes bien visibles */
        @media (min-width: 1000px) {
            .status-column { min-width: 33vw;}
        }
    </style>
</head>
<body class="min-h-screen">
    <button id="activate-speech"
        style="position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;background:#1117c2;color:white;font-size:2.5rem;display:flex;align-items:center;justify-content:center;">
        Cliquez ici pour activer les annonces vocales
    </button>
    <div class="flex flex-col md:flex-row h-screen">
        <!-- EN ATTENTE -->
        <div class="flex-1 status-column">
            <div class="bg-amber-500 text-white p-4 sticky top-0 rounded-t-lg">
                <h2 class="text-xl font-bold text-center">EN ATTENTE DE PAIEMENT</h2>
            </div>
            <div class="orders-container bg-white">
                <div id="pending-orders" class="grid grid-cols-2 gap-6"></div>
                <div id="no-pending-orders" class="hidden text-center py-10 text-gray-500">Aucune commande en attente</div>
            </div>
        </div>
        <div class="w-2 bg-gray-200 hidden md:block"></div>
        <!-- EN PRÉPARATION -->
        <div class="flex-1 status-column">
            <div class="bg-blue-500 text-white p-4 sticky top-0 rounded-t-lg">
                <h2 class="text-xl font-bold text-center">EN PRÉPARATION</h2>
            </div>
            <div class="orders-container bg-white">
                <div id="processing-orders" class="grid grid-cols-2 gap-6"></div>
                <div id="no-processing-orders" class="hidden text-center py-10 text-gray-500">Aucune commande en préparation</div>
            </div>
        </div>
        <div class="w-2 bg-gray-200 hidden md:block"></div>
        <!-- PRÊTES -->
        <div class="flex-1 status-column">
            <div class="bg-green-500 text-white p-4 sticky top-0 rounded-t-lg">
                <h2 class="text-xl font-bold text-center">PRÊTES À EMPORTER</h2>
            </div>
            <div class="orders-container bg-white">
                <div id="ready-orders" class="grid grid-cols-2 gap-6"></div>
                <div id="no-ready-orders" class="hidden text-center py-10 text-gray-500">Aucune commande prête</div>
            </div>
        </div>
    </div>
    <script>
    let speechEnabled = false;

    function unlockSpeech() {
        speechEnabled = true;
        var utter = new SpeechSynthesisUtterance("");
        utter.lang = 'fr-FR';
        utter.rate = 1.3;
        window.speechSynthesis.speak(utter);
        document.getElementById('activate-speech').style.display = 'none';
    }
    document.getElementById('activate-speech').onclick = unlockSpeech;

    function getOldList(key) {
        try { return JSON.parse(localStorage.getItem(key)) || []; } catch(e) { return []; }
    }
    function setOldList(key, arr) {
        localStorage.setItem(key, JSON.stringify(arr));
    }

    // Pour la voix : n'annonce QUE le numéro sans le "CMD"
    function announce(type, num) {
        if (!speechEnabled) return;
        let msg = '';
        if(type === 'pending') msg = `Commande numéro ${num}, veuillez régler votre commande au comptoir.`;
        if(type === 'ready') msg = `La commande numéro ${num} est prête.`;
        const utter = new SpeechSynthesisUtterance(msg);
        utter.lang = 'fr-FR';
        utter.rate = 1.3;
        window.speechSynthesis.speak(utter);
    }

    function updateDisplay(data) {
        // Génère les cases avec "CMD"
        function showOrders(list, containerId, noId, cssClass, emptyMsg) {
            const ctn = document.getElementById(containerId);
            const msg = document.getElementById(noId);
            if (list.length === 0) {
                ctn.innerHTML = '';
                msg.classList.remove('hidden');
            } else {
                msg.classList.add('hidden');
                ctn.innerHTML = list.map(num =>
                    `<div class="order-card ${cssClass} text-white rounded-xl p-6 shadow-md flex items-center justify-center font-bold">
                        CMD ${num}
                    </div>`
                ).join('');
            }
        }
        showOrders(data.en_attente, 'pending-orders', 'no-pending-orders', 'order-pending', 'Aucune commande en attente');
        showOrders(data.en_cuisine, 'processing-orders', 'no-processing-orders', 'order-processing', 'Aucune commande en préparation');
        showOrders(data.prete, 'ready-orders', 'no-ready-orders', 'order-ready', 'Aucune commande prête');
    }

    function refresh() {
        fetch(window.location.pathname + '?json=1')
            .then(r => r.json())
            .then(data => {
                let oldPending = getOldList('oldPending');
                let oldReady = getOldList('oldReady');
                let newPending = data.en_attente.filter(num => !oldPending.includes(num));
                let newReady = data.prete.filter(num => !oldReady.includes(num));
                setOldList('oldPending', data.en_attente);
                setOldList('oldReady', data.prete);
                newPending.forEach(num => announce('pending', num));
                newReady.forEach(num => announce('ready', num));
                updateDisplay(data);
            });
    }

    refresh();
    setInterval(refresh, 1500);
    </script>
</body>
</html>
