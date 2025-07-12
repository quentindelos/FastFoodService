<?php
require 'config.php';
require '../source/model/phpmailer/PHPMailer-master/src/PHPMailer.php';
require '../source/model/phpmailer/PHPMailer-master/src/SMTP.php';
require '../source/model/phpmailer/PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!empty($_POST['email'])) {
    $email = htmlspecialchars($_POST['email']);

    // Vérification si l'email existe déjà dans la base de données
    $check = $pdo->prepare('SELECT id_client, nom FROM clients WHERE email = ?');
    $check->execute([$email]);

    // Si l'email n'existe pas, on renvoie un message d'erreur
    if ($check->rowCount() == 0) {
        echo "Aucun compte trouvé avec cette adresse email.";
        exit;
    }

    // Génération du token de réinitialisation de mot de passe
    $token = bin2hex(random_bytes(16));

    // Mise à jour du token dans la base de données
    $update = $pdo->prepare('UPDATE clients SET reset_password_token = ? WHERE email = ?');
    if ($update->execute([$token, $email])) {

        // Envoi de l'email de réinitialisation avec un lien pour modifier le mot de passe
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'smashmade.inscriptions@gmail.com';
            $mail->Password = 'aiqa magv wobj rxgi';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('smashmade.inscriptions@gmail.com', 'SmashMade');
            $mail->addAddress($email);

            $mail->CharSet = 'UTF-8';

            $mail->isHTML(true);
            $mail->Subject = 'Réinitialisation de votre mot de passe SmashMade';
            $mail->Body = "
                Bonjour,<br><br>
                Nous avons reçu une demande de réinitialisation de votre mot de passe.<br><br>
                Pour réinitialiser votre mot de passe, veuillez cliquer sur le lien suivant : <br>
                <a href='https://smashmade.fastfoodservice.fr/la-carte/modifier_motdepasse_anim.php?token=$token'>Réinitialiser mon mot de passe</a><br><br>
                Si vous n'avez pas demandé cette réinitialisation, vous pouvez ignorer cet email.<br><br>
                Merci !
            ";

            $mail->send();
            header('Location: email_envoye.php');
            exit;

        } catch (Exception $e) {
            echo "Erreur lors de l'envoi de l'email : {$mail->ErrorInfo}";
        }

    } else {
        echo "Erreur lors de la mise à jour du token.";
    }
} else {
    echo "Veuillez entrer une adresse email.";
}
?>
