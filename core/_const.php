<?php

/*
 *  Début
 * 
 */

// $dao = _connect(HOST, USER, PASS, S_DB);
// $requete = "SELECT * FROM acs_settings WHERE deleted = -1";
// $execution = $dao->prepare($requete);
// $execution->execute();
// if ($execution->rowCount() > 0) {
//     while ($lignes = $execution->fetch(PDO::FETCH_OBJ)) {
//         define($lignes->nom, $lignes->valeur);
//     }
// }

const PROTOCOLE = "http://";
const URL_LOGICIEL = "http://lathenee.localhost";
const NETPRO_SITE_WEB = "";
const _env = "";
const _USER_ = "";


define('HTTP_PATH', PROTOCOLE . $HTTP_HOST);

/* * **** SITE CONFIG & PATH */
const _version = "educrat";

const BASE_REF = "amarys_couturier_salon_000001.";
const DOSSIER_REF = "agence_000001";

$assets = _version;
if ($get["module"] === "landing") {
    $assets = _version . "/landing";
}

define("CSS_PATH_BE", "/assets/backend/$assets/css");
define("IMG_PATH_BE", "/assets/backend/$assets/images");
define("JS_PATH_BE", "/assets/backend/$assets/js");
define("MED_PATH_BE", "/assets/backend/$assets/media");
define("PLU_PATH_BE", "/assets/backend/$assets/plugins");
define("VEN_PATH_BE", "/assets/backend/$assets/vendors");
define("PRO_PATH_BE", "/assets/backend/" . _version . "/project");

define("_FECSS_", "/assets/frontend/$assets/css");
define("_FEIMG_", "/assets/frontend/$assets/img");
define("_FEMED_", "/assets/frontend/$assets/media");
define("_FEFONT_", "/assets/frontend/$assets/fonts");
define("_FEJS_", "/assets/frontend/$assets/js");


/* * **** SIGNATURE MAIL */
define('MODELE_SIGNATURE_EMAIL', '<p>#raison_sociale# #forme_juridique#<br />
Adresse : #adresse# #code_postal# #ville#<br />
Email : #email#<br />
Téléphone : #telephone#<br />
Site Web : #site_web#<br />
#informations_conseiller#
#coordonnees_conseiller#
</p>');

const __ENSEIGNE = "L'Athénée";
const __RAISON_SOCIALE = "CENTURY 21";
const __FORME_JURIDIQUE = "SAS";
const __ADRESSE = "Route du Méridien - Les Almadies ";
const __CP = "BP 3826";
const __VILLE = "DAKAR / SENEGAL";
const __EMAIL = "mdawa88@gmail.com";
const __COMMERCIAL = "mdawa88@gmail.com";
const __TELEPHONE = "+(221) 33 869 78 87";

const __WEBMAIL = "accueil@lathenee.sn";
const __ReCAPTCHA_PUBLIC = "6LexmpccAAAAAG1fqsfQzx_jHJGp_JYzmaKGo-Eb";
const __ReCAPTCHA_SECRET = "6LexmpccAAAAAOYDPS4CrQ6-3-7ogSAquQH4cIkx";

/* * **** FOR WEBSITE */
/*const _USER_ = "connecte";
const _ALIVED_TIME_TOKEN = 604800;*/ // for a week (7 days)

$allowed_domains = [
    NETPRO_SITE_WEB,
    PROTOCOLE . URL_LOGICIEL
];

const _LICENCE_HTML2PDF_ = "l2UTp3kyCksf";