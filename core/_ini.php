<?php

header('Content-type: text/html; charset=UTF-8');

(session_status() === PHP_SESSION_ACTIVE ? null : session_start()); /* Démarre la session. */

setlocale(LC_TIME, 'fr_FR.UTF8', 'fr.UTF8', 'fr_FR.UTF-8', 'fr.UTF-8');

error_reporting(0);

$server = filter_input_array(INPUT_SERVER);

extract($server);

$get = filter_input_array(INPUT_GET); # On récupère la varibale module et les autres variables passées par get dans l'URL.

extract($get);

$route = $DOCUMENT_ROOT; # str_replace('/frontend', '', $DOCUMENT_ROOT); #  On récupère la route.

require_once($route . '/core/model/connect.php');

require_once($route . '/core/_const.php');

require_once($route . '/core/language/fr.php');

(file_exists($route . '/core/_const_bd.php')) ? require_once($route . '/core/_const_bd.php') : '';

# require_once($route . '/core/model/connect.php');

require_once($route . '/core/model/Model.class.php');

require_once($route . '/core/model/Outils.class.php');

require_once($route . '/core/plugins/smarty/libs/Smarty.class.php');

require_once($route . '/core/plugins/vendor/autoload.php'); # for html2pdf et phpmailer

require_once($route . '/core/_func.php');

if (defined('_ENV') && _ENV === "DEV") {

    require_once($route . '/core/_mail.php');
} else {

    require_once($route . '/core/_mail_amazon.php');
}

require_once($route . '/core/model/ZipData.class.php');

# nperror(); # tracking error or not

/*
 * INITIALISATION des variables qui doivent être visibles partout.
 * CRÉATION des objets qui serviront partout. 
 *          Exemple : connexion à la base, moteur de template.
 * ASSIGNATION des variables visibles partout au template.
 */

$dao = _connect(HOST, USER, PASS, S_DB);

$model = new Model($dao, ""); # Mise en marche des requêtes basiques.

$model->app($app);

$oSmarty = new Smarty;

$language = [];

if ($get) {


    $path = (isset($app) && $app === 'backend') ? HTTP_PATH . REAL_BE_PATH : HTTP_PATH;

    $language = (isset($module) && isset($lang[$module])) ? array_merge($lang['commun'], $lang[$module]) : $lang['commun'];

    $template = (isset($app)) ? $route . '/templates/' . $app . '/' . _version : ''; # Chemin vers le dossier template.

    $oSmarty->assign('template', $template);

    if (isset($_SESSION[$app][_USER_]->nom_dossier)) { # Pour acceder plus facilement au dossier storage des agences.
        $oSmarty->assign('storagepath', $route . _STORAGE_PATH . 'agences/' . $_SESSION[$app][_USER_]->nom_dossier);
    }
}

$moment = date("Y-m-d H:i:s");