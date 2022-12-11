<?php

//error_reporting(E_ALL);
//$site_name = Nom du site qu'on utilisera comme texte du From (equivalent $nameFrom)
//$admin_mail = variable à utiliser comme reply to (equivalent $from)
//$destinataire = destinataire du mail (equivalent $to)
//$sujet = Sujet du message (equivalent $subject)
//$message_texte = texte du mail (equivalent $body)
//$nameTo = alias du nom du destinataire (optionnel)
//A prevoir : txt_mail_defaut (Services informations), adr_mail_defaut (infos@....) et mdp_mail_defaut (...) dans site_settings

use PHPMailer\PHPMailer\PHPMailer;

function sendMail($nameFrom, $from, $to, $subject, $body, $nameTo = null, $fichiersJoints = null, $ical_content = "")
{
    $mail = new PHPMailer();
    $mail->CharSet = 'utf-8';
    //$mail->XMailer = 'net-profil.com';
    $mail->IsSMTP(); // telling the class to use SMTP
    $mail->SMTPDebug = 0; // enables SMTP debug information (for testing)
    // 1 = errors and messages
    // 2 = messages only
    $mail->SMTPAuth = true; // enable SMTP authentication
    $mail->SMTPSecure = "tls"; // sets the prefix to the servier
    // If you're using Amazon SES in a region other than US West (Oregon),
    // replace email-smtp.us-west-2.amazonaws.com with the Amazon SES SMTP
    // endpoint in the appropriate region.
    $mail->Host = "email-smtp.eu-west-3.amazonaws.com";
    $mail->Port = 587;
    $mail->Username = "AKIAUS4ZVOB6V24NRM6Y";
    $mail->Password = "BKYcgQtbJ80mvaPQL2DKsEWHus91+A1jJUH37N3mwJp4"; // GMAIL password
    
    $sender = 'mail@net-profil.com';

    if ($from) {
        //$mail->SetFrom($from, $nameFrom);
        $mail->SetFrom($sender, $nameFrom);
        $mail->AddReplyTo($from, $nameFrom);
    } else {
        $mail->SetFrom('no-reply@net-profil.com', 'Service Informations');
        $mail->AddReplyTo("no-reply@net-profil.com", "Service Informations");
    }
    if (defined('BCC_MAIL')) {
        $mail->addBCC(BCC_MAIL); // Faire une copie cachée
    }
    if (!empty($_SESSION[$app][_USER_]->email_superviseur)) {
        $mail->addBCC($_SESSION[$app][_USER_]->email_superviseur); // Faire une copie cachée
    }
    $mail->Subject = $subject;

    $mail->MsgHTML($body);

    $mail->AddAddress($to, $nameTo);

    if (($fichiersJoints) && (is_array($fichiersJoints))) {
        foreach ($fichiersJoints as $cle => $valeur) {
            if (isset($valeur['content_PDF'])) {
                $mail->AddStringAttachment($valeur['content_PDF'], $valeur['nom_fichier_pdf'], 'base64', 'application/pdf'); // attachment
            } else {
                $mail->AddAttachment($valeur['chemin_fichier'], $valeur['nom_fichier']);
            }
        }
    }

    if (!empty($ical_content)) {
        $mail->addStringAttachment($ical_content, 'ical.ics', 'base64', 'text/calendar');
    }

    if (!$mail->Send()) {
        echo $mail->ErrorInfo;
        return false;
    }

    $mail->SmtpClose(); // à supprimer si vous n'utilisez pas SMTP

    unset($mail);

    return true;
}