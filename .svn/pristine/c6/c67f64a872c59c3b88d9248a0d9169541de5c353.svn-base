<?php

/**
 * Description of Queries
 *
 * @author Mohamed DIOUF
 * 
 */


require 'Param.class.php';

class Model extends Param
{

    /* Used to know in what format datas will be returned. */
    public $format = 'fetched';

    private $dao;

    private $datas;

    private $table, $champ, $clause = null, $having, $orderBy, $groupBy, $limit;

    function __construct($dao, $table)
    {
        $this->setDao($dao);
        $this->setTable($table);

        $this->app();
    }

    /*
     *  Getters et setters
     */

    public function setDao($dao)
    {

        $this->dao = $dao;
    }

    public function setTable($table)
    {

        $this->table = is_array($table) ? implode(", ", $table) : $table;
    }

    public function setChamp($champ = "*")
    {

        $this->champ = is_array($champ) ? implode(", ", $champ) : $champ;
    }

    public function setClause($clause)
    {
        $this->clause = $clause;
    }

    public function setHaving($having)
    {
        $this->having = $having;
    }

    public function setOrderBy($orderBy)
    {

        $this->orderBy = $orderBy;
    }

    public function setGroupBy($groupBy)
    {

        $this->groupBy = $groupBy;
    }

    public function setLimit($limit)
    {

        $this->limit = $limit;
    }

    public function getTable()
    {

        if (empty($this->table) || is_numeric($this->table) || is_null($this->table)) {
            throw new InvalidArgumentException('Model.php - Erreur BD :: L\'attribut table doit être obligatoirement un tableau ou une chaine de caractères.');
        }

        return preg_replace("|\.+|", ".", $this->table);
    }

    public function getChamp()
    {

        if (empty($this->champ) || is_numeric($this->champ) || is_null($this->champ)) {
            throw new InvalidArgumentException('Model.php - Erreur BD :: L\'attribut champ doit être obligatoirement un tableau ou une chaine de caractères.');
        }

        return $this->champ;
    }

    public function getClause($____delopt = true)
    {
        $clause = empty($this->clause) ? "1=1" : $this->clause;

        if ($____delopt == false) {
            return $clause;
        }

        $a = $this->getAliasSigne();

        if (count($a) > 0) {
            foreach ($a as $s) {
                $clause .= " AND " . $s . ".deleted=-1";
            }
        } else {
            $clause .= ' AND deleted=-1';
        }

        return $clause;
    }

    public function getHaving()
    {

        return $this->having;
    }

    public function getOrderBy()
    {

        return $this->orderBy;
    }

    public function getGroupBy()
    {

        return $this->groupBy;
    }

    public function getLimit()
    {

        return $this->limit;
    }

    public function pdostatus()
    {
        return $this->dao->getAttribute(PDO::ATTR_ERRMODE);
    }

    /*
     *  QUERIES EXECUTE
     */

    private function execute($query, $track, $donnees = [])
    {
        $execution = $this->dao->prepare($query);
        $execution->execute($donnees);

        #tracking
        $this->log($query, $execution, $track);

        //A débattre sur ce point avec Mouhamed Diouf
        //$this->release();

        return $execution;
    }

    private function fetch($execution, $with = null)
    {
        $i = 0;
        $this->datas = [];
        if ($execution->rowCount() > 0) {
            while ($lignes = $execution->fetch(PDO::FETCH_OBJ)) {
                $key = ($with === null || !is_string($with)) ? $i : $lignes->$with;
                ($this->format === 'fetchedcompact') ?
                    $this->datas[$key][] = $lignes :
                    $this->datas[$key] = $lignes;
                $i++;
            }
        }
    }

    public function release()
    {
        foreach (['table', 'champ', 'clause', 'orderBy', 'groupBy', 'limit'] as $attr) {
            $method = 'set' . ucfirst($attr);
            if (method_exists($this, $method)) {
                $this->$method(null);
            }
        }
        $this->format = 'fetched';
    }

    /*
     *  QUERIES GENERALES
     */

    public function getMax($track = false)
    {

        $requete = "SELECT MAX(" . $this->getChamp() . ") as maximum FROM  " . $this->getTable() . " WHERE 1=1 ";

        $requete .= (!empty($this->getClause)) ? 'AND ' . $this->getClause : '';

        $execution_requete = $this->dao->prepare($requete);
        $execution_requete->execute();

        #tracking
        $this->log($requete, $execution_requete, $track);

        $max = 0;

        if ($execution_requete->rowCount()) {

            $max = $execution_requete->fetchColumn(0);
        }

        return $max;
    }

    public function getCount($champ = "*", $track = false)
    {

        try {
            $requete = "SELECT count(" . $champ . ") as nombre_d_enregistrements FROM " . $this->getTable();
        } catch (Exception $ex) {
            die($ex->getMessage());
        }

        $requete .= ($this->getClause() != "") ? " WHERE " . $this->getClause() : "";
        $requete .= ($this->getOrderBy() != "") ? " ORDER BY " . $this->getOrderBy() : "";
        $requete .= ($this->getLimit() != "") ? " LIMIT " . $this->getLimit() : "";

        $lignes = $this->execute($requete, $track)->fetch(PDO::FETCH_OBJ);

        return (isset($lignes->nombre_d_enregistrements)) ? (int) $lignes->nombre_d_enregistrements : 0;
    }

    /* Get all datas from table (only get specific fields) */

    public function getData($release = false, $track = false, $fetchWith = null, $getClause = true, $delopt = true)
    {

        try {
            $requete = "SELECT " . $this->getChamp() . " FROM " . $this->getTable();
        } catch (Exception $ex) {
            die($ex->getMessage());
        }

        $requete .= ($this->getClause() != "" && $getClause == true) ? " WHERE " . $this->getClause($delopt) : "";
        $requete .= ($this->getHaving() != "") ? " HAVING " . $this->getHaving() : "";
        $requete .= ($this->getGroupBy() != "") ? " GROUP BY " . $this->getGroupBy() : "";
        $requete .= ($this->getOrderBy() != "") ? " ORDER BY " . $this->getOrderBy() : "";
        $requete .= ($this->getLimit() != "") ? " LIMIT " . $this->getLimit() : "";

        $execution = $this->execute($requete, $track);

        $this->fetch($execution, $fetchWith);

        ($release) ? $this->release() : null;

        return is_null($this->datas) ? [] : $this->datas;
    }

    public function getRow($rownumber = 0, $release = false, $track = false, $fetchWith = null, $getClause = true, $delopt = true)
    {
        $rows = $this->getData($release, $track, $fetchWith, $getClause, $delopt);
        return (is_array($rows) && count($rows) > 0 && isset($rows[$rownumber])) ? $rows[$rownumber] : null;
    }

    public function recordExists($track = false)
    {

        return $this->getCount($track);
    }

    public function remove($track = false)
    {
        try {
            $requete = "DELETE FROM  " . $this->getTable();
        } catch (Exception $ex) {
            die($ex->getMessage());
        }

        $requete .= $this->getClause() != "" ? " WHERE " . $this->getClause() : "";

        $execution = $this->execute($requete, $track);

        return $execution->rowCount();
    }

    public function deleteData($track = false)
    {
        try {
            $requete = "UPDATE " . $this->getTable() . " SET deleted=1, date_suppression=NOW()";
        } catch (Exception $ex) {
            die($ex->getMessage());
        }

        $requete .= $this->getClause() != "" ? " WHERE " . $this->getClause() : "";

        $execution = $this->execute($requete, $track);

        return $execution->rowCount();
    }

    public function freeQuery($requete, $track = false, $ignoreClause = false, $backdatas = false)
    {
        try {
            if ($ignoreClause === false) {
                $requete .= $this->getClause() != "" ? " WHERE " . $this->getClause() : "";
            }
        } catch (Exception $ex) {
            die($ex->getMessage());
        }

        $requete .= ($this->getLimit() != "") ? " LIMIT " . $this->getLimit() : "";

        $execution = $this->execute($requete, $track);

        if ($backdatas === true) {
            $this->fetch($execution, null);
        }

        return ($backdatas === true) ? $this->datas : $execution->rowCount();
    }

    public function query($requete, $track = false)
    {
        $execution = $this->execute($requete, $track);

        return $execution->rowCount();
    }

    public function lastInsertId($track = false)
    {
        try {
            $requete = 'SELECT MAX(id) as maxId FROM ' . $this->getTable();
        } catch (Exception $ex) {
            die($ex->getMessage());
        }

        $requete .= ($this->getClause() != "") ? " WHERE " . $this->getClause() : "";
        $requete .= ($this->getOrderBy() != "") ? " ORDER BY " . $this->getOrderBy() : "";
        $requete .= ($this->getLimit() != "") ? " LIMIT " . $this->getLimit() : "";

        $lignes = $this->execute($requete, $track)->fetch(PDO::FETCH_OBJ);

        return (isset($lignes->maxId)) ? (int) $lignes->maxId : 0;
    }

    /*
     *  INSERT QUERY & UPDATE QUERY & SAVE
     */

    public function insertOne($donnees = [], $index = "id", $track = false)
    {
        try {
            $this->setNull($donnees);
        } catch (Exception $ex) {
            return -1;
        }

        try {
            $table = $this->getTable();
        } catch (Exception $ex) {
            die($ex->getMessage());
        }

        $requete = "INSERT INTO " . $table . "(" . implode(',', array_keys($donnees)) . ") VALUES(:" . implode(', :', array_keys($donnees)) . ")";

        $insert = $this->execute($requete, $track, $donnees);

        return ($insert->errorInfo()[0] === "00000") ? (($index == "id") ? $this->dao->lastInsertId() : $donnees[$index]) : false;
    }

    public function updateOne($donnees = [], $index = "id", $valeurIndex = null, $track = false)
    {

        try {
            $this->setNull($donnees);
        } catch (Exception $ex) {
            return -1;
        }

        $set = "";
        foreach ($donnees as $champ => $valeur) {
            //$set .= ((in_array($champ, ['password', 'mot_de_passe']) && empty($valeur)) || ($champ == $index)) ? "" : ("" . $champ . " = :" . $champ . ", ");
            $set .= ((in_array($champ, ['password', 'mot_de_passe']) && empty($valeur))) ? "" : ("" . $champ . " = :" . $champ . ", ");
        }

        $requete = "UPDATE " . $this->table . " SET " . substr($set, 0, -2);

        $requete .= ($this->getClause() != '1=1 AND deleted=-1') ? " WHERE " . $this->getClause() : (" WHERE $index = " . (!empty($valeurIndex) ? "'" . $valeurIndex . "'" : ":$index"));

        $update = $this->execute($requete, $track, $donnees);

        return (isset($donnees[$index]) && $update->errorInfo()[0] === "00000") ? $donnees[$index] : false;
    }

    public function insertOrUpdate($donnees = [], $index = "id", $track = false)
    {
        try {
            $this->setNull($donnees);
        } catch (Exception $ex) {
            return -1;
        }

        try {
            $table = $this->getTable();
        } catch (Exception $ex) {
            die($ex->getMessage());
        }

        $set = "";
        foreach ($donnees as $champ => $valeur) {
            $set .= ((in_array($champ, ['password', 'mot_de_passe']) && empty($valeur)) || ($champ == $index)) ? "" : ("" . $champ . " = :" . $champ . ", ");
        }

        $requete = "INSERT INTO " . $table . "(" . implode(',', array_keys($donnees)) . ") VALUES(:" . implode(', :', array_keys($donnees)) . ")";
        $requete .= " ON DUPLICATE KEY UPDATE " . substr($set, 0, -2);

        $this->execute($requete, $track, $donnees);

        return ($index == "id") ? $this->dao->lastInsertId() : $donnees[$index];
    }

    public function record($donnees, $obligatoires = [], $track = false)
    {

        $serialise = $this->filter($donnees, $obligatoires);

        if ($serialise->etat) {
            $id_record = (isset($serialise->donnees['id']) && (int) $serialise->donnees['id'] > 0) ? $this->updateOne($serialise->donnees, "id", null, $track) : $this->insertOne($serialise->donnees, "id", $track);

            if ($id_record > 0) {
                return $id_record;
            }
            return -2;
        }

        return -3; //$serialise->donnees; // On renvoie les champs obligatoires qui n'ont pas été définis.
    }

    public function recordInsertOrUpdate($donnees, $obligatoires = [], $track = false)
    {

        $serialise = $this->filter($donnees, $obligatoires);

        if ($serialise->etat) {
            $id_record = $this->insertOrUpdate($serialise->donnees, $track);
            if ($id_record > 0) {
                return $id_record;
            }
        }

        return -3; //$serialise->donnees; // On renvoie les champs obligatoires qui n'ont pas été définis.
    }

    public function recordExistsFree($tables, $champ = "", $clause = "1 = 1", $track = false)
    {

        $requete = "SELECT " . $champ . " FROM  " . $tables;
        $requete .= !empty($clause) ? " WHERE " . $clause : "";

        $execution_requete = $this->dao->prepare($requete);
        $execution_requete->execute();

        #tracking
        $this->log($requete, $execution_requete, $track);

        $resultat = $execution_requete->fetchColumn(0);

        return (isset($resultat) && $resultat !== false) ? $resultat : null;
    }

    /*
     * 
     * Construction du fichier contant qui va contenir
     * l'ensemble des tables ainsi que leurs champs pour faciliter leur accès 
     * depuis le code PHP
     * 
     */

    public function getElementType($typesCommuns = [], $typesByAgences = [], $pour = '', $nomBD = '')
    {

        $chaine = implode('", "', $typesCommuns);

        $this->setChamp("*");
        $this->setTable("crm_np_liste_element_form");
        $this->setClause("type IN (\"$chaine\") AND actif = 1");
        $this->setOrderBy("rang");

        $donnees = $this->getData();

        if (count($typesByAgences) > 0) {
            $chaine = implode('", "', $typesByAgences);
            $nomBD = !empty($nomBD) ? $nomBD : str_replace('.', '', BASE_REF);

            $this->setTable($nomBD . ".crm_np_liste_element_form");
            $this->setClause("type IN (\"$chaine\") AND actif = 1");
            $this->setOrderBy("rang");
            $donnees = array_merge($donnees, $this->getData());
        }

        $retour = [];

        foreach ($donnees as $valeur) {

            if ($pour == '' || $pour == 'liste') {
                $retour[$valeur->type][$valeur->code] = ['code' => $valeur->code, 'libelle' => $valeur->libelle, 'description' => $valeur->description, 'pictogramme' => $valeur->pictogramme];
            }

            if ($pour == 'form') {
                $retour[$valeur->type][] = (object) ['code' => $valeur->code, 'libelle' => $valeur->libelle, 'description' => $valeur->description, 'pictogramme' => $valeur->pictogramme];
            }
        }

        return $retour;
    }

    public function getDeptByCp($cp)
    {

        $this->setChamp("*");
        $this->setTable("crm_np_departement");
        $this->setClause("num IN (SELECT num_departement from crm_np_ville WHERE code_postal=" . $cp . " AND deleted = -1)");
        $this->setOrderBy("id");

        $donnees = $this->getData();

        return $donnees[0];
    }

    # Pour vider une ou plusieurs tables définies dans un tableau
    public function _truncate($tables = [])
    {
        if (is_array($tables) && count($tables)) {
            $this->release();
            foreach ($tables as $table) {
                $execution_requete = $this->dao->prepare("TRUNCATE TABLE $table");
                $execution_requete->execute();
            }
        }
    }
    /*
     * 
     * Construction du fichier contant qui va contenir
     * l'ensemble des tables ainsi que leurs champs pour faciliter leur accès 
     * depuis le code PHP
     * 
     */

    private function getDatabaseContent($db = S_DB)
    {

        $requete = "SHOW TABLES FROM " . $db;

        $execution_requete = $this->dao->prepare($requete);
        $execution_requete->execute();

        $tables = $champ = [];

        while ($lignes = $execution_requete->fetch(PDO::FETCH_BOTH)) {

            $requete_secondaire = "SHOW COLUMNS FROM " . $db . "." . $lignes[0];

            $execution_requete_secondaire = $this->dao->prepare($requete_secondaire);
            $execution_requete_secondaire->execute();

            while ($lignes_secondaire = $execution_requete_secondaire->fetch(PDO::FETCH_BOTH)) {

                $index = $lignes[0] . "#" . $lignes_secondaire[0];
                $champ[$index] = $lignes_secondaire[0];
            }

            $tables[$lignes[0]] = $lignes[0];
        }

        return ['tables' => $tables, 'champ' => $champ];
    }

    public function DropDataBases($bd)
    {

        $requete = "DROP DATABASE " . $bd;

        $execution_requete = $this->dao->prepare($requete);
        $execution_requete->execute();

        return $execution_requete;
    }

    public function makeconstfile($route, $prefixe, $db = S_DB)
    {

        $infos = $this->getDatabaseContent($db);

        $prebase = ($db != S_DB) ? "AGENCE_" : "";

        $fichier = fopen($route . '/core/_const_bd.php', 'a+');

        if (S_DB == $db) {
            ftruncate($fichier, 0);
        }

        $contenu = ($db == S_DB) ? "<?php \n\n/* Le fichier contient l'ensemble des tables de la base de données connectée ainsi que leurs champs.*/ \n\n" : "";

        foreach ($infos['tables'] as $cle => $valeur) {

            $contenu .= "// Table " . strtoupper($valeur) . " et ses champs. \n";

            $contenu .= "const " . $prebase . strtoupper($cle) . " = '" . ($valeur) . "', ";

            foreach ($infos['champ'] as $cle1 => $valeur1) {

                $concordance = explode("#", $cle1);

                ($concordance[0] === $cle) ? $contenu .= $prebase . strtoupper(substr($cle, strlen($prefixe), strlen($cle))) . "_" . strtoupper($valeur1) . " = '" . ($valeur1) . "', " : '';
            }

            $contenu .= ";\n\n";
        }


        fputs($fichier, str_replace(', ;', ';', $contenu));

        fclose($fichier);

        echo 'Fichier de constantes généré avec succès !';
    }

    public function makeconstfileBis($route, $prefixe, $db = S_DB)
    {

        $infos = $this->getDatabaseContent($db);

        $prebase = "AGENCE_";

        $contenu = "<?php \n\n/* Le fichier contient l'ensemble des tables de la base de données connectée ainsi que leurs champs.*/ \n\n";

        foreach ($infos['tables'] as $cle => $valeur) {

            $contenu .= "// Table " . strtoupper($valeur) . " et ses champs. \n";

            $contenu .= "const " . $prebase . strtoupper($cle) . " = '" . ($valeur) . "', ";

            foreach ($infos['champ'] as $cle1 => $valeur1) {

                $concordance = explode("#", $cle1);

                ($concordance[0] === $cle) ? $contenu .= $prebase . strtoupper(substr($cle, strlen($prefixe), strlen($cle))) . "_" . strtoupper($valeur1) . " = '" . ($valeur1) . "', " : '';
            }

            $contenu .= ";\n\n";
        }


        return $contenu;
    }

    public function getAliasSigne()
    {
        $tab = explode(',', $this->table);
        $a = [];
        if (count($tab) > 1) {
            foreach ($tab as $t) {
                $as = explode(' ', trim($t));
                if (isset($as[1]))
                    $a[count($a)] = $as[1];
            }
        }

        return (preg_match('#(LEFT|RIGHT)(.*)?JOIN#U', $this->table)) ? array_merge($a, $this->getAliasUsingJoin()) : $a;
    }

    public function getAliasUsingJoin()
    {
        $a = [];

        $string = substr($this->table, stripos($this->table, " ON ") + 4);
        $tab = explode("=", $string);
        if (count($tab) > 1) {
            foreach ($tab as $t) {
                $as = explode('.', trim($t));
                $a[count($a)] = $as[0];
            }
        }

        return $a;
    }
}
