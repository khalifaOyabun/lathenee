<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../core/_ini.php';

$model = new Model($dao, ''); /* Mise en marche des requêtes basiques. */
$model->makeconstfile($route, 'CRM_NP_'); /* Exemples. */

$model->release();
$model->setClause('actif=1');
$model->setChamp("nom_bd");
$model->settable('crm_np_agences');
$bd = "crm_netprofil_".$model->getData()[0]->nom_bd;
$model->makeconstfile($route, 'CRM_NP_', $bd); /* Exemples. */

