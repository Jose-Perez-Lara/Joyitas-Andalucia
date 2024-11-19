<?php

function getListFromDB($tabla, $id = 0)
{
    $list = array();
    global $servername, $username, $password, $dbname;

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    } catch (PDOException $e) {
        echo "Error de conexión: " . $e->getMessage();
        exit();
    }

    try {
        if ($id == 0) {
            switch ($tabla) {
                case 'artistas':
                    $stmt = $conn->prepare("SELECT * FROM $tabla");
                    break;
                case 'conciertos':
                    $stmt = $conn->prepare("SELECT c.idConcierto, c.establecimiento, c.provincia, 
                            GROUP_CONCAT(a.nombre, ' ', a.apellidos ORDER BY a.nombre) AS artistas
                        FROM conciertos c
                        JOIN artista_concierto ac ON c.idConcierto = ac.idConcierto
                        JOIN artistas a ON ac.idArtista = a.idArtista
                        GROUP BY c.idConcierto, c.establecimiento, c.provincia");
                    break;
            }
        } else {
            switch ($tabla) {
                case 'artistas':
                    $stmt = $conn->prepare("SELECT * FROM $tabla WHERE idArtista =$id");
                    break;
                case 'conciertos':
                    $stmt = $conn->prepare("SELECT c.idConcierto, c.establecimiento, c.provincia, 
                            GROUP_CONCAT(a.nombre, ' ', a.apellidos ORDER BY a.nombre) AS artistas
                        FROM conciertos c
                        JOIN artista_concierto ac ON c.idConcierto = ac.idConcierto
                        JOIN artistas a ON ac.idArtista = a.idArtista
                        GROUP BY c.idConcierto, c.establecimiento, c.provincia
                        WHERE c.idConcierto = $id");
                    break;
            }
        }

        $stmt->execute();


        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error al realizar la consulta: " . $e->getMessage();
    }

    $conn = null;

    return $list;
}


function getMarkUp($tabla, $mode)
{
    switch ($mode) {
        case 0:
            return getTableMarkUp($tabla);
            break;
        case 1:
            return getFormMarkup($tabla);
            break;
        default:
            # code...
            break;
    }
}


function getFormMarkUp($tabla)
{
    $output = '';
    if (isset($tabla[0]['idArtista'])) {
        $output .= '<h1>' . $tabla[0]['nombre'] . ' ' . $tabla[0]['apellidos'] . '</h1>';
        $output .= '<label for="nombre">Nombre del Artista:</label>';
        $output .= '<input required type="text" id="nombre" name="nombre" value="' . $tabla[0]['nombre'] . '"><br>';

        $output .= '<label for="apellidos">Apellidos del Artista:</label>';
        $output .= '<input required type="text" id="apellidos" name="apellidos" value="' . $tabla[0]['apellidos'] . '"><br>';

        $output .= '<label for="fechaNacimiento">Fecha de nacimiento:</label>';
        $output .= '<input required type="date" id="fechaNacimiento" name="fechaNacimiento" value="' . $tabla[0]['fechaNacimiento'] . '"><br>';

        $output .= '<input style="display:none;" required type="text" id="idArtista" name="idArtista" value="' . $tabla[0]['idArtista'] . '"><br>';
        $output .= '<input style="display:none;" type="text" name="operacion" value="valuesEditar">';
    } elseif (isset($tabla[0]['idConcierto'])) {
        $output .= '<h1>Concierto</h1>';
        $output .= '<label for="establecimiento">Establecimiento:</label>';
        $output .= '<input required type="text" id="establecimiento" name="establecimiento" value="' . $tabla[0]['establecimiento'] . '"><br>';

        $output .= '<label for="provincia">Provincia:</label>';
        $output .= '<input required type="text" id="provincia" name="provincia" value="' . $tabla[0]['provincia'] . '"><br>';

        $output .= '<input style="display:none;" required type="text" id="idConcierto" name="idConcierto" value="' . $tabla[0]['idConcierto'] . '"><br>';
        $output .= '<input style="display:none;" type="text" name="operacion" value="valuesEditar">';
    }

    $output .= '<button type="submit">editar</button>';
    return $output;
}
function getTableMarkup($tabla)
{
    $output = '<table>
                <thead>
                <tr>';

    if (isset($tabla[0]['idConcierto'])) {
        $output .= '<td>Establecimiento</td>';
        $output .= '<td>Localizacion</td>';
        $output .= '<td>Artistas</td>';
    } else {
        $output .= '<td>Nombre</td>';
        $output .= '<td>Apellidos</td>';
        $output .= '<td>Fecha de Nacimiento</td>';
    }
    $output .= '<td>Opciones</td>';
    $output .= '</tr>
                </thead>
                <tbody>';
    foreach ($tabla as $fila) {
        $output .= '<tr>';
        if (isset($fila['idConcierto'])) {
            $output .= '<td>' . $fila['establecimiento'] . '</td>';
            $output .= '<td>' . $fila['provincia'] . '</td>';
            $output .= '<td>' . $fila['artistas'] . '</td>';
        } else {
            $output .= '<td>' . $fila['nombre'] . '</td>';
            $output .= '<td>' . $fila['apellidos'] . '</td>';
            $output .= '<td>' . $fila['fechaNacimiento'] . '</td>';
        }
        $output .= '<td>

        <button type="submit" name="operacion" value="eliminar_' . ($fila['idArtista'] ?? $fila['idConcierto']) . '">
            <img style="width: 30px;" src="https://cdn-icons-png.flaticon.com/512/58/58326.png">
        </button>

        <button type="submit" name="operacion" value="editar_' . ($fila['idArtista'] ?? $fila['idConcierto']) . '">
            <img style="width: 30px;" src="https://cdn-icons-png.flaticon.com/512/2919/2919564.png">
        </button>

        </td></tr>';
    }
    $output .= '</tbody>';
    $output .= '</table>';
    return $output;
}



function delete($tabla, $id)
{
    global $servername, $username, $password, $dbname;
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    } catch (PDOException $e) {
        echo "Error de conexión: " . $e->getMessage();
        exit();
    }
    try {

        switch ($tabla) {
            case 'artistas':
                $sql = "DELETE FROM $tabla WHERE idArtista = $id";
                break;
            case 'conciertos':
                $sql = "DELETE FROM $tabla WHERE idConcierto = $id";
                break;
        }
        $stmt = $conn->prepare($sql);

        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error al realizar la consulta: " . $e->getMessage();
    }

    $conn = null;
}

function edit($tabla, $id, $values)
{
    global $servername, $username, $password, $dbname;

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    } catch (PDOException $e) {
        echo "Error de conexión: " . $e->getMessage();
        exit();
    }

    try {

        switch ($tabla) {
            case 'artistas':
                $stmt = $conn->prepare("UPDATE artistas SET nombre = :nuevoNombre, apellidos = :nuevosApellidos, fechaNacimiento = :nuevaFecha WHERE idArtista = :idArtista");
                $stmt->bindParam(':nuevoNombre', $values['nombre'], PDO::PARAM_STR);
                $stmt->bindParam(':nuevosApellidos', $values['apellidos'], PDO::PARAM_STR);
                $stmt->bindParam(':nuevaFecha', $values['fechaNacimiento'], PDO::PARAM_STR);
                $stmt->bindParam(':idArtista', $id, PDO::PARAM_INT);
                break;
            case 'conciertos':
                $stmt = $conn->prepare("UPDATE conciertos SET establecimiento = :nuevoEstablecimiento, provincia = :nuevaProvincia WHERE idConcierto = :idConcierto");
                $stmt->bindParam(':nuevoEstablecimiento', $values['establecimiento'], PDO::PARAM_STR);
                $stmt->bindParam(':nuevaProvincia', $values['provincia'], PDO::PARAM_STR);
                $stmt->bindParam(':idConcierto', $id, PDO::PARAM_STR);
                break;
        }

        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error al realizar la consulta: " . $e->getMessage();
    }
}
