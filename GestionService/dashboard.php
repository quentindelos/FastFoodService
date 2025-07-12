<?php
session_start();

if (!isset($_SESSION['user']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - SmashMade</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <style>
        /* 🔽 Menu déroulant */
        .dashboard-heading {
    display: flex;
    align-items: center;
    gap: 20px;
    margin: 50px 0 20px;
    padding-left: 15px;
    animation: fadeInDown 0.8s ease;
}

.heading-icon {
    font-size: 42px;
    color: #ffc107; /* Couleur directe sans dégradé */
    line-height: 1;
    animation: fadeInDown 0.8s ease;
}


.main-title {
    font-size: 32px;
    font-weight: 700;
    color: #ffffff;
    margin: 0;
}

.subtitle {
    font-size: 16px;
    color: #dddddd;
    margin: 4px 0 0;
}

@keyframes fadeInDown {
    0% { opacity: 0; transform: translateY(-20px); }
    100% { opacity: 1; transform: translateY(0); }
}

@keyframes pulse {
    0% { transform: scale(1); }
    100% { transform: scale(1.08); }
}

        .dropdown {
            position: relative;
            display: inline-block;
            margin-right: 40px;
        }

        .dropdown-toggle {
            background: transparent;
            color: #f8f8f8;
            font-weight: bold;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .dropdown-toggle i {
            margin-right: 5px;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #111;
            min-width: 160px;
            border-radius: 8px;
            box-shadow: 0px 8px 16px rgba(0,0,0,0.3);
            z-index: 999;
        }

        .dropdown-content a {
            color: white;
            padding: 12px 16px;
            display: block;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .dropdown-content a:hover {
            background-color: #444;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        /* 🔥 Apparition fluide */
        .fade-in {
            opacity: 0;
            animation: fadeInUp 1s ease-out forwards;
        }

        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* 📱 Grille responsive et élargie */
        .card {
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 30px 30px; /* 🆕 Espace interne vertical augmenté */
    border-radius: 35px;
    backdrop-filter: blur(10px);
    background: rgba(0, 0, 0, 0.4);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 80px; /* 🆙 Plus d’espace entre les lignes */
    margin-top: 40px;
    margin-bottom: 20px;
}
.container {
    margin-bottom: 30px;
    margin-top: 30px;
}
.feature-title-box {
    padding-top: 30px;
    padding-bottom: 10px;
    animation: fadeIn 1.2s ease-in-out;
}

.gradient-text {
    background: linear-gradient(90deg, #ffb347, #ffcc33);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.heading-icon i {
    font-size: 36px;
    color: #ffc107;
}


    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="nav-content">
        <div class="logo">
            <i class="fa-solid fa-burger"></i>
            <span class="smashmade-text">SoulMade Manager</span>
        </div>

        <div class="dropdown">
            <button class="dropdown-toggle">
                <i class="fas fa-user"></i> Mon compte <i class="fas fa-caret-down"></i>
            </button>
            <div class="dropdown-content">
                <a href="profil.php"><i class="fas fa-id-card"></i> Modifier son profil</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
            </div>
        </div>
    </div>
</nav>

<!-- Contenu -->
<div class="container fade-in">
    <!-- ✅ Header stylisé -->
    
    <div class="dashboard-heading mb-5">
      <div class="heading-icon"><i class="fas fa-chart-line"></i></div>
        <div>
            <h2 class="main-title gradient-text">Accès rapide aux fonctionnalités</h2>
            <p class="subtitle">Votre tableau de bord centralisé, adapté à votre rôle</p>
        </div>
    </div>

    <!-- ✅ Blocs fonctionnels -->
    <div class="dashboard-grid">
        <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'manager'): ?>
            <div class="card">
                <h4>📅 Gestion du Planning</h4>
                <p>Créer et modifier les horaires des employés.</p>
                <a href="planning.php" class="btn">Accéder</a>
            </div>
        <?php endif; ?>

        <?php if (in_array($_SESSION['role'], ['cuisinier', 'manager', 'admin'])): ?>
            <div class="card">
                <h4>🗓️ Mon Planning</h4>
                <p>Consulter vos horaires de travail prévus.</p>
                <a href="mon_planning.php" class="btn">Accéder</a>
            </div>
        <?php endif; ?>

        <?php if (in_array($_SESSION['role'], ['admin', 'manager', 'cuisinier'])): ?>
            <div class="card">
                <h4>🕒 Pointage</h4>
                <p>Les employés peuvent pointer leur arrivée et départ.</p>
                <a href="pointage.php" class="btn">Accéder</a>
            </div>
        <?php endif; ?>

        <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'manager'): ?>
            <div class="card">
                <h4>👥 Gestion des Employés</h4>
                <p>Ajouter, modifier ou supprimer un employé.</p>
                <a href="employes.php" class="btn">Accéder</a>
            </div>
        <?php endif; ?>

        <?php if (in_array($_SESSION['role'], ['admin', 'manager', 'cuisinier'])): ?>
            <div class="card">
                <h4>📦 Gestion des Stocks</h4>
                <p>Suivi et gestion des stocks en temps réel.</p>
                <a href="gestion_stock.php" class="btn">Accéder</a>
            </div>
        <?php endif; ?>

        <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'manager'): ?>
            <div class="card">
                <h4>📈 Statistiques des Ventes</h4>
                <p>Visualiser les performances de vente.</p>
                <a href="stats.php" class="btn">Accéder</a>
            </div>

            <div class="card">
                <h4>🚨 Alertes Stock</h4>
                <p>Être informé des stocks critiques.</p>
                <a href="stocks_alertes.php" class="btn">Accéder</a>
            </div>
        <?php endif; ?>
    </div>
</div>
