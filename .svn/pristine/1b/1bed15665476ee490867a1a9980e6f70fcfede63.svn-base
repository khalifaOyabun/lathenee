<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require 'core/_ini.php';

$model = new Model($dao, ''); /* Mise en marche des requêtes basiques. */
$get = filter_input_array(INPUT_GET);

$model->setTable(CRM_CANDIDAT_RECHERCHE);

if (in_array('generationConstBD', $get)) {
    $model->makeconstfile($route, 'CRM_'); /* Exemples. */
    exit();
}

if (in_array('updateRechercheReference', $get)) {
    updateRechercheReference($model);
    exit();
}

if (in_array('exportCandidatsCSV', $get)) {
    exportCandidatsCSV($model);
    exit();
}

