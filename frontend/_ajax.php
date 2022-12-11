<?php

/* Inclusion des fichiers nécessaires */
require('../core/_ini.php' );

$model = new Model($dao, ''); /* Mise en marche des requêtes basiques. */

$config = JSONFileToArray($route . _STORAGE_PATH . 'agences/' . $_SESSION[_USER_]->nom_dossier . '/config.json');

$clause_for_hide_negociateur_field = ($model->connected('NEGOCIATEUR') && ($action !== 'detail')) ? " nom_champ <> 'attribue_a' AND " : "";

if ($model->connected('NEGOCIATEUR')) {

    $acces_negociateurs_chargement_documents = false;
    $acces_negociateurs_classement_par_departement = false;
    $acces_negociateurs_validation_affaires_par_defaut = false;
    $acces_acheteurs_recherches_autres_negociateurs = false;
    $acces_edition_recherches_acheteurs_autres_negociateurs = false;

    if (isset($config['site_settings']['droits_actifs']) && in_array('ACCES_ACHETEURS_RECHERCHES_AUTRES_NEGOCIATEURS', $config['site_settings']['droits_actifs'])) {
        $acces_acheteurs_recherches_autres_negociateurs = true;
    }
    if (isset($config['site_settings']['droits_actifs']) && in_array('ACCES_EDITION_RECHERCHES_ACHETEURS_AUTRES_NEGOCIATEURS', $config['site_settings']['droits_actifs'])) {
        $acces_edition_recherches_acheteurs_autres_negociateurs = true;
    }
    if (isset($config['site_settings']['droits_actifs']) && in_array('ACCES_NEGOCIATEURS_CHARGEMENT_DOCUMENTS', $config['site_settings']['droits_actifs'])) {
        $acces_negociateurs_chargement_documents = true;
    }
    if (isset($config['site_settings']['droits_actifs']) && in_array('ACCES_NEGOCIATEURS_VALIDATION_AFFAIRES_PAR_DEFAUT', $config['site_settings']['droits_actifs'])) {
        $acces_negociateurs_validation_affaires_par_defaut = true;
    }
    if (isset($config['site_settings']['droits_actifs']) && in_array('ACCES_NEGOCIATEURS_CLASSEMENT_PAR_DEPARTEMENT', $config['site_settings']['droits_actifs'])) {
        $acces_negociateurs_classement_par_departement = true;
        $departements_autorises = buildLef(get_dept_autorises($model));
    }
} else {
    $acces_negociateurs_chargement_documents = true;
    $acces_negociateurs_classement_par_departement = false;
    $acces_acheteurs_recherches_autres_negociateurs = true;
    $acces_negociateurs_validation_affaires_par_defaut = true;
    $acces_edition_recherches_acheteurs_autres_negociateurs = true;
}

$assigned['acces_negociateurs_chargement_documents'] = $acces_negociateurs_chargement_documents;

if (isset($get['action'])) {
    switch ($get['action']) {
        case "loadAgency":
            if ((int) $id > 0) {
                $model->setClause('id = ' . $id);
                $model->setLimit('1');
                $model->settable(CRM_NP_AGENCES);
                $model->setChamp("*");
                $agence = $model->getData();
                if (count($agence)) {
                    $_SESSION[_USER_]->id_agence = $id;
                    $_SESSION[_USER_]->nom_bd = 'crm_netprofil_' . $agence[0]->nom_bd;
                    $_SESSION[_USER_]->nom_dossier = $agence[0]->nom_bd;
                    $_SESSION[_USER_]->nom_agence = $agence[0]->raison_sociale;
                    $_SESSION[$app]['notification']['succes'] = "L'agence &laquo;" . $agence[0]->raison_sociale . "&raquo; a été chargée avec succès vous pouvez maintenant la configurer.";
                    echo json_encode([1]);
                    exit();
                }
            }
            echo json_encode([-1, "Impossible de charger les données de cette agence."]);
            exit();
            break;
        case "uploadFileUppy":
            if ($acces_negociateurs_chargement_documents === false) {
                echo json_encode([-1, "Impossible de charger ce documents. Vous n'êtes pas autorisé à charger des documents."]);
                exit();
            }

            $info = pathinfo($_FILES['files']['name'][0]);

            $name = gen_title_filter($info['filename'], false) . '.' . $info['extension'];

            $docpathname = getdir($route . _STORAGE_PATH, "agences/" . $_SESSION[_USER_]->nom_dossier . "/documents/" . $get['from'] . "/" . $get['id'] . "/");
            $target = $docpathname . $name;

            if (move_uploaded_file($_FILES['files']['tmp_name'][0], $target)) {
                $docname = HTTP_PATH . _STORAGE_PATH . 'agences/' . $_SESSION[_USER_]->nom_dossier . '/documents/' . $get['from'] . '/' . $get['id'] . '/' . $name;
                $docs = ['extension' => $info['extension'], 'name' => $info['filename'], 'tname' => troncate($info['filename'], 45), 'fullname' => $docname, 'size' => getFileSize($target)];
                $oSmarty->assign('docs', $docs);

                $vue = "/modules/canvas/documents/_inc/_inc_doc_list_model.tpl";
                $oSmarty->display($template . $vue);
            } else {
                echo json_encode([-1, "Impossible de charger ce documents. Veuillez contacter l'administrateur."]);
            }

            exit();
            break;
        case "removeUploadedFileUppy":
            if ($acces_negociateurs_chargement_documents === false) {
                echo json_encode([-1, "Impossible de charger ce documents. Vous n'êtes pas autorisé à modifier des documents."]);
                exit();
            }

            $aFilnames = json_decode($get['filename'], true);
            if (is_null($aFilnames)) {
                $aFilnames[] = $get['filename'];
            }

            $deleted = [];
            foreach ($aFilnames as $filename) {
                $info = pathinfo($filename);
                $name = gen_title_filter($info['filename'], false) . '.' . $info['extension'];
                $docpathname = getdir($route . _STORAGE_PATH, "agences/" . $_SESSION[_USER_]->nom_dossier . "/documents/" . $get['from'] . "/" . $get['id'] . "/");
                $target = $docpathname . $name;

                if (unlink($target)) {
                    $deleted[] = $info['filename'];
                }
            }

            if (count($deleted) > 0) {

                if (is_null($aFilnames)) {
                    echo json_encode(['success', 'La suppression a réussi !', $info['filename']]);
                } else {
                    echo json_encode(['success', 'La suppression a réussi !', $deleted]);
                }
            } else {
                echo json_encode(['error', 'La suppression a échoué !']);
            }

            exit();
            break;
        case "copyDocs":
        case "moveDocs":
            if ($acces_negociateurs_chargement_documents === false) {
                echo json_encode([-1, "Impossible de charger ce documents. Vous n'êtes pas autorisé à modifier des documents."]);
                exit();
            }
            $aFilnames = json_decode($get['filename'], true);
            $items = json_decode($get['items'], true);

            $moved = [];
            $uncopied = 0;
            $copied = 0;

            foreach ($aFilnames as $filename) {

                $info = pathinfo($filename);
                $docpathnamefrom = getdir($route . _STORAGE_PATH, "agences/" . $_SESSION[_USER_]->nom_dossier . "/documents/" . $get['from'] . "/" . $get['id'] . "/");
                $name = gen_title_filter($info['filename'], false) . '.' . $info['extension'];
                $from = $docpathnamefrom . $name;

                foreach ($items as $item) {
                    $docpathname = getdir($route . _STORAGE_PATH, "agences/" . $_SESSION[_USER_]->nom_dossier . "/documents/" . $get['from'] . "/" . $item . "/");
                    $target = $docpathname . $name;

                    if (copy($from, $target)) {
                        $copied = 1;
                    } else {
                        $uncopied++;
                    }
                }
                if ($get['action'] === "moveDocs" && $copied === 1) {
                    if (unlink($from)) {
                        $moved[] = $info['filename'];
                    }
                }
            }

            if ($get['action'] === "moveDocs") {
                if (count($moved) > 0) {
                    echo json_encode(['success', 'Le déplacement a réussi !', $moved]);
                } else {
                    echo json_encode(['error', 'Le déplacement a échoué !']);
                }
            } else {
                if ($uncopied === count($aFilnames)) {
                    echo json_encode(['error', 'La copie a échoué !']);
                } else {
                    echo json_encode(['success', 'La copie a réussi !']);
                }
            }

            exit();
            break;
        case "dataBySourceData":
            if (!empty($_source)) {
                if (!in_array("crm_np_liste_element_form", explode('.', $_source))) {
                    $listeTableCommune = getBaseTables(S_DB);
                    $datas = (in_array($_source, $listeTableCommune)) ? getSchema($_source, S_DB) : getSchema($_source, $_SESSION[_USER_]->nom_bd);
                } else {
                    $model->release();
                    $model->setTable($_source);
                    $model->setChamp("DISTINCT(type) as type");
                    //$model->setClause("actif = 1 AND SUBSTR(type, 1, 1) <> '_'");
                    $model->setClause("actif = 1");

                    $datas = $model->fillArrays($model->getData(), "type", "type");
                }
                if (count($datas)) {
                    $option = "<option label='Table $_source'></option>";
                    foreach ($datas as $k => $v) {
                        $option .= "<option value='$k'>$v</option>";
                    }
                    echo json_encode($option);
                    exit();
                }
            }
            echo json_encode([-1, "Impossible de charger les données de cette table."]);
            exit();
            break;
        case "userByTable":

            if (!empty($_source)) {

                $tables = [
                    "acheteur" => ["table" => $_SESSION[_USER_]->nom_bd . ".crm_np_acheteur", "fields" => "id, CONCAT(prenom, ' ', nom) AS input_val", "clause" => "archive = -1"],
                    "affaire" => ["table" => $_SESSION[_USER_]->nom_bd . ".crm_np_affaire", "fields" => "id, reference AS input_val", "clause" => "actif = -1"],
                    "contact" => ["table" => $_SESSION[_USER_]->nom_bd . ".crm_np_contact", "fields" => "id, CONCAT(prenom, ' ', nom) AS input_val", "clause" => "actif = 1"],
                    "recherche_acheteur" => ["table" => $_SESSION[_USER_]->nom_bd . ".crm_np_acheteur_recherche", "fields" => "id, reference AS input_val", "clause" => "archive = -1"],
                    "vendeur" => ["table" => $_SESSION[_USER_]->nom_bd . ".crm_np_affaire_vendeur", "fields" => "id, CONCAT(prenom, ' ', nom) AS input_val", "1=1"],
                    "utilisateur" => ["table" => CRM_NP_UTILISATEUR, "fields" => "id, CONCAT(prenom, ' ', nom) AS input_val", "clause" => "actif = 1"]
                ];

                $_tsource = strtolower(str_replace(" ", "_", $_source));

                if (isset($tables[$_tsource])) {

                    $model->release();
                    $model->setTable($tables[$_tsource]["table"]);
                    $model->setChamp($tables[$_tsource]["fields"]);
                    $model->setClause($tables[$_tsource]["clause"]);
                    $datas = $model->getData();
                    if (count($datas)) {
                        $option = "<option value='' disabled selected></option>";
                        foreach ($datas as $k => $v) {
                            $option .= "<option value=" . $v->id . ">" . $v->input_val . "</option>";
                        }
                        echo json_encode($option);
                        exit();
                    }
                    // echo json_encode([-1, "Requete."]);
                    exit();
                }

                // echo json_encode([-1, "Le type que vous avez saisi est incorrect."]);
                exit();
            }
            echo json_encode([-1, "Impossible de charger les données de cette table."]);
            exit();
            break;
        case "loadVillesFromDeptForResearch":

            extract($get);
            $villes = [];
            if (isset($departements) && count($departements)) {
                foreach ($departements as $departement) {
                    $villes = array_merge($villes, $_SESSION[$app][$module]['index']['villes_recherche'][$departement]);
                }
            } else {
                foreach ($_SESSION[$app][$module]['index']['villes_recherche'] as $ville) {
                    $villes = array_merge($villes, $ville);
                }
            }

            $contenu = '';
            if (count($villes)) {
                $contenu .= '<option value="">Sélectionner une ville</option>';
                foreach ($villes as $ville) {
                    $contenu .= '<option value=' . $ville['code'] . '>(' . $ville['num_departement'] . ') ' . $ville['libelle'] . '</option>';
                }
            } else {
                $contenu .= '<option value="">Aucune ville trouvée</option>';
            }

            echo $contenu;
            exit();
            break;
        case "loadVillesFromDeptForResearchFromBD":

            extract($get);

            $contenu = '';

            if (isset($departements) && is_array($departements) && count($departements)) {
                $model->release();
                $model->setTable('crm_np_ville');
                $clause = 'num_departement IN ("' . implode('", "', $departements) . '")';

                $model->setClause($clause);
                $model->setChamp("id, nom_reel, code_postal, num_departement");
                $model->setOrderBy("nom_reel");
                $villes = $model->getData();

                if (count($villes)) {
                    $contenu .= '<option value="">Sélectionner une ville</option>';
                    foreach ($villes as $ville) {
                        $contenu .= '<option value=' . $ville->id . '>(' . $ville->num_departement . ') ' . $ville->nom_reel . '</option>';
                    }
                } else {
                    $contenu .= '<option value="">Aucune ville trouvée</option>';
                }
            } else {
                $contenu .= '<option value="">Choisir dans la liste</option>';
            }

            echo $contenu;
            exit();
            break;
        case "userByTable":

            if (!empty($_source)) {

                $tables = [
                    "acheteur" => ["table" => $_SESSION[_USER_]->nom_bd . ".crm_np_acheteur", "fields" => "id, CONCAT(prenom, ' ', nom) AS input_val", "clause" => "archive = -1"],
                    "affaire" => ["table" => $_SESSION[_USER_]->nom_bd . ".crm_np_affaire", "fields" => "id, reference AS input_val", "clause" => "actif = -1"],
                    "contact" => ["table" => $_SESSION[_USER_]->nom_bd . ".crm_np_contact", "fields" => "id, CONCAT(prenom, ' ', nom) AS input_val", "clause" => "actif = 1"],
                    "recherche_acheteur" => ["table" => $_SESSION[_USER_]->nom_bd . ".crm_np_acheteur_recherche", "fields" => "id, reference AS input_val", "clause" => "archive = -1"],
                    "vendeur" => ["table" => $_SESSION[_USER_]->nom_bd . ".crm_np_affaire_vendeur", "fields" => "id, CONCAT(prenom, ' ', nom) AS input_val", "1=1"],
                    "utilisateur" => ["table" => CRM_NP_UTILISATEUR, "fields" => "id, CONCAT(prenom, ' ', nom) AS input_val", "clause" => "actif = 1"]
                ];

                $_tsource = strtolower(str_replace(" ", "_", $_source));

                if (isset($tables[$_tsource])) {

                    $model->release();
                    $model->setTable($tables[$_tsource]["table"]);
                    $model->setChamp($tables[$_tsource]["fields"]);
                    $model->setClause($tables[$_tsource]["clause"]);
                    $datas = $model->getData();
                    if (count($datas)) {
                        $option = "<option value='' disabled selected></option>";
                        foreach ($datas as $k => $v) {
                            $option .= "<option value=" . $v->id . ">" . $v->input_val . "</option>";
                        }
                        echo json_encode($option);
                        exit();
                    }
                    // echo json_encode([-1, "Requete."]);
                    exit();
                }

                // echo json_encode([-1, "Le type que vous avez saisi est incorrect."]);
                exit();
            }
            echo json_encode([-1, "Impossible de charger les données de cette table."]);
            exit();
            break;
        case "loadOtherSignInformations":

            if (!empty($_id) && (int) $_id > 0) {

                $model->release();
                $model->setTable($_SESSION[_USER_]->nom_bd . '.crm_np_affaire');
                $model->setChamp("code_type_transaction");
                $model->setClause('id = ' . $_id);
                $ctt = $model->getData();
                if (count($ctt)) {
                    echo json_encode([$ctt[0]->code_type_transaction]);
                    exit();
                }
            }
            echo json_encode([-1, "Impossible de charger les données de cette affaire."]);
            exit();
            break;
        case "recordSignature":
            $column = (array) json_decode($datas);
            if (iseditable($model, $column['id_acheteur_recherche'], $acces_acheteurs_recherches_autres_negociateurs) === false) {
                echo json_encode([-1, "Vous ne avez pas accès aux informations cette recherche."]);
                exit();
            }
            if (iseditable($model, $column['id_acheteur_recherche'], $acces_edition_recherches_acheteurs_autres_negociateurs) === false) {
                echo json_encode([-1, "Vous ne pouvez pas modifier cette recherche."]);
                exit();
            }
            $champsAControler = [
                'EMPTY' => ['id_affaire', 'date_signature', 'id_acheteur_recherche', 'prix_reel_ht', 'prix_reel_ttc']
            ];
            $erreur = $model->SaisieControle($column, $champsAControler);
            if ($erreur[0] == true) {
                echo json_encode([-1, $erreur[1] . " (" . $erreur[2] . ")"]);
                exit();
            }

            if (isset($column['id']) && (int) $column['id'] > 0) {
                $message = "La signature a été  mise à jour avec succès ! ";
                $model->release();
                $model->setTable($_SESSION[_USER_]->nom_bd . '.crm_np_acheteur_recherche_signature');
                $model->setChamp('id_affaire');
                $model->setClause('id = ' . $column['id']);
                $old_affaire = $model->getData();
            } else {
                $message = "La signature a été enregistrée avec succès ! ";
                $column['date_creation'] = date('Y-m-d H:i:s');
            }

            $column['date_modification'] = date('Y-m-d H:i:s');
            $model->release();
            $model->setTable($_SESSION[_USER_]->nom_bd . '.crm_np_acheteur_recherche_signature');
            $returned = $model->record($column);

            if ($returned > 0) {
                // On met a jour la recherche concrernee
                $recherche["id"] = $column['id_acheteur_recherche'];
                $recherche["code_raison_archivage_acheteur"] = "SIGNATURE";
                $recherche["archive"] = 1;

                $model->release();
                $model->setTable($_SESSION[_USER_]->nom_bd . '.crm_np_acheteur_recherche');
                $model->record($recherche);

                // On met a jour l'affaire concernee
                $affaire["actif"] = -1;
                $affaire["id"] = $column['id_affaire'];
                $affaire["raison_archivage"] = "VENDUE";

                $model->release();
                $model->setTable($_SESSION[_USER_]->nom_bd . '.crm_np_affaire');
                $model->record($affaire);

                // s'il arrive l'affaire concernee soit changee, on met a jour l'ancienne affaire comme non vendue
                if (isset($old_affaire[0]->id_affaire) && $old_affaire[0]->id_affaire != $column['id_affaire']) {
                    $affaire_old["actif"] = 1;
                    $affaire_old["id"] = $old_affaire[0]->id_affaire;
                    $affaire_old["raison_archivage"] = null;

                    $model->record($affaire_old);
                }

                echo json_encode([1, $message]);
                exit();
            }

            echo json_encode([-1, $language['commun_msg_erreur_exec_requete']]);
            exit();
            break;
        default:
            echo json_encode([-1, $language['commun_msg_action_non_autorisee']]);
            exit();
            break;
    }
}

/* * ***** Début Réellement utilisé *********** */

if (isset($get['module']) && (isset($get['source']) && $get['source'] == "loadVilleParcpordept")) {

    extract($get);
    $clause = '1=1';
    $clause .= (isset($cp)) ? ' AND code_postal LIKE "%' . $cp . '%" ' : '';
    $clause .= (isset($num)) ? ' AND num_departement = "' . $num . '" ' : '';

    if (isset($departements_autorises)) {
        $clause .= ' AND num_departement IN ("' . implode('","', $departements_autorises) . '") ';
    }

    $model->setChamp("id, nom_reel, code_postal");
    $model->setTable('crm_np_ville');
    $model->setClause($clause);
    $model->setOrderBy('nom_reel');
    $model->setLimit(500);

    $villes = $model->getData();

    $contenu = '';

    if (count($villes)) {
        if (isset($allowEmpty)) {
            $contenu .= '<option value=""></option>';
        }
        foreach ($villes as $ville) {
            $cp = substr($ville->code_postal, 0, 5);
            $contenu .= '<option value=' . $ville->id . '#' . $cp . '>' . $ville->nom_reel . ' (' . $ville->code_postal . ')</option>';
        }
    } else {
        $contenu .= '<option value="">Aucune ville trouvée</option>';
    }

    echo $contenu;
    exit();
}

if (isset($get['source']) && $get['source'] == "affaireCompta") {

    extract($get);
    $donnees["valeur"] = $valeurChamp;

    $model->setTable($_SESSION[_USER_]->nom_bd . '.crm_np_affaire_donnees_comptables_champs_valeur');
    $model->setClause("id_affaire = " . $id_affaire . " AND id_champ = " . $id_champ . " AND colonne = '" . $colonne . "'");
    $nb_record = $model->recordExists(true);

    if ($nb_record > 0) {
        $model->updateOne($donnees, "id_champ");
    } else {
        $donnees["id_affaire"] = $id_affaire;
        $donnees["id_champ"] = $id_champ;
        $donnees["colonne"] = $colonne;
        $model->insertOne($donnees, "id_champ");
    }

    exit();
}

if (isset($get['module']) && (isset($get['source']) && $get['source'] == "changeStatutTache")) {

    extract($get);

    $donnees["id"] = $idTache;
    $donnees["code_statut_tache"] = $newStatut;

    $model->setTable($_SESSION[_USER_]->nom_bd . '.crm_np_tache');
    $idTache = $model->updateOne($donnees);

    $succes = 'Statut changé !';
    $oSmarty->assign('succes', $succes);
    $oSmarty->display($route . '/templates/backend/' . _version . "/common/_notification.tpl");

    exit();
}

if (isset($get['module']) && (isset($get['source']) && $get['source'] == "chargetemp")) {

    extract($get);

    $source = $route . '/templates/backend/' . _version . "/modules/" . $module . '/inc/' . $temp . '.tpl';

    $oSmarty->display($source);

    exit();
}

if (isset($get['module']) && (isset($get['source']) && $get['source'] == "changeActionTache")) {

    extract($get);

    $donnees["id"] = $idTache;
    $donnees["code_type_action_tache"] = $newStatut;

    $model->setTable($_SESSION[_USER_]->nom_bd . '.crm_np_tache');
    $idTache = $model->updateOne($donnees);

    $succes = 'Action changée !';
    $oSmarty->assign('succes', $succes);
    $oSmarty->display($route . '/templates/backend/' . _version . "/common/_notification.tpl");

    exit();
}

if (isset($get['module']) && (isset($get['source']) && $get['source'] == "changePriotiteTache")) {

    extract($get);

    $donnees["id"] = $idTache;
    $donnees["code_priorite_tache"] = $newPriorite;

    $model->setTable($_SESSION[_USER_]->nom_bd . '.crm_np_tache');
    $idTache = $model->updateOne($donnees);

    $succes = 'Statut changé !';
    $oSmarty->assign('succes', $succes);
    $oSmarty->display($route . '/templates/backend/' . _version . "/common/_notification.tpl");

    exit();
}

if (isset($get['module']) && (isset($get['source']) && $get['source'] == "deleteConcerneFromTache")) {

    extract($get);

    $model->setTable($_SESSION[_USER_]->nom_bd . '.crm_np_tache_concerne');
    $model->setClause('id=' . $idConcerne);

    $model->deleteData();
    $succes = 'Ligne suprimée !';
    $oSmarty->assign('succes', $succes);
    $oSmarty->display($route . '/templates/backend/' . _version . "/common/_notification.tpl");

    echo $contenu;
    exit();
}

if (isset($get['module']) && (isset($get['source']) && $get['source'] == "tacheConcerne")) {

    extract($get);
    $contenu = '';
    switch ($sources) {
        // case 'AFFAIRE':
        //     $model->setChamp("id, rcs, raison_sociale");
        //     $model->setTable('crm_np_agences');
        //     $model->setClause("");
        //     $model->setOrderBy('id');
        //     $model->setLimit(500);
        //     $concerne = $model->getData();
        //     if (count($concerne)) {
        //         if (isset($allowEmpty)) {
        //             $contenu .= '<option value=""></option>';
        //         }
        //         foreach ($concerne as $cons) {
        //             $cp = substr($cons->code_postal, 0, 5);
        //             $contenu .= '<option value=' . $sources . '#' . $cons->id . '>' . $cons->raison_sociale . ' (' . $cons->rcs . ')</option>';
        //         }
        //     } else {
        //         $contenu .= '<option>Aucune donnée trouvée</option>';
        //     }
        //     break;
        case 'CONTACT':
            $model->setChamp("id, nom, prenom");
            $model->setTable($_SESSION[_USER_]->nom_bd . '.crm_np_contact');
            $model->setClause("");
            $model->setOrderBy('id');
            $model->setLimit(500);
            $concerne = $model->getData();

            if (count($concerne)) {
                if (isset($allowEmpty)) {
                    $contenu .= '<option value=""></option>';
                }
                foreach ($concerne as $cons) {
                    $cp = substr($cons->code_postal, 0, 5);
                    $contenu .= '<option value=' . $sources . '#' . $cons->id . '>' . $cons->prenom . ' ' . $cons->nom . '</option>';
                }
            } else {
                $contenu .= '<option>Aucune donnée trouvée</option>';
            }
            break;
        case 'ACHETEURRECHERCHE':
            $model->setChamp("a.id, a.nom, a.prenom, ar.reference");
            $model->setTable($_SESSION[_USER_]->nom_bd . '.crm_np_acheteur a,' . $_SESSION[_USER_]->nom_bd . '.crm_np_acheteur_recherche ar');
            $model->setClause("a.id=ar.id");
            $model->setOrderBy('id');
            $model->setLimit(500);
            $concerne = $model->getData();

            if (count($concerne)) {
                if (isset($allowEmpty)) {
                    $contenu .= '<option value=""></option>';
                }
                foreach ($concerne as $cons) {
                    $cp = substr($cons->code_postal, 0, 5);
                    $contenu .= '<option value=' . $sources . '#' . $cons->id . '>' . $cons->reference . ' (' . $cons->prenom . ' ' . $cons->prenom . ')</option>';
                }
            } else {
                $contenu .= '<option>Aucune donnée trouvée</option>';
            }
            break;

        case 'VENDEUR':
            $model->setChamp("id, nom, prenom");
            $model->setTable($_SESSION[_USER_]->nom_bd . '.crm_np_affaire_vendeur');
            $model->setClause("");
            $model->setOrderBy('id');
            $model->setLimit(500);
            $concerne = $model->getData();

            if (count($concerne)) {
                if (isset($allowEmpty)) {
                    $contenu .= '<option value=""></option>';
                }
                foreach ($concerne as $cons) {
                    $contenu .= '<option value=' . $sources . '#' . $cons->id . '> (' . $cons->nom . ' ' . $cons->prenom . ')</option>';
                }
            } else {
                $contenu .= '<option>Aucune donnée trouvée</option>';
            }
            break;

        case 'AFFAIRE':
            $model->setChamp("id, reference, code_type_transaction, cp");
            $model->setTable($_SESSION[_USER_]->nom_bd . '.crm_np_affaire');
            $model->setClause("actif=1");
            $model->setOrderBy('id');
            $model->setLimit(500);
            $concerne = $model->getData();

            if (count($concerne)) {
                if (isset($allowEmpty)) {
                    $contenu .= '<option value=""></option>';
                }
                foreach ($concerne as $cons) {
                    $cp = getDeptByCp($cons->cp);
                    var_dump($cp);
                    $contenu .= '<option value=' . $sources . '#' . $cons->id . '>' . $cons->reference . '-' . $cons->code_type_transaction . ' ' . $cons->prenom . ')</option>';
                }
            } else {
                $contenu .= '<option>Aucune donnée trouvée</option>';
            }
            break;

        case 'UTILISATEUR':
            $model->setChamp("id, nom, prenom, code_civilite");
            $model->setTable('crm_np_utilisateur');
            $model->setClause("id_agence=" . $_SESSION[_USER_]->id_agence);
            $model->setOrderBy('nom');
            $model->setLimit(500);
            $concerne = $model->getData();

            if (count($concerne)) {
                if (isset($allowEmpty)) {
                    $contenu .= '<option value=""></option>';
                }
                foreach ($concerne as $cons) {
                    $contenu .= '<option value=' . $sources . '#' . $cons->id . '>' . $cons->nom . ' ' . $cons->prenom . ' (' . $cons->code_civilite . ')</option>';
                }
            } else {
                $contenu .= '<option>Aucune donnée trouvée</option>';
            }
            break;
    }



    echo $contenu;
    exit();
}

if (isset($get['module']) && (isset($get['source']) && $get['source'] == "loadVilleMultiple")) {

    extract($get);
    $clause = '1=1';
    $clause .= (isset($cp)) ? ' AND nom_reel LIKE "%' . $ville . '%" ' : '';

    $model->setChamp("id, nom_reel, code_postal");
    $model->setTable('crm_np_ville');
    $model->setClause($clause);
    $model->setOrderBy('nom_reel');
    $model->setLimit(500);

    $villes = $model->getData();

    $contenu = '';

    if (count($villes)) {
        if (isset($allowEmpty)) {
            $contenu .= '<option value=""></option>';
        }
        foreach ($villes as $ville) {
            $contenu .= '<option value=' . $ville->id . '>' . $ville->nom_reel . ' (' . $ville->code_postal . ')</option>';
        }
    } else {
        $contenu .= '<option>Aucune ville trouvée</option>';
    }

    echo $contenu;
    exit();
}

if (isset($get['module']) && (isset($get['source']) && $get['source'] == "checkmail")) {

    extract($get);
    $clause = '1=1';
    $clause .= (isset($email)) ? ' AND email="' . $email . '"' : '';

    $model->setChamp("*");
    $model->setTable($_SESSION[_USER_]->nom_bd . '.crm_np_acheteur');
    $model->setClause($clause);
    $model->setOrderBy('');

    $email = $model->getData();

    $contenu = '';

    if (count($email)) {
        $model->setChamp("*");
        $model->setTable($_SESSION[_USER_]->nom_bd . '.crm_np_acheteur_recherche');
        $model->setClause("id_acheteur=" . $email[0]->id);
        $model->setOrderBy('');
        $search = $model->getData();
        $searchLink = "";
        $searchLinks = "";
        if (count($search) > 0) {
            $searchLink = "auteur des recherches de Référence(s) - ";
            foreach ($search as $rech) {
                $searchLink .= '<a href="' . HTTP_PATH . '/acheteursrecherche/detail/' . base64_encode($rech->id) . '" class="alert-link" title="Fiche de la recherche." target="_blank">' . $rech->reference . '</a>,';

                $searchLinks .= '<a href="' . HTTP_PATH . '/acheteursrecherche/modification/' . base64_encode($email[0]->id) . '/' . base64_encode($rech->id) . '" class="alert-link" title="Fiche de la recherche." target="_blank">' . $rech->reference . '</a>,';
            }
        }

        $contenu .= '<div class="alert alert-info alert-dismissible fade show" role="alert"><p><strong>Attention !</strong> Cette adresse email est déjà rattachée à l\'acheteur ' . $email[0]->prenom . ' ' . $email[0]->nom . ' ' . $searchLink . ' <br>Si vous souhaitez lui créer une nouvelle recherche, vous pouvez continuer la saisie, sinon vous cliquez ci-dessous sur un acheteur déjà existante que vous souhaitez modifier : <br>
                <strong>-Réfèrence:</strong> <a href="' . HTTP_PATH . '/acheteursrecherche/ajout/' . base64_encode($email[0]->id) . '" class="alert-link" title="Modificer l\'acheteur.">' . $searchLinks . '</a><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></p></div>';
    } else {
        $contenu .= '';
    }

    echo $contenu;
    exit();
}

if (isset($get['module']) && (isset($get['source']) && $get['source'] == "acheteurExiste")) {

    extract($get);
    $clause = '1=1';
    $clause .= (isset($email)) ? ' AND email="' . $email . '"' : '';

    $model->setChamp("*");
    $model->setTable($_SESSION[_USER_]->nom_bd . '.crm_np_acheteur');
    $model->setClause($clause);
    $model->setOrderBy('');

    $email = $model->getData();

    $contenu = '';

    if (count($email) > 0) {
        $contenu .= '<div class="alert alert-info alert-dismissible fade show" role="alert"><p><strong>Attention !</strong> Cette adresse email est déjà rattachée à l\'acheteur ' . $email[0]->prenom . ' ' . $email[0]->nom . '.<br>Si vous souhaitez l\'ajouter comme associé cliquer sur le lien ci-dessous, Ou bien bien vous pouvez continuer la saisie avec un autre adresse email : <br>
                <strong>-Definir comme Associé:</strong> <a href="' . HTTP_PATH . '/acheteursrecherche/setassocier/' . $idrech . '/' . $email[0]->id . '" class="alert-link" title="Modificer l\'acheteur.">' . $email[0]->prenom . ' ' . $email[0]->nom . '</a><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></p></div>';
    } else {
        $contenu .= '';
    }

    echo $contenu;
    exit();
}

if (isset($get['module']) && (isset($get['source']) && $get['source'] == "typeFamile")) {

    extract($get);
    $model->release();
    $model->setTable($_SESSION[_USER_]->nom_bd . '.crm_np_affaire_vis_famille_champ');
    $model->setClause("utilise_pour='" . $values . "'");
    $model->setOrderBy('rang ASC');
    $model->setChamp('*');
    $lstchampFamille = (array) $model->getData();

    $model->release();
    $model->setTable($_SESSION[_USER_]->nom_bd . '.crm_np_affaire_vis_champ');
    $model->setClause('');
    $model->setOrderBy('');
    $model->setChamp('*');
    $lstchamp = (array) $model->getData();

    $assigned['lstchamp'] = $lstchamp;
    $assigned['lstchampFamille'] = $lstchampFamille;
    $oSmarty->assign('assigned', $assigned);
    $oSmarty->assign('reloaded', "yes");
    $oSmarty->display($template . '/modules/' . $module . '/' . 'inc_liste_champs.tpl');
    exit();
}

/* Recherche enseigne ou raison sociale à la saisie du RCS */
if (isset($get['rcs']) && (!empty($get['rcs']))) {
    $rcs = str_replace(" ", "", $get['rcs']);
    $tab_raison_sociale = Array();
    $enseigne = "";
    if ((preg_match('#^[0-9]*$#', $rcs)) && (strlen(trim($rcs)) == 9)) {
        $tab_raison_sociale = checkRCS($rcs);
        $enseigne = "";
        if (!empty($tab_raison_sociale['enseigne'])) {
            $enseigne = $tab_raison_sociale['enseigne'];
        } elseif (!empty($tab_raison_sociale['denomination'])) {
            $enseigne = $tab_raison_sociale['denomination'];
        }
    }
    echo $enseigne;
}

/* Genere nom base de données */
if (isset($get['store']) && (!empty($get['store']))) {
    echo (isset($gentitle) && (int) $gentitle === 1) ? gen_title_filter($store, false, false, 'lower') : $store;
    exit();
}

/* * ***** Fin Réellement utilisé*********** */

if (isset($get['module']) && isset($get['source']) && $get['source'] == "SelectPertinance") {
    $app = "frontend";
    if (isset($get['values'])) {
        switch ($get['values']) {
            case '1':
                $_SESSION[$app][$module]['trie'] = "hav.prix ASC";
                break;
            case '2':
                $_SESSION[$app][$module]['trie'] = "hav.prix DESC";
                break;
            default:
                $_SESSION[$app][$module]['trie'] = "hav.date_creation DESC";
                break;
        }
    }
    exit();
}

if (isset($get['module']) && (isset($get['source']) && $get['source'] == "addNewLineVille")) {

    $oSmarty->assign('num', $get['num']);
    $oSmarty->assign('module', $get['module']);
    $oSmarty->display($route . '/templates/backend/' . _version . "/modules/" . $get['module'] . "/_inc_set_new_ville.tpl");
    exit();
}

if (isset($get['module']) && (isset($get['source']) && $get['source'] == "addNewLineConcerne")) {
    $elemForm = $model->getElementType(['SOURCE_CONCERNE']);
    $oSmarty->assign('num', $get['num']);
    $oSmarty->assign('sources_cons', $elemForm['SOURCE_CONCERNE']);
    $oSmarty->assign('module', $get['module']);
    $oSmarty->display($route . '/templates/backend/' . _version . "/modules/" . $get['module'] . "/_inc_new_concerne_line.tpl");
    exit();
}

if (isset($get['module']) && (isset($get['source']) && $get['source'] == "loadVilleParcpordepts")) {

    extract($get);
    $clause = '1=1';
    $clause .= (isset($cp)) ? ' AND code_postal LIKE "%' . $cp . '%" ' : '';
    $clause .= (isset($num)) ? ' AND num_departement = "' . $num . '" ' : '';

    if (isset($departements_autorises)) {
        $clause .= ' AND num_departement IN ("' . implode('","', $departements_autorises) . '") ';
    }

    $model->setChamp("id, nom_reel, code_postal");
    $model->setTable(_NOM_CRMDEV_TBL_VILLE);
    $model->setClause($clause);
    $model->setOrderBy('nom_reel');
    $model->setLimit(500);

    $villes = $model->getData();

    $contenu = '';

    if (count($villes)) {
        if (isset($allowEmpty)) {
            $contenu .= '<option value=""></option>';
        }
        foreach ($villes as $ville) {
            $contenu .= '<option value=' . $ville->id . '>' . $ville->nom_reel . ' (' . $ville->code_postal . ')</option>';
        }
    } else {
        $contenu .= '<option value="">Aucune ville trouvée</option>';
    }

    echo $contenu;
    exit();
}

if (isset($get['action']) && ($get['action'] == 'ajouteraupanier')) {

    extract($get);

    $succes = $erreur = "";
    if ($idAgence > 0) {

        $donnees['id_agence_a_vendre'] = $idAgence;
        $donnees['id_candidat'] = $_SESSION[_USER_]->id;

        $clause = "id_candidat = " . $_SESSION[_USER_]->id . " AND id_agence_a_vendre = " . $idAgence;
        $model->setChamp('*');
        $model->setTable(HE_CANDIDAT_AGENCE_A_VENDRE_INTERESSEE);
        $model->setClause($clause);
        $resultat = $model->record($donnees);

        if ($resultat == -2) {
            $erreur = 'Cette agence a déjà été ajoutée dans votre panier !';
        } else {
            $succes = 'Cette agence a été ajoutée dans votre panier !';
        }
    } else {
        $erreur = 'Fiche non valide !';
    }

    $oSmarty->assign('succes', $succes);
    $oSmarty->assign('erreur', $erreur);
    $oSmarty->display($route . '/templates/frontend/' . _version . "/common/_notification.tpl");
    exit();
}

if (isset($get['action']) && ($get['action'] == 'emptyallsearchsession')) {
    extract($get);
    $app = "frontend";
    if (isset($_SESSION[$app][$module]['search'])) {
        //$_SESSION[$app][$module]['search'] = [];
        unset($_SESSION[$app][$module]['search']);
    }
    exit();
}

/* * Réellement utilisé dans le projet agence à vendre */



/* Estimer agence affichage activites choisies */
if (isset($get['estimer_agence_activites_choisies'])) {

    $estimer_agence_activites_choisies = json_decode($get['estimer_agence_activites_choisies']);

    if (is_array($estimer_agence_activites_choisies)) {
        if (in_array(83, $estimer_agence_activites_choisies)) {
            $transaction = "oui";
            $oSmarty->assign("transaction", $transaction);
        }
        if (in_array(84, $estimer_agence_activites_choisies)) {
            $location = "oui";
            $oSmarty->assign("location", $location);
        }
        if (in_array(85, $estimer_agence_activites_choisies)) {
            $gestion_locative = "oui";
            $oSmarty->assign("gestion_locative", $gestion_locative);
        }
        if (in_array(86, $estimer_agence_activites_choisies)) {
            $syndic_copropriete = "oui";
            $oSmarty->assign("syndic_copropriete", $syndic_copropriete);
        }
    }

    $oSmarty->display($route . '/templates/frontend/' . _version . "/modules/estimervotreagence/estimer-agence-choix-activites.tpl");
}

if (isset($get['action']) && ($get['action'] == 'supprimerPanier')) {
    extract($_POST);
    //$checked = explode(',', $checked);

    $succes = $erreur = "";
    if (isset($checked) && !empty($checked)) {
        $model->setTable(HE_CANDIDAT_AGENCE_A_VENDRE_INTERESSEE);
        $model->setClause(CANDIDAT_AGENCE_A_VENDRE_INTERESSEE_ID_AGENCE_A_VENDRE . ' IN (' . $checked . ')');
        $suppression = $model->deleteData();

        $succes = $erreur = "";
        if ($suppression > 0) {
            $succes = "Suppression de votre panier réussie !";
        } else {
            $erreur = "Une erreur est survenue lors de la suppression !";
        }
    } else {
        $erreur = "Vous devez sélectionner au moins un élément dans le panier !";
    }

    $clause = "hc.id=" . $_SESSION[_USER_]->id . " AND hcav.id_candidat=hc.id AND hcav.id_agence_a_vendre=hav.id AND hav.actif = 1";
    $model->setChamp('hcav.*,hav.*');
    $model->setTable(HE_CANDIDAT_AGENCE_A_VENDRE_INTERESSEE . ' hcav,' . HE_CANDIDAT . ' hc,' . HE_AGENCE_A_VENDRE_NEW . ' hav');
    $model->setClause($clause);
    $agence_a_vendre = $model->getData();

    if (count($agence_a_vendre) > 0) {
        $model->setTable(_NOM_CRMDEV_TBL_LISTELEMENTFORM);
        $liste_element_form = $model->getElementType(['TYPE_AGENCE'], 'liste');
        for ($i = 0; $i < count($agence_a_vendre); $i++) {

            $nom_departement = $nom_departement_url = '';
            if (!empty($agence_a_vendre[$i]->num_department)) {
                $clause_dept = "num = '" . $agence_a_vendre[$i]->num_department . "'";
                $model->setChamp('name, name_url');
                $model->setTable(_NOM_CRMDEV_TBL_DEPARTEMENT);
                $model->setClause($clause_dept);
                $model->setOrderBy('');
                $dept = $model->getData();
                if (count($dept) > 0) {
                    $nom_departement = $dept[0]->name;
                    $nom_departement_url = $dept[0]->name_url;
                    $agence_a_vendre[$i]->id_ville_agence = $dept[0]->name;
                }
            }

            $ag = "";
            if (!empty($agence_a_vendre[$i]->id_type_agence)) {
                $id_type_agence = explode("#", substr(substr($agence_a_vendre[$i]->id_type_agence, 0, -1), 1));
                foreach ($id_type_agence as $values) {
                    $ag .= $liste_element_form['TYPE_AGENCE'][$values]['libelle'] . ' - ';
                }
            }

            $agence_a_vendre[$i]->id_type_agence = $ag;

            $agence_a_vendre[$i]->url = generate_link_detail(HTTP_PATH, $nom_departement_url, '', $agence_a_vendre[$i]->titre, $agence_a_vendre[$i]->id);
        }
    }

    $oSmarty->assign('agence_a_vendre', $agence_a_vendre);
    $oSmarty->assign('succes', $succes);
    $oSmarty->assign('erreur', $erreur);
    $oSmarty->assign('affiche_notif_ajax', 1);
    $oSmarty->assign('template', $route . '/templates/frontend/' . _version);
    $oSmarty->display($route . '/templates/frontend/' . _version . "/modules/panier/tableau_resultat.tpl");

    exit();
}

if (isset($get['action']) && ($get['action'] == 'imprimerPanier')) {
    extract($_POST);
    $checked = explode(',', $checked);
    if (count($checked) > 0) {
        $_SESSION['frontend']['panier']['tableauId'] = $checked;
    }

    exit();
}


if (isset($get['action']) && ($get['action'] == 'panierSendMail')) {

    extract($_POST);
    // Langue
    $langue = array_merge($lang['commun'], $lang['panier']);

    if (!isset($_GET['afaire'])) {
        $_SESSION['frontend']['notification']['erreur'] = "Vous devez choisir une option !";
        exit();
    } elseif (($_GET['afaire'] == 'sendMailToFriend') && isMail($_GET['email']) == false) {
        $_SESSION['frontend']['notification']['erreur'] = "Vous devez saisir une adresse email valide !";
        exit();
    }

    if (isset($checked) && !empty($checked)) {
        $model->setTable("he_agence_a_vendre_new hav LEFT JOIN " . _NOM_CRMDEV_TBL_DEPARTEMENT . " hd ON hav.num_department = hd.num");
        $model->setClause('hav.id IN (' . $checked . ')');
        $model->setChamp("hav.*, hd.name as nom_departement, hd.name_url");
        $model->setOrderBy("hav.titre");
        $listAgence = $model->getData();
        $model->release();

        $liste_biens = "<ul>";
        for ($i = 0; $i < count($listAgence); $i++) {
            $url = generate_link_detail(HTTP_PATH, $listAgence[$i]->name_url, '', $listAgence[$i]->titre, $listAgence[$i]->id);
            $liste_biens .= '<li><a href="' . $url . '" target="_blank">' . $listAgence[$i]->titre . ' (' . $listAgence[$i]->reference . ')</a></li>';
        }
        $liste_biens .= "</ul>";

        if ($_GET['afaire'] == 'sendMailToSite') {
            $model->setTable(_NOM_CRMDEV_TBL_LISTELEMENTFORM);
            $liste_element_form = $model->getElementType(['CIVILITE'], 'liste');
            $civilite = ($_SESSION[_USER_]->id_civilite > 0) ? $liste_element_form['CIVILITE'][$_SESSION[_USER_]->id_civilite]['libelle'] : '';

            $tab_search = ['#civilite#', '#nom#', '#prenom#', '#email#', '#telephone#', '#portable#', '#liste_biens#'];
            $tab_replace = [$civilite, $_SESSION[_USER_]->nom, $_SESSION[_USER_]->prenom, $_SESSION[_USER_]->email, $_SESSION[_USER_]->telephone, $_SESSION[_USER_]->portable, $liste_biens];

            $sujet = str_replace('#site_name#', SITE_NAME, $langue['courriel_panier_sujet']);
            $message = str_replace($tab_search, $tab_replace, $langue['courriel_panier_contenu']);

            $envoiMail = sendMail(SITE_NAME, SITE_MAIL, ADMIN_MAIL, $sujet, $message);
            $envoiMail1 = sendMail(SITE_NAME, SITE_MAIL, SITE_MAIL, $sujet, $message);
            //$envoiMail2 = sendMail(SITE_NAME, SITE_MAIL, SUPERVISEUR_MAIL, $sujet, $message, "", $infoFichierJoint);

            if ($envoiMail1) {
                $_SESSION['frontend']['notification']['succes'] = "Merci, vous venez d'envoyer votre panier à notre équipe, nous vous revenons sous peu.";
            }
        }

        if ($_GET['afaire'] == 'sendMailToFriend') {
            $model->setTable(_NOM_CRMDEV_TBL_LISTELEMENTFORM);
            $liste_element_form = $model->getElementType(['CIVILITE'], 'liste');
            $civilite = ($_SESSION[_USER_]->id_civilite > 0) ? $liste_element_form['CIVILITE'][$_SESSION[_USER_]->id_civilite]['libelle'] : '';

            $url_site = "<a href='" . HTTP_PATH . "' target='_blank'>" . SITE_NAME . "</a>";
            $tab_search = ['#civilite#', '#nom#', '#prenom#', '#url_site#', '#liste_biens#'];
            $tab_replace = [$civilite, $_SESSION[_USER_]->nom, $_SESSION[_USER_]->prenom, $url_site, $liste_biens];

            $sujet = str_replace('#site_name#', SITE_NAME, $langue['courriel_panier_suggestion_sujet']);
            $message = str_replace($tab_search, $tab_replace, $langue['courriel_panier_suggestion_contenu']);

            $envoiMail = sendMail(SITE_NAME, SITE_MAIL, $_GET['email'], $sujet, $message);
            $envoiMail1 = sendMail(SITE_NAME, SITE_MAIL, SITE_MAIL, $sujet, $message);
            //$envoiMail2 = sendMail(SITE_NAME, SITE_MAIL, SUPERVISEUR_MAIL, $sujet, $message);

            if ($envoiMail) {
                $_SESSION['frontend']['notification']['succes'] = "Vous venez d'envoyer votre panier avec le propriétaire de l'adresse " . $_GET['email'];
            }
        }
    } else {
        $_SESSION['frontend']['notification']['erreur'] = "Vous devez selectionner au moins un élément de votre panier !";
    }
}

if (isset($get['action']) && ($get['action'] == 'supprimerAlerte')) {

    $succes = $erreur = '';
    if (isset($get['idAlert']) && ($get['idAlert'] > 0)) {
        $model->setTable(HE_CANDIDAT_RECHERCHE);
        $model->setClause(CANDIDAT_RECHERCHE_ID . ' = ' . $_GET['idAlert']);
        $suppression = $model->deleteData();

        if ($suppression > 0) {
            $succes = "Suppression de votre panier réussie !";
        } else {
            $erreur = "Une erreur est survenue lors de la suppression !";
        }
    } else {
        $erreur = "Vous n'avez choisi aucune alerte à supprimer !";
    }

    $model->release();
    $model->setTable('he_candidat_recherche');
    $model->setChamp('*');
    $model->setClause('id_candidat = "' . $_SESSION[_USER_]->id . '" AND archive = -1');
    $alertCandidat = $model->getData();

    $model->release();
    $model->setTable(_NOM_CRMDEV_TBL_LISTELEMENTFORM);
    $liste_element_form = $model->getElementType(['TYPE_AGENCE'], 'liste');
    $tableauDonnees['typeAgenceListe'] = $liste_element_form['TYPE_AGENCE'];

    if (count($alertCandidat) > 0) {

        for ($i = 0; $i < count($alertCandidat); $i++) {
            $typeAgenceSouhaite = explode("#", substr(substr($alertCandidat[$i]->id_type_agence_souhaitee, 0, -1), 1));
            $alertCandidat[$i]->typeAgenceSouhaite = $typeAgenceSouhaite;

            $vils = (!empty($alertCandidat[$i]->lieu_implantation_souhaite)) ? str_replace("#", ",", substr(substr($alertCandidat[$i]->lieu_implantation_souhaite, 0, -1), 1)) : '';
            $villes = [];
            if (!empty($vils)) {
                $clause = ' id in (' . $vils . ') ';
                $model->setTable(_NOM_CRMDEV_TBL_VILLE);
                $model->setClause($clause);
                $model->setChamp('id, nom_reel, code_postal');
                $model->setOrderBy('nom_reel');
                $villes = $model->getData();
            }
            $alertCandidat[$i]->ville = $villes;

            $alertCandidat[$i]->date_previsionnelle_ouverture = ($alertCandidat[$i]->date_previsionnelle_ouverture != '0000-00-00') ? convertDateTime($alertCandidat[$i]->date_previsionnelle_ouverture) : '';
            $alertCandidat[$i]->titre = !empty($alertCandidat[$i]->date_previsionnelle_ouverture) ? "Date prévisionnelle ouverture : " . $alertCandidat[$i]->date_previsionnelle_ouverture . " " : '';
            $alertCandidat[$i]->titre .= !empty($alertCandidat[$i]->remarque) ? "Remarque : " . $alertCandidat[$i]->remarque : '';
        }
    }
    $tableauDonnees['alertCandidat'] = $alertCandidat;

    $oSmarty->assign('tableauDonnees', $tableauDonnees);
    $oSmarty->assign('succes', $succes);
    $oSmarty->assign('erreur', $erreur);
    $oSmarty->assign('affiche_notif_ajax', 1);
    $oSmarty->display($route . '/templates/frontend/' . _version . "/modules/alerte/tableau_resultat.tpl");

    exit();
}

function iseditable($model, $idrecherche, $editable) {
    if ($editable === false) {
        $model->release();
        $model->setTable($_SESSION[_USER_]->nom_bd . '.crm_np_acheteur_recherche');
        $model->setClause('id = ' . $idrecherche);
        $model->setChamp("attribue_a");

        $madeby = $model->getData();
        if (isset($madeby[0]) && $madeby[0]->attribue_a == $_SESSION[_USER_]->id) {
            return true;
        }

        return false;
    }

    return true;
}