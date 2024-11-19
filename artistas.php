<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
include_once('app/vars.php');
include_once('app/functions.php');

$mode = 0;
$idArtistas = getListFromDB('artistas');
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

            $idArtistas = getListFromDB('artistas', $id);

            break;
        case 'valuesEditar':
            $values = [
                'nombre' => $_POST['nombre'],
                'apellidos' => $_POST['apellidos'],
                'fechaNacimiento' => $_POST['fechaNacimiento']
            ];
            edit('artistas', $_POST['idArtista'], $values);
            $idArtistas = getListFromDB('artistas');
            break;
    }
}

$bodyOutput = getMarkup($idArtistas, $mode);

include_once("./templates/templateArtistas.tpl.php");
