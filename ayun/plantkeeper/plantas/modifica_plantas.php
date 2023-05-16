<?php
session_start();
include '/home/gestio10/procedimientos_almacenados/config_ayun.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['csrf_token']==$_SESSION['csrf_token']) {
    switch($_POST['accion_bbdd']){
        case 'ingreso':
            $especie = mysqli_real_escape_string($link, $_POST['especie']);
            $ubicacion = mysqli_real_escape_string($link, $_POST['ubicacion']);
            $humedad_sustrato_minima = intval($_POST['humedad_sustrato_minima']);
            $humedad_sustrato_maxima = intval($_POST['humedad_sustrato_maxima']);
            $tamano = intval($_POST['tamano']);

            $sql = "INSERT INTO plantas (especie, ubicacion, humedad_sustrato_minima, humedad_sustrato_maxima, tamano) VALUES (?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($link, $sql);

            if ($stmt === false) {
                echo "ERROR: No se pudo preparar la consulta SQL. " . mysqli_error($link);
            } else {
                mysqli_stmt_bind_param($stmt, "ssiii", $especie, $ubicacion, $humedad_sustrato_minima, $humedad_sustrato_maxima, $tamano);

                if (mysqli_stmt_execute($stmt)) {
                    echo "Planta añadida con éxito.";
                } else {
                    echo "ERROR: No se pudo ejecutar la consulta. " . mysqli_error($link);
                }

                mysqli_stmt_close($stmt);
            }
            break;
        case 'eliminacion':
            $id = intval($_POST['id']);
            $sql = "DELETE FROM plantas WHERE id = ?";
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
