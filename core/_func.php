<?php
/* * ***** Début Réellement utilisé*********** */
/*
 * 
 * all var and const needed
 * 
 *  */

$e_loaded_pic = [
    1 => "La taille du fichier téléchargé excède la valeur de upload_max_filesize, configurée dans le php.ini.", #UPLOAD_ERR_INI_SIZE
    2 => "La taille du fichier téléchargé excède la valeur de MAX_FILE_SIZE, qui a été spécifiée dans le formulaire HTML.", #UPLOAD_ERR_FORM_SIZE
    3 => "Le fichier n'a été que partiellement téléchargé.", #UPLOAD_ERR_PARTIAL
    4 => "Aucun fichier n'a été téléchargé.", #UPLOAD_ERR_NO_FILE
    6 => "Un dossier temporaire est manquant.", #UPLOAD_ERR_NO_TMP_DIR
    7 => "Échec de l'écriture du fichier sur le disque.", #UPLOAD_ERR_CANT_WRITE
    8 => "Une extension PHP a arrêté l'envoi de fichier." #UPLOAD_ERR_EXTENSION - PHP ne propose aucun moyen de déterminer quelle extension est en cause. L'examen du phpinfo() peut aider. Introduit en PHP 5.2.0.
];
/* * * End ********** */

function getSchema($table, $db, $schema = "columns")
{
    $baseDeDonnees = PREFIXE_BD . str_replace(PREFIXE_BD, '', $db);
    $con = mysqli_connect(HOST, USER, PASS, $baseDeDonnees) or die("Error : ");
    $requete = ($schema === "tables") ? "SHOW TABLES " : "SHOW COLUMNS FROM " . $table;

    $execution_requete = mysqli_query($con, $requete);
    $champ = [];
    if (mysqli_num_rows($execution_requete) > 0) {
        while ($lignes = mysqli_fetch_row($execution_requete)) {
            $champ[$lignes[0]] = ($lignes[0] === "crm_np_liste_element_form") ? $baseDeDonnees . "." . $lignes[0] : $lignes[0];
        }
    }

    return $champ;
}

function getFreeTableColumns($model, $table, $db)
{
    $baseDeDonnees = PREFIXE_BD . str_replace(PREFIXE_BD, '', $db);
    $con = mysqli_connect(HOST, USER, PASS, $baseDeDonnees) or die("Error : ");
    $requete = "SHOW COLUMNS FROM  $baseDeDonnees.$table";
    $execution_requete = mysqli_query($con, $requete);

    $model->release();
    $model->setTable("$baseDeDonnees.crm_np_affaire_vis_champ");
    $model->setChamp("DISTINCT(nom_champ) as nom_champ");
    $fieldInVisChamp = $model->fillArrays($model->getData(), "nom_champ", "nom_champ");

    $champ = [];
    if (mysqli_num_rows($execution_requete) > 0) {
        while ($lignes = mysqli_fetch_row($execution_requete)) {
            if (!in_array($lignes[0], $fieldInVisChamp)) {
                $index = $lignes[0];
                $champ[$index] = $lignes[0];
            }
        }
    }

    return $champ;
}

function getBaseTables($db)
{
    $baseDeDonnees = PREFIXE_BD . str_replace(PREFIXE_BD, '', $db);
    $con = mysqli_connect(HOST, USER, PASS, $baseDeDonnees) or die("Error : ");
    $requete = "SHOW TABLES FROM " . $baseDeDonnees;

    $execution_requete = mysqli_query($con, $requete);

    $tables = [];
    if (mysqli_num_rows($execution_requete) > 0) {
        while ($lignes = mysqli_fetch_row($execution_requete)) {
            $tables[] = $lignes[0];
        }
    }
    return $tables;
}

function uploadFile($fichier, $nom_enregistre, $chemin, $ext = array('pdf', 'doc', 'docx', 'png', 'jpg', 'jpeg'), $maxfilesize = _MAX_AVATAR_SIZE)
{
    if (isset($fichier["name"]) && (!empty($fichier["name"]))) {
        $poidsFichier = filesize($fichier['tmp_name']);
        if (($poidsFichier <= $maxfilesize) && ($poidsFichier > 0)) {
            $fileinfo = pathinfo($fichier["name"]);
            $nomFichier = $nom_enregistre . "." . $fileinfo["extension"];

            $temporary = addslashes($fichier['tmp_name']);
            $tab_extension = is_array($ext) ? $ext : array('pdf', 'doc', 'docx', 'png', 'jpg', 'jpeg');

            if (in_array(strtolower($fileinfo["extension"]), $tab_extension)) {
                return copy($temporary, $chemin . $nomFichier) ?
                    array($chemin, $nomFichier, $fileinfo["extension"]) :
                    array(false, "Le fichier n'a pas été copié.");
            }

            return array(false, "L'extension du fichier n'est pas correcte.");
        }
        $tailleEnMo = _MAX_AVATAR_SIZE / (1024 * 1024);
        return array(false, "La taille du fichier dépasse la taille autorisée (" . $tailleEnMo . " Mo).");
    }

    return array(false, "Le fichier est vide.");
}

function uploadFileBlob($fichier, $nom_enregistre, $chemin, $defaultextension = null)
{

    $mimetype = ["image/png" => "png", "image/jpeg" => "jpeg", "image/jpg" => "jpg"];
    if (isset($fichier["name"]) && (!empty($fichier["name"]))) {

        $temporary = addslashes($fichier['tmp_name']);
        $extension = isset($mimetype[$fichier['type']]) ? $mimetype[$fichier['type']] : false;

        if ($extension) {

            $nomFichier = $nom_enregistre . "." . (is_null($defaultextension) ? $extension : $defaultextension);
            $pathname = $chemin . basename($nomFichier);

            return (is_dir($chemin) && (move_uploaded_file($temporary, $pathname) || copy($temporary, $pathname))) ?
                array($chemin, $nomFichier, $extension) :
                array(false, "La copie n'a pas lieu. Vérifiez que le repertoire existe.");
        }

        return array(false, "L'extension du fichier n'est pas correcte.");
    }

    return array(false, "Le fichier est vide.");
}

function exportDB($DB, $path, $onFile = true, $tablesANePasVider = [])
{
    $baseDeDonnees = PREFIXE_BD . str_replace(PREFIXE_BD, '', $DB);
    $con = mysqli_connect(HOST, USER, PASS, $baseDeDonnees) or die("Erreur de connexion à la base de données (" . $baseDeDonnees . ") : ");
    $tables_in_database = mysqli_query($con, "SHOW TABLES");
    $tables = [];
    if (mysqli_num_rows($tables_in_database) > 0) {
        while ($row = mysqli_fetch_row($tables_in_database)) {
            array_push($tables, $row[0]);
        }
    }
    $contents = "--\n-- Database: `" . $baseDeDonnees . "`\n--\n-- --------------------------------------------------------\n\n\n\n";
    foreach ($tables as $table) {
        #Get the query for table creation
        $table_query = mysqli_query($con, "SHOW CREATE TABLE " . $table);
        $table_query_res = mysqli_fetch_row($table_query);
        $contents .= "--\n-- Table structure for table `" . $table . "`\n--\n\n";
        $contents .= $table_query_res[1] . ";\n\n\n\n";

        if (!in_array($table, $tablesANePasVider) && count($tablesANePasVider) > 0) {
            $contents .= "--\n-- Truncate table `" . $table . "`\n--\n\n";
            $contents .= "TRUNCATE TABLE " . $table . ";\n\n\n\n";
        }

        //Si le tableau est vide on prend tout, ou on prend uniquement les données des tables indiquées
        if (in_array($table, $tablesANePasVider) || count($tablesANePasVider) == 0) {
            $result = mysqli_query($con, "SELECT * FROM " . $table);
            $no_of_columns = mysqli_num_fields($result);
            $no_of_rows = mysqli_num_rows($result);

            $insert_limit = 100;
            $insert_count = 0;
            $total_count = 0;
            while ($result_row = mysqli_fetch_row($result)) {
                /**
                 * For the first time when $insert_count is 0 and when $insert_count reached the $insert_limit 
                 * and again set to 0 this if condition will execute and append the INSERT query in the sql file. 
                 */
                if ($insert_count == 0) {
                    $contents .= "--\n-- Dumping data for table `" . $table . "`\n--\n\n";
                    $contents .= "INSERT INTO " . $table . " VALUES ";
                }
                #Values part of an INSERT query will start from here eg. ("1","mitrajit","India"),
                $insert_query = "";
                $contents .= "\n(";
                for ($j = 0; $j < $no_of_columns; $j++) {
                    #Replace any "\n" with "\\n" escape character.
                    #addslashes() function adds escape character to any double quote or single quote eg, \" or \'
                    $insert_query .= "'" . str_replace("\n", "\\n", addslashes($result_row[$j])) . "',";
                }
                #Remove the last unwanted comma (,) from the query.
                $insert_query = substr($insert_query, 0, -1) . "),";
                /*
                 *  If $insert_count reached to the insert limit of a single INSERT query
                 *  or $insert count reached to the number of total rows of a table
                 *  or overall total count reached to the number of total rows of a table
                 *  this if condition will exceute.
                 */
                if ($insert_count == ($insert_limit - 1) || $insert_count == ($no_of_rows - 1) || $total_count == ($no_of_rows - 1)) {
                    #Remove the last unwanted comma (,) from the query and append a semicolon (;) to it
                    $contents .= substr($insert_query, 0, -1);
                    $contents .= ";\n\n\n\n";
                    $insert_count = 0;
                } else {
                    $contents .= $insert_query;
                    $insert_count++;
                }
                $total_count++;
            }
        }

        // if(in_array($table , ['crm_np_affaire_donnees_comptables_champs', 'crm_np_affaire_calculette_champs'])){
        if (in_array($table, ['crm_np_affaire_calculette_champs'])) {
            $theWhere = ($table == "crm_np_affaire_donnees_comptables_champs") ? " source_champ = 'cree'" : " natif = -1";
            $contents .= "DELETE FROM " . $table . " WHERE" . $theWhere . ";";
        }
    }
    if ($onFile == true) {
        $fp = fopen($path . '/' . $baseDeDonnees . '.sql', 'w+');
        fwrite($fp, $contents);
        return true;
    } else {
        return $contents;
    }
}

# La fonction suivante génére les variable constante dans le fichier const_db

function GenConstDb($model, $route, $nom_bd)
{
    # $model->release();
    # $model->setClause('nom_bd="'.$nom_bd.'" AND actif=1');
    # $model->setChamp("nom_bd");
    # $model->setTable('crm_np_agences');
    # $bd = PREFIXE_BD.$model->getData()[0]->nom_bd;
    $baseDeDonnees = PREFIXE_BD . str_replace(PREFIXE_BD, '', $nom_bd);
    $contenu = $model->makeconstfileBis($route, 'CRM_NP_', $baseDeDonnees); /* Exemples. */

    $fichier = fopen($route . '/storage/agences/' . $nom_bd . '/const_bd.php', 'w');

    fputs($fichier, str_replace(', ;', ';', $contenu));

    fclose($fichier);

    return true;
}

/* Cette fonction permet de generer les données de l'agence dans un fichier JSON.
 * 
 * $fichier : agence.json
 * 
 * $agence : la base de données de l'agence.
 * 
 *  */

function JSONGen($model, $route, $nom_bd)
{
    $model->setTable("crm_np_agences");
    $model->setClause('nom_bd = "' . $nom_bd . '" AND actif=1');
    $model->setChamp("*");
    $model->setOrderBy("id");
    $agence = $model->getRow();

    if ($agence != null) {
        $donnees["agence"] = (array) $agence;
        $donnees["agence"]["nom_bd"] = PREFIXE_BD . $agence[0]->nom_bd;
        $donnees["agence"]["nom_dossier"] = $agence[0]->nom_bd;
        if ($agence[0]->id_ville > 0) {
            $model->setTable("crm_np_ville");
            $model->setClause('id=' . $agence[0]->id_ville . '');
            $model->setChamp("*");
            $model->setOrderBy("");
            $ville = $model->getData();
            $donnees["agence"]["ville"] = $ville[0];
        }

        if (!empty($agence[0]->code_forme_juridique)) {
            $listFormJuridique = $model->getElementType(['FORME_JURIDIQUE']);
            $donnees["agence"]["forme_juridique"] = isset($listFormJuridique['FORME_JURIDIQUE'][$agence[0]->code_forme_juridique]['libelle']) ? $listFormJuridique['FORME_JURIDIQUE'][$agence[0]->code_forme_juridique]['libelle'] : "";
        } else {
            $donnees["agence"]["forme_juridique"] = "";
        }
    }

    $model->release();

    $baseDeDonnees = PREFIXE_BD . str_replace(PREFIXE_BD, '', $nom_bd);
    $model->setTable($baseDeDonnees . ".crm_np_site_settings");
    # $model->setTable(BASE_REF . "crm_np_site_settings");
    $model->setClause('');
    $model->setChamp("*");
    $model->setOrderBy("");
    $seeting = $model->getData();
    $setings = [];
    for ($i = 0; $i < count($seeting); $i++) {
        if (!empty($seeting[$i]->valeur[0]) && ($seeting[$i]->valeur[0] == "#")) {
            $tab = explode("##", substr($seeting[$i]->valeur, 1, -1));
            $setings[$seeting[$i]->nom] = $tab;
        } else {
            $setings[$seeting[$i]->nom] = $seeting[$i]->valeur;
        }
    }
    $donnees["site_settings"] = $setings;

    $fp = fopen($route . _STORAGE_PATH . 'agences/' . $nom_bd . '/config.json', 'w');
    fwrite($fp, json_encode($donnees));
    fclose($fp);
}

function JSONGenGlobalAgence($model, $route)
{
    $model->setTable("crm_np_agences");
    $model->setClause('actif=1');
    $model->setChamp("id, nom_bd, site_web, logo");
    $model->setOrderBy("id");
    $agences = $model->getData();

    $agenceTab = [];

    foreach ($agences as $a) {
        $agenceTab[$a->site_web] = (array) $a;
    }

    $fp = fopen($route . '/frontend/config.json', 'w');
    fwrite($fp, json_encode($agenceTab));
    fclose($fp);
}

function JSONGenFromCommun($model, $route, $toGen = 'villes')
{
    $table = 'crm_np_ville';
    switch ($toGen) {
        case 'villes':
            $table = 'crm_np_ville';
            break;
        case 'department':
            $table = 'crm_np_department';
            break;
        case 'user':
            $table = 'crm_np_utilisateur';
            break;
    }
    $model->setTable($table);
    $model->setClause('');
    $model->setChamp("*");
    $model->setOrderBy("id");
    $datas = $model->getData();
    $donnees = null;
    foreach ($datas as $data) {
        if ($toGen === "department") {
            $donnees['byId'][$data->id] = (array) $data;
            $donnees['byNum'][$data->num] = (array) $data;
        } else {
            $donnees[$data->id] = (array) $data;
        }
    }

    $fp = fopen($route . _STORAGE_PATH . 'agences/' . $toGen . '.json', 'w');
    fwrite($fp, json_encode($donnees));
    fclose($fp);
}

function rappelWeek($model)
{
    global $app;
    $model->release();
    $model->setTable($_SESSION[$app][_USER_]->nom_bd . '.crm_np_tache t, crm_np_utilisateur u');
    $model->setChamp('*');
    $model->setLimit(10);
    $model->setGroupBy('t.id');
    $model->setOrderBy('t.date_debut ASC');

    $model->setClause('t.code_type_tache="RAPPEL" AND t.attribue_a=u.id AND DATE(t.date_echeance) >= CURDATE() AND t.date_echeance < CURDATE() + INTERVAL 7 DAY AND (t.attribue_a =' . $_SESSION[$app][_USER_]->id . ' OR t.id in (SELECT id_tache from ' . $_SESSION[$app][_USER_]->nom_bd . '.crm_np_tache_concerne where id_concerne=' . $_SESSION[$app][_USER_]->id . ' AND code_source_concerne="UTILISATEUR" AND deleted = -1))');
    $AllRappelWeek_s = $model->getData();
    return $AllRappelWeek_s;
}

function tacheWeek($model)
{
    global $app;
    $model->release();
    $model->setTable($_SESSION[$app][_USER_]->nom_bd . '.crm_np_tache t, crm_np_utilisateur u');
    $model->setChamp('*');
    $model->setLimit(10);
    $model->setGroupBy('t.id');
    $model->setOrderBy('t.date_debut ASC');
    $model->setClause('t.code_type_tache="EVENEMENT" AND t.attribue_a=u.id AND DATE(t.date_echeance) >= CURDATE() AND t.date_echeance < CURDATE() + INTERVAL 7 DAY AND (t.attribue_a =' . $_SESSION[$app][_USER_]->id . ' OR t.id in (SELECT id_tache from ' . $_SESSION[$app][_USER_]->nom_bd . '.crm_np_tache_concerne where id_concerne=' . $_SESSION[$app][_USER_]->id . ' AND code_source_concerne="UTILISATEUR" AND deleted = -1))');
    $AllTacheWeek_s = $model->getData();

    return $AllTacheWeek_s;
}

function JSONGenAffaireChamp($model, $route, $nom_bd, $nom_doss)
{
    //$model->setTable($nom_bd . ".crm_np_affaire_vis_champ");
    $model->setTable(BASE_REF . "crm_np_affaire_vis_champ");
    $model->setClause('');
    $model->setChamp("*");
    $model->setOrderBy("id");
    $affairechamp = $model->getData();

    $donnees = [];

    foreach ($affairechamp as $champ) {
        $donnees[$champ->code_type_transaction][$champ->nom_champ] = $champ;
    }

    //$fp = fopen($route . _STORAGE_PATH . 'agences/' . $nom_doss . '/liste_champs_affaires.json', 'w');
    $fp = fopen($route . _STORAGE_PATH . 'agences/' . DOSSIER_REF . '/liste_champs_affaires.json', 'w');
    fwrite($fp, json_encode($donnees));
}

function genLEFJSON($model, $route, $nom_bd = '')
{
    global $app;

    $nom_b = BASE_REF;
    $nom_bd = ($nom_bd != '') ? $nom_bd : $_SESSION[$app][_USER_]->nom_dossier;
    $model->release();
    $model->setChamp("*");
    $model->setTable("crm_np_liste_element_form");
    $model->setClause("actif = 1");
    $model->setOrderBy("rang");

    $donnees = $model->getData();

    $model->setTable($nom_b . "crm_np_liste_element_form");
    $model->setClause("actif = 1");
    $model->setOrderBy("rang");
    $donnees = array_merge($donnees, $model->getData());

    $retourListe = [];
    $retourForm = [];

    foreach ($donnees as $valeur) {

        $retourListe[$valeur->type][$valeur->code] = ['code' => $valeur->code, 'libelle' => $valeur->libelle, 'description' => $valeur->description, 'pictogramme' => $valeur->pictogramme];

        $retourForm[$valeur->type][] = (object) ['code' => $valeur->code, 'libelle' => $valeur->libelle, 'description' => $valeur->description, 'pictogramme' => $valeur->pictogramme];
    }

    $JSONDATA['form'] = $retourForm;
    $JSONDATA['liste'] = $retourListe;

    $fp = fopen($route . _STORAGE_PATH . 'agences/' . $nom_bd . '/liste_element_form.json', 'w');
    fwrite($fp, json_encode($JSONDATA));
    fclose($fp);
}

#get JSON file to array

function JSONFileToArray($path)
{
    global $app;
    global $route, $model;

    $array = [];

    if (file_exists($path) === false) {
        JSONGen($model, $route, $_SESSION[$app][_USER_]->nom_dossier);
    }
    if (file_exists($path)) {
        $config = file_get_contents($path);
        $array = json_decode($config, true);
    }
    return $array;
}

function getDBName($model, $idfk, $fkTablename = CRM_NP_AGENCES)
{
    $model->release();
    $model->setTable(CRM_NP_AGENCES);
    $model->setClause('id = ' . (($fkTablename === CRM_NP_AGENCES) ? $idfk : '(SELECT id_agence FROM ' . $fkTablename . ' WHERE id = "' . $idfk . '") AND actif=1'));
    $model->setChamp(AGENCES_NOM_BD);
    $agence = $model->getData();

    if (count($agence) === 0) {
        return false;
    }

    return $agence[0]->nom_bd;
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

function createFolder($dest, $defaut)
{
    if (!file_exists($dest)) {
        mkdir($dest, 0755);
    }
    foreach ($iterator = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($defaut, \RecursiveDirectoryIterator::SKIP_DOTS),
        \RecursiveIteratorIterator::SELF_FIRST
    ) as $item) {
        if ($item->isDir()) {
            mkdir($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
        } else {
            copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
        }
    }
}

function uploadPicture($photo, $nom_enregistre, $chemin, $largeur, $suprimOriginal = 'non', $crop = true)
{
    # Si le nom de la photo n'est pas vide.
    if (isset($photo["error"]) && ((int) $photo["error"] === 0)) {

        # Traitement du nom de la photo.
        $tabExtensionPhoto = explode('.', addslashes($photo["name"]));
        $idExtensionPhoto = count($tabExtensionPhoto) - 1;
        $nomFichier = $nom_enregistre . "." . strtolower($tabExtensionPhoto[$idExtensionPhoto]);

        $nomFichierOriginal = 'original_' . $nomFichier;

        # nom temporaire sur le serveur:
        $nomTemporaire = addslashes($photo['tmp_name']);

        # On vérifie l'extention.
        if (in_array(strtolower($tabExtensionPhoto[$idExtensionPhoto]), array('jpeg', 'jpg', 'gif', 'png'))) {

            copy($nomTemporaire, $chemin . $nomFichierOriginal);

            if ($crop) {
                #creation de la miniature
                $taille = getimagesize($nomTemporaire);

                $hauteur = $taille[1] * ($largeur / $taille[0]);

                miniature($chemin . $nomFichierOriginal, $nomFichier, $chemin, $largeur, $hauteur);

                if ($suprimOriginal == 'oui') {
                    unlink($chemin . $nomFichierOriginal);
                }
            } else {
                rename($chemin . $nomFichierOriginal, $chemin . $nomFichier);
            }

            return array($chemin, $nomFichier);
        }

        return array(false, "L'extension du fichier n'est pas bon.");
    }

    global $e_loaded_pic;
    return array(false, (isset($e_loaded_pic[$photo["error"]])) ? $e_loaded_pic[$photo["error"]] : "Une erreur inconnu s'est produite lors du chargement de la photo.");
}

function miniatureheight($filename, $largeur)
{
    $taille = getimagesize($filename);
    return $taille[1] * ($largeur / $taille[0]);
}

function miniature($file, $output, $output_folder, $width, $height)
{

    if (!file_exists($output_folder . $output)) {

        if (!fopen($output_folder . $output, "xb")) {

            echo "Error in the function 'miniature', the picture '" . $output . "' doesn't exist and faile to create.\nPage:'" . $_SERVER['PHP_SELF'] . "'";
            return false;
        }
    } elseif (filemtime($output_folder . $output) < filemtime($file)) {
        if (!fopen($output_folder . $output, "wb")) {
            echo "Error in the function 'miniature', the picture '" . $output . "' doesn't exist and faile to create.\nPage:'" . $_SERVER['PHP_SELF'] . "'";
            return false;
        }
    } else {
        return $output;
    }

    #ouverture de l'image et calcul des hauteurs
    $image_src = imagecreatefrom($file);
    $image_dest = imagecreatetruecolor($width, $height);
    $width_src = imagesx($image_src);
    $height_src = imagesy($image_src);

    if (!imagecopyresampled($image_dest, $image_src, 0, 0, 0, 0, $width, $height, $width_src, $height_src)) {
        echo "Error in the function 'miniature', the picture '" . $output . "' isn't resized.\nPage:'" . $_SERVER['PHP_SELF'] . "'";
        return false;
    }

    imagepng($image_dest, $output_folder . $output);

    return $output;
}

/**
 * Fonction permettant de créer une image selon son type
 * 
 * @param String $url L'url de l'image
 * @return Ressouce la ressource de l'image
 */
function imagecreatefrom($url)
{
    $info = getimagesize($url);
    switch ($info[2]) {
        case IMAGETYPE_GIF:
            $res = imagecreatefromgif($url);
            break;
        case IMAGETYPE_JPEG:
            $res = imagecreatefromjpeg($url);
            break;
        case IMAGETYPE_PNG:
            $res = imagecreatefrompng($url);
            break;
        default:
            $res = false;
            break;
    }

    return $res;
}

function createDataBase($nomBase, $templateSql, $onFile = true)
{
    $link = mysqli_connect(HOST, USER, PASS);

    # Check connection
    if ($link === false) {
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }

    # Attempt create database query execution
    $baseDeDonnees = PREFIXE_BD . str_replace(PREFIXE_BD, '', $nomBase);
    $sql = "CREATE DATABASE " . $baseDeDonnees . " character set UTF8 collate utf8_general_ci";
    if (mysqli_query($link, $sql)) {
        $res = "Database created successfully";
    } else {
        $res = "ERROR: Could not able to execute $sql. " . mysqli_error($link);
    }

    mysqli_select_db($link, $baseDeDonnees) or die('Error selecting MySQL database: ');
    if ($onFile == true) {
        $sql = file_get_contents($templateSql);
    } else {
        $sql = $templateSql;
    }

    if (mysqli_multi_query($link, $sql)) {
        do {
            /* sStockage du premier résultat */
            // if ($result = mysqli_store_result($link)) {
            //     while ($row = mysqli_fetch_row($result)) {
            //         printf("%s\n", $row[0]);
            //     }
            //     mysqli_free_result($result);
            // }
            /* Affichage d'une séparation */
            if (mysqli_more_results($link)) {
                continue;
            }
        } while (mysqli_more_results($link) && mysqli_next_result($link));
        $res = "success";
    } else {
        $res = "error";
    }
    # Close connection
    mysqli_close($link);

    return $res;
}

#Création de a signature des mails

function signature($info = [])
{
    $signature = '<hr/>';
    /* $signature .= '<table cellspacing="10">
      <tr>
      <td colspan="2"><a href="' . HTTP_PATH . '" target="_BLANK"><img src = "' . $info['logo'] . '" width="100"></a></td>
      <td valign = "top">' . $info["information"] . '</td>
      </tr>
      </table>'; */
    $signature .= $info["information"];

    return $signature;
}

#Cette fonction prépare le sujet du mail, le nameFrom, l'email From et la signature du mail
#  param[0] = $configJson, param[1] = $infoConseiller, param[2] = $infoConseiller"

function prepareMail($route, $sujet, $message, $params = [], $expsite = null)
{

    global $app;

    $info_agence['agence']['raison_sociale'] = NETPRO_RAISON_SOCIALE;
    $info_agence['agence']['forme_juridique'] = NETPRO_FORME_JURIDIQUE;
    $info_agence['agence']['adresse'] = NETPRO_ADRESSE;
    $info_agence['agence']['cp'] = NETPRO_CODE_POSTAL;
    $info_agence['agence']['nom_ville'] = NETPRO_VILLE;
    $info_agence['agence']['informations_conseiller'] = "";
    $info_agence['agence']['coordonnees_conseiller'] = "";
    $info_agence['agence']['email_site'] = NETPRO_EMAIL;
    $info_agence['agence']['telephone'] = NETPRO_TELEPHONE;
    $info_agence['agence']['site_web'] = NETPRO_SITE_WEB;
    $info_agence['agence']['logo'] = _STORAGE_PATH . 'agences/agenceModele/logo.svg';
    $info_agence['agence']['signature_mail'] = MODELE_SIGNATURE_EMAIL;

    if (isset($params[0]) && count($params[0])) {

        $info_agence = $params[0];
        $info_agence['agence']['informations_conseiller'] = '';
        if (isset($params[1]) && !empty($params[1])) {
            $info_agence['agence']['informations_conseiller'] = 'Votre conseiller : ' . $params[1] . '</br>';
        }
        $info_agence['agence']['coordonnees_conseiller'] = '';
        if (isset($params[2]) && !empty($params[2])) {
            $info_agence['agence']['coordonnees_conseiller'] = 'Coordonnées conseiller : ' . $params[2] . '</br>';
        }
        if (!empty($info_agence['agence']['logo'])) {
            $info_agence['agence']['logo'] = _STORAGE_PATH . 'agences/' . $info_agence['agence']['nom_dossier'] . '/' . $info_agence['agence']['logo'];
        }
        $info_agence['agence']['nom_ville'] = isset($info_agence['agence']['ville']['nom_reel']) ? $info_agence['agence']['ville']['nom_reel'] : "";
        $info_agence['agence']['telephone'] = $info_agence['agence']['telephone_1'];

        $prefixe_sujet = $info_agence['agence']['enseigne'];
        $expediteur = $info_agence['agence']['email_site'];
    } else {
        $prefixe_sujet = PROJECT_NAME;
        $expediteur = isset($_SESSION[$app][_USER_]->email) ? $_SESSION[$app][_USER_]->email : $expsite;
    }

    $signature['logo'] = $info_agence['agence']['logo'];
    $signature['information'] = str_replace(['#enseigne#', '#raison_sociale#', '#forme_juridique#', '#adresse#', '#code_postal#', '#ville#', '#informations_conseiller#', '#coordonnees_conseiller#', '#email#', '#telephone#', '#site_web#'], [$info_agence['agence']['enseigne'], $info_agence['agence']['raison_sociale'], $info_agence['agence']['forme_juridique'], $info_agence['agence']['adresse'], $info_agence['agence']['cp'], $info_agence['agence']['nom_ville'], $info_agence['agence']['informations_conseiller'], $info_agence['agence']['coordonnees_conseiller'], $info_agence['agence']['email_site'], $info_agence['agence']['telephone'], $info_agence['agence']['site_web']], $info_agence['agence']['signature_mail']);
    $message .= signature($signature);

    $sujet = $prefixe_sujet . " - " . $sujet;

    $retour = ['prefixe_sujet' => $prefixe_sujet, 'expediteur' => $expediteur, 'sujet' => $sujet, 'message' => $message];
    return $retour;
}

#Cette fonction génére un numéro de série

function genereNumSerie($numero, $longueur = 6)
{
    $numSerie = $numero;
    $aRajouter = $longueur - strlen($numero);

    if ($aRajouter > 0) {
        for ($i = 0; $i < $aRajouter; $i++) {
            $numSerie = "0" . $numSerie;
        }
    }

    return $numSerie;
}

function generate_link($site_url, $module, $action = '', $id = '')
{

    $link = $site_url . "/" . gen_title_filter($module);
    $link .= !empty($action) ? "/" . gen_title_filter($action) : "";
    $link .= !empty($id) ? "/" . base64_encode($id) : "";

    return str_replace(array("\r", "\n"), '', $link);
}

function troncate($chaine, $lg_max)
{
    if (strlen($chaine) > $lg_max) {
        $chaine = substr($chaine, 0, $lg_max);
        $last_space = strrpos($chaine, " ");
        if ($last_space === false) {
            $last_space = strrpos($chaine, "-");
        }
        if ($last_space === false) {
            $last_space = strrpos($chaine, "_");
        }
        $chaine = substr($chaine, 0, $last_space) . "...";
    }
    return $chaine;
}

#Cette fonction deleted un dossier non vide et son contenu

function clearDir($dossier, $strict = true)
{
    $ouverture = opendir($dossier);
    if (!$ouverture)
        return;
    while ($fichier = readdir($ouverture)) {
        if ($fichier == '.' || $fichier == '..')
            continue;
        if (is_dir($dossier . "/" . $fichier)) {
            $r = clearDir($dossier . "/" . $fichier);
            if (!$r)
                return false;
        } else {
            $r = unlink($dossier . "/" . $fichier);
            if (!$r)
                return false;
        }
    }
    closedir($ouverture);
    if ($strict) {
        $r = rmdir($dossier);
        if (!$r)
            return false;
    }
    return true;
}

/* * ***** Fin Réellement utilisé*********** */

#Constructeur d'url fiche détaillée

function generate_link_detail($site_url, $departement, $ville, $titre, $id)
{

    $link = $site_url;
    $link .= !empty($departement) ? "/" . $departement : "";
    $link .= !empty($ville) ? "/" . $ville : "";
    $link .= "/" . gen_title_filter($titre, false, false);
    $link .= "/ref-" . base64_encode($id);

    return str_replace(array("\r", "\n"), '', $link);
}

function generate_link_localisation($site_url, $departement = '', $ville = '')
{

    $link = $site_url;
    $link .= !empty($departement) ? "/" . $departement : "";
    $link .= !empty($ville) ? "/" . $ville : "";

    return str_replace(array("\r", "\n"), '', $link);
}

#Générateur de texte sans caractères spéciaux ni d'espace

function gen_title_filter($title, $compact = true, $nocasechange = true, $case = 'lower', $_underscore = false)
{
    trim($title);

    $title = preg_replace('#\$#', '', $title);
    $title = preg_replace('#Ç#', 'C', $title);
    $title = preg_replace('#ç#', 'c', $title);
    $title = preg_replace('#è|é|ê|ë#', 'e', $title);
    $title = preg_replace('#È|É|Ê|Ë#', 'E', $title);
    $title = preg_replace('#à|á|â|ã|ä|å#', 'a', $title);
    $title = preg_replace('#@|À|Á|Â|Ã|Ä|Å#', 'A', $title);
    $title = preg_replace('#ì|í|î|ï#', 'i', $title);
    $title = preg_replace('#Ì|Í|Î|Ï#', 'I', $title);
    $title = preg_replace('#ð|ò|ó|ô|õ|ö#', 'o', $title);
    $title = preg_replace('#Ò|Ó|Ô|Õ|Ö#', 'O', $title);
    $title = preg_replace('#ù|ú|û|ü#', 'u', $title);
    $title = preg_replace('#Ù|Ú|Û|Ü#', 'U', $title);
    $title = preg_replace('#ý|ÿ#', 'y', $title);
    $title = preg_replace('#Ý#', 'Y', $title);
    $title = preg_replace("#'|\]|\[|!|@|%|&|\(|\)|_|\^|\*|\+| |\?|,|:|;|°|<|>|/#", '-', $title);
    $title = preg_replace('#&deg;#', '-', $title);

    $title = preg_replace('#&amp;#', '-', $title);
    $title = preg_replace('#&Ccedil;#', 'C', $title);
    $title = preg_replace('#&ccedil;#', 'c', $title);
    $title = preg_replace('#&egrave;|&eacute;|&ecirc;|&euml;#', 'e', $title);
    $title = preg_replace('#&Egrave;|&Eacute;|&Ecirc;|&Euml;#', 'E', $title);
    $title = preg_replace('#&agrave;|&aacute;|&acirc;|&atilde;|&auml;|&aring;#', 'a', $title);
    $title = preg_replace('#&Agrave;|&Aacute;|&Acirc;|&Atilde;|&Auml;|&Aring;#', 'A', $title);
    $title = preg_replace('#&igrave;|&iacute;|&icirc;|&iuml;#', 'i', $title);
    $title = preg_replace('#&Igrave;|&Iacute;|&Icirc;|&Iuml;#', 'I', $title);
    $title = preg_replace('#&otilde;|&ograve;|&oacute;|&ocirc;|&otilde;|&ouml;#', 'o', $title);
    $title = preg_replace('#&Ograve;|&Oacute;|&Ocirc;|&Otilde;|&Ouml;#', 'O', $title);
    $title = preg_replace('#&ugrave;|&uacute;|&ucirc;|&uuml;#', 'u', $title);
    $title = preg_replace('#&Ugrave;|&Uacute;|&Ucirc;|&Uuml;#', 'U', $title);
    $title = preg_replace('#&yacute;|&yuml;#', 'y', $title);
    $title = preg_replace('#&Yacute;#', 'Y', $title);

    $title = preg_replace("#-+#", '-', $title);
    if ($compact) {
        $title = preg_replace("#-#", '', $title);
    } else {
        if ($_underscore) {
            $title = preg_replace("#-#", '_', $title);
        }
    }

    return ($nocasechange) ? $title : (($case == 'lower') ? strtolower($title) : strtoupper($title));
}

#Contrôle format mail

function isMail($adresse)
{
    return filter_var($adresse, FILTER_VALIDATE_EMAIL);
}

#Contrôle format mail

function isURL($url)
{
    return filter_var($url, FILTER_VALIDATE_URL);
}

function isTel($tel, $format = 'free')
{

    $pattern = [
        "free" => "([-. ]?[0-9]{2})+",
        "fr" => "^(?:0|\(?\+33\)?\s?|0033\s?)[1-79](?:[\.\-\s]?\d\d){4}",
        "fr" => "^(?:0|\(?\+33\)?\s?|0033\s?)[1-79](?:[\.\-\s]?\d\d){4}",
        "fr" => "^(?:0|\(?\+33\)?\s?|0033\s?)[1-79](?:[\.\-\s]?\d\d){4}",
    ];
    return (isset($pattern[$format])) ? (bool) preg_match("#" . $pattern[$format] . "$#", $tel) : false;
}

/*
 * Reconversion de datetime francaise - anglaise ou inversement
 */

function convertDateTime($date, $delimiteur = '-', $choix = 'fr')
{
    $newDate = '';
    if (!empty($date)) {
        $tableauGlobal = explode(' ', trim($date));
        $tableaudate = explode($delimiteur, $tableauGlobal[0]);
        if (count($tableaudate) == 3) {
            #français vers anglais
            if ($choix == 'en') {
                if (count($tableaudate) > 0) {
                    $newDate = $tableaudate[2] . "-" . $tableaudate[1] . "-" . $tableaudate[0];
                }
            }

            #anglais vers français
            if ($choix == 'fr') {
                if (count($tableaudate) > 0) {
                    $newDate = $tableaudate[2] . "/" . $tableaudate[1] . "/" . $tableaudate[0];
                }
            }

            if (isset($tableauGlobal[1])) {
                $newDate .= " " . $tableauGlobal[1];
            }
        }
    }
    $newDate = !empty($newDate) ? $newDate : $date;
    return $newDate;
}

function convDate($date, $format = 'fr', $hour = false)
{
    if (empty($date) || substr($date, 0, 10) === "0000-00-00" || substr($date, 0, 10) === "00-00-0000") {
        return null;
    }
    $formattxt = ($format === 'fr') ? ('d-m-Y' . ($hour ? ' H:i:s' : '')) : ('Y-m-d' . ($hour ? ' H:i:s' : ''));
    return date($formattxt, strtotime(($format === 'fr') ? $date : str_replace('/', '-', $date)));
}

function textDate($date, $formatTxt = '%d %b %Y - %R', $format = 'fr')
{
    if (empty($date) || substr($date, 0, 10) === "0000-00-00" || substr($date, 0, 10) === "00-00-0000") {
        return null;
    }

    return strftime($formatTxt, strtotime(($format === 'fr') ? $date : str_replace('/', '-', $date)));
}

function text2Date($date, $formatTxt = '%d %B %Y')
{
    if (empty($date) || substr($date, 0, 10) === "0000-00-00" || substr($date, 0, 10) === "00-00-0000") {
        return null;
    }

    return strptime($date, $formatTxt);
}

function fdate($fdate)
{
    $_ = [
        "janvier" => 1, "jan." => 1, "janv." => 1, "jan" => 1, "janv" => 1,
        "février" => 2, "fév." => 2, "fév." => 2, "févr." => 2, "févr" => 2,
        "mars" => 3,
        "avril" => 4, "avr." => 4, "avr" => 4,
        "mai" => 5,
        "juin" => 6,
        "juillet" => 7, "juil." => 7, "juil" => 7, "jui." => 7, "jui" => 7,
        "août" => 8,
        "septembre" => 9, "sept." => 9, "sept" => 9,
        "octobre" => 10, "oct." => 10, "oct" => 10,
        "novembre" => 11, "nov." => 11, "nov" => 11,
        "december" => 12, "décembre" => 12, "déc." => 12, "déc" => 12, "dec." => 12, "dec" => 12
    ];

    return str_replace(array_keys($_), array_values($_), $fdate);
}

# timestamp en millisecondes

function millitime()
{
    return round((microtime(true) * 1000));
}

/**
 * Cette fonction explode ou impode les liste_elements_form separes par ##
 * @param <type> $tomanup : chaine ou tableau
 */
function buildLef($tomanup, $act = "explode", $char = "#", $strict = true)
{
    if ($act === "implode") {
        if (is_array($tomanup) || is_object($tomanup)) {
            $cleaned = clean((array) $tomanup, $strict);
            return count($cleaned) ? $char . implode($char . $char, $cleaned) . $char : null;
        }

        return $char . $tomanup . $char;
    }

    return (is_array($tomanup) || is_object($tomanup)) ? (array) $tomanup : ((empty($tomanup)) ? [] : explode($char . $char, substr(trim($tomanup), 1, -1)));
}

/**
 * Cette fonction explode les abonnements passerelles ou les chaines du meme type
 * @param <type> $tomanup : tableau
 * @param <type> $dimension : dimension du tableau
 */
function getDataPassActive($tomanup, $dimension = 1)
{
    $tableau = [];
    $tomanup = (array) $tomanup;
    if (count($tomanup) > 0) {
        for ($i = 0; $i < count($tomanup); $i++) {
            if ($dimension == 1) {
                $pass = explode("::", $tomanup[$i]);
                if (!empty($pass[0])) {
                    $tableau[] = $pass[0];
                }
            } else {
                $tableauProvisoire = explode("::", $tomanup[$i]);
                if (!empty($tableauProvisoire[0])) {
                    $tableau[$tableauProvisoire[0]] = $tableauProvisoire[1];
                }
            }
        }
    }

    return $tableau;
}

function clean($array, $strict = true)
{
    foreach ($array as $key => $value) {
        if (empty($value) && $strict) {
            unset($array[$key]);
        }
        if ($value === "" && $strict === false) {
            unset($array[$key]);
        }
    }
    return array_values($array);
}

/**
 * Cette fonction génére un code aléatoire ayant une longueur
 * @param <type> $length
 */
function randomANUM($length, $mixed = false)
{

    $longueur = ($length ? $length : 4);
    $gen = "";
    $upperlower = ['strtolower', 'strtoupper'];
    for ($i = 1; $i <= $longueur; $i++) {
        $d = rand(1, 30) % 2;
        if ($mixed) {
            $gen .= $upperlower[array_rand($upperlower)]($d ? chr(rand(65, 90)) : chr(rand(48, 57)));
        } else {
            $gen .= ($d ? chr(rand(65, 90)) : chr(rand(48, 57)));
        }
    }

    return $gen;
}

function precedent($niveau)
{
?>
    <script>
        var niveau = <?php echo (float) $niveau; ?>;
        niveau = (niveau == 0) ? -1 : niveau;
        window.history.go(niveau);
    </script>
<?php
    return;
}

function checkRCS($rcs)
{
    if (!$rcs) {
        return False;
    }
    $rcs = htmlspecialchars($rcs);
    $ch = curl_init("https://www.infogreffe.fr/services/entreprise/rest/recherche/parPhrase?phrase=$rcs&typeProduitMisEnAvant=EXTRAIT");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $output = curl_exec($ch);
    curl_close($ch);

    $dec_obj = json_decode($output, True);
    $raison_sociale_arr = array();

    if (count($dec_obj) > 0) {
        foreach ($dec_obj as $obj) {
            # Search for the correct obj, that contains denomination
            # $dec_obj['entrepRCSStoreResponse']['items'][0]['libelleEntreprise'];
            if (is_array($obj)) {
                if (isset($obj['items'][0]['libelleEntreprise'])) {
                    $raison_sociale_arr['enseigne'] = $obj['items'][0]['libelleEntreprise'];
                    $raison_sociale_arr['address'] = $obj['items'][0]['adresse'];
                    $raison_sociale_arr['activite'] = $obj['items'][0]['activite'];
                    $raison_sociale_arr['siren'] = $obj['items'][0]['siren'] . $obj['items'][0]['nic'];
                    break;
                }
            }
        }
    }

    return $raison_sociale_arr;
}

function getFileSize($aPath = '', $aShort = true, $aCheckIfFileExist = true)
{
    if ($aCheckIfFileExist && !file_exists($aPath)) {
        return 0;
    }
    $size = filesize($aPath);
    if (empty($size)) {
        return '0 ' . ($aShort ? 'o' : 'octets');
    }

    $l = array();
    $l[] = array('name' => 'octets', 'abbr' => 'o', 'size' => 1);
    $l[] = array('name' => 'kilo octets', 'abbr' => 'ko', 'size' => 1024);
    $l[] = array('name' => 'mega octets', 'abbr' => 'Mo', 'size' => 1048576);
    $l[] = array('name' => 'giga octets', 'abbr' => 'Go', 'size' => 1073741824);
    $l[] = array('name' => 'tera octets', 'abbr' => 'To', 'size' => 1099511627776);
    $l[] = array('name' => 'peta octets', 'abbr' => 'Po', 'size' => 1125899906842620);
    foreach ($l as $k => $v) {
        if ($size < $v['size']) {
            return round($size / $l[$k - 1]['size'], 2) . ' ' . ($aShort ? $l[$k - 1]['abbr'] : $l[$k - 1]['name']);
        }
    }
    $l = end($l);
    return round($size / $l['size'], 2) . ' ' . ($aShort ? $l['abbr'] : $l['name']);
}

function FileGetInfo($aPath = '', $aCheckIfFileExist = true)
{
    if ($aCheckIfFileExist && !file_exists($aPath)) {
        return 0;
    }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    return finfo_file($finfo, $aPath);
}

#Pour lutter contre les spams

function detectTags($string)
{
    $resultat = -1;
    # Pas de code de la forme <tag attr=XXX>texte</tag>
    $testHTML = preg_match("/<[^>]+>/", $string);
    # Pas de code de la forme [tag attr=XXX]
    $testTag = preg_match("/[[^>]+]/", $string);
    # pas de lien commençant par http:#…
    $testURL = preg_match("#mailto|<a|href|http|https|www#", $string);

    if ($testHTML || $testTag || $testURL) {
        $resultat = 1;
    }
    return $resultat;
}

# Fonction pour détecter des expressions considérées comme spam dans un message
#stripos() - Recherche la position de la première occurrence dans une chaîne, sans tenir compte de la casse

function checkString($message)
{
    $list_exp_bannie = array("fx-brokers", "essayerudite", "yandex", "xrumer", "б", "ж", "л", "п", "ц", "bit.ly", "doctor", "weight", "costumers", "games");
    $resultat = -1;
    foreach ($list_exp_bannie as $key) {
        if (stripos($message, $key) !== false) {
            $resultat = 1;
            break;
        }
    }
    return $resultat;
}

/**
 * Début estimateur
 */
# Return an array with the price ['low', 'average', 'high']
function estimer_prix($ca)
{
    $ret = array(
        'low' => 0,
        'average' => 0,
        'high' => 0
    );
    $act_check = false;
    # TODO: check if global = SUM of rest
    if (isset($ca)) {
        $low_arr = array();
        $average_arr = array();
        $high_arr = array();

        if (isset($ca[1])) {
            # Transaction : between  0.25 et 0.35 of the average turn over
            $avg = sp_average($ca[1]);
            $low_arr[] = 0.25 * $avg;
            $average_arr[] = 0.30 * $avg;
            $high_arr[] = 0.35 * $avg;
            $act_check = true;
        }
        if (isset($ca[2])) {
            # Location : one time the turn over
            $avg = sp_average($ca[2]);
            $low_arr[] = $avg;
            $average_arr[] = $avg;
            $high_arr[] = $avg;
            $act_check = true;
        }
        if (isset($ca[3])) {
            # Gestion locative between  2.2 and 2.7 times  of the average turn over
            $avg = sp_average($ca[3]);
            $low_arr[] = 2.20 * $avg;
            $average_arr[] = 2.45 * $avg;
            $high_arr[] = 2.70 * $avg;
            $act_check = true;
        }
        if (isset($ca[4])) {
            # Syndic de copro between 0.8 and 1.5 times of the average turn over
            $avg = sp_average($ca[4]);
            $low_arr[] = 0.80 * $avg;
            $average_arr[] = 1.15 * $avg;
            $high_arr[] = 1.50 * $avg;
            $act_check = true;
        }
        if (count($low_arr) && count($average_arr) && count($high_arr)) {
            $ret = array(
                'low' => array_sum($low_arr),
                'average' => array_sum($average_arr),
                'high' => array_sum($high_arr)
            );
        }
    }

    return $ret;
}

# Special function to calculate average as agent wants

function sp_average($arr)
{
    # Remove zero values from array
    $arr = array_filter($arr);
    if (count($arr) <= 1) {
        # In case of empty arr, return 0
        return isset($arr[0]) ? $arr[0] : 0;
    } else {
        # Check if difference between values > 15%
        $big_diff = false;
        for ($i = 1; $i < count($arr); $i++) {
            # Always divide bigger / smaller
            $larr = array($arr[$i], $arr[$i - 1]);
            $logos = max($larr) / min($larr);
            if ($logos > 1.15) {
                $big_diff = true;
            }
        }
        if ($big_diff) {
            $prev_avg = array_sum($arr) / count($arr);
            return ($arr[0] + $prev_avg) / 2;
        } else {
            return array_sum($arr) / count($arr);
        }
    }
}

function handle_agence_info($input)
{
    $ret = array();
    # Handle the CA part, make sure it numeric only
    if (isset($input['ca'])) {
        foreach ($input['ca'] as $key => $val_arr) {
            $ret['ca'][intval($key)] = array_map('intval', $val_arr);
            # CA this year cannot equal 0
            if ($ret['ca'][intval($key)] == 0) {
                return false;
            }
        }
    }

    return $ret;
}

/**
 * Fin estimateur
 */
# Fonction pagination

function pagination($num, $per_page = 10, $page = 1, $url = '?')
{

    $total = $num;
    $adjacents = "2";

    $page = ($page == 0 ? 1 : $page);
    $start = ($page - 1) * $per_page;

    $prev = $page - 1;
    $next = $page + 1;
    $lastpage = ceil($total / $per_page);
    $lpm1 = $lastpage - 1;

    $pagination = "";
    if ($lastpage > 1) {
        $pagination .= "<ul class='pagination'>";
        $pagination .= "<li class='details' style='margin-top:2px'>Page $page à $lastpage</li>";
        if ($lastpage < 7 + ($adjacents * 2)) {
            for ($counter = 1; $counter <= $lastpage; $counter++) {
                if ($counter == $page) {
                    $pagination .= "<li><a class='current'>$counter</a></li>";
                } else {
                    $pagination .= "<li><a href='{$url}page=$counter'>$counter</a></li>";
                }
            }
        } elseif ($lastpage > 5 + ($adjacents * 2)) {
            if ($page < 1 + ($adjacents * 2)) {
                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                    if ($counter == $page) {
                        $pagination .= "<li><a class='current'>$counter</a></li>";
                    } else {
                        $pagination .= "<li><a href='{$url}page=$counter'>$counter</a></li>";
                    }
                }
                $pagination .= "<li class='dot'>...</li>";
                $pagination .= "<li><a href='{$url}page=$lpm1'>$lpm1</a></li>";
                $pagination .= "<li><a href='{$url}page=$lastpage'>$lastpage</a></li>";
            } elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
                $pagination .= "<li><a href='{$url}page=1'>1</a></li>";
                $pagination .= "<li><a href='{$url}page=2'>2</a></li>";
                $pagination .= "<li class='dot'>...</li>";
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                    if ($counter == $page) {
                        $pagination .= "<li><a class='current'>$counter</a></li>";
                    } else {
                        $pagination .= "<li><a href='{$url}page=$counter'>$counter</a></li>";
                    }
                }
                $pagination .= "<li class='dot'>..</li>";
                $pagination .= "<li><a href='{$url}page=$lpm1'>$lpm1</a></li>";
                $pagination .= "<li><a href='{$url}page=$lastpage'>$lastpage</a></li>";
            } else {
                $pagination .= "<li><a href='{$url}page=1'>1</a></li>";
                $pagination .= "<li><a href='{$url}page=2'>2</a></li>";
                $pagination .= "<li class='dot'>..</li>";
                for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                    if ($counter == $page) {
                        $pagination .= "<li><a class='current'>$counter</a></li>";
                    } else {
                        $pagination .= "<li><a href='{$url}page=$counter'>$counter</a></li>";
                    }
                }
            }
        }

        if ($page < $counter - 1) {
            $pagination .= "<li><a href='{$url}page=$next'>Suivant</a></li>";
            $pagination .= "<li><a href='{$url}page=$lastpage'>Dernier</a></li>";
        } else {
            $pagination .= "<li><a class='current'>Suivant</a></li>";
            $pagination .= "<li><a class='current'>Dernier</a></li>";
        }
        $pagination .= "</ul>\n";
    }

    return $pagination;
}

function pagDatas($model, $module, $page, $voir)
{

    $authentique = $page;
    $returned = [];

    do {
        $courant = (int) $page;
        $nRows = $model->getCount();
        $model->setLimit(($courant * $voir - $voir) . ", " . $voir);

        if ($nRows > $voir) {
            $returned['pagination'] = pagination($nRows, $voir, $courant, HTTP_PATH . "/$module");
        }

        $datas = $model->getData();
        $page--;

        if ($courant === 1 && $courant != $authentique) {
            return [-1];
        }
        if (count($datas) > 0 && $courant != $authentique) {
            return [-2, $courant];
        }
    } while (count($datas) == 0 && $courant != 1);

    $returned['datas'] = !empty($datas) ? $datas : null;

    return $returned;
}

function isUnique($model, $table, $clause, $field = "id", $kind = 'count')
{
    $model->release();
    $model->setTable($table);
    $model->setChamp($field);
    $model->setClause($clause);
    return ($kind === 'field') ? $model->getData(false) : $model->getCount($field);
}

function loadDataFromJson($array)
{
    $datasBrute = [];
    if (!isset($array["status"]) || $array["status"] === "en_creation") {
        foreach ($array as $tab => $tabValue) {
            if (is_array($tabValue) && ($tab !== "avatar")) {
                foreach ($tabValue as $field => $rowInfo) {
                    if ($rowInfo["name"] === "niveau_saisie") {
                        if (!isset($datasBrute["niveau_saisie"]) || $datasBrute["niveau_saisie"] < $rowInfo["value"]) {
                            $datasBrute[$rowInfo["name"]] = $rowInfo["value"];
                        }
                    } else if ($rowInfo["name"] === "id_ville" && !empty($rowInfo["value"])) {
                        $v_cp = explode("#", $rowInfo["value"]);
                        $datasBrute["id_ville"] = $v_cp[0];
                        $datasBrute["cp"] = $v_cp[1];
                    } else {
                        $datasBrute[$rowInfo["name"]] = $rowInfo["value"];
                    }
                }
            } else {
                if (($tab === "id") || ($tab === "avatar")) {
                    $datasBrute[$tab] = $tabValue; # On recupere l'avatar dans le fichier
                }
            }
        }
        if (isset($array["cree_par"]) && (int) $array["cree_par"] > 0) {
            $datasBrute["cree_par"] = $array["cree_par"];
        }
    }
    return $datasBrute;
}

function getFiles($folderpath, $beginingFilename = "", $fullfilepath = true, $filetype = [], $folderfile = 'file')
{

    $dir_iterator = new RecursiveDirectoryIterator($folderpath, RecursiveDirectoryIterator::SKIP_DOTS);
    $iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);
    $files = [];

    foreach ($iterator as $element) {
        $filename = $element->getFilename();
        $fullfilename = $element->getPathname();
        if ($filename === "." || $filename === "..") {
            continue;
        }
        if ($folderfile === 'file' && is_dir($fullfilename)) {
            continue;
        }
        if ($folderfile === 'folder' && is_file($fullfilename)) {
            continue;
        }
        if (!empty($beginingFilename) && count($filetype)) {
            $infosfile = pathinfo($filename);
            if ($beginingFilename === substr($filename, 0, strlen($beginingFilename)) && (isset($infosfile['extension']) && in_array($infosfile['extension'], $filetype))) {
                $files[] = ($fullfilepath) ? $fullfilename : $filename;
            }
            continue;
        }
        if (count($filetype)) {
            $infosfile = pathinfo($filename);
            if (isset($infosfile['extension']) && in_array($infosfile['extension'], $filetype)) {
                $files[] = ($fullfilepath) ? $element->getPathname() : $filename;
            }
            continue;
        }
        if (!empty($beginingFilename)) {
            if ($beginingFilename === substr($filename, 0, strlen($beginingFilename))) {
                $files[] = ($fullfilepath) ? $element->getPathname() : $filename;
            }
        }
    }
    return $files;
}

function getAllFoldersAndFiles($path, $authorized = '')
{

    $dir_iterator = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
    $iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);

    $tree = [];
    foreach ($iterator as $element) {
        $filename = $element->getFilename();
        if ($filename !== "." && $filename !== ".." && !$element->isDir()) {
            $files[] = $element->getPathname();
        }
    }
    return $files;
}

function sms($from, $to, $message = '')
{

    include_once $_SERVER["DOCUMENT_ROOT"] . '/core/plugins/osms/Osms.php';

    $config = array(
        'clientId' => 'm1IksG4cHFAqACBJvKZbP6U13KwXyfYf',
        'clientSecret' => 'XOc1sO7jkMVMP1OU'
    );

    $osms = new Osms($config);
    $osms->getTokenFromConsumerKey();

    $compteur = 0;
    if (!is_array($to)) {
        $sent = sendSms($osms, $from, $to, $message);
        return (!isset($sent['error']) || empty($sent['error'])) ? 1 : $sent['error'];
    }

    foreach ($to as $one) {
        $sent = sendSms($osms, $from, $one, $message);
        if (!isset($sent['error']) || empty($sent['error'])) {
            $compteur++;
        }
    }
    return $compteur;
}

function sendSms($osms, $emetteur, $destinataire, $message)
{
    $isSent = $osms->sendSms(
        # sender
        "tel:+221$emetteur",
        # receiver
        "tel:+221$destinataire",
        # message
        $message,
        'BeSport'
    );

    return $isSent;
}

# Vérifie si un dossier existe et crée les repertoires listés sinon

function getdir($roots, $dirname)
{
    $dirArr = explode("/", $dirname);
    $current = null;

    foreach ($dirArr as $dir) {
        if (empty($dir)) {
            continue;
        }
        $current .= $dir . "/";
        if (!is_dir($roots . $current)) {
            mkdir($roots . $current, 0755);
        }
    }
    return $roots . $dirname;
}

function notify()
{
    global $app, $oSmarty;
    if (isset($_SESSION[$app]["notification"]["erreur"])) {
        $oSmarty->assign("erreur", $_SESSION[$app]["notification"]["erreur"]);
        unset($_SESSION[$app]["notification"]);
    }

    if (isset($_SESSION[$app]["notification"]["succes"])) {
        $oSmarty->assign("succes", $_SESSION[$app]["notification"]["succes"]);
        unset($_SESSION[$app]["notification"]);
    }
}

function putHTTP($url)
{
    if (preg_match('@(' . HTTP_PROTOCOLE . ':#)@i', $url) === 0) {
        return HTTP_PROTOCOLE . $url;
    }

    return $url;
}

function moveUploadedFile($files, $uploads_dir)
{
    $tmp_name = $files["tmp_name"];
    $name = basename($files["name"]); # basename() peut empêcher les attaques de système de fichiers;  la validation/assainissement supplémentaire du nom de fichier peut être approprié
    $extTab = explode(".", $name);
    $ext = $extTab[count($extTab) - 1];
    $okDir = (in_array($ext, ["png", "jpg", "jpeg"])) ? $ext : "png";
    move_uploaded_file($tmp_name, $uploads_dir . "." . $okDir);
}

function agencies($model, $clause = "actif = 1")
{
    # Récupération des agences
    $model->release();
    $model->setTable("crm_np_agences");
    $model->setChamp("id, raison_sociale, enseigne, nom_bd, nombre_comptes, connexions_autorisees");
    $model->setClause($clause);
    $model->setOrderBy("enseigne ASC");
    $agences = $model->getData(false, false, 'id');

    return $agences;
}

function news($model)
{
    # Récupération des actualites
    $model->release();
    $model->setTable("crm_np_actualite_netprofil");
    $model->setChamp("*");
    $model->setClause("actif = 1");
    $model->setOrderBy("date DESC");
    $model->setLimit("3");
    return $model->getData(true, false, "id");
}

function listeIdUsers($model, $idAgence, $exception = [], $type = '')
{
    # Récupération des agences
    $model->release();
    $model->setTable(CRM_NP_UTILISATEUR);
    $model->setChamp([UTILISATEUR_ID]);
    $clauseAdd = count($exception) > 0 ? " AND id NOT IN (" . implode(',', $exception) . ")" : '';
    $clauseAdd .= !empty($type) ? " AND code_type_utilisateur = '" . $type . "'" : '';
    $model->setClause("actif = 1 AND id_agence = " . $idAgence . $clauseAdd);
    $model->setOrderBy("nom");
    $users = $model->fillArrays($model->getData(), 'id', 'id');

    return $users;
}

function ifUsed($model, $tables, $value)
{
    foreach ($tables as $detail) {
        if ((isset($detail[2])) && ($detail[2] == 'like')) {
            $clauseDelete = $detail[1] . " LIKE '%#" . $value . "#%'";
        } else {
            $clauseDelete = $detail[1] . " = " . $value;
        }

        if ($model->recordExistsFree($detail[0], $detail[1], $clauseDelete)) {
            return 1;
        }
    }

    return -1;
}

//Style de référence HORECA
function generateReferenceHORECA($model, $type, $num_agence = '', $ref_personne = '', $id_personne = '')
{
    switch ($type) {
        case 'HORECA-PERSONNE':
            return 'P' . getLastNum($model, 'PERSONNE');
            break;

        case 'HORECA-RECHERCHE-PERSONNE':
            return $ref_personne . '-R' . getLastNum($model, 'RECHPERSONNE', $id_personne);
            break;

        case 'HORECA-BIEN':
            return $num_agence . '-' . getLastNum($model, 'BIEN', '', $num_agence);
            break;

        default:
            return null;
            break;
    }
}

function getLastNum($model, $groupe = 'PERSONNE', $id_personne = '', $num_agence = '')
{
    $dRef = 1;
    switch ($groupe) {
        case 'PERSONNE':
            $model->release();
            $model->setTable("crm_np_personne");
            $model->setChamp("CAST(REPLACE(reference_personne, 'P', '') AS SIGNED) as max");
            $model->setClause('');
            $model->setOrderBy('max DESC');
            $model->setLimit(1);
            $max = $model->getData();
            if (isset($max[0]->max) || $max[0]->max > 0) {
                $dRef = $max[0]->max + 1;
            }
            break;

        case 'RECHPERSONNE':
            $model->release();
            $model->setTable("crm_np_acheteur_recherche");
            $model->setChamp("COUNT(reference) as max");
            $model->setClause('id_acheteur = ' . $id_personne);
            $model->setOrderBy('');
            $max = $model->getData();
            if (isset($max[0]->max) || $max[0]->max > 0) {
                $dRef = $max[0]->max + 1;
            }
            break;

        case 'BIEN':
            $model->release();
            $model->setTable(BASE_REF . "crm_np_affaire");
            //$model->setChamp("CAST(REPLACE(reference, '" . $num_agence . "-', '') AS SIGNED) as max");
            $model->setChamp("MAX(id) as max");
            $model->setClause('');
            $model->setOrderBy('');
            $model->setLimit('');
            $max = $model->getData(true);
            if (isset($max[0]->max) || $max[0]->max > 0) {
                $dRef = $max[0]->max + 1;
            }
            break;

        default:

            break;
    }
    return $dRef;
}

// function ifUsed($model, $tables, $value)
// {
//     foreach ($tables as $table => $detail) {
//         if ((isset($detail[1])) && ($detail[1] == 'like')) {
//             $clauseDelete = $detail[0] . " LIKE '%#" . $value . "#%'";
//         } else {
//             $clauseDelete = $detail[0] . " = " . $value;
//         }
//         if ($model->recordExistsFree($table, $detail[0], $clauseDelete)) {
//             return 1;
//         }
//     }
//     return -1;
// }
#Fonction de génération de références 

function generateReferenceAffaire($model, $type, $type_trans = '', $idNegau)
{

    switch ($type) {
        case 'TYPETRANSACT-NUMGEN-INITNEGO':
            return getCodeTypeTrans($model, $type_trans) . '' . getGenNum($model) . '' . getNegoInitial($model, $idNegau);
            break;

        case 'TYPETRANSACT-NUMAUTO-INITNEGO':
            return getCodeTypeTrans($model, $type_trans) . '' . getAutoNum($model) . '' . getNegoInitial($model, $idNegau);
            break;

        case 'NUMAUTO-INITNEGO':
            return getAutoNum($model) . '' . getNegoInitial($model);
            break;

        case 'NUMGEN-INITNEGO':
            return getGenNum($model) . '' . getNegoInitial($model);
            break;

        case 'TYPETRANSACT-NUMGEN':
            return getCodeTypeTrans($model, $type_trans) . '' . getGenNum($model);
            break;

        case 'TYPETRANSACT-NUMAUTO':
            return getCodeTypeTrans($model, $type_trans) . '' . getAutoNum($model);
            break;

        default:
            return null;
            break;
    }
}

function getCodeTypeTransInitial($code_type_transaction, $table = 'crm_np_affaire')
{
    switch ($code_type_transaction) {
        case 'VENTE':
            $transaction = ($table == 'crm_np_acheteur_recherche') ? "RECH-VEN" : "VEN";
            break;
        case 'LOCATION':
            $transaction = ($table == 'crm_np_acheteur_recherche') ? "RECH-LOC" : "LOC";
            break;
        default:
            $transaction = ($table == 'crm_np_acheteur_recherche') ? "RECH-CDB" : "CDB";
            break;
    }
    return $transaction;
}

function getNegoInitial($model, $id)
{
    $model->release();
    $model->setTable('crm_np_utilisateur');
    $model->setChamp("CONCAT(SUBSTRING(nom, 1, 1),SUBSTRING(prenom, 1, 1)) as initial");
    $model->setClause("id = " . $id);
    $negociateur = $model->getData();
    return isset($negociateur[0]->initial) ? $negociateur[0]->initial : '';
}

function getAutoNum($model)
{
    $model->setTable(BASE_REF . "crm_np_affaire");
    $model->setClause("");
    $model->setChamp("id");
    $affaireID = $model->lastInsertId() + 1;
    $id = _nDigits($affaireID, 6);
    $nu = $id;
    return $nu;
}

function getGenNum($model)
{
    $dRef = date('m/Y');
    $model->setTable(BASE_REF . "crm_np_affaire");
    $model->setClause('reference LIKE "%/' . date('Y') . '/%"');
    $model->setChamp("CAST(SUBSTR(reference, 9) AS SIGNED)");
    $prochain = (int) $model->getMax() + 1;
    return $dRef . '/' . _nDigits($prochain, 4);
}

function _nDigits($prochain, $n)
{
    $returned = '';

    for ($i = 0; $i < ($n - strlen($prochain)); $i++) {
        $returned .= '0';
    }

    return $returned . $prochain;
}

function paginations($num, $per_page = 10, $page = 1, $url = '?')
{

    $total = $num;
    $adjacents = "2";

    $page = ($page == 0 ? 1 : $page);
    $start = ($page - 1) * $per_page;

    $prev = $page - 1;
    $next = $page + 1;
    $lastpage = ceil($total / $per_page);
    $lpm1 = $lastpage - 1;

    $pagination = "";
    if ($lastpage > 1) {
        $pagination .= "<ul class='kt-pagination__links'>";
        if ($page != 1) {
            $pagination .= "<li class='kt-pagination__link--first'>
            <a href='{$url}page=1'><i class='fa fa-angle-double-left kt-font-brand'></i></a>
        </li>
        <li class='kt-pagination__link--next'>
            <a href='{$url}page=$prev'><i class='fa fa-angle-left kt-font-brand'></i></a>
        </li>";
        }
        if ($lastpage < 7 + ($adjacents * 2)) {
            for ($counter = 1; $counter <= $lastpage; $counter++) {
                if ($counter == $page)
                    $pagination .= "<li class='kt-pagination__link--active'><a class='current'>$counter</a></li>";
                else
                    $pagination .= "<li><a href='{$url}page=$counter'>$counter</a></li>";
            }
        } elseif ($lastpage > 5 + ($adjacents * 2)) {
            if ($page < 1 + ($adjacents * 2)) {
                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                    if ($counter == $page)
                        $pagination .= "<li class='kt-pagination__link--active'><a class='current'>$counter</a></li>";
                    else
                        $pagination .= "<li><a href='{$url}page=$counter'>$counter</a></li>";
                }
                $pagination .= "<li class='dot'>...</li>";
                $pagination .= "<li><a href='{$url}page=$lpm1'>$lpm1</a></li>";
                $pagination .= "<li><a href='{$url}page=$lastpage'>$lastpage</a></li>";
            } elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
                $pagination .= "<li><a href='{$url}page=1'>1</a></li>";
                $pagination .= "<li><a href='{$url}page=2'>2</a></li>";
                $pagination .= "<li class='dot'>...</li>";
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                    if ($counter == $page)
                        $pagination .= "<li class='kt-pagination__link--active'><a class='current'>$counter</a></li>";
                    else
                        $pagination .= "<li><a href='{$url}page=$counter'>$counter</a></li>";
                }
                $pagination .= "<li class='dot'>..</li>";
                $pagination .= "<li><a href='{$url}page=$lpm1'>$lpm1</a></li>";
                $pagination .= "<li><a href='{$url}page=$lastpage'>$lastpage</a></li>";
            } else {
                $pagination .= "<li><a href='{$url}page=1'>1</a></li>";
                $pagination .= "<li><a href='{$url}page=2'>2</a></li>";
                $pagination .= "<li class='dot'>..</li>";
                for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++) {
                    if ($counter == $page)
                        $pagination .= "<li class='kt-pagination__link--active'><a class='current'>$counter</a></li>";
                    else
                        $pagination .= "<li><a href='{$url}page=$counter'>$counter</a></li>";
                }
            }
        }

        if ($page < $counter - 1) {
            $pagination .= "<li class='kt-pagination__link--prev'>
                <a href='{$url}page=$next'><i class='fa fa-angle-right kt-font-brand'></i></a>
            </li>
            <li class='kt-pagination__link--last'>
                <a href='{$url}page=$lastpage'><i class='fa fa-angle-double-right kt-font-brand'></i></a>
            </li>";
        }
        $pagination .= "</ul>\n";
    }


    return $pagination;
}

# METHOD:" . $method . "

function i_cal($donnees, $method = "REQUEST", $concernes = [])
{
    global $app;
    if ($method !== "REQUEST") {
        $status = "CANCELLED";
        $method = "CANCEL";
        $sequence = 1;
    } else {
        $status = "CONFIRMED";
        $sequence = 0;
    }

    $attendee = '';
    $i = 0;
    if (count($concernes)) {
        foreach ($concernes as $infosConcerne) {
            $i++;
            $attendee .= "ATTENDEE;CUTYPE=INDIVIDUAL;ROLE=REQ-PARTICIPANT;PARTSTAT=ACCEPTED;RSVP=FALSE;CN=" . $infosConcerne->val . ";X-NUM-GUESTS=0:mailto:" . $infosConcerne->email;
            $attendee .= ($i < count($concernes)) ? "\n" : "";
        }
    }

    return "BEGIN:VCALENDAR
PRODID:-#Google Inc#Google Calendar 70.9054#EN
VERSION:2.0
CALSCALE:GREGORIAN
METHOD:" . $method . "
BEGIN:VEVENT
DTSTART:" . date("Ymd\THis", strtotime($donnees->date_debut)) . "Z
DTEND:" . date("Ymd\THis", strtotime($donnees->date_echeance)) . "Z
DTSTAMP:" . date("Ymd\THis", strtotime($donnees->date_modification)) . "Z
UID:" . $donnees->id . "
ORGANIZER;CN=" . $_SESSION[$app][_USER_]->nom . " " . $_SESSION[$app][_USER_]->prenom . ":mailto:" . $_SESSION[$app][_USER_]->email . "
" . $attendee . "
CLASS:PRIVATE
DESCRIPTION:" . $donnees->objet . "
LOCATION:" . $donnees->lieu . "
URL:ND
SEQUENCE:" . $sequence . "
STATUS:" . $status . "
SUMMARY:" . $donnees->type_tache . " - " . $donnees->libelle . "
TRANSP:OPAQUE
PRIORITY:5
BEGIN:VALARM
DESCRIPTION:Rappel - " . $donnees->type_tache . " - " . $donnees->libelle . "
TRIGGER:-P0DT0H30M0S
ACTION:DISPLAY
END:VALARM
END:VEVENT
END:VCALENDAR";

    /* return "BEGIN:VCALENDAR
      VERSION:2.0
      CALSCALE:GREGORIAN
      METHOD:REQUEST
      PRODID:CRM NET PROFIL
      BEGIN:VEVENT
      ORGANIZER;CUTYPE=INDIVIDUAL;ROLE=REQ-PARTICIPANT;PARTSTAT=ACCEPTED;CN=" . $_SESSION[$app][_USER_]->nom . " " . $_SESSION[$app][_USER_]->prenom . ";X-NUM-GUESTS=0:MAILTO:" . $_SESSION[$app][_USER_]->email . "
      CLASS:PRIVATE
      STATUS:CONFIRMED
      DESCRIPTION:" . $donnees->objet . "
      TRANSP:OPAQUE
      UID:" . $donnees->id . "
      DTSTAMP:" . date("Ymd\THis", strtotime($donnees->date_debut)) . "Z
      DTSTART:" . date("Ymd\THis", strtotime($donnees->date_debut)) . "Z
      DTEND:" . date("Ymd\THis", strtotime($donnees->date_echeance)) . "Z
      SUMMARY:" . $donnees->type_tache . " - " . $donnees->libelle . "
      LOCATION:" . $donnees->lieu . "
      URL:ND
      BEGIN:VALARM
      ACTION:DISPLAY
      DESCRIPTION:REMINDER
      TRIGGER:-PT30M
      REPEAT:2
      DURATION:PT15M
      END:VALARM
      END:VEVENT
      END:VCALENDAR"; */
}

function maselection($model)
{
    global $route, $acces_mandats_affaires_autres_negociateurs;
    global $acces_negociateurs_classement_par_departement;
    global $app;
    if ($acces_negociateurs_classement_par_departement) {
        global $departements_autorises;
    }

    $model->release();
    $model->setTable(BASE_REF . 'crm_np_affaire a LEFT JOIN crm_np_ville v ON a.id_ville = v.id, crm_np_liste_element_form lef, ' . BASE_REF . 'crm_np_affaire_type at');
    $model->setChamp("a.id as id, reference, lef.libelle as type_transaction, at.libelle as type_affaire, v.nom_reel as ville, prix, cp, prospect");

    $clause = "a.id_type_affaire = at.id  AND a.code_type_transaction = lef.code AND a.id IN (SELECT id_affaire FROM " . BASE_REF . "crm_np_wish_list WHERE email = '" . $_SESSION[$app][_USER_]->email . "' AND deleted = -1) AND a.actif = 1";
    if (isset($acces_negociateurs_classement_par_departement) && $acces_negociateurs_classement_par_departement) {
        $clause .= isset($departements_autorises) ? ' AND a.num_departement IN ("' . implode('","', $departements_autorises) . '") ' : '';
    }
    $clause .= (($acces_mandats_affaires_autres_negociateurs) ? '' : ' AND a.attribue_a = ' . $_SESSION[$app][_USER_]->id);
    $model->setClause($clause);
    $model->setOrderBy('reference');

    $selections = $model->getData(true);

    $config = JSONFileToArray($route . _STORAGE_PATH . 'agences/' . $_SESSION[$app][_USER_]->nom_dossier . '/config.json');
    for ($i = 0; $i < count($selections); $i++) {
        $selections[$i]->photo = (getDefaultImageAffaire($selections[$i]->id, $route) != "") ? getDefaultImageAffaire($selections[$i]->id, $route) : "";
    }

    return $selections;
}

function getDefaultImageAffaire($id, $route)
{
    global $app;

    $files = (is_dir($route . _STORAGE_PATH . 'agences/' . DOSSIER_REF . '/images/affaires/affaire_' . $id)) ? array_slice(scandir($route . _STORAGE_PATH . 'agences/' . DOSSIER_REF . '/images/affaires/affaire_' . $id), 2) : [];
    $photo = "";
    for ($j = 0; $j < count($files); $j++) {
        if (strpos($files[$j], '_default_') !== false) {
            # $new_name = '_default_' . $files[$j];
            $photo = $files[$j];
        }
    }

    if ($photo != "") {
        $photo = HTTP_PATH . _STORAGE_PATH . 'agences/' . DOSSIER_REF . '/images/affaires/affaire_' . $id . '/' . $photo;
    } else {
        if (count($files) > 0) {
            $photo = HTTP_PATH . _STORAGE_PATH . 'agences/' . DOSSIER_REF . '/images/affaires/affaire_' . $id . '/' . $files[0];
        }
    }
    $config = JSONFileToArray($route . _STORAGE_PATH . 'agences/' . $_SESSION[$app][_USER_]->nom_dossier . '/config.json');
    $photo = !file_exists(str_replace(HTTP_PATH, $route, $photo)) ? HTTP_PATH . _STORAGE_PATH . 'agences/' . $_SESSION[$app][_USER_]->nom_dossier . '/' . $config['agence']['logo'] : $photo; #Ajouter par Ousmane le 27/02/2021
    return $photo;
}

function dansmaselection($model)
{

    global $app;

    $model->release();
    $model->setTable(BASE_REF . 'crm_np_wish_list');
    $model->setChamp("id_affaire");
    $model->setClause("email = '" . $_SESSION[$app][_USER_]->email . "' AND id_affaire IN (SELECT id FROM " . BASE_REF . "crm_np_affaire WHERE actif = 1 AND deleted = -1)");

    $selections = $model->getData(true);
    return $model->fillArrays($selections, 'id_affaire', 'id_affaire');
}

function encompromis($model, $field = 'id_affaire')
{
    # On recupere les affaires en compromis
    $model->release();
    $model->setChamp($field);
    $model->setTable(BASE_REF . '.crm_np_compromis_information');
    $encompromisarray = $model->getData();
    return $model->fillArrays($encompromisarray, $field, $field);
}

# Fonction permettant de changer les numeros mandat existants en electronique

function manueltoelectronique($model, $table, $datas)
{
    $table = (in_array($table, ["crm_np_affaire", "crm_np_affaire_mandat"])) ? BASE_REF . $table : $table;
    $current = 1;
    foreach ($datas as $data) {
        $model->setTable($table);
        $model->record(["id" => $data->id, "numero_mandat" => _nDigits($current, 6)]);
        $current++;
    }
}

# DANGER ! Fonction permettant de changer les references existantes en electronique pour affaire a_ et recherche r_

function manueltoelectronique_a_r($model, $table, $datas, $which = 'recherche')
{
    global $config;

    $tableloaded = (in_array($table, ["crm_np_affaire", "crm_np_affaire_mandat"])) ? BASE_REF . $table : $table;

    $electronique = ($which === 'affaire') ? $config['site_settings']["choix_reference_affaire"] : $config['site_settings']["choix_reference_recherche"];
    if (!in_array($which, ['affaire', 'recherche']) || $electronique === "") {
        return; # Pour plus de prudence. Eviter de reecrire les references
    }
    $model->release();
    $model->setTable($tableloaded);
    $model->setClause("id > 0");
    $model->updateOne(["reference" => ""]);

    foreach ($datas as $data) {
        $donnees = (array) $data;
        $nouvellereference = gen_reference($model, $electronique, $donnees, $table, date("m/Y/", strtotime($donnees['date_creation'])));
        $model->release();
        $model->setTable($tableloaded);
        $model->updateOne(["id" => $data->id, "reference" => $nouvellereference]);
    }
}

function electronique($model, $table, $field)
{

    $table = (in_array($table, ["crm_np_affaire", "crm_np_affaire_mandat"])) ? BASE_REF . $table : $table;

    $model->release();
    $model->setTable($table);
    $model->setChamp("MAX(CAST($field AS SIGNED)) as nummax");
    $max = $model->getData();
    return _nDigits($max[0]->nummax + 1, 6);
}

function getCurrentIncrement($model)
{

    $dRef = date('m/Y');

    $champ = "CAST(SUBSTR(reference, 9) AS SIGNED)";
    $clause = 'reference LIKE "%/' . date('Y') . '/%"';

    $prochain = (int) $model->getMax($champ, $clause) + 1;

    return $dRef . '/' . _nDigits($prochain, 4);
}

function gen_reference($model, $modele, $donnees = [], $table = 'crm_np_affaire', $date = '')
{

    $tableloaded = ($table === "crm_np_affaire") ? BASE_REF . $table : $table;

    if (in_array($modele, ['TYPETRANSACT-NUMGEN-INITNEGO', 'TYPETRANSACT-NUMGEN', 'TYPETRANSACT-NUMAUTO-INITNEGO', 'TYPETRANSACT-NUMAUTO'])) {
        $typetransact = getCodeTypeTransInitial($donnees['code_type_transaction'], $table) . "-";
    } else {
        $typetransact = null;
    }

    if (in_array($modele, ['TYPETRANSACT-NUMGEN-INITNEGO', 'TYPETRANSACT-NUMGEN', 'NUMGEN-INITNEGO', 'NUMGEN'])) {
        $middle = $typetransact . (empty($date) ? date("m/Y/") : $date);
        $model->release();
        $model->setTable($tableloaded);
        $model->setChamp("MAX(CAST(SUBSTRING(reference, " . (strlen($middle) + 1) . ", 3) AS SIGNED)) as nummax");
        $model->setClause("reference LIKE '$middle%'");
        # $model->setClause("SUBSTRING(reference, 1, " . (strlen($middle)) . ") = '$middle'");
        $querymax = $model->getData();

        $max = isset($querymax[0]->nummax) ? $querymax[0]->nummax : 0;
        $reference = $middle . _nDigits($max + 1, 3);
    } else {
        $model->release();
        $model->setTable($tableloaded);
        $model->setChamp("MAX(CAST(SUBSTRING(reference, " . (strlen($typetransact) + 1) . ", 6) AS SIGNED)) as nummax");
        $querymax = $model->getData();
        $max = isset($querymax[0]->nummax) ? $querymax[0]->nummax : 0;
        $reference = $typetransact . _nDigits($max + 1, 6);
    }

    if (in_array($modele, ['TYPETRANSACT-NUMGEN-INITNEGO', 'NUMGEN-INITNEGO', 'NUMAUTO-INITNEGO', 'TYPETRANSACT-NUMAUTO-INITNEGO'])) {

        $reference .= "-" . getNegoInitial($model, $donnees['attribue_a']);
    }

    return strtoupper($reference);
}

function dump($datas)
{
    print_r("<pre>");
    var_dump($datas);
    print_r("</pre>");
}

function partieldump($agence, $datas)
{
    global $app;
    if ($_SESSION[$app][_USER_]->nom_bd === $agence) {
        dump($datas);
        exit();
    }
}

function dumpforsuper($datas, $forced = true, $exit = true)
{
    global $model;
    if ((bool) $model->connected("SUPERADMINISTRATEUR") === true || $forced) {
        dump($datas);
        if ($exit)
            exit();
    }
}

if (!function_exists('array_key_first')) {

    function array_key_first(array $arr)
    {
        foreach ($arr as $key => $unused) {
            return $key;
        }
        return NULL;
    }
}

function get_dept_autorises($model)
{
    global $app;

    $model->release();
    $model->setTable(CRM_NP_UTILISATEUR);
    $model->setChamp(UTILISATEUR_DEPARTEMENTS_AUTORISES);
    $model->setClause("id = " . $_SESSION[$app][_USER_]->id);
    $result = $model->getData();
    if (count($result)) {
        return $result[0]->departements_autorises;
    }

    return null;
}

function alertes($model, $id, $lef, $csc = 'AFFAIRE')
{
    global $app;

    # Récupérations des alertes
    $clausealerte = "id IN (SELECT id_tache FROM " . $_SESSION[$app][_USER_]->nom_bd . ".crm_np_tache_concerne WHERE code_source_concerne = '" . $csc . "' AND id_concerne = " . $id . ")";
    if ($model->connected('NEGOCIATEUR')) {
        $clausealerte .= " AND (attribue_a = " . $_SESSION[$app][_USER_]->id . " OR cree_par = " . $_SESSION[$app][_USER_]->id . ")";
    }
    if ($csc === 'UTILISATEUR') {
        $clausealerte = "((id IN (SELECT id_tache FROM " . $_SESSION[$app][_USER_]->nom_bd . ".crm_np_tache_concerne WHERE code_source_concerne = '" . $csc . "' AND id_concerne = " . $id . ")) OR attribue_a = " . $id . ")";
    }
    $model->release();
    $model->setTable($_SESSION[$app][_USER_]->nom_bd . '.crm_np_tache');
    $model->setChamp("id, code_type_tache, libelle, date_debut, date_echeance");
    $model->setOrderBy("date_creation DESC");
    $model->setClause($clausealerte);
    $tache = (array) $model->getData();

    $alertes = [];
    $type_taches = $lef['TYPE_TACHE'];

    for ($i = 0; $i < count($tache); $i++) {
        $alertes[$i]['id'] = $tache[$i]->id;
        $alertes[$i]['code'] = $tache[$i]->code_type_tache;
        $alertes[$i]['title'] = (!empty($tache[$i]->libelle)) ? $tache[$i]->libelle : "Sans titre";
        $alertes[$i]['start'] = textDate($tache[$i]->date_debut);
        $alertes[$i]['end'] = textDate($tache[$i]->date_echeance);

        $pictogramme = isset($type_taches[$tache[$i]->code_type_tache]) ? $type_taches[$tache[$i]->code_type_tache]['pictogramme'] : 'brand';
        $alertes[$i]['classNames'] = $pictogramme;
    }

    return $alertes;
}

function iseditable($model, $id, $editable, $table = 'crm_np_acheteur_recherche')
{
    global $acces_mandats_affaires_autres_agences, $acces_acheteurs_recherches_autres_agences;
    global $app;

    $basetarget = in_array($table, ['crm_np_affaire']) ? BASE_REF . $table : $_SESSION[$app][_USER_]->nom_bd . ".$table";

    if ($editable === false) {
        $model->release();
        $model->setTable(!in_array($table, ['crm_np_acheteur_recherche', 'crm_np_personne']) ? $basetarget : $table);
        $model->setClause("id = '$id'" . ($table === 'crm_np_personne' ? " AND code_type_personne LIKE '%#ACQUEREUR#%'" : ""));
        $model->setChamp("attribue_a" . (in_array($table, ['crm_np_acheteur_recherche', 'crm_np_affaire']) ? ", id_agence" : ""));

        $madeby = $model->getData();
        if (in_array($table, ['crm_np_acheteur_recherche', 'crm_np_affaire'])) {
            if (($table == 'crm_np_affaire' && $acces_mandats_affaires_autres_agences == true) || ($table == 'crm_np_acheteur_recherche' && $acces_acheteurs_recherches_autres_agences == true)) {
                if (isset($madeby[0]) && $madeby[0]->attribue_a == $_SESSION[$app][_USER_]->id) {
                    return true;
                }
            } else {
                if (isset($madeby[0]) && $madeby[0]->id_agence == $_SESSION[$app][_USER_]->id_agence && $madeby[0]->attribue_a == $_SESSION[$app][_USER_]->id) {
                    return true;
                }
            }
        } else {
            if (isset($madeby[0]) && $madeby[0]->attribue_a == $_SESSION[$app][_USER_]->id) {
                return true;
            }
        }

        return false;
    }

    return true;
}

function flashflush($app, $modulealive)
{
    global $app;
    if (isset($_SESSION[$app])) {
        foreach ($_SESSION[$app] as $deathmodule => $v) {
            if ($modulealive !== $deathmodule) {
                unset($_SESSION[$app][$deathmodule]["flash"]);
            }
        }
    }
}

function ftp_upload($ftp_server, $ftp_user_name, $ftp_user_pass, $source, $dest)
{
    $resultat = [];
    # création de la connexion  ---------------------------------------
    $conn_id = ftp_connect("$ftp_server");

    # authentification avec nom de compte et mot de passe -------------
    $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

    # vérification de la connexion ------------------------------------
    if ((!$conn_id) || (!$login_result)) {
        $resultat[] = "impossible de se connecter au serveur FTP<br/>";
    } else {
        $resultat[] = "Connecté à <B>" . $ftp_server . "</B>, avec <B>" . $ftp_user_name . "</B><br/>";
    }

    # Activation du mode passif
    ftp_pasv($conn_id, true);

    # transfert du fichier -------------------------------------------
    $upload = ftp_put($conn_id, $dest, $source, FTP_BINARY);

    # Vérification de téléchargement ----------------------------------
    if (!$upload) {
        $resultat[] = 'echec du transfert des fichiers par FTP (la connexion en elle meme avait ete acceptee)<br/>';
    } else {
        $resultat[] = "Téléchargement de <B>" . $source . "</B> sur <B>" . $ftp_server . "</B> réussi<br/>";
    }

    # fermeture de la connexion FTP.------------------------------------
    ftp_quit($conn_id);
    return $resultat;
}

function allvars($texte)
{
    preg_match_all("|\#[a-zA-Z_0-9:]*\#|U", $texte, $chaines);
    return (isset($chaines[0]) && count($chaines[0]) > 0) ? $chaines[0] : [];
}

function docs($route, $folder, $docsinfo = false, $splitname = false, $order = false)
{
    # Recuperer les documents 
    $docpathname = getdir($route . _STORAGE_PATH, "agences/" . DOSSIER_REF . "/documents/$folder/");

    if ($order == false) {
        $docs = (is_dir($docpathname)) ? array_slice(scandir($docpathname), 2) : [];
    } else {
        $docs = (is_dir($docpathname)) ? scandir($docpathname, $order) : [];
    }
    $returned = [];

    foreach ($docs as $i => $doc) {
        if (is_dir($route . _STORAGE_PATH . "agences/" . DOSSIER_REF . "/documents/$folder/$doc")) {
            continue;
        }
        $info = pathinfo($doc);
        $docname = HTTP_PATH . _STORAGE_PATH . "agences/" . DOSSIER_REF . "/documents/$folder/$doc";
        $returned[$i] = ['realpath' => $route . _STORAGE_PATH . "agences/" . DOSSIER_REF . "/documents/$folder/$doc", 'extension' => $info['extension'], 'name' => $info['filename'], 'fullname' => $docname, 'size' => getFileSize($docpathname . $doc)];
        if (is_array($docsinfo)) {
            $returned[$i]['namefromtable'] = isset($docsinfo[strtoupper($info['filename'])]) ? $docsinfo[strtoupper($info['filename'])] : $info['filename'];
        }
        if ($splitname != false) {
            $returned[$i]['namesplited'] = (strpos($info['filename'], $splitname) > -1) ? explode($splitname, $info['filename']) : [$info['filename']];
        }
    }
    return $returned; # Fonction pour alerte et document
}

# FOR AFFAIRES

function GetInputElement($table, $nomBD = '')
{

    global $app;

    $table_commun = ['crm_np_acces_module', 'crm_np_acheteur_panier', 'crm_np_acheteur_recherche', 'crm_np_acheteur_recherche_detail', 'crm_np_actualite_netprofil', 'crm_np_agences', 'crm_np_code_naf', 'crm_np_const_settings', 'crm_np_department', 'crm_np_droits', 'crm_np_faq', 'crm_np_lettre_type', 'crm_np_liste_element_form', 'crm_np_mail_type', 'crm_np_personne', 'crm_np_utilisateur', 'crm_np_utilisateur_groupe_droits', 'crm_np_ville'];

    $nomBD = empty($nomBD) ? (isset($_SESSION[$app][_USER_]->nom_bd) ? $_SESSION[$app][_USER_]->nom_bd : str_replace(".", "", BASE_REF)) : $nomBD;

    if ($nomBD . '.crm_np_liste_element_form' == $table) {
        return $nomBD . '.crm_np_liste_element_form';
    } elseif (PREFIXE_BD . 'commun.crm_np_liste_element_form' == $table) {
        return $table;
    } else {
        if (in_array($table, $table_commun)) {
            return $table;
        } else {
            //return $nomBD . '.' . $table;
            return BASE_REF . $table;
        }
    }
}

function _get_family_affaire($model, $current_affaire, $family, $defaultSelected, $action, $caller = "", $clauseMaJ = "", $nomBD = "", $for = "VIEW")
{
    global $clause_for_hide_negociateur_field, $__rights;
    global $app;

    //$nomBD = empty($nomBD) ? $_SESSION[$app][_USER_]->nom_bd : $nomBD;
    $nomBD = empty($nomBD) ? BASE_REF : $nomBD;

    $for_suffix = "_" . strtolower($for);

    $isprospect = ($action === "ajoutprospect" || (isset($current_affaire['prospect']) && $current_affaire['prospect'] == 1)) ? " AND (vc.visible$for_suffix=1 OR nom_champ ='code_cession_affaire')" : " AND vc.visible$for_suffix = 1";

    if ($action === "ajout" || ($action === "ajoutprospect" && $caller !== "ajax")) {
        $clauseforfield = "fc.utilise_pour='" . strtoupper($for) . "' AND vc.code_type_transaction='" . $defaultSelected['code_type_transaction'] . "' AND fc.code_type_transaction='" . $defaultSelected['code_type_transaction'] . "' AND vc.types_affaires_autorises LIKE '%#" . $defaultSelected['id_type_affaire'] . "#%' AND vc.id_famille$for_suffix = fc.id  AND fc.rang = $family $isprospect";
    } elseif ($action === "detail") {
        $clauseforfield = "fc.utilise_pour='" . strtoupper($for) . "' AND vc.code_type_transaction='" . $defaultSelected['code_type_transaction'] . "' AND fc.code_type_transaction='" . $defaultSelected['code_type_transaction'] . "' AND vc.types_affaires_autorises LIKE '%#" . $defaultSelected['id_type_affaire'] . "#%' AND vc.id_famille$for_suffix = fc.id $isprospect";
    } elseif ($caller === "ajax") {
        $clause_type_affaire = (isset($defaultSelected['id_type_affaire']) && (int) $defaultSelected['id_type_affaire'] > 0) ? "AND vc.types_affaires_autorises LIKE '%#" . $defaultSelected['id_type_affaire'] . "#%'" : "";
        $clauseforfield = "$clauseMaJ fc.utilise_pour='" . strtoupper($for) . "' AND vc.code_type_transaction='" . $defaultSelected['code_type_transaction'] . "' AND fc.code_type_transaction='" . $defaultSelected['code_type_transaction'] . "' $clause_type_affaire AND vc.id_famille$for_suffix = fc.id AND fc.id= $family $isprospect";
    } else {
        if (isset($_SESSION['defaultSelected']['idFamille']) && (int) $_SESSION['defaultSelected']['idFamille'] > 0) {
            $clauseforfield = "nom_champ NOT IN ('code_type_transaction','id_type_affaire') AND fc.utilise_pour='" . strtoupper($for) . "' AND vc.code_type_transaction='" . $defaultSelected['code_type_transaction'] . "' AND fc.code_type_transaction='" . $defaultSelected['code_type_transaction'] . "' AND vc.types_affaires_autorises LIKE '%#" . $defaultSelected['id_type_affaire'] . "#%' AND vc.id_famille$for_suffix = fc.id AND fc.id=" . $_SESSION['defaultSelected']['idFamille'] . $isprospect;
        } else {
            $clauseforfield = "nom_champ NOT IN ('code_type_transaction', 'id_type_affaire') AND fc.utilise_pour='" . strtoupper($for) . "' AND vc.code_type_transaction='" . $defaultSelected['code_type_transaction'] . "' AND fc.code_type_transaction='" . $defaultSelected['code_type_transaction'] . "' AND vc.types_affaires_autorises LIKE '%#" . $defaultSelected['id_type_affaire'] . "#%' AND vc.id_famille$for_suffix = fc.id AND fc.rang = $family $isprospect";
        }
    }

    # On ne recupere que les droits de lecture car seuls les champs sur lesquels le droit de lecture est present sont envoyes au formulaire.
    $clauseforfield .= (isset($__rights["lecture"]["id"]) && count($__rights["lecture"]["id"]) > 0) ? " AND vc.id IN ('" . implode("','", $__rights["lecture"]["id"]) . "')" : "";

    $model->release();
    $model->setTable(BASE_REF . "crm_np_affaire_vis_famille_champ fc, " . BASE_REF . "crm_np_affaire_vis_champ vc");
    $model->setClause($clause_for_hide_negociateur_field . $clauseforfield);
    $model->setChamp("fc.*, fc.id as id_famille, vc.*");
    $model->setOrderBy("fc.rang, vc.rang$for_suffix");

    $famillesChamp = $model->getData(true, false);

    return $famillesChamp;
}

function _get_sous_family_affaire($model, $current_affaire, $family, $defaultSelected, $action, $caller = '', $clauseMaJ = '', $nomBD = '', $for = 'VIEW')
{

    global $clause_for_hide_negociateur_field;
    global $app;

    $nomBD = empty($nomBD) ? BASE_REF : $nomBD;
    //$nomBD = empty($nomBD) ? $_SESSION[$app][_USER_]->nom_bd : $nomBD;
    // $nomBD = empty($nomBD) ? str_replace('.', '', BASE_REF) : $nomBD;

    $model->release();

    $for_suffix = "_" . strtolower($for);
    $isprospect = ($action === "ajoutprospect" || (isset($current_affaire['prospect']) && $current_affaire['prospect'] == 1)) ? ' AND (vc.visible' . $for_suffix . '=1 OR nom_champ ="code_cession_affaire")' : ' AND vc.visible' . $for_suffix . '=1';

    if ($action === "ajout" || ($action === "ajoutprospect" && $caller !== "ajax")) {

        $clauseforfield = 'fc.utilise_pour="' . strtoupper($for) . '" AND vc.code_type_transaction="' . $defaultSelected['code_type_transaction'] . '" AND fc.code_type_transaction="' . $defaultSelected['code_type_transaction'] . '" AND vc.types_affaires_autorises LIKE "%#' . $defaultSelected['id_type_affaire'] . '#%" AND vc.id_sous_famille' . $for_suffix . '=fc.id  AND fc.rang= ' . $family . $isprospect;
    } elseif ($action === "detail") {

        $clauseforfield = 'fc.utilise_pour="' . strtoupper($for) . '" AND vc.code_type_transaction="' . $defaultSelected['code_type_transaction'] . '" AND fc.code_type_transaction="' . $defaultSelected['code_type_transaction'] . '" AND vc.types_affaires_autorises LIKE "%#' . $defaultSelected['id_type_affaire'] . '#%" AND vc.id_sous_famille' . $for_suffix . '=fc.id' . $isprospect;
    } elseif ($caller === "ajax") {
        $clause_type_affaire = (isset($defaultSelected['id_type_affaire']) && (int) $defaultSelected['id_type_affaire'] > 0) ? 'AND vc.types_affaires_autorises LIKE "%#' . $defaultSelected['id_type_affaire'] . '#%"' : '';
        $clauseforfield = $clauseMaJ . 'fc.utilise_pour="' . strtoupper($for) . '" AND vc.code_type_transaction="' . $defaultSelected['code_type_transaction'] . '" AND fc.code_type_transaction="' . $defaultSelected['code_type_transaction'] . '" ' . $clause_type_affaire . ' AND vc.id_sous_famille' . $for_suffix . '=fc.id AND fc.id=' . $family . $isprospect;
    } else {

        if (isset($_SESSION['defaultSelected']['idFamille']) && (int) $_SESSION['defaultSelected']['idFamille'] > 0) {
            $clauseforfield = 'nom_champ NOT IN("code_type_transaction","id_type_affaire") AND fc.utilise_pour="' . strtoupper($for) . '" AND vc.code_type_transaction="' . $defaultSelected['code_type_transaction'] . '" AND fc.code_type_transaction="' . $defaultSelected['code_type_transaction'] . '" AND vc.types_affaires_autorises LIKE "%#' . $defaultSelected['id_type_affaire'] . '#%" AND vc.id_sous_famille' . $for_suffix . '=fc.id AND fc.id=' . $_SESSION['defaultSelected']['idFamille'] . $isprospect;
        } else {
            $clauseforfield = 'nom_champ NOT IN("code_type_transaction","id_type_affaire") AND fc.utilise_pour="' . strtoupper($for) . '" AND vc.code_type_transaction="' . $defaultSelected['code_type_transaction'] . '" AND fc.code_type_transaction="' . $defaultSelected['code_type_transaction'] . '" AND vc.types_affaires_autorises LIKE "%#' . $defaultSelected['id_type_affaire'] . '#%" AND vc.id_sous_famille' . $for_suffix . '=fc.id AND fc.rang=' . $family . $isprospect;
        }
    }

    $model->setTable(BASE_REF . 'crm_np_affaire_vis_sous_famille_champ fc, ' . BASE_REF . 'crm_np_affaire_vis_champ vc');
    $model->setClause($clause_for_hide_negociateur_field . $clauseforfield);
    $model->setChamp("fc.*,vc.*");
    $model->setOrderBy("fc.rang, vc.rang$for_suffix");

    $famillesChamp = $model->getData();

    // if ($action === "ajoutprospect" || (isset($current_affaire['prospect']) && $current_affaire['prospect'] == 1)) {
    //     if ((!isset($_SESSION['defaultSelected']['idFamille']) || ((int) $family == $_SESSION['defaultSelected']['idFamille'])) || $action === "detail") {
    //         $model->release();
    //         $model->setTable(BASE_REF . 'crm_np_affaire_vis_famille_champ fc, ' . $nomBD . '.crm_np_affaire_vis_champ vc');
    //         $model->setChamp("fc.*,vc.*");
    //         $model->setOrderBy("fc.rang, vc.rang$for_suffix");
    //         $model->setClause('nom_champ IN ("vendeur") AND fc.utilise_pour="' . strtoupper($for) . '" AND vc.code_type_transaction="' . $defaultSelected['code_type_transaction'] . '" AND fc.code_type_transaction="' . $defaultSelected['code_type_transaction'] . '" AND vc.types_affaires_autorises LIKE "%#' . $defaultSelected['id_type_affaire'] . '#%" AND vc.id_famille' . $for_suffix . '=fc.id'); # AND vc.visible' . $for_suffix . '=1
    //         if ($model->getCount('fc.id')) {
    //             $famillesChamp = array_merge($famillesChamp, $model->getData());
    //         }
    //     }
    // }

    return $famillesChamp;
}

function getDetailAffaire($model, $id_function_affaire, $lef, $action = 'detail', $ignoreDroitAcces = false, $nomBD = '', $for = 'VIEW', $usesubfamily = true)
{

    global $acces_mandats_affaires_autres_negociateurs, $departements_autorises, $lefagence, $assigned, $acces_mandats_affaires_autres_agences;

    $nomBD = empty($nomBD) ? BASE_REF : $nomBD;
    $model->release();
    $model->setTable($nomBD . '.crm_np_affaire');
    $model->setOrderBy("id");
    $model->setChamp("*");
    if ($ignoreDroitAcces === true) {
        $model->setClause("id = '$id_function_affaire'");
    } else {
        $model->setClause("id = '$id_function_affaire'" . (($acces_mandats_affaires_autres_negociateurs) ? "" : " AND attribue_a = " . $_SESSION[$app][_USER_]->id) . ($acces_mandats_affaires_autres_agences ? "" : " AND id_agence = " . $_SESSION[$app][_USER_]->id_agence));
    }
    $current_affaire = $model->getData(false);

    if (!isset($current_affaire[0])) {
        return false;
    }
    $current_affaire = (array) $current_affaire[0];

    $model->release();
    $model->setTable(BASE_REF . 'crm_np_affaire_vis_famille_champ');
    if ($for === 'VIEW') {
        $model->setClause('code_type_transaction = "' . $current_affaire['code_type_transaction'] . '" AND utilise_pour="VIEW" AND actif=1 AND id IN (SELECT DISTINCT(id_famille_view) FROM ' . BASE_REF . 'crm_np_affaire_vis_champ vc where vc.visible_view=1 AND vc.actif=1 AND vc.types_affaires_autorises LIKE "%#' . $current_affaire['id_type_affaire'] . '#%" AND deleted = -1)');
    } else {
        $model->setClause('code_type_transaction = "' . $current_affaire['code_type_transaction'] . '" AND utilise_pour="IMPRESSION" AND actif=1 AND id IN (SELECT DISTINCT(id_famille_impression) FROM ' . BASE_REF . 'crm_np_affaire_vis_champ vc where vc.visible_impression=1 AND vc.actif=1 AND vc.types_affaires_autorises LIKE "%#' . $current_affaire['id_type_affaire'] . '#%" AND deleted = -1)');
    }
    $model->setChamp("*");
    $model->setOrderBy("rang");
    $allFamilles = $model->getData();
    $assigned['allFamilles'] = $allFamilles;

    $famillesChamp = _get_family_affaire($model, $current_affaire, null, $current_affaire, "detail", '', '', $nomBD, $for);

    $champInput = [];
    $source = [];

    for ($i = 0; $i < count($famillesChamp); $i++) {
        if (($famillesChamp[$i]->source_donnees != "LIBRE") && ($famillesChamp[$i]->source_donnees != "") && ($famillesChamp[$i]->source_donnees != null)) {
            $clause = '';
            $champ = "*";

            switch ($famillesChamp[$i]->source_donnees) {
                case BASE_REF . 'crm_np_liste_element_form':
                case PREFIXE_BD . 'commun.crm_np_liste_element_form':
                    $type = buildLef($famillesChamp[$i]->donnees);
                    $source[$famillesChamp[$i]->nom_champ] = (isset($lefagence['form'][$type[0]])) ? $lefagence['form'][$type[0]] : null;

                    if (in_array($famillesChamp[$i]->code_type_form_champ, ['CHECKBOX', 'MULTISELECT'])) {
                        $currentListeLef = buildLef($current_affaire[$famillesChamp[$i]->nom_champ]);
                        $libelleListeLef = [];
                        if ($famillesChamp[$i]->nom_champ == 'code_passerelle') {
                            foreach ($currentListeLef as $valueListeLef) {
                                if (isset($lefagence['liste'][$type[0]][$valueListeLef]['libelle'])) {
                                    $libelleListeLef[] = [$valueListeLef, $lefagence['liste'][$type[0]][$valueListeLef]['libelle']];
                                }
                            }
                            $current_affaire[$famillesChamp[$i]->nom_champ] = $libelleListeLef;
                        } else {
                            foreach ($currentListeLef as $valueListeLef) {
                                if (isset($lefagence['liste'][$type[0]][$valueListeLef]['libelle'])) {
                                    $libelleListeLef[] = $lefagence['liste'][$type[0]][$valueListeLef]['libelle'];
                                }
                            }
                            $current_affaire[$famillesChamp[$i]->nom_champ] = (count($libelleListeLef) > 0) ? implode(', ', $libelleListeLef) : "";
                        }
                    } else {
                        $current_affaire[$famillesChamp[$i]->nom_champ] = (isset($lefagence['liste'][$type[0]][$current_affaire[$famillesChamp[$i]->nom_champ]]['libelle'])) ? $lefagence['liste'][$type[0]][$current_affaire[$famillesChamp[$i]->nom_champ]]['libelle'] : "";
                    }
                    break;

                case 'crm_np_affaire_mandat':
                    $idmandat = (isset($current_affaire['id_mandat'])) ? $current_affaire['id_mandat'] : null;
                    if ((int) $idmandat > 0) {
                        $model->release();
                        $model->setTable(GetInputElement($famillesChamp[$i]->source_donnees, $nomBD));
                        $model->setClause('id = ' . $idmandat);
                        $model->setLimit("1");
                        $model->setChamp("*");
                        $mandat = $model->getData();
                        if (isset($mandat[0])) {
                            $mandat[0]->date_creation = textDate($mandat[0]->date_creation);
                            $mandat[0]->date_modification = textDate($mandat[0]->date_modification);
                            $mandat[0]->date_mandat = textDate($mandat[0]->date_mandat, '%d %b %Y');
                            $mandat[0]->echeance_mandat = textDate($mandat[0]->echeance_mandat, '%d %b %Y');
                            $mandat[0]->code_type_mandat = isset($lef['TYPE_MANDAT'][$mandat[0]->code_type_mandat]) ? $lef['TYPE_MANDAT'][$mandat[0]->code_type_mandat]['libelle'] : "";
                            $mandat[0]->tacite_reconduction_txt = ($mandat[0]->tacite_reconduction == 1) ? "Oui" : "Non";
                            $current_affaire[$famillesChamp[$i]->nom_champ] = $mandat[0];
                        } else {
                            $current_affaire[$famillesChamp[$i]->nom_champ] = null;
                        }
                    } else {
                        $current_affaire[$famillesChamp[$i]->nom_champ] = null;
                    }
                    break;

                case 'crm_np_ville':
                    if (isset($current_affaire)) {
                        $idville = (isset($current_affaire[$famillesChamp[$i]->nom_champ])) ? $current_affaire[$famillesChamp[$i]->nom_champ] : 0;
                        if ((int) $idville > 0) {
                            $model->release();
                            $model->setTable(GetInputElement($famillesChamp[$i]->source_donnees, $nomBD));
                            $model->setClause('id = ' . $idville);
                            $model->setLimit("1");
                            $model->setChamp("nom_reel, num_departement");
                            $ville = $model->getData();
                        }
                        $current_affaire[$famillesChamp[$i]->nom_champ] = isset($ville[0]) ? $ville[0]->nom_reel : null;
                        $nomDept = "";
                        if (isset($ville[0]->num_departement) && !empty($ville[0]->num_departement)) {
                            $model->release();
                            $model->setTable("crm_np_department");
                            $model->setClause('num = ' . $ville[0]->num_departement);
                            $model->setLimit("1");
                            $model->setChamp("nom");
                            $dept = $model->getData();
                            $nomDept = (count($dept) > 0) ? $dept[0]->nom : "";
                        }
                        $current_affaire[$famillesChamp[$i]->nom_champ . "_departement"] = $nomDept;
                    }
                    break;

                case 'crm_np_utilisateur':
                    if (isset($current_affaire)) {
                        $id = (isset($current_affaire[$famillesChamp[$i]->nom_champ])) ? $current_affaire[$famillesChamp[$i]->nom_champ] : null;
                        if ((int) $id > 0) {
                            $model->release();
                            $model->setTable(GetInputElement($famillesChamp[$i]->source_donnees, $nomBD));
                            $model->setClause("id = '$id'");
                            $model->setChamp("id, code_civilite, nom, prenom, portable, email, cp, id_ville, telephone, adresse, departements_autorises, assurance_aupres_de, num_police, photo");
                            $user = $model->getData();
                            if ($action === 'detail') {
                                $nom_nego = (isset($lef['CIVILITE'][$user[0]->code_civilite]['libelle'])) ? $lef['CIVILITE'][$user[0]->code_civilite]['libelle'] . ' ' : '';
                                $nom_nego .= $user[0]->nom . ' ' . $user[0]->prenom;
                                if ($famillesChamp[$i]->config_specifique === -1) {
                                    $current_affaire[$famillesChamp[$i]->nom_champ] = $nom_nego;
                                } else {
                                    $current_affaire[$famillesChamp[$i]->nom_champ] = ['id' => $user[0]->id, 'nom' => $nom_nego, 'telephone' => $user[0]->telephone, 'portable' => $user[0]->portable, 'email' => $user[0]->email];
                                }
                                if ($famillesChamp[$i]->nom_champ == "attribue_a") {
                                    $current_affaire[$famillesChamp[$i]->nom_champ] = ['id' => $user[0]->id, 'nom' => $nom_nego, 'telephone' => $user[0]->telephone, 'portable' => $user[0]->portable, 'email' => $user[0]->email];
                                } else {
                                    $current_affaire[$famillesChamp[$i]->nom_champ] = $nom_nego;
                                }
                            } else {
                                $current_affaire[$famillesChamp[$i]->nom_champ] = (array) $user[0];
                            }
                        } else {
                            $current_affaire[$famillesChamp[$i]->nom_champ] = null;
                        }
                    }
                    break;

                case 'crm_np_affaire_type':
                    if (isset($current_affaire)) {
                        $id = (isset($current_affaire[$famillesChamp[$i]->nom_champ])) ? $current_affaire[$famillesChamp[$i]->nom_champ] : null;
                        if ((int) $id > 0) {
                            $model->release();
                            $model->setTable(GetInputElement($famillesChamp[$i]->source_donnees, $nomBD));
                            $model->setClause('id = ' . $id);
                            $model->setLimit("1");
                            $model->setChamp("libelle");
                            $user = $model->getData();
                            $current_affaire[$famillesChamp[$i]->nom_champ] = (count($user) > 0) ? $user[0]->libelle : "";
                        } else {
                            $current_affaire[$famillesChamp[$i]->nom_champ] = null;
                        }
                    }
                    break;

                case 'crm_np_department':
                    if (isset($current_affaire)) {
                        $id = (isset($current_affaire[$famillesChamp[$i]->nom_champ])) ? $current_affaire[$famillesChamp[$i]->nom_champ] : null;
                        $nomDept = "";
                        if ((int) $id > 0) {
                            $model->release();
                            $model->setTable(GetInputElement($famillesChamp[$i]->source_donnees, $nomBD));
                            $model->setClause('id = ' . $id . (isset($departements_autorises) ? ' AND num IN ("' . implode('","', $departements_autorises) . '")' : ''));
                            $model->setLimit("1");
                            $model->setChamp("nom");
                            $dept = $model->getData();
                            $nomDept = (count($dept) > 0) ? $dept[0]->nom : "";
                        }
                        $current_affaire[$famillesChamp[$i]->nom_champ] = $nomDept;
                    }
                    break;

                case 'crm_np_affaire_donnees_comptables_champs':
                    $idaffaire = (isset($current_affaire['id'])) ? $current_affaire['id'] : null;
                    $model->release();
                    $table = $nomBD . '.crm_np_affaire_donnees_comptables_groupe_champs g,';
                    $table .= $nomBD . '.crm_np_affaire_donnees_comptables_champs c, ';
                    $table .= $nomBD . '.crm_np_affaire_donnees_comptables_champs_valeur v ';

                    $model->setTable($table);
                    $model->setChamp('c.*, v.*, g.id as id_groupe, g.code_groupe as code_groupe, g.libelle_groupe as libelle_groupe, g.rang as rang_groupe');
                    $clause = 'g.visibilite_donnees_comptables = 1 AND c.visibilite_donnees_comptables = 1 AND g.code_groupe = c.code_groupe_champ_donnees_comptables AND c.actif = 1 AND c.id = v.id_champ AND id_affaire = ' . $idaffaire;
                    $model->setClause($clause);
                    $model->setOrderBy("rang_groupe, c.rang, v.colonne");
                    $liste_dc = $model->getData(true);
                    $liste_donnees_comptables = [];

                    if (count($liste_dc) > 0) {
                        foreach ($liste_dc as $val_dc) {
                            $code_groupe = strtolower($val_dc->code_groupe_champ_donnees_comptables);
                            $code_champ = strtolower($val_dc->code_champ);
                            $colonne = $val_dc->colonne;
                            $liste_donnees_comptables[$code_groupe][$code_champ]['libelle_groupe'] = $val_dc->libelle_groupe;
                            $liste_donnees_comptables[$code_groupe][$code_champ]['libelle_champ'] = $val_dc->libelle_champ;
                            $liste_donnees_comptables[$code_groupe][$code_champ]['operateur_calcul'] = $val_dc->operateur_calcul;
                            $liste_donnees_comptables[$code_groupe][$code_champ]['visibilite'] = $val_dc->visibilite;
                            $liste_donnees_comptables[$code_groupe][$code_champ][$colonne] = $val_dc->valeur;
                        }
                    }

                    $model->release();
                    $model->setTable($nomBD . '.crm_np_affaire_donnees_comptables_champs_valeur');
                    $clause = 'id_affaire = ' . $idaffaire;
                    $model->setClause($clause);
                    $assigned['nb_colonne'] = $model->getCount('DISTINCT colonne');

                    $assigned['liste_donnees_comptables'] = $liste_donnees_comptables;

                    break;
                case 'crm_np_affaire_detail_avp_champs':
                    $idaffaire = (isset($current_affaire['id'])) ? $current_affaire['id'] : null;
                    $model->release();
                    $model->setTable(BASE_REF . 'crm_np_affaire_detail_avp_champs');
                    $model->setChamp("`id`,`groupe`,`code_champ`,`libelle_champ`");
                    # $model->setClause("code_champ IN (SELECT code_champ FROM " . BASE_REF . "crm_np_affaire_detail_avp_champs_valeur WHERE id_affaire = '$idaffaire' AND deleted=-1");
                    $champs = $model->getData(false, false, "code_champ");
                    $assigned["avp_champs"] = $champs;

                    $model->release();
                    $model->setTable(BASE_REF . 'crm_np_affaire_detail_avp_champs_valeur');

                    $champspargroupe["AVP"]["groupe"] = "AVP";
                    $champspargroupe["AVP"]["champs"] = [];

                    foreach ($champs as $kc => $vc) {
                        if ($vc->groupe === "AVP") {
                            $model->setChamp("`libelle_champ`, `ca_remise`,`coeff`,`valorisation`");
                        } else {
                            $model->setChamp("`montant_charges_ht`,`retraitement`");
                        }
                        $model->setClause("`code_champ` = '$vc->code_champ' AND id_affaire = '$idaffaire'");
                        $valeurchamp = $model->getData();
                        if (count($valeurchamp)) {
                            $champs[$kc]->valeurchamps = $valeurchamp[0];
                        } else {
                            if ($vc->groupe === "AVP") {
                                if ($vc->code_champ === "divers_ca") {
                                    $champs[$kc]->valeurchamps = ["libelle_champ" => null, "ca_remise" => null, "coeff" => null, "valorisation" => null];
                                } else {
                                    continue;
                                }
                            } else {
                                $champs[$kc]->valeurchamps = ["montant_charges_ht" => null, "retraitement" => null];
                            }
                        }
                        $champspargroupe[$vc->groupe]["groupe"] = $vc->groupe;
                        $champspargroupe[$vc->groupe]["champs"][$vc->code_champ] = $champs[$kc];
                    }

                    $source[$famillesChamp[$i]->nom_champ] = $champspargroupe;
                    break;
                case 'crm_np_affaire_detail_surface':
                case 'crm_np_affaire_detail_collaborateur':
                case 'crm_np_affaire_detail_fournisseur':
                case 'crm_np_affaire_cond_exp_boulangerie':
                case 'crm_np_affaire_cond_exp_bar_resto':
                case 'crm_np_affaire_cond_exp_hotel':
                case 'crm_np_affaire_etat_general_local':
                case 'crm_np_affaire_detail_bureau':
                case 'crm_np_affaire_description_immeuble':
                case 'crm_np_affaire_detail_charges_locatives':
                    if (isset($current_affaire)) {
                        $id = (isset($id_function_affaire)) ? $id_function_affaire : null;
                        if ((int) $id > 0) {
                            $model->release();
                            $model->setTable(GetInputElement($famillesChamp[$i]->source_donnees, $nomBD));
                            $model->setClause('id_affaire = ' . $id);
                            $model->setChamp("*");
                            $data = $model->getData();
                            $current_affaire[$famillesChamp[$i]->nom_champ] = (count($data) > 0) ? $data : null;

                            if (isset($current_affaire[$famillesChamp[$i]->nom_champ]) && in_array($famillesChamp[$i]->source_donnees, ['crm_np_affaire_cond_exp_boulangerie', 'crm_np_affaire_cond_exp_hotel', 'crm_np_affaire_cond_exp_bar_resto', 'crm_np_affaire_detail_bureau', 'crm_np_affaire_description_immeuble'])) {
                                $current_affaire[$famillesChamp[$i]->nom_champ] = (count($data) > 0) ? $data[0] : null;
                                if ($famillesChamp[$i]->source_donnees === 'crm_np_affaire_cond_exp_hotel') {
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->categorie_hotel = (isset($lefagence['liste']['_LP_HORECA_CATEGORIE_HOTEL'][$current_affaire[$famillesChamp[$i]->nom_champ]->categorie_hotel]['libelle'])) ? $lefagence['liste']['_LP_HORECA_CATEGORIE_HOTEL'][$current_affaire[$famillesChamp[$i]->nom_champ]->categorie_hotel]['libelle'] : '';
                                }
                                if ($famillesChamp[$i]->source_donnees === 'crm_np_affaire_detail_bureau') {
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->type_de_plateau = (isset($lefagence['liste']['_LP_HORECA_BUREAU_TYPE_DE_PLATEAU'][$current_affaire[$famillesChamp[$i]->nom_champ]->type_de_plateau]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BUREAU_TYPE_DE_PLATEAU'][$current_affaire[$famillesChamp[$i]->nom_champ]->type_de_plateau]['libelle'] : '';
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->type_de_norme = (isset($lefagence['liste']['_LP_HORECA_BUREAU_TYPE_DE_NORME'][$current_affaire[$famillesChamp[$i]->nom_champ]->type_de_norme]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BUREAU_TYPE_DE_NORME'][$current_affaire[$famillesChamp[$i]->nom_champ]->type_de_norme]['libelle'] : '';
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->type_energie = (isset($lefagence['liste']['_LP_HORECA_BUREAU_TYPE_ENERGIE'][$current_affaire[$famillesChamp[$i]->nom_champ]->type_energie]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BUREAU_TYPE_ENERGIE'][$current_affaire[$famillesChamp[$i]->nom_champ]->type_energie]['libelle'] : '';
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->domotique = (isset($lefagence['liste']['_LP_HORECA_BUREAU_DOMOTIQUE'][$current_affaire[$famillesChamp[$i]->nom_champ]->domotique]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BUREAU_DOMOTIQUE'][$current_affaire[$famillesChamp[$i]->nom_champ]->domotique]['libelle'] : '';
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->type_de_securite = (isset($lefagence['liste']['_LP_HORECA_BUREAU_TYPE_DE_SECURITE'][$current_affaire[$famillesChamp[$i]->nom_champ]->type_de_securite]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BUREAU_TYPE_DE_SECURITE'][$current_affaire[$famillesChamp[$i]->nom_champ]->type_de_securite]['libelle'] : '';
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->isolation_thermique = (isset($lefagence['liste']['_LP_HORECA_BUREAU_ISOLATION_THERMIQUE'][$current_affaire[$famillesChamp[$i]->nom_champ]->isolation_thermique]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BUREAU_ISOLATION_THERMIQUE'][$current_affaire[$famillesChamp[$i]->nom_champ]->isolation_thermique]['libelle'] : '';
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->type_eclairage = (isset($lefagence['liste']['_LP_HORECA_BUREAU_TYPE_ECLAIRAGE'][$current_affaire[$famillesChamp[$i]->nom_champ]->type_eclairage]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BUREAU_TYPE_ECLAIRAGE'][$current_affaire[$famillesChamp[$i]->nom_champ]->type_eclairage]['libelle'] : '';
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->sanitaire = (isset($lefagence['liste']['_LP_HORECA_BUREAU_SANITAIRES'][$current_affaire[$famillesChamp[$i]->nom_champ]->sanitaire]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BUREAU_SANITAIRES'][$current_affaire[$famillesChamp[$i]->nom_champ]->sanitaire]['libelle'] : '';
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->tarif_electricite = (isset($lefagence['liste']['_LP_HORECA_BUREAU_TARIF_ELECTRICITE'][$current_affaire[$famillesChamp[$i]->nom_champ]->tarif_electricite]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BUREAU_TARIF_ELECTRICITE'][$current_affaire[$famillesChamp[$i]->nom_champ]->tarif_electricite]['libelle'] : '';
                                }
                                if ($famillesChamp[$i]->source_donnees === 'crm_np_affaire_description_immeuble') {
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->type_immeuble = (isset($lefagence['liste']['_LP_HORECA_BUREAU_TYPE_IMMEUBLE'][$current_affaire[$famillesChamp[$i]->nom_champ]->type_immeuble]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BUREAU_TYPE_IMMEUBLE'][$current_affaire[$famillesChamp[$i]->nom_champ]->type_immeuble]['libelle'] : '';
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->etat = (isset($lefagence['liste']['_LP_HORECA_BUREAU_ETAT'][$current_affaire[$famillesChamp[$i]->nom_champ]->etat]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BUREAU_ETAT'][$current_affaire[$famillesChamp[$i]->nom_champ]->etat]['libelle'] : '';
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->portes_metalliques = (isset($lefagence['liste']['_LP_HORECA_BUREAU_TYPE_PORTE'][$current_affaire[$famillesChamp[$i]->nom_champ]->portes_metalliques]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BUREAU_TYPE_PORTE'][$current_affaire[$famillesChamp[$i]->nom_champ]->portes_metalliques]['libelle'] : '';
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->portes_basculantes = (isset($lefagence['liste']['_LP_HORECA_BUREAU_TYPE_PORTE'][$current_affaire[$famillesChamp[$i]->nom_champ]->portes_basculantes]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BUREAU_TYPE_PORTE'][$current_affaire[$famillesChamp[$i]->nom_champ]->portes_basculantes]['libelle'] : '';
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->portail_exterieur = (isset($lefagence['liste']['_LP_HORECA_BUREAU_TYPE_PORTE'][$current_affaire[$famillesChamp[$i]->nom_champ]->portail_exterieur]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BUREAU_TYPE_PORTE'][$current_affaire[$famillesChamp[$i]->nom_champ]->portail_exterieur]['libelle'] : '';
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->courant_faible = (isset($lefagence['liste']['_LP_HORECA_BUREAU_COURANT_FAIBLE'][$current_affaire[$famillesChamp[$i]->nom_champ]->courant_faible]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BUREAU_COURANT_FAIBLE'][$current_affaire[$famillesChamp[$i]->nom_champ]->courant_faible]['libelle'] : '';
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->aide_manoeuvre = (isset($lefagence['liste']['_LP_HORECA_BUREAU_AIDE_MANOEUVRE'][$current_affaire[$famillesChamp[$i]->nom_champ]->aide_manoeuvre]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BUREAU_AIDE_MANOEUVRE'][$current_affaire[$famillesChamp[$i]->nom_champ]->aide_manoeuvre]['libelle'] : '';
                                    $autres_details = buildlef($current_affaire[$famillesChamp[$i]->nom_champ]->autres_details);
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->autres_details = null;
                                    foreach ($autres_details as $detail) {
                                        $current_affaire[$famillesChamp[$i]->nom_champ]->autres_details[] = (isset($lefagence['liste']['_LP_HORECA_BUREAU_DESCRIPTION_IMMEUBLE'][$detail]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BUREAU_DESCRIPTION_IMMEUBLE'][$detail]['libelle'] : '';
                                    }

                                    /* foreach ($current_affaire[$famillesChamp[$i]->nom_champ] as $k => $collaborateur) {
                                      $current_affaire[$famillesChamp[$i]->nom_champ][$k]->code_civilite = (isset($lef['CIVILITE'][$collaborateur->code_civilite]['libelle'])) ? $lef['CIVILITE'][$collaborateur->code_civilite]['libelle'] : '';
                                      $current_affaire[$famillesChamp[$i]->nom_champ][$k]->code_type_contrat_personnel = (isset($lef['TYPE_CONTRAT_PERSONNEL'][$collaborateur->code_type_contrat_personnel]['libelle'])) ? $lef['TYPE_CONTRAT_PERSONNEL'][$collaborateur->code_type_contrat_personnel]['libelle'] : '';
                                      } */
                                }
                            }
                            if (isset($current_affaire[$famillesChamp[$i]->nom_champ]) && $famillesChamp[$i]->source_donnees === 'crm_np_affaire_detail_collaborateur') {
                                foreach ($current_affaire[$famillesChamp[$i]->nom_champ] as $k => $collaborateur) {
                                    $current_affaire[$famillesChamp[$i]->nom_champ][$k]->code_civilite = (isset($lef['CIVILITE'][$collaborateur->code_civilite]['libelle'])) ? $lef['CIVILITE'][$collaborateur->code_civilite]['libelle'] : '';
                                    $current_affaire[$famillesChamp[$i]->nom_champ][$k]->code_type_contrat_personnel = (isset($lef['TYPE_CONTRAT_PERSONNEL'][$collaborateur->code_type_contrat_personnel]['libelle'])) ? $lef['TYPE_CONTRAT_PERSONNEL'][$collaborateur->code_type_contrat_personnel]['libelle'] : '';
                                }
                            }
                            if (isset($current_affaire[$famillesChamp[$i]->nom_champ]) && $famillesChamp[$i]->source_donnees === 'crm_np_affaire_detail_surface') {
                                // dump($current_affaire[$famillesChamp[$i]->nom_champ]);exit();
                                foreach ($current_affaire[$famillesChamp[$i]->nom_champ] as $k => $surface) {
                                    $current_affaire[$famillesChamp[$i]->nom_champ][$k]->type_de_surface = (isset($lefagence['liste']['_LP_HORECA_BIEN_TYPE_DE_SURFACE'][$surface->type_de_surface]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BIEN_TYPE_DE_SURFACE'][$surface->type_de_surface]['libelle'] : '';
                                    $current_affaire[$famillesChamp[$i]->nom_champ][$k]->niveau = (isset($lefagence['liste']['_LP_HORECA_LOCAL_NIVEAU'][$surface->niveau]['libelle'])) ? $lefagence['liste']['_LP_HORECA_LOCAL_NIVEAU'][$surface->niveau]['libelle'] : '';
                                }
                            }
                        } else {
                            $current_affaire[$famillesChamp[$i]->nom_champ] = null;
                        }
                    }
                    break;

                case 'crm_np_personne':
                    if ($famillesChamp[$i]->nom_champ === "vendeur") {

                        if (isset($current_affaire) && !empty($current_affaire['vendeur'])) {
                            # $clause = "code_type_personne LIKE '%VENDEUR%' AND id IN (" . substr(str_replace(",", "##", $current_affaire['vendeur']), 1, -1) . ")";
                            $clause = "id IN ('" . substr(str_replace("##", "','", $current_affaire['vendeur']), 1, -1) . "')";

                            $model->release();
                            $model->setTable(GetInputElement($famillesChamp[$i]->source_donnees));
                            $model->setClause($clause);
                            $model->setChamp($champ);
                            $model->setOrderBy('nom, raison_sociale');

                            $donnees = $model->getData();
                            if (count($donnees) > 0) {
                                foreach ($donnees as $value) {

                                    if ($famillesChamp[$i]->source_donnees === "crm_np_personne") {
                                        $value->code_type_profil_txt = (isset($value->code_type_profil) && isset($lef['TYPE_PROFIL'][$value->code_type_profil]['libelle'])) ? $lef['TYPE_PROFIL'][$value->code_type_profil]['libelle'] : "";
                                        $value->code_civilite_txt = (isset($value->code_civilite) && isset($lef['CIVILITE'][$value->code_civilite]['libelle'])) ? $lef['CIVILITE'][$value->code_civilite]['libelle'] : "";
                                        $value->code_civilite_conjoint_txt = (isset($value->code_civilite_conjoint) && isset($lef['CIVILITE'][$value->code_civilite_conjoint]['libelle'])) ? $lef['CIVILITE'][$value->code_civilite_conjoint]['libelle'] : "";

                                        $value->date_creation_txt = (isset($value->date_creation) && !empty($value->date_creation)) ? textDate($value->date_creation, '%d %b %Y') : "";
                                        $value->date_entree_txt = (isset($value->date_entree) && !empty($value->date_entree)) ? textDate($value->date_entree, '%d %b %Y') : "";
                                        $value->date_naissance_txt = (isset($value->date_naissance) && !empty($value->date_naissance)) ? textDate($value->date_naissance, '%d %b %Y') : "";

                                        $value->id_ville_txt = '';
                                        if (isset($value->id_ville) && !empty($value->id_ville)) {
                                            $model->release();
                                            $model->setTable('crm_np_ville');
                                            $model->setClause(' id = ' . $value->id_ville);
                                            $model->setChamp('nom_reel, code_postal');

                                            $villes = $model->getData();
                                            $value->id_ville_txt = $villes[0]->nom_reel . ' (' . $villes[0]->code_postal . ')';
                                        }

                                        $value->id_ville_conjoint_txt = '';
                                        if (isset($value->id_ville) && !empty($value->id_ville)) {
                                            $model->release();
                                            $model->setTable('crm_np_ville');
                                            $model->setClause(' id = ' . $value->id_ville);
                                            $model->setChamp('nom_reel, code_postal');

                                            $villes = $model->getData();
                                            $value->id_ville_conjoint_txt = $villes[0]->nom_reel . ' (' . $villes[0]->code_postal . ')';
                                        }
                                    }

                                    $source[$famillesChamp[$i]->nom_champ][] = $value;
                                }
                                $current_affaire[$famillesChamp[$i]->nom_champ] = isset($source[$famillesChamp[$i]->nom_champ]) ? $source[$famillesChamp[$i]->nom_champ] : [];
                            }
                        }
                    } else {
                        if (isset($current_affaire)) {
                            $id = (isset($id_function_affaire)) ? $id_function_affaire : null;
                            $current_affaire[$famillesChamp[$i]->nom_champ] = null;
                            if ((int) $id > 0) {
                                $model->release();
                                $model->setTable($famillesChamp[$i]->source_donnees);
                                $model->setClause("id = (SELECT " . $famillesChamp[$i]->nom_champ . " FROM " . BASE_REF . "crm_np_affaire a WHERE id = '$id')");
                                $model->setChamp("*");
                                $data = $model->getData(true);
                                $current_affaire[$famillesChamp[$i]->nom_champ] = (count($data) > 0) ? $data : null;
                                if (isset($data[0])) {
                                    if ($data[0]->code_type_profil === 'PARTICULIER') {
                                        $data[0]->filiation = isset($lef['CIVILITE'][$data[0]->code_civilite]['libelle']) ? $lef['CIVILITE'][$data[0]->code_civilite]['libelle'] . " " : "";
                                        $data[0]->filiation .= !empty($data[0]->prenom) ? $data[0]->prenom . " " : "";
                                        $data[0]->filiation .= !empty($data[0]->nom) ? $data[0]->nom . " " : "";
                                    } else {
                                        $data[0]->filiation = $data[0]->raison_sociale;
                                        $data[0]->filiation .= !empty($data[0]->rcs_siret) ? " [rcs : " . $data[0]->rcs_siret . "] " : "";
                                    }
                                    $current_affaire[$famillesChamp[$i]->nom_champ] = (array) $data[0];
                                }
                            }
                        }
                    }
                    break;

                case 'crm_np_affaire_proximite':
                case 'crm_np_affaire_capital_restant_du':
                    if (isset($current_affaire)) {
                        $id = (isset($id_function_affaire)) ? $id_function_affaire : null;
                        $current_affaire[$famillesChamp[$i]->nom_champ] = null;
                        if ((int) $id > 0) {
                            $model->release();
                            $model->setTable(GetInputElement($famillesChamp[$i]->source_donnees, $nomBD));
                            $model->setClause("id_affaire = " . $id);
                            $model->setChamp("*");
                            $data = $model->getData();
                            $current_affaire[$famillesChamp[$i]->nom_champ] = (count($data) > 0) ? $data : null;
                        }
                    }
                    break;

                default:

                    if (($famillesChamp[$i]->donnees != null) && ($famillesChamp[$i]->donnees != "")) {
                        $don = (($famillesChamp[$i]->donnees != "") && ($famillesChamp[$i]->donnees != null)) ? buildLef($famillesChamp[$i]->donnees) : '*';
                        $champ = ($don != '*') ? $don[0] . ' as code, ' . $don[1] . ' as libelle' : '*';
                    }

                    if (!empty($current_affaire[$famillesChamp[$i]->nom_champ])) {
                        $model->release();
                        $model->setTable(GetInputElement($famillesChamp[$i]->source_donnees, $nomBD));

                        if (in_array($famillesChamp[$i]->code_type_form_champ, ['CHECKBOX', 'MULTISELECT'])) {
                            $clause = isset($don[0]) ? $don[0] . ' IN ("' . implode('","', buildLef($current_affaire[$famillesChamp[$i]->nom_champ])) . '")' : 'id IN ("' . implode('","', buildLef($current_affaire[$famillesChamp[$i]->nom_champ])) . '")';
                        } else {
                            $clause = isset($don[0]) ? $don[0] . ' = "' . $current_affaire[$famillesChamp[$i]->nom_champ] . '"' : 'id = "' . $current_affaire[$famillesChamp[$i]->nom_champ] . '"';
                        }
                        $model->setClause($clause);
                        $model->setChamp($champ);

                        $model->setOrderBy("");
                        $donnees = $model->getData();

                        $libelle_value = [];
                        if (count($donnees) > 0) {
                            foreach ($donnees as $value) {
                                $source[$famillesChamp[$i]->nom_champ][] = (array) $value;
                                $libelle_value[] = isset($value->libelle) ? $value->libelle : null;
                            }
                        }
                        $current_affaire[$famillesChamp[$i]->nom_champ] = (count($libelle_value) > 0) ? implode(', ', $libelle_value) : null;
                    }
                    break;
            }
        } else {
            if (($famillesChamp[$i]->donnees != null) && ($famillesChamp[$i]->donnees != "")) {
                if (in_array($famillesChamp[$i]->code_type_form_champ, ['CHECKBOX', 'MULTISELECT'])) {
                    $currentListeDonnees = buildLef($current_affaire[$famillesChamp[$i]->nom_champ]);
                    $current_affaire[$famillesChamp[$i]->nom_champ] = (count($currentListeDonnees) > 0) ? implode(', ', $currentListeDonnees) : "";
                }
                $source[$famillesChamp[$i]->nom_champ] = buildLef($famillesChamp[$i]->donnees);
            }
        }

        if ($famillesChamp[$i]->code_type_form_champ == 'INPUT') {
            switch ($famillesChamp[$i]->code_type_valeur_champ) {
                case 'DATE':
                    $famillesChamp[$i]->code_type_valeur_champ_input = 'date';
                    $current_affaire[$famillesChamp[$i]->nom_champ] = convertDateTime($current_affaire[$famillesChamp[$i]->nom_champ]);
                    break;

                case 'INT':
                case 'PRICE':
                    $famillesChamp[$i]->code_type_valeur_champ_input = 'number';
                    break;

                case 'FLOAT':
                case 'SUPERFICIE':
                case 'DISTANCE':
                case 'VOLUME':
                    $famillesChamp[$i]->code_type_valeur_champ_input = 'number" step="0.01"';
                    break;

                default:
                    $famillesChamp[$i]->code_type_valeur_champ_input = 'text';
                    break;
            }
        }

        if ($usesubfamily === true) {
            $model->release();
            $model->setTable(BASE_REF . 'crm_np_affaire_vis_sous_famille_champ');
            $model->setChamp('*');
            $model->setClause('id = ' . $famillesChamp[$i]->id_sous_famille_view);
            $sousfamille = $model->getData();

            $lib = (count($sousfamille) > 0) ? $sousfamille[0]->libelle : "";
            $champInput[$famillesChamp[$i]->id_famille_view][$famillesChamp[$i]->id_sous_famille_view]['champs'][] = $famillesChamp[$i];
            $champInput[$famillesChamp[$i]->id_famille_view][$famillesChamp[$i]->id_sous_famille_view]['sousfamille'] = $lib;
        } else {
            $champInput[] = $famillesChamp[$i];
        }
    }

    return ['source' => $source, 'champInput' => $champInput, 'current_affaire' => $current_affaire, 'allFamilles' => $allFamilles];
}

function getDetailAffaireImpression($model, $id_function_affaire, $lef, $action = 'detail', $ignoreDroitAcces = false, $nomBD = '', $for = 'VIEW', $usesubfamily = true)
{

    global $acces_mandats_affaires_autres_negociateurs, $departements_autorises, $lefagence, $assigned, $acces_mandats_affaires_autres_agences, $villesjson;

    $nomBD = empty($nomBD) ? BASE_REF : $nomBD;
    $model->release();
    $model->setTable(BASE_REF . 'crm_np_affaire');
    $model->setOrderBy("id");
    $model->setChamp("*");
    if ($ignoreDroitAcces === true) {
        $model->setClause("id = '$id_function_affaire'");
    } else {
        $model->setClause("id = '$id_function_affaire'" . (($acces_mandats_affaires_autres_negociateurs) ? "" : " AND attribue_a = " . $_SESSION[$app][_USER_]->id) . ($acces_mandats_affaires_autres_agences ? "" : " AND id_agence = " . $_SESSION[$app][_USER_]->id_agence));
    }
    $current_affaire = $model->getData(false, false);

    if (!isset($current_affaire[0])) {
        return false;
    }
    $current_affaire = (array) $current_affaire[0];

    $model->release();
    $model->setTable(BASE_REF . 'crm_np_affaire_vis_famille_champ');
    if ($for === 'VIEW') {
        $model->setClause('code_type_transaction = "' . $current_affaire['code_type_transaction'] . '" AND utilise_pour="VIEW" AND actif=1 AND id IN (SELECT DISTINCT(id_famille_view) FROM ' . BASE_REF . 'crm_np_affaire_vis_champ vc where vc.visible_view=1 AND vc.actif=1 AND vc.types_affaires_autorises LIKE "%#' . $current_affaire['id_type_affaire'] . '#%" AND deleted = -1)');
    } else {
        $model->setClause('code_type_transaction = "' . $current_affaire['code_type_transaction'] . '" AND utilise_pour="IMPRESSION" AND actif=1 AND id IN (SELECT DISTINCT(id_famille_impression) FROM ' . BASE_REF . 'crm_np_affaire_vis_champ vc where vc.visible_impression=1 AND vc.actif=1 AND vc.types_affaires_autorises LIKE "%#' . $current_affaire['id_type_affaire'] . '#%" AND deleted = -1)');
    }
    $model->setChamp("*");
    $model->setOrderBy("rang");
    $allFamilles = $model->getData();
    $assigned['allFamilles'] = $allFamilles;

    $famillesChamp = _get_family_affaire($model, $current_affaire, null, $current_affaire, "detail", '', '', $nomBD, $for);

    $champInput = [];
    $source = [];

    for ($i = 0; $i < count($famillesChamp); $i++) {
        if (($famillesChamp[$i]->source_donnees != "LIBRE") && ($famillesChamp[$i]->source_donnees != "") && ($famillesChamp[$i]->source_donnees != null)) {
            $clause = '';
            $champ = "*";

            switch ($famillesChamp[$i]->source_donnees) {
                case BASE_REF . 'crm_np_liste_element_form':
                case PREFIXE_BD . 'commun.crm_np_liste_element_form':
                    $type = buildLef($famillesChamp[$i]->donnees);
                    $source[$famillesChamp[$i]->nom_champ] = (isset($lefagence['form'][$type[0]])) ? $lefagence['form'][$type[0]] : null;

                    if (in_array($famillesChamp[$i]->code_type_form_champ, ['CHECKBOX', 'MULTISELECT'])) {
                        $currentListeLef = buildLef($current_affaire[$famillesChamp[$i]->nom_champ]);
                        $libelleListeLef = [];
                        if ($famillesChamp[$i]->nom_champ == 'code_passerelle') {
                            foreach ($currentListeLef as $valueListeLef) {
                                if (isset($lefagence['liste'][$type[0]][$valueListeLef]['libelle'])) {
                                    $libelleListeLef[] = [$valueListeLef, $lefagence['liste'][$type[0]][$valueListeLef]['libelle']];
                                }
                            }
                            $current_affaire[$famillesChamp[$i]->nom_champ] = $libelleListeLef;
                        } else {
                            foreach ($currentListeLef as $valueListeLef) {
                                if (isset($lefagence['liste'][$type[0]][$valueListeLef]['libelle'])) {
                                    $libelleListeLef[] = $lefagence['liste'][$type[0]][$valueListeLef]['libelle'];
                                }
                            }
                            $current_affaire[$famillesChamp[$i]->nom_champ] = (count($libelleListeLef) > 0) ? implode(', ', $libelleListeLef) : "";
                        }
                    } else {
                        $current_affaire[$famillesChamp[$i]->nom_champ] = (isset($lefagence['liste'][$type[0]][$current_affaire[$famillesChamp[$i]->nom_champ]]['libelle'])) ? $lefagence['liste'][$type[0]][$current_affaire[$famillesChamp[$i]->nom_champ]]['libelle'] : "";
                    }
                    break;

                case 'crm_np_ville':
                    if (isset($current_affaire)) {
                        $idville = (isset($current_affaire[$famillesChamp[$i]->nom_champ])) ? $current_affaire[$famillesChamp[$i]->nom_champ] : 0;
                        if ((int) $idville > 0) {
                            if (isset($villesjson[$idville])) {
                                $__ville = (object) $villesjson[$idville];
                            } else {
                                $model->release();
                                $model->setTable(GetInputElement($famillesChamp[$i]->source_donnees, $nomBD));
                                $model->setClause("id = '$idville'");
                                $model->setChamp("nom_reel, num_departement");
                                $__ville = $model->getData();
                                $__ville = isset($__ville[0]) ? $__ville[0] : [];
                            }
                        }
                        $current_affaire[$famillesChamp[$i]->nom_champ] = isset($__ville->nom_reel) ? $__ville->nom_reel : null;
                        $nomDept = "";
                        if (isset($__ville->num_departement) && !empty($__ville->num_departement)) {
                            $model->release();
                            $model->setTable("crm_np_department");
                            $model->setClause('num = ' . $__ville->num_departement);
                            $model->setLimit("1");
                            $model->setChamp("nom");
                            $dept = $model->getData();
                            $nomDept = (count($dept) > 0) ? $dept[0]->nom : "";
                        }
                        $current_affaire[$famillesChamp[$i]->nom_champ . "_departement"] = $nomDept;
                    }
                    break;

                case 'crm_np_utilisateur':
                    if (isset($current_affaire)) {
                        $id = (isset($current_affaire[$famillesChamp[$i]->nom_champ])) ? $current_affaire[$famillesChamp[$i]->nom_champ] : null;
                        if ((int) $id > 0) {
                            $model->release();
                            $model->setTable(GetInputElement($famillesChamp[$i]->source_donnees, $nomBD));
                            $model->setClause("id = '$id'");
                            $model->setChamp("id, code_civilite, nom, prenom, portable, email, cp, id_ville, telephone, adresse, departements_autorises, assurance_aupres_de, num_police, photo");
                            $user = $model->getData();
                            if ($action === 'detail') {
                                $nom_nego = (isset($lef['CIVILITE'][$user[0]->code_civilite]['libelle'])) ? $lef['CIVILITE'][$user[0]->code_civilite]['libelle'] . ' ' : '';
                                $nom_nego .= $user[0]->nom . ' ' . $user[0]->prenom;
                                if ($famillesChamp[$i]->config_specifique === -1) {
                                    $current_affaire[$famillesChamp[$i]->nom_champ] = $nom_nego;
                                } else {
                                    $current_affaire[$famillesChamp[$i]->nom_champ] = ['id' => $user[0]->id, 'nom' => $nom_nego, 'telephone' => $user[0]->telephone, 'portable' => $user[0]->portable, 'email' => $user[0]->email];
                                }
                                if ($famillesChamp[$i]->nom_champ == "attribue_a") {
                                    $current_affaire[$famillesChamp[$i]->nom_champ] = ['id' => $user[0]->id, 'nom' => $nom_nego, 'telephone' => $user[0]->telephone, 'portable' => $user[0]->portable, 'email' => $user[0]->email];
                                } else {
                                    $current_affaire[$famillesChamp[$i]->nom_champ] = $nom_nego;
                                }
                            } else {
                                $current_affaire[$famillesChamp[$i]->nom_champ] = (array) $user[0];
                            }
                        } else {
                            $current_affaire[$famillesChamp[$i]->nom_champ] = null;
                        }
                    }
                    break;

                case 'crm_np_affaire_type':
                    if (isset($current_affaire)) {
                        $id = (isset($current_affaire[$famillesChamp[$i]->nom_champ])) ? $current_affaire[$famillesChamp[$i]->nom_champ] : null;
                        if ((int) $id > 0) {
                            $model->release();
                            $model->setTable(GetInputElement($famillesChamp[$i]->source_donnees, $nomBD));
                            $model->setClause('id = ' . $id);
                            $model->setLimit("1");
                            $model->setChamp("libelle");
                            $user = $model->getData();
                            $current_affaire[$famillesChamp[$i]->nom_champ] = (count($user) > 0) ? $user[0]->libelle : "";
                        } else {
                            $current_affaire[$famillesChamp[$i]->nom_champ] = null;
                        }
                    }
                    break;

                case 'crm_np_department':
                    if (isset($current_affaire)) {
                        $id = (isset($current_affaire[$famillesChamp[$i]->nom_champ])) ? $current_affaire[$famillesChamp[$i]->nom_champ] : null;
                        $nomDept = "";
                        if ((int) $id > 0) {
                            $model->release();
                            $model->setTable(GetInputElement($famillesChamp[$i]->source_donnees, $nomBD));
                            $model->setClause('id = ' . $id . (isset($departements_autorises) ? ' AND num IN ("' . implode('","', $departements_autorises) . '")' : ''));
                            $model->setChamp("nom");
                            $dept = $model->getData();
                            $nomDept = (count($dept) > 0) ? $dept[0]->nom : "";
                        }
                        $current_affaire[$famillesChamp[$i]->nom_champ] = $nomDept;
                    }
                    break;
                case 'crm_np_affaire_detail_surface':
                case 'crm_np_affaire_cond_exp_boulangerie':
                case 'crm_np_affaire_cond_exp_bar_resto':
                case 'crm_np_affaire_cond_exp_hotel':
                case 'crm_np_affaire_etat_general_local':
                case 'crm_np_affaire_detail_bureau':
                case 'crm_np_affaire_description_immeuble':
                case 'crm_np_affaire_detail_charges_locatives':
                    if (isset($current_affaire)) {
                        $id = (isset($id_function_affaire)) ? $id_function_affaire : null;
                        if ((int) $id > 0) {
                            $model->release();
                            $model->setTable(GetInputElement($famillesChamp[$i]->source_donnees, $nomBD));
                            $model->setClause('id_affaire = ' . $id);
                            $model->setChamp("*");
                            $data = $model->getData();
                            $current_affaire[$famillesChamp[$i]->nom_champ] = (count($data) > 0) ? $data : null;

                            if (isset($current_affaire[$famillesChamp[$i]->nom_champ]) && in_array($famillesChamp[$i]->source_donnees, ['crm_np_affaire_cond_exp_boulangerie', 'crm_np_affaire_cond_exp_hotel', 'crm_np_affaire_cond_exp_bar_resto', 'crm_np_affaire_detail_bureau', 'crm_np_affaire_description_immeuble'])) {
                                $current_affaire[$famillesChamp[$i]->nom_champ] = (count($data) > 0) ? $data[0] : null;
                                if ($famillesChamp[$i]->source_donnees === 'crm_np_affaire_cond_exp_hotel') {
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->categorie_hotel = (isset($lefagence['liste']['_LP_HORECA_CATEGORIE_HOTEL'][$current_affaire[$famillesChamp[$i]->nom_champ]->categorie_hotel]['libelle'])) ? $lefagence['liste']['_LP_HORECA_CATEGORIE_HOTEL'][$current_affaire[$famillesChamp[$i]->nom_champ]->categorie_hotel]['libelle'] : '';
                                }
                                if ($famillesChamp[$i]->source_donnees === 'crm_np_affaire_detail_bureau') {
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->type_de_plateau = (isset($lefagence['liste']['_LP_HORECA_BUREAU_TYPE_DE_PLATEAU'][$current_affaire[$famillesChamp[$i]->nom_champ]->type_de_plateau]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BUREAU_TYPE_DE_PLATEAU'][$current_affaire[$famillesChamp[$i]->nom_champ]->type_de_plateau]['libelle'] : '';
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->type_de_norme = (isset($lefagence['liste']['_LP_HORECA_BUREAU_TYPE_DE_NORME'][$current_affaire[$famillesChamp[$i]->nom_champ]->type_de_norme]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BUREAU_TYPE_DE_NORME'][$current_affaire[$famillesChamp[$i]->nom_champ]->type_de_norme]['libelle'] : '';
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->type_energie = (isset($lefagence['liste']['_LP_HORECA_BUREAU_TYPE_ENERGIE'][$current_affaire[$famillesChamp[$i]->nom_champ]->type_energie]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BUREAU_TYPE_ENERGIE'][$current_affaire[$famillesChamp[$i]->nom_champ]->type_energie]['libelle'] : '';
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->domotique = (isset($lefagence['liste']['_LP_HORECA_BUREAU_DOMOTIQUE'][$current_affaire[$famillesChamp[$i]->nom_champ]->domotique]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BUREAU_DOMOTIQUE'][$current_affaire[$famillesChamp[$i]->nom_champ]->domotique]['libelle'] : '';
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->type_de_securite = (isset($lefagence['liste']['_LP_HORECA_BUREAU_TYPE_DE_SECURITE'][$current_affaire[$famillesChamp[$i]->nom_champ]->type_de_securite]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BUREAU_TYPE_DE_SECURITE'][$current_affaire[$famillesChamp[$i]->nom_champ]->type_de_securite]['libelle'] : '';
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->isolation_thermique = (isset($lefagence['liste']['_LP_HORECA_BUREAU_ISOLATION_THERMIQUE'][$current_affaire[$famillesChamp[$i]->nom_champ]->isolation_thermique]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BUREAU_ISOLATION_THERMIQUE'][$current_affaire[$famillesChamp[$i]->nom_champ]->isolation_thermique]['libelle'] : '';
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->type_eclairage = (isset($lefagence['liste']['_LP_HORECA_BUREAU_TYPE_ECLAIRAGE'][$current_affaire[$famillesChamp[$i]->nom_champ]->type_eclairage]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BUREAU_TYPE_ECLAIRAGE'][$current_affaire[$famillesChamp[$i]->nom_champ]->type_eclairage]['libelle'] : '';
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->sanitaire = (isset($lefagence['liste']['_LP_HORECA_BUREAU_SANITAIRES'][$current_affaire[$famillesChamp[$i]->nom_champ]->sanitaire]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BUREAU_SANITAIRES'][$current_affaire[$famillesChamp[$i]->nom_champ]->sanitaire]['libelle'] : '';
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->tarif_electricite = (isset($lefagence['liste']['_LP_HORECA_BUREAU_TARIF_ELECTRICITE'][$current_affaire[$famillesChamp[$i]->nom_champ]->tarif_electricite]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BUREAU_TARIF_ELECTRICITE'][$current_affaire[$famillesChamp[$i]->nom_champ]->tarif_electricite]['libelle'] : '';
                                }
                                if ($famillesChamp[$i]->source_donnees === 'crm_np_affaire_description_immeuble') {
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->type_immeuble = (isset($lefagence['liste']['_LP_HORECA_BUREAU_TYPE_IMMEUBLE'][$current_affaire[$famillesChamp[$i]->nom_champ]->type_immeuble]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BUREAU_TYPE_IMMEUBLE'][$current_affaire[$famillesChamp[$i]->nom_champ]->type_immeuble]['libelle'] : '';
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->etat = (isset($lefagence['liste']['_LP_HORECA_BUREAU_ETAT'][$current_affaire[$famillesChamp[$i]->nom_champ]->etat]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BUREAU_ETAT'][$current_affaire[$famillesChamp[$i]->nom_champ]->etat]['libelle'] : '';
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->portes_metalliques = (isset($lefagence['liste']['_LP_HORECA_BUREAU_TYPE_PORTE'][$current_affaire[$famillesChamp[$i]->nom_champ]->portes_metalliques]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BUREAU_TYPE_PORTE'][$current_affaire[$famillesChamp[$i]->nom_champ]->portes_metalliques]['libelle'] : '';
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->portes_basculantes = (isset($lefagence['liste']['_LP_HORECA_BUREAU_TYPE_PORTE'][$current_affaire[$famillesChamp[$i]->nom_champ]->portes_basculantes]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BUREAU_TYPE_PORTE'][$current_affaire[$famillesChamp[$i]->nom_champ]->portes_basculantes]['libelle'] : '';
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->portail_exterieur = (isset($lefagence['liste']['_LP_HORECA_BUREAU_TYPE_PORTE'][$current_affaire[$famillesChamp[$i]->nom_champ]->portail_exterieur]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BUREAU_TYPE_PORTE'][$current_affaire[$famillesChamp[$i]->nom_champ]->portail_exterieur]['libelle'] : '';
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->courant_faible = (isset($lefagence['liste']['_LP_HORECA_BUREAU_COURANT_FAIBLE'][$current_affaire[$famillesChamp[$i]->nom_champ]->courant_faible]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BUREAU_COURANT_FAIBLE'][$current_affaire[$famillesChamp[$i]->nom_champ]->courant_faible]['libelle'] : '';
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->aide_manoeuvre = (isset($lefagence['liste']['_LP_HORECA_BUREAU_AIDE_MANOEUVRE'][$current_affaire[$famillesChamp[$i]->nom_champ]->aide_manoeuvre]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BUREAU_AIDE_MANOEUVRE'][$current_affaire[$famillesChamp[$i]->nom_champ]->aide_manoeuvre]['libelle'] : '';
                                    $autres_details = buildlef($current_affaire[$famillesChamp[$i]->nom_champ]->autres_details);
                                    $current_affaire[$famillesChamp[$i]->nom_champ]->autres_details = null;
                                    foreach ($autres_details as $detail) {
                                        $current_affaire[$famillesChamp[$i]->nom_champ]->autres_details[] = (isset($lefagence['liste']['_LP_HORECA_BUREAU_DESCRIPTION_IMMEUBLE'][$detail]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BUREAU_DESCRIPTION_IMMEUBLE'][$detail]['libelle'] : '';
                                    }

                                    /* foreach ($current_affaire[$famillesChamp[$i]->nom_champ] as $k => $collaborateur) {
                                      $current_affaire[$famillesChamp[$i]->nom_champ][$k]->code_civilite = (isset($lef['CIVILITE'][$collaborateur->code_civilite]['libelle'])) ? $lef['CIVILITE'][$collaborateur->code_civilite]['libelle'] : '';
                                      $current_affaire[$famillesChamp[$i]->nom_champ][$k]->code_type_contrat_personnel = (isset($lef['TYPE_CONTRAT_PERSONNEL'][$collaborateur->code_type_contrat_personnel]['libelle'])) ? $lef['TYPE_CONTRAT_PERSONNEL'][$collaborateur->code_type_contrat_personnel]['libelle'] : '';
                                      } */
                                }
                            }
                            if (isset($current_affaire[$famillesChamp[$i]->nom_champ]) && $famillesChamp[$i]->source_donnees === 'crm_np_affaire_detail_collaborateur') {
                                foreach ($current_affaire[$famillesChamp[$i]->nom_champ] as $k => $collaborateur) {
                                    $current_affaire[$famillesChamp[$i]->nom_champ][$k]->code_civilite = (isset($lef['CIVILITE'][$collaborateur->code_civilite]['libelle'])) ? $lef['CIVILITE'][$collaborateur->code_civilite]['libelle'] : '';
                                    $current_affaire[$famillesChamp[$i]->nom_champ][$k]->code_type_contrat_personnel = (isset($lef['TYPE_CONTRAT_PERSONNEL'][$collaborateur->code_type_contrat_personnel]['libelle'])) ? $lef['TYPE_CONTRAT_PERSONNEL'][$collaborateur->code_type_contrat_personnel]['libelle'] : '';
                                }
                            }
                            if (isset($current_affaire[$famillesChamp[$i]->nom_champ]) && $famillesChamp[$i]->source_donnees === 'crm_np_affaire_detail_surface') {
                                // dump($current_affaire[$famillesChamp[$i]->nom_champ]);exit();
                                foreach ($current_affaire[$famillesChamp[$i]->nom_champ] as $k => $surface) {
                                    $current_affaire[$famillesChamp[$i]->nom_champ][$k]->type_de_surface = (isset($lefagence['liste']['_LP_HORECA_BIEN_TYPE_DE_SURFACE'][$surface->type_de_surface]['libelle'])) ? $lefagence['liste']['_LP_HORECA_BIEN_TYPE_DE_SURFACE'][$surface->type_de_surface]['libelle'] : '';
                                    $current_affaire[$famillesChamp[$i]->nom_champ][$k]->niveau = (isset($lefagence['liste']['_LP_HORECA_LOCAL_NIVEAU'][$surface->niveau]['libelle'])) ? $lefagence['liste']['_LP_HORECA_LOCAL_NIVEAU'][$surface->niveau]['libelle'] : '';
                                }
                            }
                        } else {
                            $current_affaire[$famillesChamp[$i]->nom_champ] = null;
                        }
                    }
                    break;

                case 'crm_np_affaire_proximite':
                case 'crm_np_affaire_capital_restant_du':
                    if (isset($current_affaire)) {
                        $id = (isset($id_function_affaire)) ? $id_function_affaire : null;
                        $current_affaire[$famillesChamp[$i]->nom_champ] = null;
                        if ((int) $id > 0) {
                            $model->release();
                            $model->setTable(GetInputElement($famillesChamp[$i]->source_donnees, $nomBD));
                            $model->setClause("id_affaire = " . $id);
                            $model->setChamp("*");
                            $data = $model->getData();
                            $current_affaire[$famillesChamp[$i]->nom_champ] = (count($data) > 0) ? $data : null;
                        }
                    }
                    break;

                default:

                    if (($famillesChamp[$i]->donnees != null) && ($famillesChamp[$i]->donnees != "")) {
                        $don = (($famillesChamp[$i]->donnees != "") && ($famillesChamp[$i]->donnees != null)) ? buildLef($famillesChamp[$i]->donnees) : '*';
                        $champ = ($don != '*') ? $don[0] . ' as code, ' . $don[1] . ' as libelle' : '*';
                    }

                    if (!empty($current_affaire[$famillesChamp[$i]->nom_champ])) {
                        $model->release();
                        $model->setTable(GetInputElement($famillesChamp[$i]->source_donnees, $nomBD));

                        if (in_array($famillesChamp[$i]->code_type_form_champ, ['CHECKBOX', 'MULTISELECT'])) {
                            $clause = isset($don[0]) ? $don[0] . ' IN ("' . implode('","', buildLef($current_affaire[$famillesChamp[$i]->nom_champ])) . '")' : 'id IN ("' . implode('","', buildLef($current_affaire[$famillesChamp[$i]->nom_champ])) . '")';
                        } else {
                            $clause = isset($don[0]) ? $don[0] . ' = "' . $current_affaire[$famillesChamp[$i]->nom_champ] . '"' : 'id = "' . $current_affaire[$famillesChamp[$i]->nom_champ] . '"';
                        }
                        $model->setClause($clause);
                        $model->setChamp($champ);

                        $model->setOrderBy("");
                        $donnees = $model->getData();

                        $libelle_value = [];
                        if (count($donnees) > 0) {
                            foreach ($donnees as $value) {
                                $source[$famillesChamp[$i]->nom_champ][] = (array) $value;
                                $libelle_value[] = isset($value->libelle) ? $value->libelle : null;
                            }
                        }
                        $current_affaire[$famillesChamp[$i]->nom_champ] = (count($libelle_value) > 0) ? implode(', ', $libelle_value) : null;
                    }
                    break;
            }
        } else {
            if (($famillesChamp[$i]->donnees != null) && ($famillesChamp[$i]->donnees != "")) {
                if (in_array($famillesChamp[$i]->code_type_form_champ, ['CHECKBOX', 'MULTISELECT'])) {
                    $currentListeDonnees = buildLef($current_affaire[$famillesChamp[$i]->nom_champ]);
                    $current_affaire[$famillesChamp[$i]->nom_champ] = (count($currentListeDonnees) > 0) ? implode(', ', $currentListeDonnees) : "";
                }
                $source[$famillesChamp[$i]->nom_champ] = buildLef($famillesChamp[$i]->donnees);
            }
        }

        if ($famillesChamp[$i]->code_type_form_champ == 'INPUT') {
            switch ($famillesChamp[$i]->code_type_valeur_champ) {
                case 'DATE':
                    $famillesChamp[$i]->code_type_valeur_champ_input = 'date';
                    $current_affaire[$famillesChamp[$i]->nom_champ] = convertDateTime($current_affaire[$famillesChamp[$i]->nom_champ]);
                    break;

                case 'INT':
                case 'PRICE':
                    $famillesChamp[$i]->code_type_valeur_champ_input = 'number';
                    break;

                case 'FLOAT':
                case 'SUPERFICIE':
                case 'DISTANCE':
                case 'VOLUME':
                    $famillesChamp[$i]->code_type_valeur_champ_input = 'number" step="0.01"';
                    break;

                default:
                    $famillesChamp[$i]->code_type_valeur_champ_input = 'text';
                    break;
            }
        }

        if ($usesubfamily === true) {
            $model->release();
            $model->setTable(BASE_REF . 'crm_np_affaire_vis_sous_famille_champ');
            $model->setChamp('*');
            $model->setClause('id = ' . $famillesChamp[$i]->id_sous_famille_view);
            $sousfamille = $model->getData();

            $lib = (count($sousfamille) > 0) ? $sousfamille[0]->libelle : "";
            $champInput[$famillesChamp[$i]->id_famille_view][$famillesChamp[$i]->id_sous_famille_view]['champs'][] = $famillesChamp[$i];
            $champInput[$famillesChamp[$i]->id_famille_view][$famillesChamp[$i]->id_sous_famille_view]['sousfamille'] = $lib;
        } else {
            $champInput[] = $famillesChamp[$i];
        }
    }

    return ['source' => $source, 'champInput' => $champInput, 'current_affaire' => $current_affaire, 'allFamilles' => $allFamilles];
}

function _get_family_affaire_impression($model, $current_affaire, $family, $defaultSelected, $action, $caller = '', $clauseMaJ = '')
{

    global $clause_for_hide_negociateur_field, $__rights;

    $model->release();

    $isprospect = ($action === "ajoutprospect" || (isset($current_affaire['prospect']) && $current_affaire['prospect'] == 1)) ? ' AND (vc.visible_impression=1 OR nom_champ ="code_cession_affaire")' : ' AND vc.visible_impression=1';

    if (($action === "ajout") || ($action === "ajoutprospect" && $caller !== "ajax")) {

        $clauseforfield = 'fc.utilise_pour="IMPRESSION" AND vc.code_type_transaction="' . $defaultSelected['code_type_transaction'] . '" AND fc.code_type_transaction="' . $defaultSelected['code_type_transaction'] . '" AND vc.types_affaires_autorises LIKE "%#' . $defaultSelected['id_type_affaire'] . '#%" AND vc.id_famille_impression=fc.id  AND fc.rang= ' . $family . $isprospect;
    } elseif ($action === "detail") {

        $clauseforfield = 'fc.utilise_pour="IMPRESSION" AND vc.code_type_transaction="' . $defaultSelected['code_type_transaction'] . '" AND fc.code_type_transaction="' . $defaultSelected['code_type_transaction'] . '" AND vc.types_affaires_autorises LIKE "%#' . $defaultSelected['id_type_affaire'] . '#%" AND vc.id_famille_impression=fc.id' . $isprospect;
    } elseif ($caller === "ajax") {
        $clause_type_affaire = (isset($defaultSelected['id_type_affaire']) && (int) $defaultSelected['id_type_affaire'] > 0) ? 'AND vc.types_affaires_autorises LIKE "%#' . $defaultSelected['id_type_affaire'] . '#%"' : '';
        $clauseforfield = $clauseMaJ . 'fc.utilise_pour="IMPRESSION" AND vc.code_type_transaction="' . $defaultSelected['code_type_transaction'] . '" AND fc.code_type_transaction="' . $defaultSelected['code_type_transaction'] . '" ' . $clause_type_affaire . ' AND vc.id_famille_impression=fc.id AND fc.id=' . $family . $isprospect;
    } else {

        if (isset($_SESSION['defaultSelected']['idFamille']) && (int) $_SESSION['defaultSelected']['idFamille'] > 0) {
            $clauseforfield = 'nom_champ NOT IN("code_type_transaction","id_type_affaire") AND fc.utilise_pour="IMPRESSION" AND vc.code_type_transaction="' . $defaultSelected['code_type_transaction'] . '" AND fc.code_type_transaction="' . $defaultSelected['code_type_transaction'] . '" AND vc.types_affaires_autorises LIKE "%#' . $defaultSelected['id_type_affaire'] . '#%" AND vc.id_famille_impression=fc.id AND fc.id=' . $_SESSION['defaultSelected']['idFamille'] . $isprospect;
        } else {
            $clauseforfield = 'nom_champ NOT IN("code_type_transaction","id_type_affaire") AND fc.utilise_pour="IMPRESSION" AND vc.code_type_transaction="' . $defaultSelected['code_type_transaction'] . '" AND fc.code_type_transaction="' . $defaultSelected['code_type_transaction'] . '" AND vc.types_affaires_autorises LIKE "%#' . $defaultSelected['id_type_affaire'] . '#%" AND vc.id_famille_impression=fc.id AND fc.rang=' . $family . $isprospect;
        }
    }

    # On ne recupere que les droits de lecture car seuls les champs sur lesquels le droit de lecture est present sont envoyes au formulaire.
    $clauseforfield .= (isset($__rights["lecture"]["id"]) && count($__rights["lecture"]["id"]) > 0) ? " AND vc.id IN ('" . implode("','", $__rights["lecture"]["id"]) . "')" : "";

    $model->setTable(BASE_REF . 'crm_np_affaire_vis_famille_champ fc, ' . BASE_REF . 'crm_np_affaire_vis_champ vc');
    $model->setClause($clause_for_hide_negociateur_field . $clauseforfield);
    $model->setChamp("fc.*,vc.*");
    $model->setOrderBy("fc.rang, vc.rang_impression");

    $famillesChamp = $model->getData();

    if ($action === "ajoutprospect" || (isset($current_affaire['prospect']) && $current_affaire['prospect'] == 1)) {
        if ((!isset($_SESSION['defaultSelected']['idFamille']) || ((int) $family == $_SESSION['defaultSelected']['idFamille'])) || $action === "detail") {

            $model->release();

            $model->setTable(BASE_REF . 'crm_np_affaire_vis_famille_champ fc, ' . BASE_REF . 'crm_np_affaire_vis_champ vc');
            $model->setChamp("fc.*,vc.*");
            $model->setOrderBy("fc.rang, vc.rang_impression");
            $model->setClause('nom_champ IN ("vendeur") AND fc.utilise_pour="IMPRESSION" AND vc.code_type_transaction="' . $defaultSelected['code_type_transaction'] . '" AND fc.code_type_transaction="' . $defaultSelected['code_type_transaction'] . '" AND vc.types_affaires_autorises LIKE "%#' . $defaultSelected['id_type_affaire'] . '#%" AND vc.id_famille_impression=fc.id'); # AND vc.visible_view=1
            if ($model->getCount('fc.id')) {
                $famillesChamp = array_merge($famillesChamp, $model->getData());
            }
        }
    }

    return $famillesChamp;
}

function getDetailConcerne($model, $tableauCle)
{
    global $route, $oSmarty, $assigned, $config, $acces_acheteurs_recherches_autres_agences;
    global $app;
    $liste = [];
    $lef = $model->getElementType(["CIVILITE", "TYPE_MANDAT", "TYPE_PROFIL", "LICENCE", "FORME_JURIDIQUE"], ["STATUT_PERSONNE", "ORIGINE_ACHETEUR", "SCORING", "DIPLOME_ACHETEUR", "TYPE_PROFIL", "CESSION_AFFAIRE"]);
    $recherche_details = (function ($recherche) use ($model) {
        $model->release();
        $model->setTable('crm_np_acheteur_recherche_detail');
        $model->setClause('id_acheteur_recherche=' . $recherche);
        $model->setChamp("*");
        return $model->getData(false, false, 'nom_champ');
    });
    $type_input = ["INT", "FLOAT", "DISTANCE", "VOLUME", "SUPERFICIE", "PRICE"];
    $unite = ["INT" => "", "FLOAT" => "", "DISTANCE" => "m", "VOLUME" => "m&sup3;", "SUPERFICIE" => "m&sup2;", "PRICE" => "&euro;"];

    foreach ($tableauCle as $group => $valeur) {
        switch ($group) {
            case 'AGENCE':
                $liste['AGENCE'][0] = $config['agence'];
                break;
            case 'UTILISATEUR':
                $clause = "id IN (" . implode(',', $valeur) . ")";
                $model->release();
                $model->setChamp("*");
                $model->setTable('crm_np_utilisateur');
                $model->setClause($clause);
                $model->setOrderBy("nom");
                $resultat = $model->getData();
                $liste['UTILISATEUR'] = [];

                $champsConcernes = ['nom', 'prenom', 'adresse', 'cp', 'telephone', 'portable', 'email'];
                for ($i = 0; $i < count($resultat); $i++) {

                    $liste['UTILISATEUR'][$i]['photo_negociateur'] = (!empty($resultat[$i]->photo) && file_exists($route . _STORAGE_PATH . "images/utilisateurs/" . $resultat[$i]->photo)) ? _STORAGE_PATH . "images/utilisateurs/" . $resultat[$i]->photo : MED_PATH_BE . "/users/avatar-default-icon.png";
                    $liste['UTILISATEUR'][$i]['id'] = $resultat[$i]->id;
                    $liste['UTILISATEUR'][$i]['civilite_negociateur'] = (isset($resultat[$i]->code_civilite) && !empty($resultat[$i]->code_civilite) && isset($lef["CIVILITE"][$resultat[$i]->code_civilite])) ? $lef["CIVILITE"][$resultat[$i]->code_civilite]['libelle'] : "";
                    foreach ($champsConcernes as $champ) {
                        $liste['UTILISATEUR'][$i][$champ . '_negociateur'] = $resultat[$i]->$champ;
                    }
                    $liste['UTILISATEUR'][$i]['num_police_assurance_negociateur'] = $resultat[$i]->num_police;

                    $liste['UTILISATEUR'][$i]['ville_negociateur'] = '';
                    if ($resultat[$i]->id_ville > 0) {
                        $model->setTable(CRM_NP_VILLE);
                        $model->setChamp(VILLE_NOM);
                        $model->setClause(VILLE_ID . " = " . $resultat[$i]->id_ville);
                        $ville = $model->getData(true);
                        $liste['UTILISATEUR'][$i]['ville_negociateur'] = (count($ville)) ? $ville[0]->nom : "";
                    }

                    $liste['UTILISATEUR'][$i]['departements_autorises'] = '';
                    if (strlen($resultat[$i]->departements_autorises) > 2) {
                        $departements_line = str_replace("##", ",", substr($resultat[$i]->departements_autorises, 1, -1));
                        $model->setTable(CRM_NP_DEPARTMENT);
                        $model->setChamp(DEPARTMENT_NOM);
                        $model->setClause(DEPARTMENT_ID . " IN (" . $departements_line . ")");
                        $model->setOrderBy(DEPARTMENT_NOM);
                        $departements = $model->getData(true);
                        $liste['UTILISATEUR'][$i]['departements_autorises'] = (isset($departements) && count($departements)) ? $departements : [];
                    }
                }
                break;

            case 'PERSONNE':
                $clause = "id IN (" . implode(',', $valeur) . ")";
                $model->release();
                $model->setChamp("*");
                $model->setTable("crm_np_personne");
                $model->setClause($clause);
                $model->setOrderBy('nom, raison_sociale');
                $resultat = $model->getData();
                $liste[$group] = [];

                $champsConcernes = ['rcs_siret', 'raison_sociale', 'nom', 'prenom', 'adresse', 'cp', 'telephone', 'portable', 'email', 'login', 'societe', 'fonction', 'note'];
                for ($i = 0; $i < count($resultat); $i++) {
                    //$liste[$group][$i]['photo_personne'] = (!empty($resultat[$i]->photo) && file_exists($route . _STORAGE_PATH . "agences/" . $_SESSION[$app][_USER_]->nom_dossier . "/images/personnes/" . $resultat[$i]->photo)) ? _STORAGE_PATH . "agences/" . $_SESSION[$app][_USER_]->nom_dossier . "/images/personnes/" . $resultat[$i]->photo : MED_PATH_BE . "/users/avatar-default-icon.png";
                    $liste[$group][$i]['photo_personne'] = (!empty($resultat[$i]->photo) && file_exists($route . _STORAGE_PATH . "agences/" . DOSSIER_REF . "/images/personnes/" . $resultat[$i]->photo)) ? _STORAGE_PATH . "agences/" . DOSSIER_REF . "/images/personnes/" . $resultat[$i]->photo : MED_PATH_BE . "/users/avatar-default-icon.png";
                    $liste[$group][$i]['id'] = $resultat[$i]->id;
                    $liste[$group][$i]['code_type_profil'] = $resultat[$i]->code_type_profil;
                    $liste[$group][$i]['code_type_profil_personne'] = $resultat[$i]->code_type_profil;
                    $liste[$group][$i]['civilite_personne'] = (isset($resultat[$i]->code_civilite) && !empty($resultat[$i]->code_civilite) && isset($lef["CIVILITE"][$resultat[$i]->code_civilite])) ? $lef["CIVILITE"][$resultat[$i]->code_civilite]['libelle'] : "";
                    $liste[$group][$i]['statut_personne'] = (isset($resultat[$i]->code_statut_personne) && !empty($resultat[$i]->code_statut_personne) && isset($lef["STATUT_PERSONNE"][$resultat[$i]->code_statut_personne])) ? $lef["STATUT_PERSONNE"][$resultat[$i]->code_statut_personne]['libelle'] : "";
                    $liste[$group][$i]['motdepasse_personne'] = $resultat[$i]->password;
                    foreach ($champsConcernes as $champ) {
                        $liste[$group][$i][$champ . '_personne'] = $resultat[$i]->$champ;
                    }
                    $liste[$group][$i]['ville_personne'] = '';
                    if ($resultat[$i]->id_ville > 0) {
                        $model->setTable(CRM_NP_VILLE);
                        $model->setChamp(VILLE_NOM);
                        $model->setClause(VILLE_ID . " = " . $resultat[$i]->id_ville);
                        $ville = $model->getData(true);
                        $liste[$group][$i]['ville_personne'] = (count($ville)) ? $ville[0]->nom : "";
                    }
                }

                break;

            case 'AFFAIRE':
                $resultat = [];
                foreach ($valeur as $k => $id) {
                    $detail = getDetailAffaire($model, $id, $lef, 'lettremailtype', false, '', 'VIEW', false);

                    if ($detail != false && is_array($detail)) {
                        foreach ($detail['champInput'] as $kchamp => $vchamp) {
                            if ($vchamp->nom_champ === 'vendeur') {
                                $detail['champInput'][$kchamp]->valeur_got = $detail['current_affaire']['vendeur'];
                                continue;
                            }
                            if ($vchamp->nom_champ === 'champ34') {
                                $detail['champInput'][$kchamp]->valeur_got = $assigned['liste_donnees_comptables'];
                                continue;
                            }
                            $detail['champInput'][$kchamp]->valeur_got = $detail['current_affaire'][$vchamp->nom_champ];
                        }
                        $detail['champInput']['id'] = $id;
                        $resultat[$k] = $detail['champInput'];
                    }
                }
                $liste[$group] = [];

                $suffixe = '_affaire';
                if (count($resultat)) {
                    # $champsConcernesAffaire = ['code_scoring' => 'scoring_affaire', 'code_forme_juridique' => 'forme_juridique_affaire', 'code_licence' => 'licence_affaire'];
                    $champsConcernesNegociateur = ['nom', 'prenom', 'adresse', 'cp', 'telephone', 'portable', 'email'];
                    $champsConcernesVendeur = ['id', 'code_type_profil', 'nom', 'prenom', 'adresse', 'cp', 'telephone', 'portable', 'email', 'fax', 'date_naissance', 'lieu_naissance', 'societe', 'societe', 'fonction', 'nom_conjoint', 'prenom_conjoint', 'adresse_conjoint', 'cp_conjoint', 'telephone_conjoint', 'portable_conjoint', 'email_conjoint', 'fax_conjoint', 'date_naissance_conjoint', 'lieu_naissance_conjoint', 'societe_conjoint', 'societe_conjoint', 'fonction_conjoint'];
                    $champsConcernesMandat = ["code_type_mandat" => "type_mandat", "nature_situation_affaire" => "nature_situation_affaire_mandat", "tacite_reconduction" => "type_reconduction_mandat", "renouvelable" => "renouvellement_mandat", "observation" => "observation_mandat", "duree_irrevocabilite" => "duree_irrevocabilite_mandat", "duree_mandat" => "duree_mandat"];
                    # $champsConcernesMandat = ["num_registre_repertoire" => "num_registre_repertoire_mandat", "code_type_mandat" => "type_mandat", "nature_situation_affaire" => "nature_situation_affaire_mandat", "tacite_reconduction" => "type_reconduction_mandat", "renouvelable" => "renouvellement_mandat", "observation" => "observation_mandat", "duree_irrevocabilite" => "duree_irrevocabilite_mandat", "duree_mandat" => "duree_mandat"];
                    foreach ($resultat as $i => $affaire) {
                        foreach ($affaire as $kdet => $vdet) {
                            if (is_string($kdet) && $kdet === "id") {
                                $liste[$group][$i]["id"] = $vdet;
                                continue;
                            }
                            $variable_lexique = substr($vdet->code_libelle_champ, 1, -1);
                            # $avatardir = $route . _STORAGE_PATH . "images/utilisateurs/";
                            if ($vdet->nom_champ === 'image_affaire') {
                                $files = (is_dir($route . _STORAGE_PATH . 'agences/' . DOSSIER_REF . '/images/affaires/affaire_' . $affaire['id'])) ? array_slice(scandir($route . _STORAGE_PATH . 'agences/' . DOSSIER_REF . '/images/affaires/affaire_' . $affaire['id']), 2) : [];
                                $filesnumber = count($files);
                                if ($filesnumber > 0) {
                                    for ($j = 0; $j < $filesnumber; $j++) {
                                        $files[$j] = HTTP_PATH . _STORAGE_PATH . 'agences/' . DOSSIER_REF . '/images/affaires/affaire_' . $affaire['id'] . '/' . $files[$j];
                                    }
                                }
                                $liste[$group][$i]["images_du_bien_affaire"] = $files;
                                continue;
                            }
                            if ($vdet->nom_champ === 'code_scoring') {
                                $liste[$group][$i]["scoring_affaire"] = (isset($vdet->valeur_got) && !empty($vdet->valeur_got) && isset($lef["SCORING"][$vdet->valeur_got])) ? $lef["SCORING"][$vdet->valeur_got]['libelle'] : "";
                                continue;
                            }
                            if ($vdet->nom_champ === 'code_forme_juridique') {
                                $liste[$group][$i]["forme_juridique_affaire"] = (isset($vdet->valeur_got) && !empty($vdet->valeur_got) && isset($lef["FORME_JURIDIQUE"][$vdet->valeur_got])) ? $lef["FORME_JURIDIQUE"][$vdet->valeur_got]['libelle'] : "";
                                continue;
                            }
                            if ($vdet->nom_champ === 'code_cession_affaire') {
                                $liste[$group][$i]["cession_du_bien_affaire"] = (isset($vdet->valeur_got) && !empty($vdet->valeur_got) && isset($lef["CESSION_AFFAIRE"][$vdet->valeur_got])) ? $lef["CESSION_AFFAIRE"][$vdet->valeur_got]['libelle'] : "";
                                continue;
                            }
                            if ($vdet->nom_champ === 'code_licence') {
                                $liste[$group][$i]["licence_affaire"] = (isset($vdet->valeur_got) && !empty($vdet->valeur_got) && isset($lef["LICENCE"][$vdet->valeur_got])) ? $lef["LICENCE"][$vdet->valeur_got]['libelle'] : "";
                                continue;
                            }
                            if ($vdet->nom_champ === 'attribue_a') {
                                $liste[$group][$i][$variable_lexique . $suffixe] = [];
                                $negociateur = $vdet->valeur_got;
                                foreach ($champsConcernesNegociateur as $champ) {
                                    $liste[$group][$i][$variable_lexique . $suffixe][$champ . '_negociateur'] = $negociateur[$champ];
                                }
                                $liste[$group][$i][$variable_lexique . $suffixe]['civilite_negociateur'] = (isset($negociateur['code_civilite']) && !empty($negociateur['code_civilite']) && isset($lef["CIVILITE"][$negociateur['code_civilite']])) ? $lef["CIVILITE"][$negociateur['code_civilite']]['libelle'] : "";
                                $liste[$group][$i][$variable_lexique . $suffixe]['ville_negociateur'] = '';
                                $liste[$group][$i][$variable_lexique . $suffixe]['photo_negociateur'] = (!empty($negociateur['photo']) && file_exists($route . _STORAGE_PATH . "images/utilisateurs/" . $negociateur['photo'])) ? _STORAGE_PATH . "images/utilisateurs/" . $negociateur['photo'] : MED_PATH_BE . "/users/avatar-default-icon.png";
                                if ($negociateur['id_ville'] > 0) {
                                    $model->setTable(CRM_NP_VILLE);
                                    $model->setChamp(VILLE_NOM);
                                    $model->setClause(VILLE_ID . " = " . $negociateur['id_ville']);
                                    $ville = $model->getData(true);
                                    $liste[$group][$i][$variable_lexique . $suffixe]['ville_negociateur'] = (count($ville)) ? $ville[0]->nom : "";
                                }
                                $liste[$group][$i][$variable_lexique . $suffixe]['departements_autorises_negociateur'] = '';
                                if (strlen($negociateur['departements_autorises']) > 2) {
                                    $departements_line = str_replace("##", ",", substr($negociateur['departements_autorises'], 1, -1));
                                    $model->release();
                                    $model->setTable(CRM_NP_DEPARTMENT);
                                    $model->setChamp('CONCAT(nom, " (", num, ")") as nom');
                                    $model->setClause(DEPARTMENT_ID . " IN (" . $departements_line . ")");
                                    $model->setOrderBy('nom ASC');
                                    $departements = $model->getData(); # Getting table datas

                                    $departements_autorises = [];
                                    foreach ($departements as $keydept => $departement) {
                                        $departements_autorises[$keydept] = $departement->nom;
                                    }
                                    $liste[$group][$i][$variable_lexique . $suffixe]['departements_autorises_negociateur'] = (count($departements_autorises)) ? implode(" - ", $departements_autorises) : '';
                                }
                                $liste[$group][$i][$variable_lexique . $suffixe]['num_police_assurance_negociateur'] = $negociateur['num_police'];
                                $liste[$group][$i][$variable_lexique . $suffixe]['societe_assurance_negociateur'] = $negociateur['assurance_aupres_de'];
                                continue;
                            }
                            if ($vdet->nom_champ === 'id_mandat') {
                                $liste[$group][$i][$variable_lexique . $suffixe] = [];
                                $keysmandat = array_keys($champsConcernesMandat);
                                if (is_array($vdet->valeur_got) || is_object($vdet->valeur_got)) {
                                    foreach ($vdet->valeur_got as $kmandat => $mandat) {
                                        if (in_array($kmandat, $keysmandat)) {
                                            if ($kmandat === "tacite_reconduction") {
                                                $liste[$group][$i][$variable_lexique . $suffixe][$champsConcernesMandat[$kmandat]] = $vdet->valeur_got->tacite_reconduction_txt;
                                            } else if (in_array($kmandat, ['duree_mandat', 'duree_irrevocabilite', 'renouvelable'])) {
                                                $liste[$group][$i][$variable_lexique . $suffixe][$champsConcernesMandat[$kmandat]] = $mandat . ' mois';
                                            } else {
                                                $liste[$group][$i][$variable_lexique . $suffixe][$champsConcernesMandat[$kmandat]] = $mandat;
                                            }
                                        } else {
                                            $liste[$group][$i][$variable_lexique . $suffixe][$kmandat] = $mandat;
                                        }
                                    }
                                }
                                continue;
                            }
                            if ($vdet->nom_champ === 'vendeur') {
                                $liste[$group][$i][$variable_lexique . $suffixe] = [];
                                if (count($vdet->valeur_got) > 0) {
                                    foreach ($vdet->valeur_got as $kvendeur => $vendeur) {
                                        foreach ($champsConcernesVendeur as $champ) {
                                            $liste[$group][$i][$variable_lexique . $suffixe][$kvendeur][$champ . '_proprietaire'] = $vendeur->$champ;
                                        }
                                        $liste[$group][$i][$variable_lexique . $suffixe][$kvendeur]['id'] = $vendeur->id;
                                        $liste[$group][$i][$variable_lexique . $suffixe][$kvendeur]['civilite_proprietaire'] = isset($vendeur->code_civilite_txt) ? $vendeur->code_civilite_txt : '';
                                        $liste[$group][$i][$variable_lexique . $suffixe][$kvendeur]['civilite_conjoint_proprietaire'] = isset($vendeur->code_civilite_conjoint_txt) ? $vendeur->code_civilite_conjoint_txt : '';
                                        $liste[$group][$i][$variable_lexique . $suffixe][$kvendeur]['profil_proprietaire'] = $vendeur->code_type_profil_txt;
                                        $liste[$group][$i][$variable_lexique . $suffixe][$kvendeur]['ville_proprietaire'] = isset($vendeur->ville_conjoint) ? $vendeur->ville_conjoint : '';
                                        $liste[$group][$i][$variable_lexique . $suffixe][$kvendeur]['ville_conjoint_proprietaire'] = isset($vendeur->ville_conjoint) ? $vendeur->ville_conjoint : '';
                                    }
                                }
                                continue;
                            }
                            if (in_array($vdet->code_type_form_champ, ['TABLEAU'])) {
                                #continue; # A décommenter si on veut exclure les tableaux
                            }
                            if (in_array($vdet->code_type_form_champ, ['MULTISELECT', 'CHECKBOX'])) {
                                if (is_array($vdet->valeur_got) == false) {
                                    $liste[$group][$i][$variable_lexique . $suffixe] = $vdet->valeur_got;
                                } elseif (isset($vdet->valeur_got[0])) {
                                    $liste[$group][$i][$variable_lexique . $suffixe] = (is_array($vdet->valeur_got[0])) ? implode(" - ", $vdet->valeur_got[0]) : $vdet->valeur_got[0];
                                } else {
                                    $liste[$group][$i][$variable_lexique . $suffixe] = (is_array($vdet->valeur_got)) ? implode(" - ", $vdet->valeur_got) : $vdet->valeur_got;
                                }
                            } elseif (in_array($vdet->code_type_valeur_champ, $type_input)) {
                                $liste[$group][$i][$variable_lexique . $suffixe] = number_format((int) $vdet->valeur_got, 0, '.', ' ') . " " . $unite[$vdet->code_type_valeur_champ];
                            } else {
                                $liste[$group][$i][$variable_lexique . $suffixe] = $vdet->valeur_got;
                            }
                        }
                    }
                }

                break;

            case 'VENDEUR':

                $model->release();
                $model->setChamp("*");
                $model->setTable("crm_np_personne");
                $model->setOrderBy('nom, raison_sociale');
                $model->setClause("code_type_personne LIKE '%#VENDEUR#%' AND id IN (" . implode(',', $valeur) . ")");
                $resultat = $model->getData();
                $liste[$group] = [];

                $champsConcernes = ['raison_sociale', 'rcs_siret', 'nom', 'prenom', 'adresse', 'cp', 'telephone', 'portable', 'fax', 'email', 'siege', 'lieu_naissance', 'societe', 'fonction', 'nom_conjoint', 'prenom_conjoint', 'adresse_conjoint', 'cp_conjoint', 'telephone_conjoint', 'portable_conjoint', 'fax_conjoint', 'email_conjoint', 'lieu_naissance_conjoint', 'societe_conjoint', 'observation', 'fonction_conjoint'];
                for ($i = 0; $i < count($resultat); $i++) {
                    $liste[$group][$i]['id'] = $resultat[$i]->id;
                    $liste[$group][$i]['code_type_profil'] = $resultat[$i]->code_type_profil;
                    $liste[$group][$i]['profil_proprietaire'] = (isset($resultat[$i]->code_profil_proprietaire) && !empty($resultat[$i]->code_profil_proprietaire) && isset($lef["CIVILITE"][$resultat[$i]->code_profil_proprietaire])) ? $lef["CIVILITE"][$resultat[$i]->code_profil_proprietaire]['libelle'] : "";
                    $liste[$group][$i]['civilite_proprietaire'] = (isset($resultat[$i]->code_civilite) && !empty($resultat[$i]->code_civilite) && isset($lef["CIVILITE"][$resultat[$i]->code_civilite])) ? $lef["CIVILITE"][$resultat[$i]->code_civilite]['libelle'] : "";
                    $liste[$group][$i]['civilite_conjoint_proprietaire'] = (isset($resultat[$i]->code_civilite_conjoint) && !empty($resultat[$i]->code_civilite_conjoint) && isset($lef["CIVILITE"][$resultat[$i]->code_civilite_conjoint])) ? $lef["CIVILITE"][$resultat[$i]->code_civilite_conjoint]['libelle'] : "";
                    $liste[$group][$i]['date_naissance_proprietaire'] = (isset($resultat[$i]->date_naissance) && !empty($resultat[$i]->date_naissance)) ? convDate($resultat[$i]->date_naissance) : "";
                    $liste[$group][$i]['date_naissance_conjoint_proprietaire'] = (isset($resultat[$i]->date_naissance_conjoint) && !empty($resultat[$i]->date_naissance_conjoint)) ? convDate($resultat[$i]->date_naissance_conjoint) : "";

                    foreach ($champsConcernes as $champ) {
                        $liste[$group][$i][$champ . '_proprietaire'] = $resultat[$i]->$champ;
                    }

                    $liste[$group][$i]['ville_proprietaire'] = '';
                    if ($resultat[$i]->id_ville > 0) {
                        $model->setTable(CRM_NP_VILLE);
                        $model->setChamp(VILLE_NOM);
                        $model->setClause("id = '" . $resultat[$i]->id_ville . "'");
                        $ville = $model->getData(true);
                        $liste[$group][$i]['ville_proprietaire'] = (count($ville)) ? $ville[0]->nom : "";
                    }
                    $liste[$group][$i]['ville_conjoint_proprietaire'] = '';
                    if ($resultat[$i]->id_ville_conjoint > 0) {
                        $model->setTable(CRM_NP_VILLE);
                        $model->setChamp(VILLE_NOM);
                        $model->setClause("id = '" . $resultat[$i]->id_ville_conjoint . "'");
                        $ville = $model->getData(true);
                        $liste[$group][$i]['ville_conjoint_proprietaire'] = (count($ville)) ? $ville[0]->nom : "";
                    }
                }

                break;

            case 'ACHETEURRECHERCHE':

                $model->release();
                $model->setTable('crm_np_personne a,crm_np_acheteur_recherche ar');
                $model->setChamp("a.*, ar.*");
                $clause = "a.code_type_personne LIKE '%#ACQUEREUR#%' AND a.id = ar.id_acheteur AND ar.id IN (" . implode(',', $valeur) . ")";
                $clause .= ($acces_acheteurs_recherches_autres_agences ? "" : " AND ar.id_agence = '" . $_SESSION[$app][_USER_]->id_agence . "'");
                $model->setClause($clause);
                $resultat = $model->getData(true, false);

                include_once($route . '/backend/composants/_rapprochement.php');

                $liste[$group] = [];
                $suffixe = '_recherche';
                $champsConcernesAcheteur = ['code_type_profil', 'id_ville', 'code_civilite', 'nom', 'prenom', 'rcs_siret', 'raison_sociale', 'date_naissance', 'nationalite', 'adresse', 'cp', 'telephone', 'portable', 'email', 'password', 'societe', 'fonction', 'code_diplome_acheteur'];
                $champsConcernesRecherche = ['reference', 'id_acheteur_associe', 'numero_mandat', 'date_debut_mandat', 'duree_mandat', 'date_fin_mandat', 'description', 'code_origine_recherche_acheteur', 'code_scoring', 'detail_recherche', 'attribue_a'];
                $champsConcernesNegociateur = ['nom', 'prenom', 'adresse', 'cp', 'telephone', 'portable', 'email'];
                for ($i = 0; $i < count($resultat); $i++) {
                    $liste[$group][$i]['id'] = $resultat[$i]->id;
                    foreach ($champsConcernesRecherche as $champ) {
                        if ($champ === 'code_origine_recherche_acheteur') {
                            $liste[$group][$i]['origine_recherche'] = (isset($resultat[$i]->code_origine_recherche_acheteur) && !empty($resultat[$i]->code_origine_recherche_acheteur) && isset($lef["ORIGINE_ACHETEUR"][$resultat[$i]->code_origine_recherche_acheteur])) ? $lef["ORIGINE_ACHETEUR"][$resultat[$i]->code_origine_recherche_acheteur]['libelle'] : "";
                            continue;
                        }
                        if ($champ === 'code_scoring') {
                            $liste[$group][$i]['scoring_recherche'] = (isset($resultat[$i]->code_scoring) && !empty($resultat[$i]->code_scoring) && isset($lef["SCORING"][$resultat[$i]->code_scoring])) ? $lef["SCORING"][$resultat[$i]->code_scoring]['libelle'] : "";
                            continue;
                        }
                        if ($champ === 'id_acheteur_associe' && !empty($resultat[$i]->id_acheteur_associe)) {
                            $model->release();
                            $model->setChamp("*");
                            $model->setTable('crm_np_personne');
                            $model->setOrderBy('nom, raison_sociale');
                            $model->setClause("code_type_personne LIKE '%#ACQUEREUR#%' AND id IN (" . str_replace("#", "", str_replace("##", ",", $resultat[$i]->id_acheteur_associe)) . ")");
                            $associes = $model->getData(false);
                            $datas = [];

                            foreach ($associes as $key => $brute) {

                                $autres = '';
                                $datas[$key]['code_type_profil'] = (isset($lef['TYPE_PROFIL'][$brute->code_type_profil])) ? $lef['TYPE_PROFIL'][$brute->code_type_profil]['libelle'] . ' ' : '';
                                if ($brute->code_type_profil === 'PARTICULIER') {
                                    $acheteur = [];
                                    $acheteur[] = (isset($brute->code_civilite) && !empty($brute->code_civilite) && isset($lef["CIVILITE"][$brute->code_civilite])) ? $lef["CIVILITE"][$brute->code_civilite]['libelle'] : "";
                                    $acheteur[] = (isset($brute->nom) && !empty($brute->nom)) ? strtoupper($brute->nom) : "";
                                    $acheteur[] = (isset($brute->prenom) && !empty($brute->prenom)) ? ucwords($brute->prenom) : "";

                                    $datas[$key]['nom'] = "<span class='font-weight-bold text-danger'>" . implode(" ", $acheteur) . "</span>";
                                    $datas[$key]['nom'] .= "<br /><small><span class='font-weight-bold'>Né(e) le : </span><span class='right'>" . ((!empty($brute->date_naissance) && $brute->date_naissance !== "0000-00-00") ? textDate($brute->date_naissance, '%d %b %Y') : "ND") . "</span></small>";
                                    $autres .= (!empty($brute->nationalite)) ? "<br /><small><span class='font-weight-bold'>Nationalité : </span><span class='right'>" . $brute->nationalite . "</span></small>" : "";
                                    $autres .= (!empty($brute->societe)) ? "<br /><small><span class='font-weight-bold'>Société : </span><span class='right'>" . $brute->societe . "</span></small>" : "";
                                    $autres .= (!empty($brute->fonction)) ? "<br /><small><span class='font-weight-bold'>Fonction : </span><span class='right'>" . $brute->fonction . "</span></small>" : "";
                                    $autres .= (isset($lef['DIPLOME_ACHETEUR'][$brute->code_diplome_acheteur])) ? "<br /><small><span class='font-weight-bold'>Fonction : </span><span class='right'>" . $lef['DIPLOME_ACHETEUR'][$brute->code_diplome_acheteur]['libelle'] . "</span></small>" : "";
                                    $datas[$key]['autres'] = trim(substr($autres, 6));
                                } else {
                                    $datas[$key]['nom'] = "<span class='font-weight-bold text-success'>" . $brute->raison_sociale . "</span>";
                                    $datas[$key]['nom'] .= (!empty($brute->rcs_siret)) ? "<br /><small><span class='font-weight-bold'>RCS : </span><span class='right'>" . $brute->rcs_siret . "</span></small>" : "";
                                    $datas[$key]['autres'] = '';
                                }
                                $adresse = [];
                                if (!empty($brute->adresse)) {
                                    $adresse[] = $brute->adresse;
                                }
                                if (!empty($brute->cp)) {
                                    $adresse[] = $brute->cp;
                                }
                                if (!empty($brute->id_ville)) {
                                    $model->release();
                                    $model->setTable('crm_np_ville');
                                    $model->setChamp('nom_reel');
                                    $model->setClause('id = ' . $brute->id_ville);
                                    $ville_q = $model->getData();

                                    if (isset($ville_q[0])) {
                                        $adresse[] = $ville_q[0]->nom_reel;
                                    }
                                }
                                $coordonnees = (count($adresse)) ? "<br /><small><span class='font-weight-bold'>Adresse : </span><span class='right'>" . implode(" ", $adresse) . "</span></small>" : "";
                                $coordonnees .= (!empty($brute->email)) ? "<br /><small><span class='font-weight-bold'>E-mail : </span><span class='right' data-make-mail-me-true>" . $brute->email . "</span></small>" : "";
                                $coordonnees .= (!empty($brute->telephone)) ? "<br /><small><span class='font-weight-bold'>Téléphone : </span><span class='right'><a href='tel:" . $brute->telephone . "'>" . $brute->telephone . "</a></span></small>" : "";
                                $coordonnees .= (!empty($brute->portable)) ? "<br /><small><span class='font-weight-bold'>Portable : </span><span class='right'><a href='tel:" . $brute->portable . "'>" . $brute->portable . "</a></span></small>" : "";

                                $datas[$key]['coordonnees'] = trim(substr($coordonnees, 6));
                            }
                            $assigned['onlytableneeded'] = 1;
                            $assigned['associes'] = $datas;
                            $frame = $route . '/templates/backend/' . _version . '/modules/acheteursrecherche/inc/_inc_liste_associe.tpl';

                            $oSmarty->assign("assigned", $assigned);
                            $liste[$group][$i]['liste_associe_acheteur'] = $oSmarty->fetch($frame);
                            continue;
                        }
                        if ($champ === 'detail_recherche') {
                            $details = $recherche_details($resultat[$i]->id);
                            $brute = [];
                            if (count($details)) {
                                $brute = getsearchinfos($model, $details);
                                foreach ($brute as $vdet) {
                                    $variable_lexique = substr($vdet->code_libelle_champ, 1, -1);
                                    if ($vdet->code_filtre_type === 'MULTISELECT') {
                                        #  $liste[$group][$i]['detail_recherche'][$skdet][$variable_lexique . $suffixe] = implode(" - ", $vdet->valeur_got);
                                        $liste[$group][$i][$variable_lexique . $suffixe] = implode(" - ", $vdet->valeur_got);
                                    } elseif (in_array($vdet->code_type_valeur_champ, $type_input)) {
                                        if ($vdet->type_donnees_critere_recherche === 'intervalle') {
                                            if (count($vdet->valeur_got) === 2) {
                                                $liste[$group][$i][$variable_lexique . $suffixe][] = (empty($vdet->valeur_got[0])) ? "" : number_format($vdet->valeur_got[0], 0, '.', ' ') . " " . $unite[$vdet->code_type_valeur_champ];
                                                $liste[$group][$i][$variable_lexique . $suffixe][] = (empty($vdet->valeur_got[1])) ? "" : number_format($vdet->valeur_got[1], 0, '.', ' ') . " " . $unite[$vdet->code_type_valeur_champ];
                                                if (!empty($liste[$group][$i][$variable_lexique . $suffixe][0]) && !empty($liste[$group][$i][$variable_lexique . $suffixe][1])) {
                                                    $liste[$group][$i][$variable_lexique . $suffixe] = "entre " . $liste[$group][$i][$variable_lexique . $suffixe][0] . " et " . $liste[$group][$i][$variable_lexique . $suffixe][1];
                                                }
                                            } else {
                                                $liste[$group][$i][$variable_lexique . $suffixe] = (empty($vdet->valeur_got[0])) ? "" : number_format($vdet->valeur_got[0], 0, '.', ' ') . " " . $unite[$vdet->code_type_valeur_champ];
                                            }
                                        } else {
                                            $liste[$group][$i][$variable_lexique . $suffixe] = $vdet->valeur_got;
                                        }
                                        # $liste[$group][$i][$variable_lexique . $suffixe] .= " " . $unite[$vdet->code_type_valeur_champ];
                                    } else {
                                        $liste[$group][$i][$variable_lexique . $suffixe] = $vdet->valeur_got;
                                    }
                                }
                            }
                            continue;
                        }
                        if ($champ === 'attribue_a') {
                            # $liste[$group][$i][$variable_lexique . $suffixe] = [];
                            $model->release();
                            $model->setChamp("*");
                            $model->setTable('crm_np_utilisateur');
                            $model->setClause("id = " . $resultat[$i]->attribue_a);
                            $inegociateur = $model->getData();
                            $negociateur = (array) $inegociateur[0];

                            foreach ($champsConcernesNegociateur as $champ) {
                                $liste[$group][$i][$champ . '_attributaire'] = $negociateur[$champ];
                            }
                            $liste[$group][$i]['civilite_attributaire'] = (isset($negociateur['code_civilite']) && !empty($negociateur['code_civilite']) && isset($lef["CIVILITE"][$negociateur['code_civilite']])) ? $lef["CIVILITE"][$negociateur['code_civilite']]['libelle'] : "";
                            $liste[$group][$i]['ville_attributaire'] = '';
                            $liste[$group][$i]['photo_attributaire'] = (!empty($negociateur['photo']) && file_exists($route . _STORAGE_PATH . "images/utilisateurs/" . $negociateur['photo'])) ? _STORAGE_PATH . "images/utilisateurs/" . $negociateur['photo'] : MED_PATH_BE . "/users/avatar-default-icon.png";
                            if ($negociateur['id_ville'] > 0) {
                                $model->setTable(CRM_NP_VILLE);
                                $model->setChamp(VILLE_NOM);
                                $model->setClause(VILLE_ID . " = " . $negociateur['id_ville']);
                                $ville = $model->getData(true);
                                $liste[$group][$i]['ville_attributaire'] = (count($ville)) ? $ville[0]->nom : "";
                            }
                            $liste[$group][$i]['departements_autorises_attributaire'] = '';
                            if (strlen($negociateur['departements_autorises']) > 2) {
                                $departements_line = str_replace("##", ",", substr($negociateur['departements_autorises'], 1, -1));
                                $model->release();
                                $model->setTable(CRM_NP_DEPARTMENT);
                                $model->setChamp('CONCAT(nom, " (", num, ")") as nom');
                                $model->setClause(DEPARTMENT_ID . " IN (" . $departements_line . ")");
                                $model->setOrderBy('nom');
                                $departements = $model->getData(); # Getting table datas

                                $departements_autorises = [];
                                foreach ($departements as $keydept => $departement) {
                                    $departements_autorises[$keydept] = $departement->nom;
                                }
                                $liste[$group][$i]['departements_autorises_attributaire'] = (count($departements_autorises)) ? implode(" - ", $departements_autorises) : '';
                            }
                            $liste[$group][$i]['num_police_assurance_attributaire'] = $negociateur['num_police'];
                            $liste[$group][$i]['societe_assurance_attributaire'] = $negociateur['assurance_aupres_de'];
                            continue;
                        }
                        $liste[$group][$i][$champ . $suffixe] = $resultat[$i]->$champ;
                    }
                    foreach ($champsConcernesAcheteur as $champ) {
                        if ($champ === 'password') {
                            $liste[$group][$i]['motdepasse_acheteur'] = $resultat[$i]->password;
                            continue;
                        }
                        if ($champ === 'code_civilite') {
                            $liste[$group][$i]['civilite_acheteur'] = (isset($resultat[$i]->code_civilite) && !empty($resultat[$i]->code_civilite) && isset($lef["CIVILITE"][$resultat[$i]->code_civilite])) ? $lef["CIVILITE"][$resultat[$i]->code_civilite]['libelle'] : "";
                            continue;
                        }
                        if ($champ === 'code_diplome_acheteur') {
                            $liste[$group][$i]['diplome_acheteur'] = (isset($resultat[$i]->code_diplome_acheteur) && !empty($resultat[$i]->code_diplome_acheteur) && isset($lef["DIPLOME_ACHETEUR"][$resultat[$i]->code_diplome_acheteur])) ? $lef["DIPLOME_ACHETEUR"][$resultat[$i]->code_diplome_acheteur]['libelle'] : "";
                            continue;
                        }
                        if ($champ === 'id_ville') {
                            $liste[$group][$i]['ville_acheteur'] = '';
                            if ($resultat[$i]->id_ville > 0) {
                                $model->setTable(CRM_NP_VILLE);
                                $model->setChamp(VILLE_NOM);
                                $model->setClause(VILLE_ID . " = " . $resultat[$i]->id_ville);
                                $ville = $model->getData(true);
                                $liste[$group][$i]['ville_acheteur'] = (count($ville)) ? $ville[0]->nom : "";
                            }
                            continue;
                        }
                        $liste[$group][$i][$champ . '_acheteur'] = $resultat[$i]->$champ;
                    }
                }

                break;
        }
    }

    return $liste;
}

function convertVarLettreType($variable)
{
    return '${' . substr($variable, 1, strlen($variable) - 2) . '}';
}

function rrmdir($dir)
{
    if (is_dir($dir)) {
        $files = scandir($dir);
        foreach ($files as $file)
            if ($file != "." && $file != "..")
                rrmdir("$dir/$file");
        rmdir($dir);
    } else if (file_exists($dir))
        unlink($dir);
}

# copies files and non-empty directories

function rcopy($src, $dst)
{
    if (file_exists($dst))
        rrmdir($dst);
    if (is_dir($src)) {
        mkdir($dst);
        $files = scandir($src);
        foreach ($files as $file)
            if ($file != "." && $file != "..")
                rcopy("$src/$file", "$dst/$file");
    } else if (file_exists($src))
        copy($src, $dst);
}

function doc2pdf($file, $outdir)
{

    putenv('PATH=/usr/local/bin:/bin:/usr/bin:/usr/local/sbin:/usr/sbin:/sbin');
    putenv('HOME=' . $outdir);
    exec('/usr/bin/libreoffice --headless --convert-to pdf ' . $file . ' --outdir ' . $outdir . ' 2>&1', $output);

    # putenv('HOME=/var/www/html/net-profil/subdomains/logiciel/test/t'); # old value
    # exec('/usr/bin/libreoffice --headless --convert-to pdf /var/www/html/com/net-profil/subdomains/logiciel/test/mybondevisite2.docx --outdir /var/www/html/com/net-profil/subdomains/logiciel/test/t 2>&1', $output);
    return $output;
}

function nperror()
{

    global $route, $app;

    error_reporting(E_ALL);

    if ((defined("_ENV") && _ENV === "DEV") || (isset($_SESSION[$app][_USER_]->code_type_utilisateur) && $_SESSION[$app][_USER_]->code_type_utilisateur === "SUPERADMINISTRATEUR")) {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
    } else {
        ini_set('display_errors', 0);
        ini_set('display_startup_errors', 0);
    }

    ini_set('log_errors', 1);
    ini_set('html_errors', 1);

    if (isset($_SESSION[$app][_USER_]->id)) {
        $log = $route . _STORAGE_PATH . "agences/" . $_SESSION[$app][_USER_]->nom_dossier . "/errorlog.txt";
    } else {
        $log = $route . _STORAGE_PATH . "agences/errorlog.txt";
    }

    ini_set('error_log', $log);
}

function parseToArrayAffaires($model, $nom_bd)
{
    global $acces_mandats_affaires_autres_negociateurs, $acces_negociateurs_classement_par_departement, $departements_autorises, $acces_acheteurs_recherches_autres_negociateurs, $acces_acheteurs_recherches_autres_agences;
    global $app;
    $tab = [];

    $clause = (isset($acces_acheteurs_recherches_autres_negociateurs) && $acces_acheteurs_recherches_autres_negociateurs) ? "1=1" : "r.attribue_a = '" . $_SESSION[$app][_USER_]->id . "'" . ($acces_acheteurs_recherches_autres_agences ? "" : " AND r.id_agence = '" . $_SESSION[$app][_USER_]->id_agence . "'");

    $model->release();
    $model->setChamp("r.id, r.reference, a.nom, a.prenom, a.raison_sociale, a.rcs_siret, a.code_type_profil");
    $model->setTable('crm_np_acheteur_recherche r, crm_np_personne a');
    $model->setClause("$clause AND a.code_type_personne LIKE '%#ACQUEREUR#%' AND r.id_acheteur=a.id");
    $recherches = $model->getData();
    foreach ($recherches as $recherche) {
        $tab['rech' . $recherche->id] = ($recherche->code_type_profil === "PARTICULIER") ? $recherche->reference . ' (' . $recherche->prenom . ' ' . $recherche->nom . ')' : $recherche->reference . ' (' . $recherche->raison_sociale . '/' . $recherche->rcs_siret . ')';
    }

    $clause = (isset($acces_mandats_affaires_autres_negociateurs) && $acces_mandats_affaires_autres_negociateurs) ? '1=1' : ' attribue_a = ' . $_SESSION[$app][_USER_]->id;
    if (isset($acces_negociateurs_classement_par_departement) && $acces_negociateurs_classement_par_departement) {
        $clause .= isset($departements_autorises) ? ' AND a.num_departement IN ("' . implode('","', $departements_autorises) . '") ' : '';
    }
    $model->setChamp("a.id, a.reference, a.enseigne, a.code_type_transaction, a.id_type_affaire, a.adresse, t.libelle, v.nom_reel");
    $model->setTable($nom_bd . 'crm_np_affaire a, ' . BASE_REF . 'crm_np_affaire_type t, crm_np_ville v');
    $model->setClause($clause . ' AND a.id_type_affaire = t.id AND a.id_ville=v.id');
    $affaires = $model->getData();

    foreach ($affaires as $affaire) {
        $model->release();
        $model->setChamp("libelle");
        $model->setTable('crm_np_liste_element_form');
        $model->setClause("code='" . $affaire->code_type_transaction . "' AND actif=1");
        $tt = $model->getData();

        $tab['aff' . $affaire->id] = ($affaire->enseigne != null || $affaire->enseigne != "") ? $affaire->reference . ' (' . $affaire->enseigne . ')' : $affaire->reference . ' (' . $tt[0]->libelle . ' ' . $affaire->libelle . ' ' . $affaire->adresse . ' ' . $affaire->nom_reel . ')';
    }
    return $tab;
}

function saveNotification($model, $route, $codeNotification, $concerne, $toReplace = [], $acces = false, $idUserConcerne = null, $infosUser = [])
{

    global $app;

    $model->release();
    $model->setChamp("*");
    $model->setTable('crm_np_liste_element_form');
    $model->setClause("code='" . $codeNotification . "' AND actif=1");
    $not = $model->getData();

    if (isset($session)) {
        $infosUser['id'] = $_SESSION[$app][_USER_]->id;
        $infosUser['id_agence'] = $_SESSION[$app][_USER_]->id_agence;
        $infosUser['nom_dossier'] = $_SESSION[$app][_USER_]->nom_dossier;
    }

    if (count($not) && count($infosUser)) {

        $newNot['libelle'] = $not[0]->libelle;
        $newNot['description'] = str_replace(array_keys($toReplace), array_values($toReplace), $not[0]->description);
        $newNot['urgente'] = -1;
        $newNot['actif'] = 1;
        $newNot['picto'] = $not[0]->pictogramme;
        $newNot['id_concerne'] = $concerne['id'];
        $newNot['code_source_concerne'] = $concerne['source'];
        $newNot['date_creation'] = date('Y-m-d H:i:s');

        # On définit ceux qui y ont accès
        if ($acces === true) { # On copie à tous
            $usersNotif = listeIdUsers($model, $infosUser['id_agence'], [$infosUser['id']]);
        } else { #On copie uniquement aux administrateurs
            $usersNotif = listeIdUsers($model, $infosUser['id_agence'], [$infosUser['id']], 'ADMINISTRATEUR');
        }
        $usersNotif[$infosUser['id']] = $infosUser['id'];

        if (($idUserConcerne != null) && !isset($usersNotif[$idUserConcerne])) {
            $usersNotif[$idUserConcerne] = $idUserConcerne;
        }

        foreach ($usersNotif as $idUser => $valIdUser) {
            $filePath = $route . _STORAGE_PATH . 'agences/' . $infosUser['nom_dossier'] . '/json/notif_user_' . $valIdUser;
            $notifs = file_get_contents($filePath);
            $arraysNot = json_decode($notifs, true);
            $index = (is_array($arraysNot) > 0) ? count($arraysNot) : 0;
            $arraysNot[$index] = $newNot;
            $fp = fopen($filePath, 'w');
            fwrite($fp, json_encode($arraysNot));
            fclose($fp);
        }

        $filePath_G = $route . _STORAGE_PATH . 'agences/' . $infosUser['nom_dossier'] . '/json/notif_general';
        $notifs_G = file_get_contents($filePath_G);
        $arraysNot_G = json_decode($notifs_G, true);
        $index = (is_array($arraysNot_G) > 0) ? count($arraysNot_G) : 0;
        $arraysNot_G[$index] = $newNot;
        $fp_G = fopen($filePath_G, 'w');
        fwrite($fp_G, json_encode($arraysNot_G));
        fclose($fp_G);
    }
}

function getNotification($route)
{
    global $app;
    $notifs = JSONFileToarray($route . _STORAGE_PATH . 'agences/' . $_SESSION[$app][_USER_]->nom_dossier . '/json/notif_user_' . $_SESSION[$app][_USER_]->id);

    return $notifs;
}

function docfromcourrier($model, $clause)
{
    global $route;

    $datas = [];

    # Recuperation des courriers envoyes
    $model->release();
    $model->setTable(BASE_REF . '.crm_np_courrier_ENVoye');
    $model->setChamp("id, id_courrier_type, envoye_par, destinataire");
    $model->setClause($clause . " AND origine_courrier = 'LETTRETYPE'");
    $envoyes = $model->getData();
    foreach ($envoyes as $v) {
        $destinataires = buildLef($v->destinataire);
        if (count($destinataires) > 0) {
            foreach ($destinataires as $destinataire) {
                $pathcourriersenvoyes = getdir($route . _STORAGE_PATH, "agences/" . DOSSIER_REF . "/documents/courriersenvoyes/lettrestype/" . $v->id . "/$destinataire/");
                $content = JSONFileToArray($pathcourriersenvoyes . "_courrier.json");
                if (!isset($content["pid"])) {
                    continue;
                }
                $k = $content["pid"] . "_" . $content["recipientinfo"]["type"] . $content["recipientinfo"]["id"];
                $truncature = (($content["recipientinfo"]["type"] === "ACHETEURRECHERCHE") ? "acheteur" : strtolower($content["recipientinfo"]["type"])) . $content["recipientinfo"]["id"];

                $folder = getdir($route . _STORAGE_PATH, "agences/" . DOSSIER_REF . "/documents/courriersenvoyes/lettrestype/piecesjointes/" . $content["piecesjointes"] . "/");
                $file = getFiles($folder, $truncature, true, ['pdf'], 'file');
                if (!isset($file[0])) {
                    continue;
                }
                $datas['envoyes'][$k]["files"] = pathinfo($file[0]);
                $datas['envoyes'][$k]["files"]["httppath"] = HTTP_PATH . _STORAGE_PATH . "agences/" . DOSSIER_REF . "/documents/courriersenvoyes/lettrestype/piecesjointes/" . $content["piecesjointes"] . "/" . $datas['envoyes'][$k]["files"]["basename"];
                $datas['envoyes'][$k]["files"]["size"] = getFileSize($file[0]);
                $datas['envoyes'][$k]["files"]["nametruncature"] = ucfirst(str_replace([$truncature . "-", "-"], ["", " "], $datas['envoyes'][$k]["files"]["filename"]));
                # $datas[$k]["files"]["symbolic"] = symlink($datas[$k]["files"]["httppath"], $k);
                # $datas[$content["pid"]]["files"] = docs($route, "courriersenvoyes/lettrestype/piecesjointes/" . $content["piecesjointes"]);

                $datas['envoyes'][$k]["recipientinfo"] = $content["recipientinfo"];
                $datas['envoyes'][$k]["date"] = $content["time"];
            }
        }
    }

    # Recuperation des courriers imprimes
    $model->release();
    $model->setTable(BASE_REF . 'crm_np_courrier_imprime');
    $model->setChamp("id, pid, id_lettre_type, imprime_par, destinataire, affaires_concernees");
    $model->setClause($clause);
    $imprimes = $model->getData();

    foreach ($imprimes as $v) {
        $affairesconcernees = buildLef($v->affaires_concernees);
        $affairesconcerneesrecuperees = [];
        if (count($affairesconcernees) > 0) {
            $model->release();
            $model->setTable(BASE_REF . 'crm_np_affaire');
            $model->setChamp("id, reference, enseigne");
            $model->setClause("id IN (" . implode(",", $affairesconcernees) . ")");
            $affaires = $model->getData();
            foreach ($affaires as $affaire) {
                $affairesconcerneesrecuperees[] = $affaire->reference . (empty($affaire->enseigne) ? "" : " [$affaire->enseigne]");
            }
        }
        $destinataires = buildLef($v->destinataire);
        if (count($destinataires) > 0) {
            foreach ($destinataires as $destinataire) {
                $pathcourriersimprimes = getdir($route . _STORAGE_PATH, "agences/" . DOSSIER_REF . "/documents/courriersimprimes/" . $v->pid . "/" . $v->id . "/$destinataire/");
                $content = JSONFileToArray($pathcourriersimprimes . "_courrierimprime.json");
                if (!isset($content["pid"])) {
                    continue;
                }
                $k = $content["pid"] . "_" . $content["recipientinfo"]["type"] . $content["recipientinfo"]["id"];
                $truncature = (($content["recipientinfo"]["type"] === "ACHETEURRECHERCHE") ? "acheteur" : strtolower($content["recipientinfo"]["type"])) . $content["recipientinfo"]["id"];
                $file = getFiles($pathcourriersimprimes, "[SIGNED] " . $truncature, true, ['pdf'], 'file');
                $signed = 1;
                if (!isset($file[0])) {
                    $signed = -1;
                    $file = getFiles($pathcourriersimprimes, $truncature, true, ['pdf'], 'file');
                    if (!isset($file[0])) {
                        continue;
                    }
                }
                $datas['imprimes'][$k]["files"] = pathinfo($file[0]);
                $datas['imprimes'][$k]["files"]["httppath"] = HTTP_PATH . _STORAGE_PATH . "agences/" . DOSSIER_REF . "/documents/courriersimprimes/" . $v->pid . "/" . $v->id . "/$destinataire/" . $datas['imprimes'][$k]["files"]["basename"];
                $datas['imprimes'][$k]["files"]["size"] = getFileSize($file[0]);
                $datas['imprimes'][$k]["files"]["nametruncature"] = ucfirst(str_replace([$truncature . "-", "-", "[SIGNED] "], ["", " ", ""], $datas['imprimes'][$k]["files"]["filename"]));
                # $datas[$k]["files"]["symbolic"] = symlink($datas[$k]["files"]["httppath"], $k);
                # $datas[$content["pid"]]["files"] = docs($route, "courriersenvoyes/lettrestype/piecesjointes/" . $content["piecesjointes"]);

                $datas['imprimes'][$k]["signe"] = $signed;
                $datas['imprimes'][$k]["date"] = $content["time"];
                $datas['imprimes'][$k]["recipientinfo"] = $content["recipientinfo"];
                $datas['imprimes'][$k]["affairesconcernees"] = count($affairesconcerneesrecuperees) ? implode(" - ", $affairesconcerneesrecuperees) : "";
            }
        }
    }

    return $datas;
}

/* --------------------------------------------------------------- */
/*
  Titre : Calcul la taille d'un dossier en Octet
  URL   : https://phpsources.net/code_s.php?id=688
  Auteur           : miistracy
  Date édition     : 22 Aout 2013
  Date mise à jour : 21 Aout 2019
  Rapport de la maj:
  - fonctionnement du code vérifié
 */
/* --------------------------------------------------------------- */

function tailleDossier($Rep)
{
    $Racine = opendir($Rep);
    $Taille = 0;
    while ($Dossier = readdir($Racine)) {
        if ($Dossier != '..' && $Dossier != '.') {
            //Ajoute la taille du sous dossier
            if (is_dir($Rep . '/' . $Dossier))
                $Taille += TailleDossier($Rep . '/' . $Dossier);
            //Ajoute la taille du fichier
            else
                $Taille += filesize($Rep . '/' . $Dossier);
        }
    }
    closedir($Racine);
    return $Taille;
}

function executemyli($database, $requete)
{
    $link = mysqlidb($database);
    mysqli_select_db($link, $database);
    mysqli_query($link, $requete);
    dump(mysqli_error($link));
    mysqli_close($link);
}

function truncate($database, $tables)
{
    if (!is_array($tables) || count($tables) === 0) {
        return;
    }

    $link = mysqlidb($database);

    foreach ($tables as $table) {
        $requete = "TRUNCATE TABLE $table";
        mysqli_query($link, $requete);
    }

    mysqli_close($link);
}

function mysqlidb($database)
{
    return mysqli_connect(HOST, USER, PASS, $database);
}

function historyNavigation($titre = "", $picto = "fas fa-history")
{
    global $get;
    global $app;

    $target = $get["module"] . (isset($get["action"]) ? $get["action"] : 'index');

    $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $found_key = false;

    if (isset($_SESSION[$app][_USER_]->history)) {
        $found_key = array_search($target, array_column($_SESSION[$app][_USER_]->history, 'target'));

        if ($found_key === false) {
            if (count($_SESSION[$app][_USER_]->history) >= 8) {
                unset($_SESSION[$app][_USER_]->history[0]);
            }
        } else {
            unset($_SESSION[$app][_USER_]->history[$found_key]);
        }
    }

    $_SESSION[$app][_USER_]->history[] = array('target' => $target, 'lien' => $actual_link, 'titre' => $titre, 'picto' => $picto);
    $_SESSION[$app][_USER_]->history = array_values($_SESSION[$app][_USER_]->history);
}

function forcerTelechargement($nom, $situation, $poids)
{
    header('Content-Type: application/octet-stream');
    header('Content-Length: ' . $poids);
    header('Content-disposition: attachment; filename=' . $nom);
    header('Pragma: no-cache');
    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Expires: 0');
    readfile($situation);
    exit();
}

function cookies($name, $data, $uri)
{
    global $HTTP_HOST;
    $cookiedomain = ($HTTP_HOST != 'localhost') ? $HTTP_HOST : false;
    setcookie($name, $data, strtotime('+1 days'), $uri, $cookiedomain, true);
}

function getCookie($cookie)
{
    return filter_input(INPUT_COOKIE, $cookie);
}

function numbersfromstring($string)
{
    $numbers = [];
    $number = '';
    $tab = str_split($string);
    $max = strlen($string) - 1;

    foreach ($tab as $k => $digit) {

        if ($digit == '0' || (int) $digit > 0) {
            $number .= $digit;
            if ($k != $max)
                continue;
        }

        if ((int) $number > 0) {
            $numbers[] = (int) $number;
            $number = '';
        }
    }
    return $numbers;
}

//Declare the custom function for formatting
function pretty_print($json_data)
{

    //Initialize variable for adding space
    $space = 0;
    $flag = false;

    //Using <pre> tag to format alignment and font
    $content = "<pre>";

    //loop for iterating the full json data
    for ($counter = 0; $counter < strlen($json_data); $counter++) {

        //Checking ending second and third brackets
        if ($json_data[$counter] == '}' || $json_data[$counter] == ']') {
            $space--;
            $content .= "\n";
            $content .= str_repeat(' ', ($space * 2));
        }


        //Checking for double quote(“) and comma (,)
        if ($json_data[$counter] == '"' && ($json_data[$counter - 1] == ',' ||
            $json_data[$counter - 2] == ',')) {
            $content .= "\n";
            $content .= str_repeat(' ', ($space * 2));
        }
        if ($json_data[$counter] == '"' && !$flag) {
            if ($json_data[$counter - 1] == ':' || $json_data[$counter - 2] == ':')

                //Add formatting for question and answer
                $content .= '<span style="color:blue;font-weight:bold">';
            else

                //Add formatting for answer options
                $content .= '<span style="color:red;">';
        }
        $content .= $json_data[$counter];
        //Checking conditions for adding closing span tag
        if ($json_data[$counter] == '"' && $flag)
            $content .= '</span>';
        if ($json_data[$counter] == '"')
            $flag = !$flag;

        //Checking starting second and third brackets
        if ($json_data[$counter] == '{' || $json_data[$counter] == '[') {
            $space++;
            $content .= "\n";
            $content .= str_repeat(' ', ($space * 2));
        }
    }
    $content .= "</pre>";

    return $content;
}

function files_size_are_equal($a, $b)
{
    # Check if filesize is different
    if (filesize($a) !== filesize($b)) {
        return false;
    }
    return true;
}

function file_content_equal($a, $b)
{
    # Check if content is different
    $ah = fopen($a, 'rb');
    $bh = fopen($b, 'rb');

    $result = true;
    while (!feof($ah)) {
        if (fread($ah, 8192) != fread($bh, 8192)) {
            $result = false;
            break;
        }
    }

    fclose($ah);
    fclose($bh);

    return $result;
}

function compare($fileone, $secondfile)
{
    if (files_size_are_equal($fileone, $secondfile)) {
        if (!file_content_equal($fileone, $secondfile)) {
            return true;
        }
    } else {
        return true;
    }
    return false;
}

function comparemore($dirone, $seconddir, $log)
{

    foreach (new DirectoryIterator($dirone) as $fileInfo) {
        if (!$fileInfo->isDot() && strripos($fileInfo->getPathname(), '.DS_Store') === false) {

            if (is_file($fileInfo->getPathname()) === true) {
                $fileone = $fileInfo->getPathname();
                $secondfile = str_replace($dirone, $seconddir, $fileInfo->getPathname());

                if (!file_exists($secondfile)) {
                    continue;
                }
                if (compare($fileone, $secondfile)) {
                    $log .= "<code>$fileone</code> et <code>$secondfile</code> ont des <span class='text-danger font-weight-bold'>contenus différents</span>...<br /><br /><br />";
                } else {
                    $log .= "<code>$fileone</code> et <code>$secondfile</code> ont des <span class='text-primary font-weight-bold'>contenus identiques</span>...<br /><br /><br />";
                }
            }

            if ($fileInfo->isDir()) {
                $seconddir .= str_replace($dirone, '', $fileInfo->getPathname());
                comparemore($fileInfo->getPathname(), $seconddir, $log);
            }
        }
    }

    return $log;
}

function notification()
{
    global $app, $oSmarty;

    if (isset($_SESSION[$app]["notification"]["erreur"])) {
        $oSmarty->assign("erreur", $_SESSION[$app]["notification"]["erreur"]);
        unset($_SESSION[$app]["notification"]);
    }

    if (isset($_SESSION[$app]["notification"]["succes"])) {
        $oSmarty->assign("succes", $_SESSION[$app]["notification"]["succes"]);
        unset($_SESSION[$app]["notification"]);
    }
}

function pdf($id = null, $who = "id_affaire", $code = "")
{
    global $model;

    $model->release();
    $model->setTable(BASE_REF . 'crm_np_affaire_plan_financement_champs ');
    $model->setChamp("`id`,`groupe`,`code_champ`,`libelle_champ`,`option`,`tva_defaut`");
    if ((int) $id > 0) {
        $model->setClause("code_champ IN (SELECT code_champ FROM " . BASE_REF . "crm_np_affaire_plan_financement_champs_valeur WHERE $who = '$id' AND deleted=-1");
    }
    $champs = $model->getData(false, false, "code_champ");
    $assigned["avp_champs"] = $champs;


    foreach ($champs as $kc => $vc) {

        $model->release();
        $model->setTable(BASE_REF . 'crm_np_affaire_plan_financement_champs_valeur');
        $model->setChamp("`montant_ht`, `type`,`tva`,`montant_ttc`");

        $champs[$kc]->valeurchamps = ["montant_ht" => null, "type" => null, "tva" => null, "montant_ttc" => null];
        if ((int) $id > 0) {
            $model->setClause("`code_champ` = '$vc->code_champ' AND $who = '$id'");
            $valeurchamp = $model->getData();
            if (count($valeurchamp)) {
                $champs[$kc]->valeurchamps = $valeurchamp[0];
            }
        } elseif (!empty($code)) {
            $model->setClause("`code_champ` = '$vc->code_champ' AND `code` = '$code'");
            $valeurchamp = $model->getData();
            if (count($valeurchamp)) {
                $champs[$kc]->valeurchamps = $valeurchamp[0];
            }
        } else {
            $champs[$kc]->valeurchamps = ["montant_ht" => null, "type" => null, "tva" => null, "montant_ttc" => null];
        }
    }

    return $champs;
}

function getvendeurs($clause = "actif = 1")
{
    global $app;
    global $model, $acces_mandats_affaires_autres_agences;

    $model->release();
    $model->setChamp("vendeur");
    $model->setTable(BASE_REF . "crm_np_affaire");
    $model->setClause($clause . ($acces_mandats_affaires_autres_agences ? "" : " AND id_agence = '" . $_SESSION[$app][_USER_]->id_agence . "'"));
    $vendeurs = $model->getData(false, false);

    $return = "";
    if (isset($vendeurs[0])) {
        foreach ($vendeurs as $v) {
            if (empty($v->vendeur)) {
                continue;
            }
            $return .= $v->vendeur;
        }
    }

    return !empty($return) ? str_replace("##", ",", substr($return, 1, -1)) : $return;
}

function age($firstdate, $seconddate = "now", $format = '%y')
{
    $datetime1 = date_create($seconddate);
    $datetime2 = date_create($firstdate);

    $interval = date_diff($datetime1, $datetime2);

    return $interval->format($format);
}

function ipchecker($agenceip, $userip)
{
    foreach ($agenceip as $v) {
        if (strpos($v, "-")) {
            $plage = explode("-", preg_replace("#[^0-9\-]#Ui", "", $v));
            $userip = preg_replace("#[^0-9\-]#Ui", "", $userip);
            if ($userip >= $plage[0] && $userip <= $plage[0]) {
                return true;
            }
        } else {
            if ("#$userip#" == $v || $userip == $v) {
                return true;
            }
        }
    }

    return false;
}


function maintenance($date)
{
    global $model, $app;
    if (strtotime($date) <= time()) {
        $_SESSION[$app]['notification']['succes'] = "Le logiciel est en mode maintenance. Vous serez notifier de sa remise en marche. Nous nous excusons du désagrément causé.";
        $model->disconnect(HTTP_PATH);
    }
}

function serializepost($json)
{
    $donnees = [];
    foreach ($json as $value) {
        if (strpos($value['name'], '[]')) {
            $val = str_replace('[]', '', $value['name']);
            $val = explode('#', $val);
            if (isset($val[1])) {
                $donnees[$val[0]][$val[1]][] = $value['value'];
            } else {
                $donnees[$val[0]][] = $value['value'];
            }
        } else {
            $donnees[$value['name']] = $value['value'];
        }
    }
    return $donnees;
}

function enlettre($price)
{
    $f = new NumberFormatter("fr", NumberFormatter::SPELLOUT);
    return str_replace("-", " ", $f->format($price));
}

function code($table, $field, $v)
{
    global $model;

    $v = (object) $v;

    $x = strlen($v->prenom) > 0 ? 1 : 0;
    $y = 3;
    $z = "";
    $free = false;

    $prenom = gen_title_filter(htmlentities($v->prenom), true, false, "upper");
    $nom = gen_title_filter(htmlentities($v->nom), true, false, "upper");

    do {
        $code = substr($prenom, 0, $x) . substr($nom, 0, $y) . strtoupper(randomANUM(3)) . $z;

        if ($x == strlen($prenom)) {
            if ($y == strlen($nom)) {
                $z = (int) $z + 1;
            } else {
                $y++;
            }
        } else {
            $x++;
        }

        $model->release();
        $model->setTable($table);
        $model->setClause("$field = '$code'");
        if ($model->getCount("id") === 0) {
            $free = true;
        }
    } while ($free == false);

    return $code;
}
