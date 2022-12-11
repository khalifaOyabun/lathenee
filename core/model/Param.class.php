<?php

class Param extends Exception
{

    public $capp;

    public function app()
    {
        global $app;
        $this->capp = $app;
    }

    public function filter($donnees, $required)
    {

        $sontvides = $this->required($donnees, $required);

        return ($sontvides === false) ? (object) ['etat' => true, 'donnees' => $donnees] : (object) ['etat' => false, 'donnees' => $sontvides];
    }

    public function required($donnees, $required)
    {

        foreach ($required as $champ) {

            if (isset($donnees[$champ]) && is_null($donnees[$champ]) || empty($donnees[$champ])) {

                return (object) ['champ' => $champ];
            }
        }

        return false;
    }

    public function setNull($array)
    {

        if (!is_array($array) || count($array) == 0) {
            throw new Exception('Cette méthode doit recevoir un tableau non vide.');
        }

        foreach ($array as $key => $value) {
            $array[$key] = ($value == 0) ? null : $value;
        }
    }

    public function isEmpty($var)
    {

        if (is_array($var)) {

            return (count($var) > 0) ? false : true;
        }

        return empty($var);
    }

    public function fillArrays($donnees, $cle, $valeur = null, $embric = false)
    {

        if (is_array($donnees) || is_object($donnees)) {

            $tableau = [];

            foreach ($donnees as $donnee) {
                $donnee = (array) $donnee;
                $embric ?
                    $tableau[$donnee[$cle]][] = (!empty($valeur)) ? $donnee[$valeur] : $donnee :
                    $tableau[$donnee[$cle]] = (!empty($valeur)) ? $donnee[$valeur] : $donnee;
            }

            return $tableau;
        }

        return false;
    }

    public function online()
    {

        if ($this->capp != null) {
            return (isset($_SESSION[$this->capp][_USER_]->id) && ($_SESSION[$this->capp][_USER_]->id > 0)) ? true : false;
        }
        return (isset($_SESSION[$this->capp][_USER_]->id) && ($_SESSION[$this->capp][_USER_]->id > 0)) ? true : false;
    }

    public function authenticated($baseuri = "/connexion")
    {
        if ($this->online() === false) {
            $this->redirect(HTTP_PATH . $baseuri);
        }
        return true;
    }

    public function authenticable($baseuri = "")
    {
        if ($this->online() && isset($_SESSION[$this->capp][_USER_]->premiere_connexion) && ($_SESSION[$this->capp][_USER_]->premiere_connexion == -1)) {
            $this->redirect(HTTP_PATH . $baseuri);
        }
        if ($this->online() && isset($_SESSION[$this->capp][_USER_]->premiere_connexion) && ($_SESSION[$this->capp][_USER_]->premiere_connexion == 1)) {
            $this->redirect(HTTP_PATH . "/connexion/changement-mot-de-passe");
        }
    }

    public function firstauth()
    {
        if ($this->online() === false) {
            $this->redirect(HTTP_PATH . "/connexion");
        }
        if ($this->online() && isset($_SESSION[$this->capp][_USER_]->premiere_connexion) && ($_SESSION[$this->capp][_USER_]->premiere_connexion == -1)) {
            $this->redirect(HTTP_PATH);
        }
    }

    public function connected($who)
    {
        return ($this->online() && $_SESSION[$this->capp][_USER_]->code_type_utilisateur === $who) ? true : false;
    }

    public function super()
    {
        if ($this->connected('SUPERADMINISTRATEUR') === false) {
            $this->redirect(HTTP_PATH);
        }
    }

    public function manager()
    {
        if ($this->connected('MANAGER') === false) {
            $this->redirect(HTTP_PATH);
        }
    }

    public function supermanager()
    {
        if ($this->connected('MANAGER') === false && $this->connected('SUPERADMINISTRATEUR') === false) {
            $this->redirect(HTTP_PATH);
        }
    }

    public function _partial_disconnect($path = HTTP_PATH)
    {
        unset($_SESSION[$this->capp]);
        $this->redirect($path);
    }

    public function disconnect($path = HTTP_PATH)
    {
        session_destroy();
        $this->redirect($path);
    }

    public function redirect($path, $anchor = '')
    {
        header("location: " . $path . $anchor);
        exit();
    }

    public function trackback($referer, $action, $sess = 'trackingback')
    {

        if (isset($referer) && !stristr($referer, $action)) {
            $_SESSION[$sess] = $referer;
        }
    }

    public function backto($path, $sess = 'trackingback', $redirectAuto = true)
    {

        $rPath = isset($_SESSION[$sess]) ? $_SESSION[$sess] : $path;
        unset($_SESSION[$sess]);

        if ($redirectAuto) {
            $this->redirect($rPath);
        }

        return $rPath;
    }


    # Systeme de tracking
    public function log($requete, $execution, $track = false)
    {
        global $route, $module, $action, $server;

        if ((_ENV === "DEV" || (isset($_SESSION[$this->capp][_USER_]->code_type_utilisateur) && $_SESSION[$this->capp][_USER_]->code_type_utilisateur == "SUPERADMINISTRATEUR")) && $track) {
            var_dump($requete);
            var_dump($execution->errorInfo());
        } else {
            $log['datetimefr'] = date('d-m-Y H:i:s', microtime(true));
            if (isset($_SESSION[$this->capp][_USER_]->id)) {
                $log['username'] = $_SESSION[$this->capp][_USER_]->prenom . " " . $_SESSION[$this->capp][_USER_]->nom;
                $log['location'] = "Module : $module - Action : $action";
                $log['filename'] = isset($server['REQUEST_URI']) ? $server['REQUEST_URI'] : $server['SCRIPT_FILENAME'];
                $dir = $route . _STORAGE_PATH . "agences/" . $_SESSION[$this->capp][_USER_]->nom_dossier . "/queryfilelog.txt";
            } else {
                $log['userid'] = session_id();
                $log['username'] = session_id();
                $log['location'] = "offline";
                $log['filename'] = $server['SCRIPT_FILENAME'];
                $dir = $route . _STORAGE_PATH . "agences/queryfilelog.txt";
            }
            $log['query'] = $requete;
            $log['querytrack'] = $execution->errorInfo();
            $log['querystatus'] = ($log['querytrack'][0] === "00000") ? "SUCCESS" : "ERROR";
            $log['code'] = millitime();
            if (_tracked === "ERROR" && $log['querystatus'] === "ERROR") {
                $this->logfile($dir, $log);
            } elseif (_tracked === "SUCCESS" && $log['querystatus'] === "SUCCESS") {
                $this->logfile($dir, $log);
            } elseif (_tracked === "ALL") {
                $this->logfile($dir, $log);
            } else {
                return;
            }
        }
    }

    private function logfile($dir, $log)
    {
        $content = json_encode($log);
        $handle = fopen($dir, "a+");
        fwrite($handle, $content . "\r\n");
        fclose($handle);
    }

    public function SaisieControle($donnees, $champs)
    {

        $msg = '';
        foreach ($champs as $key => $value) {

            switch ($key) {
                case 'EMAIL':
                    foreach ($value as $cle => $valeur) {
                        if (!empty($donnees[$valeur])) {
                            if (!isMail($donnees[$valeur])) {
                                $msg = "L'adresse email saisie est incorrecte !";
                                return [true, $msg, $valeur];
                            }
                        }
                    }
                    break;
                case 'PHONE':
                    foreach ($value as $cle => $valeur) {
                        if (!empty($donnees[$valeur])) {
                            if (!preg_match('#^[+. 0-9]*$#', $donnees[$valeur])) {
                                $msg = "Le numero de téléphone saisi est incorrect !";
                                return [true, $msg, $valeur];
                            }
                        }
                    }
                    break;
                case 'EMPTY':
                    foreach ($value as $cle => $valeur) {
                        if (empty($donnees[$valeur])) {
                            $msg = "Veuillez remplir tous les champs obligatoires !";
                            return [true, $msg, $valeur];
                        }
                    }
                    break;

                case 'POSITIF':
                    foreach ($value as $cle => $valeur) {
                        $donnees[$valeur] = (int) $donnees[$valeur];
                        if ($donnees[$valeur] <= 0) {
                            $msg = "Le nombre saisi n'est pas positif !";
                            return [true, $msg, $valeur];
                        }
                    }
                    break;

                case 'NUMERIC':
                    foreach ($value as $cle => $valeur) {
                        if (!empty($donnees[$valeur])) {
                            if (!is_numeric($donnees[$valeur])) {
                                $msg = "La donnée saisie n'est pas un nombre !";
                                return [true, $msg, $valeur];
                            }
                        }
                    }
                    break;

                case 'ARRAY':
                    foreach ($value as $cle => $valeur) {
                        if ((!isset($donnees[$valeur])) || (!is_array($donnees[$valeur])) || (count($donnees[$valeur]) == 0)) {
                            $msg = "Aucun element selectionné dans le tableau !";
                            return [true, $msg, $valeur];
                        }
                    }
                    break;

                case 'RCS':
                    foreach ($value as $cle => $valeur) {
                        if (!empty($donnees[$valeur])) {
                            if (!preg_match('#^[0-9]*$#', $donnees[$valeur]) || (strlen(trim($donnees[$valeur])) != 9)) {
                                $msg = "Merci de saisir votre numéro de RCS/SIREN à 9 chiffres sans espaces !";
                                return [true, $msg, $valeur];
                            }
                        }
                    }
                    break;

                case 'EXIST':
                    foreach ($value as $cle => $valeur) {
                        if (!isset($donnees[$valeur])) {
                            $msg = "Veuillez accepter les conditions d'utilisation des données !";
                            return [true, $msg, $valeur];
                        }
                    }
                    break;
                case 'URL':
                    foreach ($value as $cle => $valeur) {
                        if (!empty($donnees[$valeur])) {
                            if (!filter_var($donnees[$valeur], FILTER_VALIDATE_URL)) {
                                return [true, $msg, $valeur];
                            }
                            $donnees[$valeur] = filter_var($donnees[$valeur], FILTER_SANITIZE_URL);
                        }
                    }
                    break;
            }
        }
        return [false, $msg];
    }
}
