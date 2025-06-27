<?php
// Page de présentation du restaurant
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmashMade</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href=".\source\assets\css\index.css">
</head>

<body>
    <header>
        <a href="#menu">Menu</a>
        <a href="#contact">Contact</a>
        <a href=".\la-carte\">Commander</a>
    </header>

    <!-- Section Hero -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1>Smash Made</h1>
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
                    <p>Depuis 2020, SmashMade vous accueille en plein cœur de Lille dans une ambiance moderne et
                        conviviale
                        pour vous faire redécouvrir le goût du vrai smash burger. Notre équipe, passionnée par la street
                        food
                        de qualité, prépare chaque jour des recettes généreuses avec des produits frais, locaux et
                        savoureux.</p>
                </div>
                <div class="col-md-6">
                    <img src="https://smashmade.fastfoodservice.fr/source/assets/img/vitrine.png" alt="Notre restaurant"
                        class="img-histoire">
                </div>
            </div>
        </div>
    </section>

<!-- Section Menu -->
<section id="menu" class="section bg-light">
    <div class="container">
        <h2 class="text-center mb-5">Notre Menu</h2>

        <div id="menuCarousel" class="carousel slide" data-bs-ride="false">
            <div class="carousel-inner rounded shadow">
                <div class="carousel-item active">
                    <img src="source/assets/img/logo.jpg" class="d-block w-100" alt="Entrées">
                </div>
                <div class="carousel-item">
                    <img src="source/assets/img/wphome.png" class="d-block w-100" alt="Plats">
                </div>
                <div class="carousel-item">
                    <img src="source/assets/img/wplogin.png" class="d-block w-100" alt="Desserts">
                </div>
            </div>

            <!-- Contrôles manuels -->
            <button class="carousel-control-prev" type="button" data-bs-target="#menuCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Précédent</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#menuCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Suivant</span>
            </button>
        </div>
    </div>
</section>



    <!-- Section Contact -->
    <section id="contact" class="section contact-info">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h3>Horaires</h3>
                    <p>Lundi - Fermé</p>
                    <p>Mardi - Mercredi - Jeudi : 19h00 - 23h00</p>
                    <p>Vendredi - Samedi : 19h00 - 23h45</p>
                    <p>Dimanche: 19h00 - 23h00</p>

                </div>
                <div class="col-md-4">
                    <h3>Contact</h3>
                    <p>📍 Adresse : 104 rue des sarrazins, Lille</p>
                    <p>📱 Téléphone : 06 95 67 47 28</p>
                </div>
                <div class="col-md-4">
                    <h3>Suivez-nous</h3>
                    <div class="social-links">
                        <a href="https://www.instagram.com/smashmade.lille/" target="_blank"><i
                                class='bx bxl-instagram'></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>