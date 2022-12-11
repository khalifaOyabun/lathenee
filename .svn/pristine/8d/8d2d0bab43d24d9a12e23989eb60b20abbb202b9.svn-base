<?php

    /* Cette fonction permet de charger un fichier PDF ou WORD issu d'un formulaire.
     * 
     * $fichier : array $_FILES
     * 
     * $nom_enregistre : String nom du fichier a l'enregistrement.
     * 
     * $chemin : chemin du fichier. 
     * 
     * $ext : permet de définir les extensions qui seront admises.
     * 
     *  */
    
    public function uploadFile($fichier, $nom_enregistre, $chemin, $ext = array('pdf', 'doc', 'docx', 'png', 'jpg')) {

        if (isset($fichier["name"]) && (!empty($fichier["name"]))) {

            // Traitement du nom du fichier.
            $tabExtension = explode('.', addslashes($fichier["name"]));

            $extension = count($tabExtension) - 1;

            $nomFichier = $nom_enregistre . "." . $tabExtension[$extension];

            // nom temporaire sur le serveur:
            $temporary = addslashes($fichier['tmp_name']);

            $tab_extension = is_array($ext) ? $ext : array('pdf', 'doc', 'docx', 'png', 'jpg');

            // On vérifie l'extention.
            if (in_array(strtolower($tabExtension[$extension]), $tab_extension)) {

                copy($temporary, $chemin . $nomFichier);

                return array($chemin, $nomFichier);
            }

            return array(false, "Error: file extension.");
        }

        return array(false, "Error: filename empty.");
    }

    /* Cette fonction permet de charger une image issue d'un formulaire.
     * 
     * $fichier : array $_FILES
     * 
     * $nom_enregistre : String nom du fichier a l'enregistrement.
     * 
     * $chemin : chemin du fichier. 
     * 
     * $ext : permet de définir les extensions qui seront admises.
     * 
     *  */
	
    public function uploadPicture($photo, $nom_enregistre, $chemin, $largeur) {

// Si le nom de la photo n'est pas vide.
        if (isset($photo["name"]) && (!empty($photo["name"]))) {

// Traitement du nom de la photo.
            $tabExtensionPhoto = explode('.', addslashes($photo["name"]));
            $idExtensionPhoto = count($tabExtensionPhoto) - 1;
            $nomFichier = $nom_enregistre . "." . strtolower($tabExtensionPhoto[$idExtensionPhoto]);

            $nomFichierOriginal = "original_" . $nomFichier;

// nom temporaire sur le serveur:
            $nomTemporaire = addslashes($photo['tmp_name']);

// On vérifie l'extention.
            if (in_array(strtolower($tabExtensionPhoto[$idExtensionPhoto]), array('jpeg', 'jpg', 'gif', 'png'))) {

                copy($nomTemporaire, $chemin . $nomFichierOriginal);

//creation de la miniature
                $taille = getimagesize($nomTemporaire);

                $hauteur = ( ($taille[1] * (($largeur) / $taille[0])) );

                miniature($chemin . $nomFichierOriginal, $nomFichier, $chemin, $largeur, $hauteur);

                unlink($chemin . $nomFichierOriginal);

                return array($chemin, $nomFichier);
            }

            return array(false, "Erreur: L'extension du fichier n'est pas bon.");
        }

        return array(false, "Erreur: Le nom du fichier n'est pas défini.");
    }
	
	    /* Traitement tableau */

    public function fillArrays($cle, $valeur = '') {

        if (is_array($donnees)) {

            $tableau = array();

            foreach ($donnees as $donnee) {

                if (is_object($donnee)) {

                    $tableau[$donnee->$cle] = (!empty($valeur)) ? $donnee->$valeur : $donnee;
                }

                if (is_array($donnee)) {

                    $tableau[$donnee[$cle]] = (!empty($valeur)) ? $donnee[$valeur] : $donnee;
                }
            }

            return $tableau;
        }

        return false;
    }
	
	public function makeconstfile($route, $prefixe) {

        $infos = $this->getDatabaseContent();

        $fichier = fopen($route . '/core/_const_bd.php', 'w+');

        $contenu = "<?php \n\n/* Le fichier contient l'ensemble des tables de la base de données connectée ainsi que leurs champs.*/ \n\n";

        foreach ($infos['tables'] as $cle => $valeur) {

            $contenu .= "// Table " . strtoupper($valeur) . " et ses champs. \n";

            $contenu .= "const " . strtoupper($cle) . " = '" . ($valeur) . "', ";

            foreach ($infos['champ'] as $cle1 => $valeur1) {

                $concordance = explode("#", $cle1);

                ($concordance[0] === $cle) ? $contenu .= strtoupper(substr($cle, strlen($prefixe), strlen($cle))) . "_" . strtoupper($valeur1) . " = '" . ($valeur1) . "', " : '';
            }

            $contenu .= ";\n\n";
        }

        fputs($fichier, str_replace(', ;', ';', $contenu));

        fclose($fichier);

        echo 'Fichier de constantes généré avec succès !';
    }
	
	public function userUnique($donnees, $getpoids = -1) {

        $id_by_email = $this->getIdByField('email', $donnees['email']);

        $id_by_login = (isset($donnees['login'])) ? $this->getIdByField('login', $donnees['login']) : 0;

        $poids = 0;

        if (!isset($donnees['id'])) {

            if ($id_by_email > 0) {

                $poids += 1;
            }
            if ($id_by_login > 0) {

                $poids += 2;
            }
        } else {
            if ($id_by_email > 0 && $donnees['id'] != $id_by_email) {

                $poids += 1;
            }
            if ($id_by_login > 0 && $donnees['id'] != $id_by_login) {

                $poids += 2;
            }
        }

        return ($getpoids == -1) ? $this->poids($poids) : $poids;
    }
	
	private function poids($poids) {

        $text = [1, 'Cette adresse e-mail est déjà prise par un autre utilisateur.', 'Ce login est déjà pris par un autre utilisateur.', 'Cette adresse e-mail et ce login sont déjà pris par un autre utilisateur.'];

        return $text[$poids];
    }
	
	public function traiteDonnees($donnees, $aexploder = [], $dateheure = -1) {

        foreach ($donnees as $cle => $valeur) {

            if (is_array($aexploder)) {
                if (in_array($cle, $aexploder)) {
                    $explode = explode('#', $valeur);
                    unset($explode[count($explode) - 1], $explode[0]);
                    $donnees[$cle] = $explode;
                }
            }

            if (is_string($valeur) && ((DateTime::createFromFormat('Y-m-d', $valeur)) !== FALSE || (DateTime::createFromFormat('Y-m-d H:i:s', $valeur)) !== FALSE || (DateTime::createFromFormat('Y-m-d h:i:s', $valeur)) !== FALSE)) {
                if ($dateheure == -1)
                    $donnees[$cle] = ($valeur != '0000-00-00' && $valeur != '0000-00-00 00:00:00') ? date('d-m-Y', strtotime($valeur)) : NULL;
                else
                    $donnees[$cle] = ($valeur != '0000-00-00' && $valeur != '0000-00-00 00:00:00') ? date('d-m-Y H:i', strtotime($valeur)) : NULL;
            }
        }

        return $donnees;
    }
	
    public function traiteChaineDieseSetLike($chaine, $separateur, $champ_table) {

        $retour = [];
        $explode = explode($separateur, $chaine);
        if (count($explode) > 0) {
            unset($explode[count($explode) - 1], $explode[0]);
            foreach ($explode as $cle => $valeur) {
                if (!empty($valeur)) {
                    $retour[] = $champ_table . " LIKE '%" . $separateur . $valeur . $separateur . "%'";
                }
            }
        }

        return $retour;
    }

    public function chargerEnSession($id, $foreign, $type, $session_cle, $isConcatable = true) {

        $oBy = ($isConcatable) ? 'id desc' : 'date_creation DESC';
        $tampon = $this->getByfk($foreign, $id, $oBy);

        $_SESSION[$session_cle][$type] = [];

        foreach ($tampon as $valeur) {

            $donnees = (array) $valeur;

            $interm = [];

            foreach ($donnees as $cle1 => $valeur1) {
                $cKey = ($isConcatable) ? $type . '_' . $cle1 : $cle1;
                $interm[$cKey] = ((DateTime::createFromFormat('Y-m-d', $valeur1)) !== FALSE) ? date('d-m-Y', strtotime($valeur1)) : $valeur1;
            }

            $_SESSION[$session_cle][$type][] = $interm;
        }
    }

