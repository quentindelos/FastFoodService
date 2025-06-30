<?php
if (isset($_GET['json'])) {
    try {
        $pdo = new PDO('mysql:host=fastfoqsmashmade.mysql.db;dbname=fastfoqsmashmade;charset=utf8', 'fastfoqsmashmade', 'rX6Lk7f8qytRoQXKHEbCki33k');
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]); exit;
    }
    function getIds($pdo, $statut) {
        $stmt = $pdo->prepare(
            "SELECT id FROM commandes
             WHERE statut = ? AND date_commande >= (NOW() - INTERVAL 24 HOUR)
             ORDER BY date_commande ASC"
        );
        $stmt->execute([$statut]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    $en_attente = getIds($pdo, 'En attente');
    $en_cuisine = getIds($pdo, 'En cuisine');
    $prete = getIds($pdo, 'Prête');
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
        body { font-family: 'Inter', sans-serif; background: #f0f2f5;}
        .order-card { margin-bottom: 16px; }
        .order-pending { background-color: #f59e0b;}
        .order-processing { background-color: #3b82f6;}
        .order-ready { background-color: #10b981; animation: pulse 2s infinite;}
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);}
            70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);}
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);}
        }
    </style>
</head>
<body class="min-h-screen p-6">
    <button id="activate-speech"
        style="position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;background:#1117c2;color:white;font-size:2.5rem;display:flex;align-items:center;justify-content:center;">
        Cliquez ici pour activer les annonces vocales
    </button>
    <main class="container mx-auto" id="main-content">
        <section class="mb-12">
            <div class="bg-amber-500 text-white p-5 rounded-t-xl">
                <h2 class="text-2xl font-bold text-center">EN ATTENTE DE PAIEMENT</h2>
            </div>
            <div class="bg-white p-6 rounded-b-xl shadow-lg min-h-[150px]">
                <div id="pending-orders" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4"></div>
                <div id="no-pending-orders" class="text-center text-gray-500 py-10"></div>
            </div>
        </section>
        <section class="mb-12">
            <div class="bg-blue-500 text-white p-5 rounded-t-xl">
                <h2 class="text-2xl font-bold text-center">EN PRÉPARATION</h2>
            </div>
            <div class="bg-white p-6 rounded-b-xl shadow-lg min-h-[150px]">
                <div id="processing-orders" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4"></div>
                <div id="no-processing-orders" class="text-center text-gray-500 py-10"></div>
            </div>
        </section>
        <section class="mb-12">
            <div class="bg-green-500 text-white p-5 rounded-t-xl">
                <h2 class="text-2xl font-bold text-center">PRÊTES À EMPORTER</h2>
            </div>
            <div class="bg-white p-6 rounded-b-xl shadow-lg min-h-[150px]">
                <div id="ready-orders" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4"></div>
                <div id="no-ready-orders" class="text-center text-gray-500 py-10"></div>
            </div>
        </section>
    </main>
    <script>
    let speechEnabled = false;

    function unlockSpeech() {
        speechEnabled = true;
        var utter = new SpeechSynthesisUtterance("");
        utter.lang = 'fr-FR';
        utter.rate = 1.4;
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

    function announce(type, id) {
        if (!speechEnabled) return;
        let msg = '';
        if(type === 'pending') msg = `Commande numéro ${id} veuillez régler votre commande au comptoir.`;
        if(type === 'ready') msg = `La commande numéro ${id} est prête.`;
        const utter = new SpeechSynthesisUtterance(msg);
        utter.lang = 'fr-FR';
        utter.rate = 1.4;
        window.speechSynthesis.speak(utter);
    }

    function updateDisplay(data) {
        function showOrders(list, containerId, noId, cssClass, emptyMsg) {
            const ctn = document.getElementById(containerId);
            const msg = document.getElementById(noId);
            if (list.length === 0) {
                ctn.innerHTML = '';
                msg.textContent = emptyMsg;
            } else {
                msg.textContent = '';
                ctn.innerHTML = list.map(id =>
                    `<div class="order-card ${cssClass} text-white rounded-xl p-6 shadow-md">
                        <div class="text-3xl font-extrabold text-center tracking-wide">CMD #${id}</div>
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
                let newPending = data.en_attente.filter(id => !oldPending.includes(id));
                let newReady = data.prete.filter(id => !oldReady.includes(id));
                setOldList('oldPending', data.en_attente);
                setOldList('oldReady', data.prete);
                newPending.forEach(id => announce('pending', id));
                newReady.forEach(id => announce('ready', id));
                updateDisplay(data);
            });
    }

    refresh();
    setInterval(refresh, 1000);
    </script>
</body>
</html>
