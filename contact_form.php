<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
// Inclure PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once 'functions.php';


require_once 'vendor/autoload.php'; // Assurez-vous que le chemin est correct
if (isset($_POST['name'], $_POST['email'], $_POST['phone'], $_POST['message'])) {

    // Clés reCAPTCHA
    $captchaSiteKey = '6LcT_v0qAAAAAGVf1k7hbrZ_JpnTxE3f253U4aPE';
    $captchaSecretKey = '6LcT_v0qAAAAAGCv8vcCcg-Y2-eAmdAtX6Gm2FKT';

    // Informations de l'email
    $php_author = "Salim Ahouad";
    $php_main_email = "contact@ahouadsalim.com";
    $php_from_email = 'no-reply@ahouadsalim.com';
    $php_subject = "Message from Website";

    // Vérification du reCAPTCHA
    $createGoogleUrl = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $captchaSecretKey . '&response=' . $_POST['g-recaptcha-response'];
    $ch = curl_init();

// Configurer l'URL et d'autres options cURL
    curl_setopt($ch, CURLOPT_URL, $createGoogleUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Exécuter la requête cURL
    $verifyRecaptcha = curl_exec($ch);

    // Vérifier s'il y a une erreur avec cURL
    if(curl_errno($ch)) {
        error_log('cURL Error: ' . curl_error($ch));
    }

    // Fermer la session cURL
    curl_close($ch);
    $decodeGoogleResponse = json_decode($verifyRecaptcha, true);

    // Récupérer les données du formulaire
    $php_email = $_POST['email'];
    $php_name = $_POST['name'];
    $php_phone = $_POST['phone'];
    $php_message = $_POST['message'];
    $user_ip = get_user_ip();
    $user_browser = get_user_browser();
    $user_os = get_user_os();
    $date = generate_date();

    // Configurer PHPMailer
    $mail = new PHPMailer(true);

// Configuration du serveur SMTP
$mail->isSMTP();
$mail->Host = 'mail.privateemail.com';  // Serveur SMTP
$mail->SMTPAuth = true;
$mail->Username = 'contact@ahouadsalim.com';  // Ton email d'authentification SMTP
$mail->Password = 'p@ssW0rd270986324S****';  // Mot de passe ou mot de passe spécifique d'application
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;

// --- Confirmation Email to Sender ---
try {
    // Paramètres pour l'email de confirmation
    $mail->setFrom('contact@ahouadsalim.com', 'Ahouad Salim');
    $mail->addAddress($php_email);  // Envoie à l'email du contact
    $mail->isHTML(true);
    $mail->Subject = 'Thank you for contacting me!';
    $mail->Body    = '<div style="padding:50px;">Hello ' . $php_name . ',<br/>'
        . 'Thank you for contacting me.<br/><br/>'
        . 'I will get back to you as soon as possible.<br/><br/>'
        . 'This is a confirmation email to acknowledge the receipt of your message.<br/>'
        . 'Your message details:<br/>'
        . '<strong>Name:</strong> ' . $php_name . '<br/>'
        . '<strong>Email:</strong> ' . $php_email . '<br/>'
        . '<strong>Phone:</strong> ' . $php_phone . '<br/>'
        . '<strong>Message:</strong> ' . $php_message . '<br/><br/>'
        . 'Best regards,<br/>Ahouad Salim</div>';
    
    // Envoi de l'email de confirmation
    if (!$mail->send()) {
        echo "Error sending confirmation email to sender.";
    }

    // --- Notification Email to You ---
    $mail->clearAddresses();  // Clear previous addresses
    $mail->addAddress('ahouadsalim@gmail.com');  // Ton email pour recevoir la notification

    // Corps du message pour la notification
    $mail->Subject = 'New Contact Form Submission';
    $mail->Body    = '<div style="padding:50px;">Hello,<br/>'
        . 'A new contact form has been submitted. Below are the details:<br/><br/>'
        . '<strong>Name:</strong> ' . $php_name . '<br/>'
        . '<strong>Email:</strong> ' . $php_email . '<br/>'
        . '<strong>Phone:</strong> ' . $php_phone . '<br/>'
        . '<strong>IP Address:</strong> ' . $user_ip . '<br/>'
        . '<strong>Browser:</strong> ' . $user_browser . '<br/>'
        . '<strong>Operating System:</strong> ' . $user_os . '<br/>'
        . '<strong>Date:</strong> ' . $date . '<br/>'
        . '<strong>Message:</strong> ' . $php_message . '<br/><br/>'
        . 'Best regards,<br/>Ahouad Salim</div>';

    // Envoi de l'email de notification
    if (!$mail->send()) {
        echo "Error sending notification email to admin.";
    } else {
        echo "success";  // Confirmation du succès de l'envoi
    }
} catch (Exception $e) {
    echo "Mailer Error: {$mail->ErrorInfo}";
}
}
?>