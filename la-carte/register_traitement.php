<?php
require 'config.php';
require '../source/model/phpmailer/PHPMailer-master/src/PHPMailer.php';
require '../source/model/phpmailer/PHPMailer-master/src/SMTP.php';
require '../source/model/phpmailer/PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!empty($_POST['email']) && !empty($_POST['name'])) {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);

    // Vérification si l'email existe déjà
    $check = $pdo->prepare('SELECT email FROM clients WHERE email = ?');
    $check->execute([$email]);
    if ($check->rowCount() > 0) {
        echo "Cet email est déjà utilisé.";
        exit;
    }

    // Génération du mot de passe temporaire
    $temp_password = bin2hex(random_bytes(4));
    $hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);
    $token = bin2hex(random_bytes(16));

    // Insertion dans la base de données avec le nom
    $insert = $pdo->prepare('INSERT INTO clients (nom, email, mot_de_passe, reset_password_token) VALUES (?, ?, ?, ?)');
    if ($insert->execute([$name, $email, $hashed_password, $token])) {

        // Envoi de l'email de confirmation avec le mot de passe temporaire
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
            $mail->Subject = 'Votre accès temporaire à SmashMade';
            $mail->Body = "
                Bonjour $name,<br><br>
                Voici votre mot de passe temporaire : <strong>$temp_password</strong><br><br>
                Pour le modifier, cliquez ici : <a href='https://smashmade.fastfoodservice.fr/la-carte/modifier_motdepasse_anim.php?token=$token'>Modifier mon mot de passe</a><br><br>
                Merci !
            ";

            $mail->send();
            header('Location: email_envoye.php');
            exit;

        } catch (Exception $e) {
            echo "Erreur lors de l'envoi de l'email : {$mail->ErrorInfo}";
        }

    } else {
        echo "Erreur lors de l'inscription.";
    }
} else {
    echo "Veuillez entrer un nom et une adresse email.";
}
?>
