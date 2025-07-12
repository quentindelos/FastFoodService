<?php
// Page de présentation du restaurant
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SoulMade</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <link rel="stylesheet" href="./source/assets/css/index.css">
  <style>
    body {
      font-family: 'Outfit', sans-serif;
      overflow-x: hidden;
    }

    .loader {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background-color: #000;
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }

    .loader h1 {
      font-family: 'Pacifico', cursive;
      font-size: 3rem;
      color: #ffc107;
      animation: fadeOut 1.5s ease forwards;
    }

    @keyframes fadeOut {
      0% { opacity: 1; }
      100% { opacity: 0; visibility: hidden; }
    }

    .hero-section {
      background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('source/assets/img/wphome.png') center/cover no-repeat;
      color: white;
      padding: 100px 0 60px;
      text-align: center;
      position: relative;
      background-attachment: fixed;
    }

    .hero-section h1 {
      font-family: 'Pacifico', cursive;
      font-size: 3rem;
      animation: fadeInDown 1s ease;
    }

    .hero-section p {
      font-size: 1.1rem;
      animation: fadeInUp 1.5s ease;
    }

    .hero-section .btn {
      animation: fadeInUp 2s ease;
    }

    .sticky-btn {
      display: none;
    }

    @media (max-width: 768px) {
      .sticky-btn {
        display: block;
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background-color: #ffc107;
        color: #000;
        border-radius: 25px;
        padding: 12px 28px;
        font-weight: bold;
        box-shadow: 0 4px 15px rgba(255, 193, 7, 0.5);
        z-index: 1000;
      }
    }

    @keyframes fadeInDown {
      from { opacity: 0; transform: translateY(-50px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(50px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .section {
      padding: 50px 15px;
    }

    .img-histoire {
      width: 100%;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }

    .contact-info h3 {
      margin-bottom: 20px;
    }

    .contact-info a {
      text-decoration: none;
      color: #212529;
    }

    .btn-command {
      margin-top: 30px;
      background-color: #ffc107;
      color: #000;
      font-weight: bold;
      border-radius: 30px;
      padding: 12px 30px;
      box-shadow: 0 4px 15px rgba(255, 193, 7, 0.4);
      transition: all 0.3s ease;
    }

    .btn-command:hover {
      background-color: #e0a800;
      box-shadow: 0 6px 20px rgba(255, 193, 7, 0.6);
    }

    footer {
      background-color: #222;
      color: #fff;
      text-align: center;
      padding: 30px 10px;
      margin-top: 50px;
      font-size: 0.9rem;
    }

    /* Animation burger fumant */
    .burger-animation {
      width: 100px;
      margin: 40px auto;
      position: relative;
    }

    .smoke {
      position: absolute;
      top: -30px;
      left: 50%;
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.5);
      animation: smokeRise 2s infinite ease-in-out;
    }

    @keyframes smokeRise {
      0% {
        opacity: 0.6;
        transform: translate(-50%, 0) scale(1);
      }
      100% {
        opacity: 0;
        transform: translate(-50%, -40px) scale(2);
      }
    }

    .reviews {
      background: #f8f9fa;
      padding: 50px 20px;
    }

    .review-card {
      background: white;
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    .review-card h5 {
      font-weight: bold;
      margin-bottom: 10px;
    }

    .review-card p {
      font-style: italic;
    }
  </style>
</head>

<body>
  <div class="loader"><h1>SoulMade</h1></div>

  <section class="hero-section">
    <div class="container">
      <div class="hero-content">
        <h1>SoulMade</h1>
        <p class="lead">Une cuisine traditionnelle française dans un cadre chaleureux</p>
        <a href="./la-carte/" class="btn btn-command">Commander maintenant</a>
        <div class="burger-animation">
          <div class="smoke"></div>
        </div>
      </div>
    </div>
  </section>

  <a href="./la-carte/" class="sticky-btn">Commander</a>

  <section class="section" data-aos="fade-up">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6">
          <h2>Notre Histoire</h2>
          <p>Depuis 2020, SoulMade vous accueille en plein cœur de Lille dans une ambiance moderne et conviviale pour vous faire redécouvrir le goût du vrai smash burger. Notre équipe, passionnée par la street food de qualité, prépare chaque jour des recettes généreuses avec des produits frais, locaux et savoureux.</p>
        </div>
        <div class="col-md-6">
          <img src="https://smashmade.fastfoodservice.fr/source/assets/img/vitrine.jpg" alt="Notre restaurant" class="img-histoire">
        </div>
      </div>
    </div>
  </section>

  <section class="reviews" data-aos="fade-up">
    <div class="container">
      <h3 class="text-center mb-5">Ce que disent nos clients</h3>
      <div class="row g-4 justify-content-center">
        <div class="col-md-4">
          <div class="review-card">
            <h5>Lucas D.</h5>
            <p>"Le meilleur burger smash que j’ai mangé de ma vie ! Une vraie tuerie ! 🍔🔥"</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="review-card">
            <h5>Claire M.</h5>
            <p>"Service rapide, staff adorable, et les frites... une dinguerie."</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="review-card">
            <h5>Sofiane B.</h5>
            <p>"Lieu chaleureux et super propre. On sent l’amour dans les plats !"</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="contact" class="section bg-light" data-aos="fade-up">
    <div class="container">
      <div class="row text-center">
        <div class="col-md-4">
          <h3>Horaires</h3>
          <p>Lundi : Fermé</p>
          <p>Mardi - Jeudi : 19h00 - 23h00</p>
          <p>Vendredi - Samedi : 19h00 - 23h45</p>
          <p>Dimanche : 19h00 - 23h00</p>
        </div>
        <div class="col-md-4">
          <h3>Contact</h3>
          <p><a href="tel:+33695674728">📱 06 95 67 47 28</a></p>
          <p><a href="https://www.google.com/maps?q=104+rue+des+sarrazins,+Lille" target="_blank">📍 104 rue des Sarrazins, Lille</a></p>
        </div>
        <div class="col-md-4">
          <h3>Suivez-nous</h3>
          <a href="https://www.instagram.com/smashmade.lille/" target="_blank"><i class='bx bxl-instagram' style="font-size: 2rem;"></i></a>
        </div>
      </div>
    </div>
  </section>

  <footer>
    &copy; <?php echo date('Y'); ?> SoulMade. Tous droits réservés.
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init({ duration: 1000 });
    window.addEventListener('load', () => {
      document.querySelector('.loader').style.display = 'none';
    });
  </script>
</body>

</html>