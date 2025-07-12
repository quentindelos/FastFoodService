<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Commande de Burgers</title>
  <link rel="stylesheet" href="css/styles.css?v=1.1">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
  <style>
   body {
  background-image: url('./img/arriere.png');
  background-size: cover;
  background-position: center;
  background-attachment: fixed;
  background-repeat: no-repeat;
  backdrop-filter: blur(0px);
}

    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 30px;
      background: #000;
    }
    .logo {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .logo i {
      font-size: 28px;
      color: #ffc107;
    }
    .logo span {
      font-family: 'Pacifico', cursive;
      font-size: 22px;
      color: #ffc107;
    }
    .user-actions button {
      background: #ffc107;
      border: none;
      padding: 8px 16px;
      margin-left: 10px;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
    }
    .menu-items {
      display: flex;
      flex-wrap: wrap;
      gap: 30px;
      padding: 20px 0;
    }
    .burger-item {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 20px;
      padding: 20px;
      width: 300px;
      display: flex;
      flex-direction: column;
      gap: 10px;
      backdrop-filter: blur(8px);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .burger-item:hover {
      transform: translateY(-4px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
    }
    .burger-item img {
      width: 100%;
      border-radius: 12px;
      object-fit: cover;
    }
    .burger-item h3 {
      margin: 8px 0 4px;
    }
    .burger-item .price {
      font-weight: bold;
      color: #ffc107;
      font-size: 18px;
    }
    .burger-item button {
      background: #ffc107;
      color: #000;
      padding: 8px 16px;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      font-weight: bold;
    }
    .order-summary {
      background: rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      padding: 20px;
      color: white;
      min-width: 280px;
      max-width: 320px;
      flex-shrink: 0;
      position: sticky;
      top: 20px;
      height: fit-content;
    }
    .order-summary h2 {
      font-size: 20px;
      margin-bottom: 10px;
    }
    .order-summary .price {
      color: #ffc107;
    }
    #checkout-button {
      background-color: #ffc107;
      border: none;
      padding: 12px;
      width: 100%;
      border-radius: 12px;
      font-weight: bold;
      cursor: pointer;
    }
    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>

<body>
    <!-- Barre de navigation -->
    <div class="navbar">
      <div class="logo" style="display: flex; align-items: center; gap: 10px;">
    <i class="fa-solid fa-burger" style="font-size: 28px; color: #ffc107;"></i>
    <span style="font-family: 'Pacifico', cursive; font-size: 22px; color: #ffc107;">SoulMade</span>
</div>


        <div class="user-actions">
            <?php if (isset($_SESSION['id_client'])): ?>
                <!-- Si l'utilisateur est connecté -->
                <button id="profile-btn" onclick="window.location.href='profil.php'">Mon Profil</button>
                <button id="logout-btn" onclick="window.location.href='logout.php'">Se déconnecter</button>
            <?php else: ?>
                <!-- Si l'utilisateur n'est pas connecté -->
                <button id="login-btn" onclick="window.location.href='login.php'">Se connecter</button>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div style="color:red; text-align:center; font-weight:bold; margin-top:20px;">
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="container">
        <!-- Conteneur pour menu et récapitulatif côte à côte -->
        <div class="main-content">
            <!-- Menu des articles -->
            <div id="menu">
                <h1>Choisissez vos articles</h1>

                <!-- Onglets pour trier les burgers -->
                <div class="tabs">
                    <button class="tab-link active" data-category="all">Tous</button>
                    <button class="tab-link" data-category="beef">Bœuf</button>
                    <button class="tab-link" data-category="chicken">Poulet</button>
                    <button class="tab-link" data-category="fries">Frites</button>
                    <button class="tab-link" data-category="drinks">Boissons</button>
                    <button class="tab-link" data-category="starters">Starters</button>
                    <button class="tab-link" data-category="desserts">Desserts</button>
                </div>

                <!-- Section des burgers -->
                <div id="burger-selection" class="menu-items">
                <div class="burger-item" data-category="chicken">
                        <img src="img/classic_cajun.png" alt="Classic Cajuin">
                        <h3>Classic Cajun</h3>
                        <p>Bun boulanger bio, poulet frit maison, double
                            american cheddar, pickles, salade et oignons rouges,
                            sauce  MAYONNAISE CLASSIC CAJUN MAISON.</p>
                        <p class="price">9.5€</p>
                        <button class="add-to-cart" data-burger="O.G. OKLAHOMA" data-price="9.5">Ajouter au
                            panier</button>
                    </div>
                    <div class="burger-item" data-category="chicken">
                        <img src="img/mustard_bbq.png" alt="Mustard BBQ">
                        <h3>Mustard BBQ</h3>
                        <p>Bun boulanger bio, poulet frit maison, double
                            american cheddar, pickles, salade et oignons rouges,
                            sauce  MOUTARDE BARBECUE MIEL.</p>
                        <p class="price">9.5€</p>
                        <button class="add-to-cart" data-burger="O.G. OKLAHOMA" data-price="9.5">Ajouter au
                            panier</button>
                    </div>




                    <div class="burger-item" data-category="beef">
                        <img src="img/ogoklahoma.jpeg" alt="O.G. OKLAHOMA">
                        <h3>O.G. OKLAHOMA</h3>
                        <p>Bun boulanger bio, steak boucher smashé façon Oklahoma (smashé avec des oignons), double
                            american cheddar, pickles, salade et sauce O.G. maison.</p>
                        <p class="price">12€</p>
                        <button class="add-to-cart" data-burger="O.G. OKLAHOMA" data-price="12">Ajouter au
                            panier</button>
                    </div>
                    <div class="burger-item" data-category="beef">
                        <img src="img/smashic.jpeg" alt="SMASHIC">
                        <h3>SMASHIC</h3>
                        <p>Bun boulanger bio, steak boucher smashé minute, double american cheddar, oignons rouges,
                            pickles, ketchup et moutarde américaine. Ton classic USA! 🇺🇸</p>
                        <p class="price">11.50€</p>
                        <button class="add-to-cart" data-burger="SMASHIC" data-price="11.5">Ajouter au panier</button>
                    </div>
                    <div class="burger-item" data-category="beef">
                        <img src="img/truffle.jpeg" alt="TRUFFLE">
                        <h3>TRUFFLE</h3>
                        <p>
                            Bun boulanger bio, steak boucher smashé minute, double american cheddar, salade, oignons
                            frits et sauce mayonnaise à la truffe d'été!</p>
                        <p class="price">12.90€</p>
                        <button class="add-to-cart" data-burger="TRUFFLE" data-price="12.9">Ajouter au panier</button>
                    </div>
                    <div class="burger-item" data-category="beef">
                        <img src="img/smokey.jpeg" alt="SMOKEY">
                        <h3>SMOKEY</h3>
                        <p>
                            Bun boulanger bio, steak boucher smashé minute, double american cheddar, bacon de boeuf,
                            salade, oignons frits et sauce mayonnaise crémeuse fumée</p>
                        <p class="price">12.90€</p>
                        <button class="add-to-cart" data-burger="SMOKEY" data-price="12.9">Ajouter au panier</button>
                    </div>
                    <div class="burger-item" data-category="beef">
                        <img src="img/dae_lovers.jpeg" alt="DAE LOVERS">
                        <h3>DAE LOVERS</h3>
                        <p>Bun boulanger bio, poulet mariné maison et frit, double american cheddar, korean coleslaw et
                            sauce DAE GOCHUJANG maison 🌶
                            SMASHMADE IS THE NEW SEOUL ! 🇰🇷</p>
                        <p class="price">12.90€</p>
                        <button class="add-to-cart" data-burger="DAE LOVERS" data-price="12.9">Ajouter au
                            panier</button>
                    </div>
                    <div class="burger-item" data-category="beef">
                        <img src="img/soss.jpeg" alt="SOSS">
                        <h3>SOSS</h3>
                        <p>Bun boulanger bio, steak boucher smashé minute, double american cheddar, bacon de boeuf,
                            onion rings, salade, oignons frits, sauce BBQ.
                            LE GOUT DU TEXAS!🌵🤠</p>
                        <p class="price">13.50€</p>
                        <button class="add-to-cart" data-burger="SOSS" data-price="13.5">Ajouter au panier</button>
                    </div>
                </div>
                <!-- Section des frites -->
                <div id="fries-selection" class="menu-items">
                    <!-- Frites Maison -->
                    <div class="burger-item" data-category="fries">
                        <img src="img/fries.jpeg" alt="fries">
                        <h3>Frites Maison</h3>
                        <p>Nos incontournables frites de pomme de terre coupées et cuites sur place en plusieurs temps.
                        </p>
                        <label for="sauce-fries-maison">Choisissez votre sauce :</label>
                        <select id="sauce-fries-maison">
                            <option value="none" data-price="0">Sans sauce</option>
                            <option value="mayonnaise" data-price="1">Mayonnaise (+1€)</option>
                            <option value="ketchup" data-price="1">Ketchup (+1€)</option>
                            <option value="bbq" data-price="1.5">Sauce BBQ (+1.5€)</option>
                            <option value="smokey" data-price="1">Sauce Smokey (+1€)</option>
                            <option value="curry" data-price="1">Sauce Curry Mangue (+1€)</option>
                            <option value="swiss" data-price="1.2">Sauce Swiss (+1.2€)</option>
                            <option value="garlic" data-price="1">Sauce Garlic (+1€)</option>
                            <option value="sweet-chilli" data-price="1">Sauce Sweet Chilli (+1€)</option>
                            <option value="truffle" data-price="1">Sauce Mayo Truffe (+1€)</option>
                        </select>
                        <p class="price">4.50€</p>
                        <button class="add-to-cart" data-burger="Frites Maison" data-price="4.5">Ajouter au
                            panier</button>
                    </div>

                    <!-- Frites de Patate Douce -->
                    <div class="burger-item" data-category="fries">
                        <img src="img/sweet_fries.jpeg" alt="sweet_fries">
                        <h3>Frites de Patate Douce</h3>
                        <p>Nos savoureuses frites de patate douce à la fois croquantes et fondantes, sucrées et salées.
                        </p>
                        <label for="sauce-patate-douce">Choisissez votre sauce :</label>
                        <select id="sauce-patate-douce">
                            <option value="none" data-price="0">Sans sauce</option>
                            <option value="mayonnaise" data-price="1">Mayonnaise (+1€)</option>
                            <option value="ketchup" data-price="1">Ketchup (+1€)</option>
                            <option value="bbq" data-price="1.5">Sauce BBQ (+1.5€)</option>
                            <option value="smokey" data-price="1">Sauce Smokey (+1€)</option>
                            <option value="curry" data-price="1">Sauce Curry Mangue (+1€)</option>
                            <option value="swiss" data-price="1.2">Sauce Swiss (+1.2€)</option>
                            <option value="garlic" data-price="1">Sauce Garlic (+1€)</option>
                            <option value="sweet-chilli" data-price="1">Sauce Sweet Chilli (+1€)</option>
                            <option value="truffle" data-price="1">Sauce Mayo Truffe (+1€)</option>
                        </select>
                        <p class="price">5.50€</p>
                        <button class="add-to-cart" data-burger="Frites de Patate Douce" data-price="5.5">Ajouter au
                            panier</button>
                    </div>

                    <!-- Cheesy Fries -->
                    <div class="burger-item" data-category="fries">
                        <img src="img/cheesy_fries.jpeg" alt="cheesy_fries">
                        <h3>Cheesy Fries</h3>
                        <p>Nos incontournables frites de pomme de terre coupées et cuites sur place en plusieurs temps
                            avec du cheddar fondu.</p>
                        <label for="sauce-cheesy">Choisissez votre sauce :</label>
                        <select id="sauce-cheesy">
                            <option value="none" data-price="0">Sans sauce</option>
                            <option value="mayonnaise" data-price="1">Mayonnaise (+1€)</option>
                            <option value="ketchup" data-price="1">Ketchup (+1€)</option>
                            <option value="bbq" data-price="1.5">Sauce BBQ (+1.5€)</option>
                            <option value="smokey" data-price="1">Sauce Smokey (+1€)</option>
                            <option value="curry" data-price="1">Sauce Curry Mangue (+1€)</option>
                            <option value="swiss" data-price="1.2">Sauce Swiss (+1.2€)</option>
                            <option value="garlic" data-price="1">Sauce Garlic (+1€)</option>
                            <option value="sweet-chilli" data-price="1">Sauce Sweet Chilli (+1€)</option>
                            <option value="truffle" data-price="1">Sauce Mayo Truffe (+1€)</option>
                        </select>
                        <p class="price">5.50€</p>
                        <button class="add-to-cart" data-burger="Cheesy Fries" data-price="5.5">Ajouter au
                            panier</button>
                    </div>

                    <!-- Loaded Fries -->
                    <div class="burger-item" data-category="fries">
                        <h3>Loaded Fries</h3>
                        <p>Frites maison coupées sur place et cuites en plusieurs bains avec cheddar, bacon de bœuf,
                            oignons frits et sauce crémeuse.</p>
                        <label for="sauce-loaded">Choisissez votre sauce :</label>
                        <select id="sauce-loaded">
                            <option value="none" data-price="0">Sans sauce</option>
                            <option value="mayonnaise" data-price="1">Mayonnaise (+1€)</option>
                            <option value="ketchup" data-price="1">Ketchup (+1€)</option>
                            <option value="bbq" data-price="1.5">Sauce BBQ (+1.5€)</option>
                            <option value="smokey" data-price="1">Sauce Smokey (+1€)</option>
                            <option value="curry" data-price="1">Sauce Curry Mangue (+1€)</option>
                            <option value="swiss" data-price="1.2">Sauce Swiss (+1.2€)</option>
                            <option value="garlic" data-price="1">Sauce Garlic (+1€)</option>
                            <option value="sweet-chilli" data-price="1">Sauce Sweet Chilli (+1€)</option>
                            <option value="truffle" data-price="1">Sauce Mayo Truffe (+1€)</option>
                        </select>
                        <p class="price">7.50€</p>
                        <button class="add-to-cart" data-burger="Loaded Fries" data-price="7.5">Ajouter au
                            panier</button>
                    </div>
                </div>
                <!-- Section des burgers -->
                <div id="starter-selection" class="menu-items">
                    <div class="burger-item" data-category="starters">
                        <img src="img/onion_rings.jpeg" alt="onion_rings">
                        <h3>Onion Rings</h3>
                        <p>6 rondelles d'oignons panés et frits.</p>
                        <p class="price">6.50€</p>
                        <button class="add-to-cart" data-burger="Onion Rings" data-price="6.5">Ajouter au
                            panier</button>
                    </div>
                    <div class="burger-item" data-category="starters">
                        <img src="img/cream_cheese_jalapenos.jpeg" alt="cream_cheese_jalapenos">
                        <h3>🌶️ CREAM CHEESE JALAPEÑOS</h3>
                        <p>5 fromages crémeux avec des morceaux de jalapenos , frit.</p>
                        <p class="price">6.50€</p>
                        <button class="add-to-cart" data-burger="CREAM CHEESE JALAPEÑOS" data-price="6.5">Ajouter au
                            panier</button>
                    </div>
                    <div class="burger-item" data-category="starters">
                        <img src="img/mozza_sticks.jpeg" alt="mozza_sticks">
                        <h3>MOZZA STICKS</h3>
                        <p>5 sticks de mozarella panés</p>
                        <p class="price">6.50€</p>
                        <button class="add-to-cart" data-burger="Mozza Sticks" data-price="6.5">Ajouter au
                            panier</button>
                    </div>
                    <div class="burger-item" data-category="starters">
                        <img src="img/chicken_nuggets.jpeg" alt="chicken_nuggets">
                        <h3>❣️✨ CHICKEN NUGGETS ✨❣️</h3>
                        <p>5 délicieux nuggets de poulet tempura</p>
                        <p class="price">7.90</p>
                        <button class="add-to-cart" data-burger="Chicken Nuggets" data-price="7.90">Ajouter au
                            panier</button>
                    </div>
                </div>


                <!-- Section des boissons -->
                <div id="drinks-selection" class="menu-items">
                    <div class="burger-item" data-category="drinks">
                        <h3>Coca-Cola</h3>
                        <p>Boisson gazeuse</p>
                        <p class="price">2.00€</p>
                        <button class="add-to-cart" data-burger="Coca-Cola" data-price="2">Ajouter au panier</button>
                    </div>
                </div>

                <!-- Section des desserts -->
                <div id="desserts-selection" class="menu-items">
                    <div class="burger-item" data-category="desserts">
                        <h3>Gâteau au chocolat</h3>
                        <p>Délicieux gâteau au chocolat fondant</p>
                        <p class="price">3.50€</p>
                        <button class="add-to-cart" data-burger="Gâteau au chocolat" data-price="3.5">Ajouter au
                            panier</button>
                    </div>
                </div>
            </div>

            <!-- Récapitulatif de la commande -->
            <div id="order-summary" class="order-summary">
                <h2>Récapitulatif de la commande</h2>
                <div id="order-list">
                    <p>Aucun article dans votre panier.</p>
                </div>
                <div id="order-total">
                    <p>Total : 0.00€</p>

                    <!-- Bouton Commander -->
                    <button id="checkout-button">Commander</button>
                </div>





            </div>
        </div>
    </div>

                    <!-- 🔘 Modale Choix du mode de paiement -->
                <div id="payment-choice-modal" class="modal">
                    <div class="modal-content">
                        <h2>Choisissez votre mode de paiement</h2>

                        <div class="payment-buttons">
                            <button id="pay-online">
                                💳 <span>Payer en ligne</span>
                            </button>
                            <button id="pay-counter">
                                💵 <span>Payer au comptoir</span>
                            </button>
                        </div>

                        <button id="back-to-cart" class="back-btn">⬅ Retour au panier</button>
                    </div>
                </div>

                <!-- 💵 Modale Formulaire pour paiement au comptoir -->
                <div id="pay-counter-form" style="display: none;" class="modal">
                    <div class="modal-content counter-style">
                        <h2>Commande au comptoir</h2>
                        <p>Pour recevoir votre récapitulatif de commande par e-mail,<br> veuillez renseigner vos
                            informations :</p>

                        <form method="post" action="paiement_comptoir.php" class="form-content">
                            <input type="email" name="email" placeholder="Votre adresse e-mail" required>
                            <input type="tel" name="telephone" placeholder="Votre numéro de téléphone" required>
                            <input type="hidden" name="cart" id="counter-cart-json">

                            <div class="form-buttons">
                                <button type="submit" class="btn-confirm">✅ Valider la commande</button>
                                <button type="button" id="cancel-counter" class="btn-cancel">↩️ Retour</button>
                            </div>
                        </form>

                        <script>
                            document.getElementById("cancel-counter").addEventListener("click", function () {
                                document.getElementById("pay-counter-form").style.display = "none";
                            });
                        </script>
                    </div>
                </div>

    <!-- Modale de personnalisation -->
    <div id="customize-modal" class="modal" style="display:none;">
        <div class="modal-content">
            <h3>Personnalisez votre burger</h3>
            <form id="customize-form">
                <p>Retirer des ingrédients :</p>
                <div id="ingredients-options"></div>

                <p style="margin-top: 15px;">Ajouter des suppléments :</p>
                <div id="extras-options">
                    <label><input type="checkbox" value="steak smash & cheese" data-price="3"> steak smash & cheese
                        (+3€)</label>
                    <label><input type="checkbox" value="pickles" data-price="0.5"> pickles (+0.5€)</label>
                    <label><input type="checkbox" value="bacon" data-price="1.5"> bacon (+1.5€)</label>
                    <label><input type="checkbox" value="oignon confits" data-price="1"> oignon confits
                        (+1€)</label>
                    <label><input type="checkbox" value="jalapenos" data-price="0.5"> jalapenos (+0.5€)</label>
                </div>

                <div style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" id="cancel-customization">Annuler</button>
                    <button type="submit">Valider</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bouton flottant mobile -->
    <button id="floating-cart-btn"
        onclick="document.getElementById('order-summary').scrollIntoView({ behavior: 'smooth' });">
        🛒 Voir le panier
    </button>
<script src="js/scripts.js?ver=1.1"></script>
    <script>
    // 🎨 Thème sombre/clair toggle
    document.getElementById('theme-toggle').addEventListener('click', function () {
        const html = document.documentElement;
        const currentTheme = html.getAttribute('data-theme');
        html.setAttribute('data-theme', currentTheme === 'dark' ? 'light' : 'dark');
    });

    // ✨ Animation d'apparition des blocs burger
    document.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll(".burger-item").forEach((item, index) => {
            item.style.animationDelay = `${index * 100}ms`;
        });
    });
    </script>
</body>
</html>