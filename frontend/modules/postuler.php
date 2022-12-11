<?php

use function PHPSTORM_META\type;

$model = new Model($dao, ''); // Mise en marche des requêtes basiques.

$role = $_GET['role'];
$today = date("d/m/Y ");
$post = filter_input_array(INPUT_POST);

// error_reporting(E_ALL);
// ini_set("display_errors", 1);

if ($post != null) {

    extract($post);
    $champsAControler = [
        'EMPTY' => ['prenom', 'nom', 'tel', 'address', 'mail', 'ville', 'date_dd', 'diplome', 'matiere', 'civilite', 'nationalite', 'date_naiss', 'niveau', 'cv'],
        'PHONE' => ['tel'],
        'NUMERIC' => ['date_dd'],

    ];

    $erreur = $model->SaisieControle($post, $champsAControler);
    print_r($post);
    exit();

    if ($erreur[0] == false) {
        $email_from = "ababacarkhalifa13@gmail.com";
        $prenom = ucwords($prenom);
        $nom = strtoupper($nom);

        $message = '
        <div style="width: 70vw;">
            <div>
                <div style ="text-align: right">
                    <span>' . $ville . '</span>, le ' . $today . ' 
                </div>
                <div style ="text-align: left">
                    <span>' . $civilite . '</span> <span>' . $prenom . '</span> <span>' . $nom . '</span> <br>
                    Titulaire d\'un <span>' . $diplome . '</span> <br>
                    Courriel : <span>' . $mail . '</span> <br>
                    Tel : <span>' . $tel . '</span><br>
                    Adresse : <span>' . $adress . '</span>
                </div>
            </div>

            <br/>

            <div style ="width: 70vw; text-align:right;">
                A Madame la<br>
                Directrice de L\'Athénée<br>
            </div>

            <div style="text-align: justify; width: 60vw; word-wrap: break-word;">
                <span style="font-weight: 500; text-decoration-line: underline;">Objet :</span> Candidature au poste de ' . $role . '.<br><br>
                    Madame,<br><br>
                    Titulaire d\'un <span>' . $diplome . '</span> depuis <span>' . $date_dd . '</span>, Je souhaite vous faire part de mon intérêt pour le poste de Professeur de Sciences Physiques, que vous proposez sur www.lathenee.sn. Les compétences en <span>' . $matiere . '</span>, acquises lors de mes précédentes expériences, correspondent au descriptif de votre offre.<br/>
                    Vous trouverez mon CV plus détaillée en pièces jointes.<br/>

                    Dans l\'attente de vous rencontrer prochainement, je vous prie de croire, Madame, à l’expression de mes salutations distinguées.<br/><br/>

                    Cordialement,

            </div>
            <br/>

            <div style="width:70vw; text-align: right"><span>' . $prenom . '</span> &nbsp;<span>' . $nom . '</span></div>
        </div>';


        $email_to = "umartista13@gmail.com";
        $email_subject = "Candidature au poste de $role mailTest";
        $boundary = md5(rand());

        function clean_string($string)
        {
            $bad = array("content-type", "bcc:", "to:", "cc:", "href");
            return str_replace($bad, "", $string);
        }

        $headers = "From: " . $prenom . " <" . $email_from . ">" . "\n";
        $headers .= "Reply-to: " . $prenom . " <" . $email_from . ">" . "\n";
        $headers .= "MIME-Version: 1.0" . "\n";
        $headers .= 'Content-Type: multipart/mixed; boundary=' . $boundary . ' ' . "\n";

        $email_message = '--' . $boundary . "\n";
        $email_message .= "Content-Type: text/html; charset=\"iso-8859-1\"" . "\n";
        $email_message .= "Content-Transfer-Encoding: 8bit" . "\n";
        $email_message .= "\n" . clean_string($message) . "\n";

        if (mail($email_to, $email_subject, $email_message, $headers)) {
            $success = "Mail envoyé !";
            // $_SESSION[$app]['notification']['success'] = "Mail envoyé !";
            // print_r("Mail envoyé !");
            // exit();
        } else {
            // $_SESSION[$app]['notification']['error'] = "L'envoie a échoué !";
            // print_r("L'envoie a échoué !");
            $error = "L'envoie a échoué !";
        }
        // $_SESSION[$app]['notification']['success'] = '';
    } else {
        $error = $erreur[1];
        // $_SESSION[$app]['notification']['error'] = $erreur[1];
        // print_r($erreur[1]);
        // exit();
    }
}
$assigned = [
    "role" => $role,
    "today" => $today,
    "success" => $success,
    "error" => $error
];

$oSmarty->assign('assigned', $assigned);