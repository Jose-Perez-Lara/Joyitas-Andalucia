<?php

include_once('app/vars.php');
include_once('app/functions.php');

$mode = 0;
$idConciertos = getListFromDB('conciertos');
if (!empty($_POST['operacion'])) {

    $postSplitted = preg_split("/_/", $_POST['operacion']);
    $operacion = $postSplitted[0];
    $id = $postSplitted[1];
    switch ($operacion) {
        case 'eliminar':

            delete($id);

            break;

        case 'editar':
            $mode = 1;

            $idConciertos = getListFromDB('conciertos', $id);

            break;
        case 'valuesEditar':
            $values = [
                'establecimiento' => $_POST['establecimiento'],
                'provincia' => $_POST['provincia'],
            ];
            edit('conciertos', $_POST['idConcierto'], $values);
            $idConciertos = getListFromDB('conciertos');
            break;
    }
}

$bodyOutput = getMarkup($idConciertos, $mode);

include_once("./templates/templateConciertos.tpl.php");
