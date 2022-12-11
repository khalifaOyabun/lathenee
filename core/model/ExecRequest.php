<?php

class ExecRequest
{
    private $request;
    private $con;

    function __construct($db)
    {
        $this->setDb($db);

        try {
            $connexionBD = new PDO('mysql:host=' . HOST . ';dbname=' . $db, USER, PASS);
            $connexionBD->exec('SET NAMES utf8');
            $this->con =  $connexionBD;
        } catch (Exception $e) {

            echo 'Une erreur est survenue lors de la connexion à la base de donnée !' . $e->getMessage();
            die();
        }
    }

    public function setDb($db)
    {
        $this->db = $db;
    }

    public function setRequest($request)
    {
        $this->request = $request;
    }

    public function ExecuteMyRequest($track = false)
    {
        $execution = $this->con->prepare($this->request);
        $execution->execute();

        $back = $execution->errorInfo();

        if ($track) {
            var_dump($this->request);
            var_dump($back);
        }

        return ($back[0] === "00000") ? true : $back;
    }
}
