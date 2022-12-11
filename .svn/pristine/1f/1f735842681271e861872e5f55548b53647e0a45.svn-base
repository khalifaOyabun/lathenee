<?php

/**
 * Description of Queries
 *
 * @author Ousmane DIENG
 * 
 */
//require 'Model.class.php';

class Outils {

    public function setConnection() {
        //_connect('localhost', '8737276489467838', '567uVVKuVaE6eTu3', 'db027604890467838');
        $dao = _connect('localhost', 'root', '', 'bd_hoquet_crm_dev');
        return $dao;
    }

    public function buildProspectGHE($donnees = [], $saveCA = 'no') {
        $dao = $this->setConnection();
        $modelOutils = new Model($dao, '');

        $donnees_agence_prospectee['rcs_agence'] = $donnees['rcs'];
        $donnees_agence_prospectee['raison_sociale_agence'] = $donnees['raison_sociale'];
        $donnees_agence_prospectee['adresse_agence'] = $donnees['adresse'];
        $donnees_agence_prospectee['cp_agence'] = $donnees['code_postal'];
        $donnees_agence_prospectee['id_ville_agence'] = $donnees['id_ville'];
        $donnees_agence_prospectee['id_type_agence'] = '#' . implode('#', $donnees['estimerAgenceChoices']) . '#';
        $donnees_agence_prospectee['date_creation'] = date('Y-m-d h:i:s');
        $donnees_agence_prospectee['prospect'] = 1;
        $donnees_agence_prospectee['id_civilite_gerant'] = ($donnees['id_civilite_personne'] > 0) ? $donnees['id_civilite_personne'] : '';
        $donnees_agence_prospectee['nom_gerant'] = $donnees['nom_prenom'];
        $donnees_agence_prospectee['cp_gerant'] = $donnees['cp_demandeur'];
        $donnees_agence_prospectee['id_ville_gerant'] = $donnees['id_ville_demandeur'];
        $donnees_agence_prospectee['telephone_gerant'] = $donnees['telephone'];
        $donnees_agence_prospectee['email_gerant'] = $donnees['email_demandeur'];
        $donnees_agence_prospectee['id_cession_agence'] = $donnees['envisage_vendre'];

        $modelOutils->setTable("crm_agenceavendre");
        $modelOutils->setChamp("id");
        $modelOutils->setClause("rcs_agence = '" . $donnees['rcs'] . "' AND actif = 1");
        $existe = $modelOutils->recordExists();
        if ($existe == 0) {
            $id_agence = $modelOutils->insertOne($donnees_agence_prospectee);
            $modelOutils->release();

            if ($id_agence > 0) {

                //Pour enregistrer les détails CA de l'agence
                if ($saveCA == 'yes') {
                    for ($i = 0; $i < 3; $i++) {
                        $donnees_agence_detail_ca['id_agenceavendre'] = $id_agence;
                        $donnees_agence_detail_ca['annee'] = date('Y') - ($i + 1);
                        $donnees_agence_detail_ca['total_ca'] = (isset($donnees['ca'][0][$i])) ? $donnees['ca'][0][$i] : '';
                        $donnees_agence_detail_ca['vente'] = (isset($donnees['ca'][1][$i])) ? $donnees['ca'][1][$i] : '';
                        $donnees_agence_detail_ca['location'] = (isset($donnees['ca'][2][$i])) ? $donnees['ca'][2][$i] : '';
                        $donnees_agence_detail_ca['gestion'] = (isset($donnees['ca'][3][$i])) ? $donnees['ca'][3][$i] : '';

                        $modelOutils->setTable("crm_agenceavendre_detail_ca");
                        $modelOutils->insertOne($donnees_agence_detail_ca);
                    }
                }

                $sujet = "GUY HOQUET CRM DEV - Ajout nouveau prospect.";
                $message = "<p>Bonjour,<br/><br/>";
                $message .= "L'agence <b>" . $donnees['raison_sociale'] . "</b> de " . $donnees['civilite_libelle'] . " " . $donnees['nom_prenom'] . "";
                $message .= (isset($donnees['ville_contenu'][$donnees['id_ville']])) ? " de " . $donnees['ville_contenu'][$donnees['id_ville']]->nom_reel . ' (' . $donnees['ville_contenu'][$donnees['id_ville']]->departement . ')' : "";
                $message .= " a été ajoutée comme prospect dans le CRM suite à sa demande de valorisation.<br /><br />";
                $message .= "<u><b>A la question : Envisagez-vous de céder votre agence ?, sa réponse est</b></u> : " . $donnees['envisage_vendre_libelle'] . ".</p>";
                $message .= '<hr/><table cellspacing="10"><tr>
                      <td valign = "top">' . SITE_NAME . ' <br/>Adresse : ' . ADRESSE_AGENCE . ', ' . CP_AGENCE . ' ' . VILLE_AGENCE . ' <br/>Téléphone :  ' . PHONE_1 . ' <br/>
                      <a href="' . HTTP_PATH . '">' . HTTP_PATH . '</a> <br/></td></tr></table>';
                $envoiMail = sendMail(SITE_NAME, $donnees['email_demandeur'], DEVELOPPEMENT_MAIL, $sujet, $message, "", $donnees['fichierJoint']);
            }
        }
    }

    public function buildCandidatRechercheGHE($donnees = []) {
        
    }

}
