<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Connect
 *
 * @author Mohamed DIOUF
 */

/* * **** MYSQL CONFIG */
const HOST = "localhost";
const USER = "root";
const PASS = "";
const S_DB = "e_carnet";
const PREFIXE_BD = "";

function _connect($host, $user, $password, $db)
{
    try {

        $connexionBD = new PDO('mysql:host=' . $host . ';dbname=' . PREFIXE_BD . $db, $user, $password);
        $connexionBD->exec('SET NAMES utf8');
        $connexionBD->exec("SET GLOBAL sql_mode=''");

        return $connexionBD;
    } catch (Exception $e) {

        echo 'Une erreur est survenue lors de la connexion à la base de donnée !' . $e->getMessage();
        die();
    }
}
