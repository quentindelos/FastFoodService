<?php
session_start();
$nomCommande = $_SESSION['nom_commande'] ?? '---';
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <title>Commande confirmée - SmashMade</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
      body {
        font-family: "Segoe UI", sans-serif;
        background-color: #f4f4f4;
        color: #333;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        margin: 0;
      }

      .container {
        background-color: white;
        padding: 30px 40px;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        max-width: 500px;
        text-align: center;
      }

      .container h1 {
        color: #1a1a1a;
        font-size: 26px;
        margin-bottom: 20px;
      }

      .container p {
        font-size: 16px;
        line-height: 1.5;
      }

      .success-icon {
        font-size: 60px;
        color: #4caf50;
        margin-bottom: 15px;
      }

      .btn {
        margin-top: 25px;
        padding: 12px 24px;
        font-size: 16px;
        border: none;
        border-radius: 8px;
        background-color: #000;
        color: #fff;
        cursor: pointer;
        transition: background-color 0.3s ease;
        text-decoration: none;
      }

      .btn:hover {
        background-color: #333;
      }
    </style>
  </head>
  <body>
    <div class="container">
      <div class="success-icon">✅</div>
      <h1>Commande enregistrée !</h1>
      <p>Votre paiement a été effectué avec succès.</p>
      <h2>
        <strong>Numéro de commande :</strong>
        <?= htmlspecialchars($nomCommande) ?>
      </h2>
      <p>
        Merci pour votre commande chez <strong>SmashMade</strong> 🍔<br /><br />
        Vous recevrez un récapitulatif à l'adresse email que vous avez fournie.<br />
        Votre commande est à régler directement au comptoir.<br /><br />
        À très bientôt !
      </p>
      <a href="https://smashmade.fastfoodservice.fr/" class="btn">Retour à l'accueil</a>
    </div>
  </body>
</html>
