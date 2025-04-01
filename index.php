<?php
// Page de présentation du restaurant
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Le Petit Bistrot - Restaurant Traditionnel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --accent-color: #e74c3c;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
        }

        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            color: white;
            text-align: center;
        }

        .hero-content h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
        }

        .section {
            padding: 5rem 0;
        }

        .menu-item {
            margin-bottom: 2rem;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .menu-item:hover {
            transform: translateY(-5px);
        }

        .price {
            color: var(--accent-color);
            font-weight: bold;
        }

        .contact-info {
            background-color: var(--primary-color);
            color: white;
        }

        .social-links a {
            color: white;
            font-size: 1.5rem;
            margin: 0 1rem;
            transition: color 0.3s ease;
        }

        .social-links a:hover {
            color: var(--accent-color);
        }
    </style>
</head>
<body>
    <!-- Section Hero -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1>Le Petit Bistrot</h1>
                <p class="lead">Une cuisine traditionnelle française dans un cadre chaleureux</p>
                <a href="#menu" class="btn btn-light btn-lg mt-3">Découvrir notre menu</a>
            </div>
        </div>
    </section>

    <!-- Section À propos -->
    <section class="section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2>Notre Histoire</h2>
                    <p>Depuis 1995, Le Petit Bistrot vous accueille dans un cadre authentique pour vous faire découvrir les saveurs de la cuisine française traditionnelle. Notre chef, passionné par son métier, crée des plats savoureux avec des ingrédients frais et locaux.</p>
                </div>
                <div class="col-md-6">
                    <img src="https://images.unsplash.com/photo-1552566626-52f8b828add9?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80" alt="Notre restaurant" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </section>

    <!-- Section Menu -->
    <section id="menu" class="section bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Notre Menu</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="menu-item">
                        <h4>Entrées</h4>
                        <p>Soupe à l'Oignon Gratinée <span class="price">12€</span></p>
                        <p>Salade Niçoise <span class="price">10€</span></p>
                        <p>Pâté Maison <span class="price">9€</span></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="menu-item">
                        <h4>Plats</h4>
                        <p>Steak-Frites <span class="price">24€</span></p>
                        <p>Coq au Vin <span class="price">22€</span></p>
                        <p>Ratatouille <span class="price">18€</span></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="menu-item">
                        <h4>Desserts</h4>
                        <p>Crème Brûlée <span class="price">8€</span></p>
                        <p>Tarte Tatin <span class="price">9€</span></p>
                        <p>Profiteroles <span class="price">10€</span></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Contact -->
    <section class="section contact-info">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h3>Horaires</h3>
                    <p>Lundi - Vendredi: 11h30 - 23h00</p>
                    <p>Samedi - Dimanche: 11h00 - 23h30</p>
                </div>
                <div class="col-md-4">
                    <h3>Contact</h3>
                    <p>123 Rue de la Gastronomie</p>
                    <p>75001 Paris</p>
                    <p>Tél: 01 23 45 67 89</p>
                </div>
                <div class="col-md-4">
                    <h3>Suivez-nous</h3>
                    <div class="social-links">
                        <a href="#"><i class='bx bxl-facebook'></i></a>
                        <a href="#"><i class='bx bxl-instagram'></i></a>
                        <a href="#"><i class='bx bxl-tripadvisor'></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>