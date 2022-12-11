<?php

$model = new Model($dao, ''); // Mise en marche des requêtes basiques.

$assigned = [];

switch ($action) {
    case "ajax":
        switch ($todo) {
            case "writeus":

                $datas = filter_input_array(INPUT_POST);
                $__infoscandidat = serializepost($datas["datas"], true);
                # dump($__infoscandidat);

                $message = "Bonjour Saloum Voyages, <br /><br />" . $__infoscandidat["filiation"] . " souhaite participer à la <strong>Oumra 2022</strong> organisée.";
                $message .= "<br /><br />La formule choisie par le candidat est : <b>" . $formule[$__infoscandidat["formule"]] . "</b>.";
                $message .= "<br /><br />Vous pouvez le contacter au <b>" . $__infoscandidat["telephone"] . "</b> pour plus d'informations.";
                $message .= "<br /><br /><u><b>Commentaire</b></u> : <br />" . $__infoscandidat["commentaire"];
                $message .= "<br /><br />Cordialement.";
                $paramMail = prepareMail("CANDIDATURE À LA OUMRA 2022", $message);
                if (sendMail($paramMail['prefixe_sujet'], $__infoscandidat["email"], __COMMERCIAL, $paramMail['sujet'], $paramMail['message'])) {
                    echo json_encode(["status" => "success", "notification" => "Votre message a été envoyé. Un conseiller vous contactera dans un bref délai."]);
                } else {
                    echo json_encode(["status" => "error", "notification" => "Votre message n'a pas été envoyé. Réessayez !"]);
                }

                exit();
                break;
            default:
                break;
        }
        break;
    default:
        break;
}


$oSmarty->assign('assigned', $assigned);
