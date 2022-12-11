
<?php

$model = new Model($dao, ''); // Mise en marche des requêtes basiques.

$lstCivilite = $model->getElementType(['CIVILITE', 'FORME_JURIDIQUE']);

$tableauDonnees = [];
switch ($action) {

    case 'authentification' :
        $donnees = filter_input_array(INPUT_POST);
        if ($donnees !== NULL) {
            $model->release();
            $model->setTable("crm_np_utilisateur");
            $model->setClause('(login = "' . $donnees['login'] . '" OR email = "' . $donnees['login'] . '")');
            $model->setChamp("*");
            $model->setOrderBy("id");
            $user = $model->getData();

            if (count($user) > 0) {

                if ($user[0]->actif != 1) {
                    $_SESSION[$app]['notification']['erreur'] = $language['connexion_compte_desactive'];
                    $model->redirect(HTTP_PATH, "/connexion");
                }

                if ($user[0]->password !== md5($donnees['password'])) {
                    $_SESSION[$app]['notification']['erreur'] = $language['connexion_motdepasse_incorrecte'];
                    $model->redirect(HTTP_PATH, "/connexion");
                } else {

                    $_SESSION[_USER_] = $user[0];
                    if ($_SESSION[_USER_]->code_type_utilisateur == "SUPERADMINISTRATEUR") {
                        $model->setClause(null);
                        $model->setOrderBy('id ASC');
                        $model->setLimit('1');
                    } else {
                        if ($_SESSION[_USER_]->id_agence > 0) {
                            $model->setClause('id = ' . $_SESSION[_USER_]->id_agence);
                            $model->setOrderBy(null);
                            $model->setLimit(null);
                        } else {
                            $_SESSION[$app]['notification']['erreur'] = $language['connexion_compte_agence_non_liee'];
                            $model->redirect(HTTP_PATH, "/connexion");
                        }
                    }

                    $model->settable("crm_np_agences");
                    $model->setChamp("nom_bd, raison_sociale, actif, site_web");
                    $nom_bd = $model->getData();

                    //On teste si l'agence n'est pas désactivée
                    if (($_SESSION[_USER_]->code_type_utilisateur != "SUPERADMINISTRATEUR") && ($nom_bd[0]->actif != 1)) {
                        $_SESSION[$app]['notification']['erreur'] = $language['connexion_agence_desactivee'];
                        $model->redirect(HTTP_PATH, "/connexion");
                    }

                    $_SESSION[_USER_]->nom_bd = PREFIXE_BD . $nom_bd[0]->nom_bd;
                    $_SESSION[_USER_]->nom_dossier = $nom_bd[0]->nom_bd;
                    $_SESSION[_USER_]->nom_agence = $nom_bd[0]->raison_sociale;

                    genLEFJSON($model, $route);

                    //$avatardir = getdir($route . _STORAGE_PATH . "agences/", $_SESSION[_USER_]->nom_dossier . "/avatars/utilisateurs/");
                    //$_SESSION[_USER_]->photo = (!empty($_SESSION[_USER_]->photo) && file_exists($avatardir . $_SESSION[_USER_]->photo)) ? HTTP_PATH . _STORAGE_PATH . "agences/" . $_SESSION[_USER_]->nom_dossier . "/avatars/utilisateurs/" . $_SESSION[_USER_]->photo : "";
                    $avatardir = $route . _STORAGE_PATH . "images/utilisateurs/";
                    $_SESSION[_USER_]->photo = (!empty($_SESSION[_USER_]->photo) && file_exists($avatardir . $_SESSION[_USER_]->photo)) ? HTTP_PATH . _STORAGE_PATH . "images/utilisateurs/" . $_SESSION[_USER_]->photo : "";

                    // if (!file_exists($route . _STORAGE_PATH . 'agences/' . $_SESSION[_USER_]->nom_dossier . '/config.json')) {
                    JSONGen($model, $route, $_SESSION[_USER_]->nom_dossier);
                    // }

                    $path = HTTP_PATH;
                    $config = JSONFileToArray($route . _STORAGE_PATH . 'agences/' . $_SESSION[_USER_]->nom_dossier . '/config.json');

                    if ((isset($config['site_settings']['domaine_par_defaut_logiciel'])) && !empty($config['site_settings']['domaine_par_defaut_logiciel']) && (getNomDomaine(HTTP_PATH) !== $config['site_settings']['domaine_par_defaut_logiciel'])) {
                        $path = _PROTOCOLE . $config['site_settings']['domaine_par_defaut_logiciel'];
                    }

                    if ($_SESSION[_USER_]->code_type_utilisateur == "SUPERADMINISTRATEUR") {
                        $model->redirect($path . '/agences/liste');
                    } else {
                        if ($user[0]->premiere_connexion == 1) {
                            $model->redirect($path, "/connexion/changement-mot-de-passe");
                        }
                        $model->redirect($path . '/');
                    }
                }
            } else {
                $_SESSION[$app]['notification']['erreur'] = $language['connexion_login_incorrect'];
                $model->redirect(HTTP_PATH, "/connexion");
            }
        }

        break;

    case 'deconnexion' :

        $model->disconnect();
        break;

    case 'changementmotdepasse' :

        $donnees = filter_input_array(INPUT_POST);

        if ($donnees !== NULL) {

            if ($donnees['password'] === $donnees['confirmation_password']) {

                $password = $donnees['password'];

                $donnees['password'] = md5($donnees['password']);
                $donnees['premiere_connexion'] = -1;
                $donnees['id'] = $_SESSION[_USER_]->id;

                unset($donnees['confirmation_password']);
                $model->release();
                $model->setTable("crm_np_utilisateur");
                $update_id = $model->updateOne($donnees);

                if ($update_id > 0) {

                    $_SESSION[_USER_]->premiere_connexion = -1;

                    // Préparation d'un courriel de réinitialisation.
                    $model->release();
                    $model->setClause('code = "REINIT_MDP_AFTER_FIRST_CONNECT"');
                    $model->setTable($_SESSION[_USER_]->nom_bd . "." . CRM_NP_COURRIER_TYPE);
                    $model->setChamp([COURRIER_TYPE_OBJET, COURRIER_TYPE_CONTENU, COURRIER_TYPE_ACTIF]);
                    if ($model->getCount() === 0) {
                        $model->setTable(CRM_NP_COURRIER_TYPE); // Si on ne trouve pas le courrier dans la table de l'agence on le cherche dans la table courrier type commune
                    }
                    $courriel = $model->getData();

                    $_SESSION[$app]['notification']['succes'] = "";
                    if (isset($courriel[0]->actif) || $courriel[0]->actif == 1) {
                        $civilite = !empty($_SESSION[_USER_]->code_civilite) ? $lstCivilite['CIVILITE'][$_SESSION[_USER_]->code_civilite]['libelle'] : "";
                        $message = str_replace(['#civilite#', '#nom#', '#prenom#', '#password#'], [$civilite, $_SESSION[_USER_]->nom, $_SESSION[_USER_]->prenom, $password], $courriel[0]->contenu);
                        $config = JSONFileToArray($route . _STORAGE_PATH . 'agences/' . $_SESSION[_USER_]->nom_dossier . '/config.json');

                        $paramMail = prepareMail($route, $courriel[0]->objet, $message, [$config]);
                        sendMail($paramMail['prefixe_sujet'], $paramMail['expediteur'], $_SESSION[_USER_]->email, $paramMail['sujet'], $paramMail['message']);
                    } else {
                        $_SESSION[$app]['notification']['succes'] = $language['commun_courriel_texte_mail_inexistant'] . "<br />";
                    }

                    $_SESSION[$app]['notification']['succes'] .= $language['commun_notification_changement_obligatoire_password_succes'];

                    //$model->historique($server, $browser, 'CONNEXION', $get);
                    $model->redirect(HTTP_PATH . '/');
                }

                $_SESSION[$app]['notification']['erreur'] = $language['commun_notification_changement_obligatoire_password_erreur_requete'];
            }

            $_SESSION[$app]['notification']['erreur'] = $language['commun_notification_changement_obligatoire_password_erreur_password_identiques'];
        }

        break;

    case 'motdepasseoublie' : //Clic sur le lien mot de passe oublié
        $donnees = filter_input_array(INPUT_POST);
        if ($donnees !== NULL) {

            $email = $donnees['email'];

            // On vérifie si un utilisateur avec cette adresse e-mail existe. 
            $model->release();
            $model->setClause('email = "' . $email . '" AND actif = 1');
            $model->settable("crm_np_utilisateur");
            $model->setChamp("*");
            $model->setOrderBy('id');
            $user = $model->getData();

            if (count($user) > 0) {
                $expediteur = $destinataire = $user[0]->email;

                if ($user[0]->id_agence > 0) {
                    $model->release();
                    $model->setClause('id = ' . $user[0]->id_agence);
                    $model->settable("crm_np_agences");
                    $model->setChamp("*");
                    $model->setOrderBy('');
                    $infos_agence = $model->getData();
                    if ($infos_agence) {
                        $expediteur = $infos_agence[0]->email_superviseur;
                        $destinataire = $infos_agence[0]->email_superviseur;
                        $model->release();
                        $model->setClause('nom = "droits_actifs" AND valeur LIKE "%MDPOUBLIE_AUTORISATION_CHANGEMENT_PAR_USER%"');
                        $model->settable("crm_netprofil_" . $infos_agence[0]->nom_bd . ".crm_np_site_settings");
                        $model->setChamp("*");
                        $model->setOrderBy('');
                        $droit_agence = $model->getData();
                        if ($droit_agence) {
                            $genere_lien_reinitialisation = true;
                            $destinataire = $user[0]->email;
                        }
                    }
                }

                if (isset($genere_lien_reinitialisation) || ($user[0]->code_type_utilisateur == "SUPERADMINISTRATEUR")) {

                    $model->release();
                    $model->setClause('code = "REINIT_MDP_AFTER_FORGET_WITH_LINK"');
                    $model->setTable("crm_netprofil_" . $infos_agence[0]->nom_bd . "." . CRM_NP_COURRIER_TYPE);
                    $model->setChamp([COURRIER_TYPE_OBJET, COURRIER_TYPE_CONTENU, COURRIER_TYPE_ACTIF]);
                    if ($model->getCount() === 0) {
                        $model->setTable(CRM_NP_COURRIER_TYPE); // Si on ne trouve pas le courrier dans la table de l'agence on le cherche dans la table courrier type commune
                    }
                    $courriel = $model->getData();

                    $_SESSION[$app]['notification']['succes'] = "";
                    if (isset($courriel[0]->actif) || $courriel[0]->actif == 1) {
                        $civilite = !empty($user[0]->code_civilite) ? $lstCivilite['CIVILITE'][$user[0]->code_civilite]['libelle'] : "";
                        $lien_reinitialisation = HTTP_PATH . "/connexion/changement-mot-de-passe-oublie/" . base64_encode(date('Y-m-d H:i:s')) . "/ref-" . base64_encode($user[0]->id);

                        $message = str_replace(['#civilite#', '#nom#', '#prenom#', '#lien#'], [$civilite, $user[0]->nom, $user[0]->prenom, $lien_reinitialisation], $courriel[0]->contenu);
                        $config = JSONFileToArray($route . _STORAGE_PATH . 'agences/' . $infos_agence[0]->nom_bd . '/config.json');

                        $paramMail = prepareMail($route, $courriel[0]->objet, $message, [$config]);
                        sendMail($paramMail['prefixe_sujet'], $paramMail['expediteur'], $destinataire, $paramMail['sujet'], $paramMail['message']);
                    } else {
                        $_SESSION[$app]['notification']['succes'] = $language['commun_courriel_texte_mail_inexistant'] . "<br />";
                        $model->redirect(HTTP_PATH . "/connexion");
                    }

                    $message_succes = $language['commun_notification_reinitialisation_password_byuser_succes'];
                } else {

                    $model->release();
                    $model->setClause('code = "REINIT_MDP_AFTER_FORGET"');
                    $model->setTable("crm_netprofil_" . $infos_agence[0]->nom_bd . "." . CRM_NP_COURRIER_TYPE);
                    $model->setChamp([COURRIER_TYPE_OBJET, COURRIER_TYPE_CONTENU, COURRIER_TYPE_ACTIF]);
                    if ($model->getCount() === 0) {
                        $model->setTable(CRM_NP_COURRIER_TYPE); // Si on ne trouve pas le courrier dans la table de l'agence on le cherche dans la table courrier type commune
                    }
                    $courriel = $model->getData();

                    $_SESSION[$app]['notification']['succes'] = "";
                    if (isset($courriel[0]->actif) || $courriel[0]->actif == 1) {
                        $civilite = !empty($user[0]->code_civilite) ? $lstCivilite['CIVILITE'][$user[0]->code_civilite]['libelle'] : "";
                        $lien_reinitialisation = HTTP_PATH . "/connexion/changement-mot-de-passe-oublie/" . base64_encode(date('Y-m-d H:i:s')) . "/ref-" . base64_encode($user[0]->id);

                        $message = str_replace(['#civilite#', '#nom#', '#prenom#', '#login#'], [$civilite, $user[0]->nom, $user[0]->prenom, $user[0]->login], $courriel[0]->contenu);
                        $config = JSONFileToArray($route . _STORAGE_PATH . 'agences/' . $infos_agence[0]->nom_bd . '/config.json');

                        $paramMail = prepareMail($route, $courriel[0]->objet, $message, [$config]);
                        sendMail($paramMail['prefixe_sujet'], $paramMail['expediteur'], $destinataire, $paramMail['sujet'], $paramMail['message']);
                    } else {
                        $_SESSION[$app]['notification']['succes'] = $language['commun_courriel_texte_mail_inexistant'] . "<br />";
                        $model->redirect(HTTP_PATH . "/connexion");
                    }

                    $message_succes = $language['commun_notification_reinitialisation_password_succes'];
                }

                $_SESSION[$app]['notification']['succes'] .= $message_succes;

                $model->redirect(HTTP_PATH . "/connexion");
            }

            $_SESSION[$app]['notification']['erreur'] = $language['commun_notification_reinitialisation_password_erreur_email_inexistant'];

            $model->redirect(HTTP_PATH . "/connexion");
        }

        break;

    case 'changementmotdepasseoublie' : //Action de changement de mot de passe après clic sur le lien généré dans le mail
        $donnees = filter_input_array(INPUT_POST);

        if ($donnees !== NULL) {
            if ($donnees['password'] === $donnees['confirmation_password']) {

                $password = $donnees['password'];
                $donnees['password'] = md5($donnees['password']);

                unset($donnees['confirmation_password']);
                $model->release();
                $model->setTable("crm_np_utilisateur");
                $update_id = $model->updateOne($donnees);

                if ($update_id > 0) {
                    $model->setClause('id = "' . $update_id . '" AND actif = 1');
                    $model->setChamp("*");
                    $model->setOrderBy("id");
                    $user = $model->getData();
                    $_SESSION[_USER_] = $user[0];

                    if ($_SESSION[_USER_]->code_type_utilisateur == "SUPERADMINISTRATEUR") {
                        $model->setClause('');
                        $model->setOrderBy('id ASC');
                        $model->setLimit('1');
                    } elseif ($_SESSION[_USER_]->id_agence > 0) {
                        $model->setClause('id = ' . $_SESSION[_USER_]->id_agence);
                        $model->setOrderBy('');
                        $model->setLimit('');
                    }
                    $model->release();
                    $model->settable("crm_np_agences");
                    $model->setChamp("*");
                    $info_agence = $model->getData();
                    $_SESSION[_USER_]->nom_bd = 'crm_netprofil_' . $info_agence[0]->nom_bd;
                    $_SESSION[_USER_]->nom_dossier = $info_agence[0]->nom_bd;
                    $_SESSION[_USER_]->premiere_connexion = -1;

                    $avatardir = getdir($route . _STORAGE_PATH . "agences/", $_SESSION[_USER_]->nom_dossier . "/avatars/utilisateurs/");
                    $_SESSION[_USER_]->photo = (!empty($_SESSION[_USER_]->photo) && file_exists($avatardir . $_SESSION[_USER_]->photo)) ? HTTP_PATH . _STORAGE_PATH . "agences/" . $_SESSION[_USER_]->nom_dossier . "/avatars/utilisateurs/" . $_SESSION[_USER_]->photo : "";

                    // Préparation d'un courriel de réinitialisation.
                    $model->release();
                    $model->setClause('code = "REINIT_MDP_USER_BY_USER_CONFIRM"');
                    $model->setTable($_SESSION[_USER_]->nom_bd . "." . CRM_NP_COURRIER_TYPE);
                    $model->setChamp([COURRIER_TYPE_OBJET, COURRIER_TYPE_CONTENU, COURRIER_TYPE_ACTIF]);
                    if ($model->getCount() === 0) {
                        $model->setTable(CRM_NP_COURRIER_TYPE); // Si on ne trouve pas le courrier dans la table de l'agence on le cherche dans la table courrier type commune
                    }
                    $courriel = $model->getData();

                    $_SESSION[$app]['notification']['succes'] = "";
                    if (isset($courriel[0]->actif) || $courriel[0]->actif == 1) {
                        $civilite = !empty($_SESSION[_USER_]->code_civilite) ? $lstCivilite['CIVILITE'][$_SESSION[_USER_]->code_civilite]['libelle'] : "";
                        $message = str_replace(['#civilite#', '#nom#', '#prenom#', '#password#'], [$civilite, $_SESSION[_USER_]->nom, $_SESSION[_USER_]->prenom, $password], $courriel[0]->contenu);
                        $config = JSONFileToArray($route . _STORAGE_PATH . 'agences/' . $_SESSION[_USER_]->nom_dossier . '/config.json');

                        $paramMail = prepareMail($route, $courriel[0]->objet, $message, [$config]);
                        sendMail($paramMail['prefixe_sujet'], $paramMail['expediteur'], $_SESSION[_USER_]->email, $paramMail['sujet'], $paramMail['message']);
                    } else {
                        $_SESSION[$app]['notification']['succes'] = $language['commun_courriel_texte_mail_inexistant'] . "<br />";
                    }

                    $_SESSION[$app]['notification']['succes'] .= $language['commun_notification_changement_obligatoire_password_succes'];

                    //$model->historique($server, $browser, 'CONNEXION', $get);
                    if ($_SESSION[_USER_]->code_type_utilisateur == "SUPERADMINISTRATEUR") {
                        $page_destination = HTTP_PATH . '/agences/liste';
                    } else {
                        $page_destination = HTTP_PATH . '/';
                    }
                    $model->redirect($page_destination);
                }

                $_SESSION[$app]['notification']['erreur'] = $language['commun_notification_changement_obligatoire_password_erreur_requete'];
            } else {
                $_SESSION[$app]['notification']['erreur'] = $language['commun_notification_changement_obligatoire_password_erreur_password_identiques'];
            }
        } else {
            if (isset($get['dateGen']) && isset($get['id'])) {
                $idUser = base64_decode($get['id']);
                $dateGen = date_create(base64_decode($get['dateGen']));
                $dat = date_diff(date_create(date('Y-m-d H:i:s')), $dateGen);

                if (($dat->format('%d') == 0) && ($dat->format('%h') == 0) && ($dat->format('%i') < 30)) {
                    
                } else {
                    $_SESSION[$app]['notification']['erreur'] = $language['commun_notification_changement_obligatoire_password_lien_expirer'];
                    $model->redirect(HTTP_PATH . '/connexion');
                }
                $tableauDonnees['idUser'] = base64_decode($get['id']);
            }
        }
        break;

    default:



        break;
}
$oSmarty->assign('tableauDonnees', $tableauDonnees);

function getNomDomaine($url) {
    $url = str_replace('http://', '', $url);
    $tab = explode(':', $url);
    return $tab;
}
