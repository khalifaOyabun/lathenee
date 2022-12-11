<?php

$model = new Model($dao, ''); // Mise en marche des requêtes basiques.

function getFieldParam($model, $field, $transaction) {
    $model->release();
    $model->setTable($_SESSION[_USER_]->nom_bd . '.crm_np_affaire_vis_champ');
    $model->setClause("critere_recherche = 1 AND actif = 1 AND nom_champ IN ('" . (is_array($field) ? implode("','", $field) : $field) . "') AND code_type_transaction = '$transaction'");
    $model->setChamp("nom_champ, libelle_champ, code_type_form_champ, code_type_valeur_champ, source_donnees, type_donnees_critere_recherche, code_filtre_type, donnees");
    $model->setOrderBy("filtre_rang");
    $query = $model->getData();

    return (count($query) ? $query : false);
}

function getsearchinfos($model, $queryresult) {
    global $lef, $lefagence;
    $brute = [];

    $details = $model->fillArrays($queryresult, 'nom_champ', null, false);
    $transaction = $details["code_type_transaction"]["valeur"];
    $params = getFieldParam($model, array_keys($details), $transaction);

    foreach ($params as $p) {
        $champsearchdetails = $details[$p->nom_champ];
        $champsearchdetails['libelle_champ'] = $p->libelle_champ;
        $champsearchdetails['code_filtre_type'] = $p->code_filtre_type;
        $champsearchdetails['code_type_form_champ'] = $p->code_type_form_champ;
        $champsearchdetails['code_type_valeur_champ'] = $p->code_type_valeur_champ;
        $champsearchdetails['type_donnees_critere_recherche'] = $p->type_donnees_critere_recherche;

        if ($p->type_donnees_critere_recherche === 'intervalle') {
            $champsearchdetails['valeur_got'] = buildLef($champsearchdetails['valeur']);
        } else {
            if (($p->source_donnees != "LIBRE") && ($p->source_donnees != "") && ($p->source_donnees != null)) {

                switch ($p->source_donnees) {
                    case $_SESSION[_USER_]->nom_bd . '.crm_np_liste_element_form':
                    case 'crm_netprofil_commun.crm_np_liste_element_form':
                        $type = buildLef($p->donnees);
                        if ($p->source_donnees === 'crm_netprofil_commun.crm_np_liste_element_form') {
                            $champsearchdetails['valeur_got'] = $lef['TYPE_TRANSACTION'][$champsearchdetails['valeur']]['libelle'];
                        } else {
                            $champsearchdetails['valeur_got'] = $lefagence['liste']['TYPE_TRANSACTION'][$champsearchdetails['valeur']]['libelle'];
                        }
                        break;

                    case 'crm_np_ville':
                        $champ = "*";
                        if (($p->donnees != null) && ($p->donnees != "")) {
                            $don = buildLef($p->donnees);
                            $champ = $don[0] . ' as code, ' . $don[1] . ' as libelle, num_departement';
                        }

                        $model->release();
                        $model->setTable(GetInputElement($p->source_donnees));
                        $model->setClause("id IN (" . implode(",", buildLef($champsearchdetails['valeur'])) . ")");
                        $model->setChamp($champ);
                        $model->setOrderBy("num_departement, nom_reel");
                        $donnees = $model->getData();
                        if (count($donnees) > 0) {
                            foreach ($donnees as $value) {
                                $champsearchdetails['valeur_got'][] = $value->libelle . "(" . $value->num_departement . ") ";
                            }
                        }
                        break;

                    default:
                        $champ = "*";
                        if (($p->donnees != null) && ($p->donnees != "")) {
                            $don = buildLef($p->donnees);
                            $champ = $don[1] . ' as libelle';
                        }
                        $model->release();
                        $model->setTable(GetInputElement($p->source_donnees));
                        $model->setClause("id IN (" . implode(",", buildLef($champsearchdetails['valeur'])) . ")");
                        $model->setChamp($champ);
                        $model->setOrderBy("libelle");
                        $donnees = $model->getData();
                        if (count($donnees) > 0) {
                            foreach ($donnees as $value) {
                                $champsearchdetails['valeur_got'][] = $value->libelle;
                            }
                        }
                        break;
                }
            } else {
                $champsearchdetails['valeur_got'] = $champsearchdetails['valeur'];
            }
        }
        $brute [] = (object) $champsearchdetails;
    }

    return $brute;
}

function applymarge($value, $marge) {

    $rate = $marge / 100;
    if ($marge > 0 && ((is_array($value) && count($value) === 1) || (is_int($value)) || (is_string($value) && (int) $value > 0))) {
        return [$value * (1 - $rate), $value * (1 + $rate)];
    }
    if ($marge > 0 && count($value) === 2) {
        return [$value[0] * (1 - $rate), $value[1] * (1 + $rate)];
    }

    return $value;
}

function buildfield($v, $appliedmarge = true) {

    if ($v->code_filtre_type === 'MULTISELECT') {
        $field = $v->nom_champ . ' IN ("' . implode('","', buildLef($v->valeur)) . '")';
        return $field;
    }
    if (in_array($v->code_type_valeur_champ, ["INT", "FLOAT", "DISTANCE", "VOLUME", "SUPERFICIE", "PRICE"])) {
        $value = ($appliedmarge === true) ? applymarge($v->valeur_got, $v->marge) : $v->valeur_got;

        if (is_array($value) && count($value) === 2) {
            $field = '(' . $v->nom_champ . ' BETWEEN ' . (float) $value[0] . ' AND ' . (float) $value[1] . ')';
        }
        if ((is_array($value) && count($value) === 1) || (is_int($value)) || (is_string($value) && (int) $value > 0)) {
            $field = '(' . $v->nom_champ . ' = ' . (float) $value . ')';
        }
        return $field;
    }
    return $field = $v->nom_champ . ' = "' . $v->valeur . '"';
}

function buildclause($searched, $optional, $applymarge = true) {
    $clause = 1;
    foreach ($searched as $bqv) {
        if (($optional === 1 && $bqv->indispensable != 1) || empty($bqv->valeur)) {
            continue;
        }

        $clause .= ' AND ' . buildfield($bqv, $applymarge);
    }
    return $clause;
}

function getdatas($model, $clause) {
    if ($clause === 1) {
        return [];
    }
    $model->release();
    $model->setTable($_SESSION[_USER_]->nom_bd . '.crm_np_affaire');
    $model->setClause($clause . " AND actif = 1");
    $model->setChamp("id");

    $query = $model->getData();
    return (count($query)) ? $query : [];
}

function recordrapprochement($model, $recherche, $aid, $rate = null) {

    if (is_array($aid) && count($aid)) {

        $model->setTable($_SESSION[_USER_]->nom_bd . '.crm_np_acheteur_recherche_affaire_correspondance');
        $rapprochement['etat'] = 1;
        $rapprochement['id_acheteur_recherche'] = $recherche;
        $rapprochement['taux_correspondance'] = $rate;

        foreach ($aid as $affaire) {
            $rapprochement['id_affaire'] = $affaire->id;
            $model->setClause("id_affaire = " . $affaire->id . " AND id_acheteur_recherche = " . $recherche);
            if ($model->getCount() > 0) {
                continue;
            }
            $model->setClause(null);
            $model->record($rapprochement);
        }
    }
}

function getaffaires($model, $id_recherhe_acheteur, $etat) {

    global $acces_mandats_affaires_autres_negociateurs;
    global $acces_negociateurs_classement_par_departement;
    if ($acces_negociateurs_classement_par_departement) {
        global $departements_autorises;
    }
    $clause = "arac.id_affaire = a.id AND a.actif = 1 AND etat = " . $etat . " AND id_acheteur_recherche = " . $id_recherhe_acheteur;
    $clause .= (isset($departements_autorises) ? ' AND a.num_departement IN ("' . implode('","', $departements_autorises) . '") ' : '');
    $clause .= (($acces_mandats_affaires_autres_negociateurs) ? '' : ' AND a.attribue_a = ' . $_SESSION[_USER_]->id);
    $model->release();
    $model->setTable($_SESSION[_USER_]->nom_bd . '.crm_np_acheteur_recherche_affaire_correspondance arac, ' . $_SESSION[_USER_]->nom_bd . '.crm_np_affaire a');
    $model->setChamp('arac.*, a.id as id_affaire, reference, code_type_transaction, id_type_affaire, num_departement, id_ville, prix, champ5');
    $model->setOrderBy('arac.taux_correspondance');
    $model->setClause($clause);
    $result = $model->getData();

    return (count($result)) ? $model->fillArrays($result, 'taux_correspondance', null, true) : [];
}

function buildaffaires($model, $result, $lefagence) {
    global $route;
    foreach ($result as $k => $tc) {
        foreach ($tc as $sk => $affaire) {
            $result[$k][$sk]['type_transaction'] = (isset($lefagence['liste']["TYPE_TRANSACTION"][$affaire['code_type_transaction']])) ? $lefagence['liste']["TYPE_TRANSACTION"][$affaire['code_type_transaction']]['libelle'] : null;
            $model->release();
            $model->setTable($_SESSION[_USER_]->nom_bd . '.crm_np_affaire_type');
            $model->setClause('id=' . $affaire['id_type_affaire']);
            $model->setChamp("libelle");
            $type_affaire = $model->getData();
            $result[$k][$sk]['type_affaire'] = (count($type_affaire) > 0) ? $type_affaire[0]->libelle : null;

            $model->release();
            $model->setTable('crm_np_ville');
            $model->setClause('id=' . $affaire['id_ville']);
            $model->setChamp("nom_reel");
            $ville = $model->getData();
            $result[$k][$sk]['nom_reel'] = null;
            if (count($ville)) {
                $result[$k][$sk]['nom_reel'] = $ville[0]->nom_reel;
            }
            $files = (is_dir($route . _STORAGE_PATH . 'agences/' . $_SESSION[_USER_]->nom_dossier . '/images/affaires/affaire_' . $affaire['id_affaire'])) ? array_slice(scandir($route . _STORAGE_PATH . 'agences/' . $_SESSION[_USER_]->nom_dossier . '/images/affaires/affaire_' . $affaire['id_affaire']), 2) : [];
            $result[$k][$sk]['image'] = HTTP_PATH . _STORAGE_PATH . 'agences/' . $_SESSION[_USER_]->nom_dossier . '/images/affaires/affaire_' . $affaire['id_affaire'] . '/default.jpg';
            for ($j = 0; $j < count($files); $j++) {
                if ($j > 0) {
                    break;
                }
                $result[$k][$sk]['image'] = HTTP_PATH . _STORAGE_PATH . 'agences/' . $_SESSION[_USER_]->nom_dossier . '/images/affaires/affaire_' . $affaire['id_affaire'] . '/' . $files[$j];
            }
        }
    }

    return $result;
}
