<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Employés</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: #2c3e50;
            padding: 20px;
            color: white;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .nav-link {
            color: white;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .nav-link:hover {
            background: #34495e;
        }
        .nav-link i {
            margin-right: 10px;
        }
        .card {
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h3 class="mb-4">FastFood Service Employés</h3>
        <nav class="nav flex-column">
            <a class="nav-link active" href="#dashboard">
                <i class='bx bxs-dashboard'></i> Tableau de bord
            </a>
            <a class="nav-link" href="#stock">
                <i class='bx bxs-box'></i> Gestion du Stock
            </a>
            <a class="nav-link" href="#ventes">
                <i class='bx bxs-chart'></i> Chiffre d'Affaires
            </a>
            <a class="nav-link" href="#employes">
                <i class='bx bxs-user-detail'></i> Employés
            </a>
        </nav>
    </div>

    <div class="main-content">
        <div class="container-fluid">
            <h2 class="mb-4">Tableau de Bord</h2>
            
            <!-- Cartes de statistiques -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Chiffre d'Affaires Journalier</h5>
                            <h3 class="card-text">€0.00</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Commandes du Jour</h5>
                            <h3 class="card-text">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Produits en Stock</h5>
                            <h3 class="card-text">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Employés Actifs</h5>
                            <h3 class="card-text">0</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Graphiques -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Chiffre d'Affaires Mensuel</h5>
                            <canvas id="caMensuel"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Produits les Plus Vendus</h5>
                            <canvas id="produitsVendus"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Graphique du chiffre d'affaires mensuel
        const ctxCA = document.getElementById('caMensuel').getContext('2d');
        new Chart(ctxCA, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
                datasets: [{
                    label: 'Chiffre d\'Affaires (€)',
                    data: [0, 0, 0, 0, 0, 0],
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Graphique des produits les plus vendus
        const ctxProduits = document.getElementById('produitsVendus').getContext('2d');
        new Chart(ctxProduits, {
            type: 'bar',
            data: {
                labels: ['Produit 1', 'Produit 2', 'Produit 3', 'Produit 4', 'Produit 5'],
                datasets: [{
                    label: 'Quantités Vendues',
                    data: [0, 0, 0, 0, 0],
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgb(75, 192, 192)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
