<?php
session_start();
include '/home/gestio10/procedimientos_almacenados/config_ayun.php';

function trazabilidad($link, $usuario, $archivo, $descripcion, $id_modificado, $query) {
    mysqli_set_charset($link, 'utf8');
    // Prepara la consulta SQL
    $stmt_2 = mysqli_prepare($link, "INSERT INTO trazabilidad_query(usuario, archivo, descripcion, id_modificado, query) VALUES (?, ?, ?, ?, ?)");
    // Verifica si la preparación de la consulta fue exitosa
    if ($stmt_2 === false) {
        die("ERROR: No se pudo preparar la consulta SQL. " . mysqli_error($link));
    }
    // Vincula los parámetros a la consulta SQL
    mysqli_stmt_bind_param($stmt_2, "sssis", $usuario, $archivo, $descripcion, $id_modificado, $query);

    // Ejecuta la consulta
    if (mysqli_stmt_execute($stmt_2)) {
        echo "Datos de trazabilidad añadidos con éxito.";
    } else {
        echo "ERROR: No se pudo ejecutar la consulta. " . mysqli_error($link);
    }

    // Cierra la declaración
    mysqli_stmt_close($stmt_2);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['csrf_token']==$_SESSION['csrf_token']) {
    switch($_POST['accion_bbdd']){
        case 'ingreso':
            $especie = mysqli_real_escape_string($link, $_POST['especie']);
            $ubicacion = mysqli_real_escape_string($link, $_POST['ubicacion']);
            $humedad_sustrato_minima = intval($_POST['humedad_sustrato_minima']);
            $humedad_sustrato_maxima = intval($_POST['humedad_sustrato_maxima']);
            $tamano = intval($_POST['tamano']);

            $sql = "INSERT INTO plantas (especie, ubicacion, humedad_sustrato_minima, humedad_sustrato_maxima, tamano) VALUES (?, ?, ?, ?, ?)";

            $stmt_2 = mysqli_prepare($link, $sql);
            
            if ($stmt === false) {
                echo "ERROR: No se pudo preparar la consulta SQL. " . mysqli_error($link);
            } else {
                mysqli_stmt_bind_param($stmt, "ssiii", $especie, $ubicacion, $humedad_sustrato_minima, $humedad_sustrato_maxima, $tamano);

                if (mysqli_stmt_execute($stmt)) {
                    trazabilidad($link, $_SESSION['usuario'], basename(__FILE__), 'ingresa planta', 0, "INSERT INTO plantas (especie, ubicacion, humedad_sustrato_minima, humedad_sustrato_maxima, tamano) VALUES ('".$especie."', '".$ubicacion."', ". $humedad_sustrato_minima.", ".$humedad_sustrato_maxima.", ".$tamano.")") 

                } else {
                    echo "ERROR: No se pudo ejecutar la consulta. " . mysqli_error($link);
                }

                mysqli_stmt_close($stmt);
            }
            break;
        case 'eliminacion':
            
            $id = $_POST['id'];
            $sql = "DELETE FROM plantas WHERE id = ?";
            trazabilidad($link, $_SESSION['usuario'], basename(__FILE__), 'elimina planta', $id, $sql.$id)
            $stmt = mysqli_prepare($link, $sql);
            if ($stmt === false) {
                echo "ERROR: No se pudo preparar la consulta SQL. " . mysqli_error($link);
            } else {
                mysqli_stmt_bind_param($stmt, "i", $id);
                if (mysqli_stmt_execute($stmt)) {
                    echo "Planta eliminada con éxito.";
                } else {
                    echo "ERROR: No se pudo ejecutar la consulta. " . mysqli_error($link);
                }
            }
            mysqli_stmt_close($stmt);
            break;
        default:
            echo "Acción no reconocida.";
            break;
    }
}
mysqli_close($link);
?>
