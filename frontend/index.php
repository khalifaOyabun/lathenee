<?php

ob_start();
/*
 * 
 * Frontend index et routage.
 * 
 */
require_once "../core/_ini.php";

/* Préparation de l'action. */
if (!isset($action)) {
    $action = ($module == "connexion") ? "connexion" : "index";
}

require "modules/" . $module . ".php"; // Routage et choix template 
$vue = "/index.tpl";

// if ($model->online() === false && !in_array($action, ["authentification", "changementmotdepasseoublie"])) {
//     $model->redirect(HTTP_PATH, "/connexion");
// }

/* Session; erreur et succes; form data */
if (isset($_SESSION[$app]["notification"]["erreur"])) {
    $oSmarty->assign("erreur", $_SESSION[$app]["notification"]["erreur"]);
    unset($_SESSION[$app]["notification"]);
}

if (isset($_SESSION[$app]["notification"]["succes"])) {
    $oSmarty->assign("succes", $_SESSION[$app]["notification"]["succes"]);
    unset($_SESSION[$app]["notification"]);
}

$get["action"] = $action;
$get["path"] = $path;

if (isset($model)) {
    $get["isNegoc"] = (bool) $model->connected("NEGOCIATEUR");
    $get["isAdmin"] = (bool) $model->connected("ADMINISTRATEUR");
    $get["isSuper"] = (bool) $model->connected("SUPERADMINISTRATEUR");
}
#getdir($route, "/frontend/templates_c/");
/* Assignation et affichage. */
$oSmarty->assign("get", $get);

$oSmarty->assign('lang', $language);    
$oSmarty->display($template . $vue);

ob_end_flush();