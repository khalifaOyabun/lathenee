<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// $config['site_settings']['droits_actifs'] = (isset($config['site_settings']['droits_actifs']) && is_array($config['site_settings']['droits_actifs'])) ? $config['site_settings']['droits_actifs'] : [];

switch ($module) {
    case "acheteursrecherche":
        $acces_notes_autres_negociateurs = _right($config, 'ACCES_NOTES_AUTRES_NEGOCIATEURS');
        $acces_negociateurs_chargement_documents = _right($config, 'ACCES_NEGOCIATEURS_CHARGEMENT_DOCUMENTS');
        $acces_mandats_affaires_autres_negociateurs = _right($config, 'ACCES_MANDATS_AFFAIRES_AUTRES_NEGOCIATEURS');
        $acces_negociateurs_classement_par_departement = _right($config, 'ACCES_NEGOCIATEURS_CLASSEMENT_PAR_DEPARTEMENT', true);
        $acces_acheteurs_recherches_autres_negociateurs = _right($config, 'ACCES_ACHETEURS_RECHERCHES_AUTRES_NEGOCIATEURS');
        $acces_edition_recherches_acheteurs_autres_negociateurs = _right($config, 'ACCES_EDITION_RECHERCHES_ACHETEURS_AUTRES_NEGOCIATEURS');
        if ($acces_negociateurs_classement_par_departement) {
            $departements_autorises = buildLef(get_dept_autorises($model));
        }
        $assigned['acces_negociateurs_chargement_documents'] = $acces_negociateurs_chargement_documents;

        break;
    case "affaires":
        $acces_notes_autres_negociateurs = _right($config, 'ACCES_NOTES_AUTRES_NEGOCIATEURS');
        $acces_generation_dossier_acquisition = _right($config, 'ACCES_GENERATION_DOSSIER_ACQUISITION');
        $acces_negociateurs_chargement_documents = _right($config, 'ACCES_NEGOCIATEURS_CHARGEMENT_DOCUMENTS');
        $acces_mandats_affaires_autres_negociateurs = _right($config, 'ACCES_MANDATS_AFFAIRES_AUTRES_NEGOCIATEURS');
        $acces_negociateurs_classement_par_departement = _right($config, 'ACCES_NEGOCIATEURS_CLASSEMENT_PAR_DEPARTEMENT', true);
        $acces_acheteurs_recherches_autres_negociateurs = _right($config, 'ACCES_ACHETEURS_RECHERCHES_AUTRES_NEGOCIATEURS');
        $acces_negociateurs_validation_affaires_par_defaut = _allright($config, 'ACCES_NEGOCIATEURS_VALIDATION_AFFAIRES_PAR_DEFAUT');
        $acces_edition_recherches_acheteurs_autres_negociateurs = _right($config, 'ACCES_EDITION_RECHERCHES_ACHETEURS_AUTRES_NEGOCIATEURS');

        if ($acces_negociateurs_classement_par_departement) {
            $departements_autorises = buildLef(get_dept_autorises($model));
            $message_departements_non_autorises = 'Vous n\'êtes pas autorisé à accéder aux informations de cette affaire (départements non-autorisés). ';
            if (count($departements_autorises) === 0 && in_array($action, ['modification', 'ajout', 'detail'])) {
                $_SESSION[$app]['notification']['erreur'] = $message_departements_non_autorises;
                if (isset($id)) {
                    $model->redirect(HTTP_PATH . '/affaires/liste#affaire' . $id);
                } else {
                    $model->redirect(HTTP_PATH . '/affaires/liste');
                }
            }
        }

        $assigned['acces_generation_dossier_acquisition'] = $acces_generation_dossier_acquisition;
        $assigned['acces_negociateurs_chargement_documents'] = $acces_negociateurs_chargement_documents;
        $assigned['acces_negociateurs_validation_affaires_par_defaut'] = $acces_negociateurs_validation_affaires_par_defaut;
        break;
    case "agenda":
        $acces_contacts_autres_negociateurs = _right($config, 'ACCES_CONTACTS_AUTRES_NEGOCIATEURS');
        $acces_mandats_affaires_autres_negociateurs = _right($config, 'ACCES_MANDATS_AFFAIRES_AUTRES_NEGOCIATEURS');
        $acces_negociateurs_classement_par_departement = _right($config, 'ACCES_NEGOCIATEURS_CLASSEMENT_PAR_DEPARTEMENT', true);
        $acces_acheteurs_recherches_autres_negociateurs = _right($config, 'ACCES_ACHETEURS_RECHERCHES_AUTRES_NEGOCIATEURS');
        if ($acces_negociateurs_classement_par_departement) {
            $departements_autorises = buildLef(get_dept_autorises($model));
        }
        break;
    case "contacts":
        $acces_notes_autres_negociateurs = _right($config, 'ACCES_NOTES_AUTRES_NEGOCIATEURS');
        $acces_contacts_autres_negociateurs = _right($config, 'ACCES_CONTACTS_AUTRES_NEGOCIATEURS');
        $acces_partage_documents_partenaires_externes = _allright($config, 'ACCES_PARTAGE_DOCUMENTS_PARTENAIRES_EXTERNES');

        $assigned['acces_partage_documents_partenaires_externes'] = $acces_partage_documents_partenaires_externes;
        break;
    case "notes":
        $acces_notes_autres_negociateurs = _right($config, 'ACCES_NOTES_AUTRES_NEGOCIATEURS');
        break;
    case "utilisateurs":
        $mdpoublie_autorisation_changement_par_user = _right($config, 'MDPOUBLIE_AUTORISATION_CHANGEMENT_PAR_USER');
        break;

    default:
        break;
}

function _right($config, $right, $opposite = false) {
    global $model;
    if ((bool) $model->connected('NEGOCIATEUR') === false) {
        return $opposite ? false : true;
    }
    if ((bool) $model->connected('NEGOCIATEUR') && (isset($config['site_settings']['droits_actifs']) && in_array($right, $config['site_settings']['droits_actifs']))) {
        return true;
    }
    return false;
}

function _allright($config, $right) {

    if ((isset($config['site_settings']['droits_actifs']) && in_array($right, $config['site_settings']['droits_actifs']))) {
        return true;
    }
    return false;
}
