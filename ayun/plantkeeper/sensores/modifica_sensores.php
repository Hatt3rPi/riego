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
            $tipo_sensor = mysqli_real_escape_string($link, $_POST['tipo_sensor']);
            $sql = "INSERT INTO sensores_disponibles (tipo_sensor) VALUES (?)";

            $stmt = mysqli_prepare($link, $sql);
            
            if ($stmt === false) {
                echo "ERROR: No se pudo preparar la consulta SQL. " . mysqli_error($link);
            } else {
                mysqli_stmt_bind_param($stmt, "s", $tipo_sensor);

                if (mysqli_stmt_execute($stmt)) {
                    trazabilidad($link, $_SESSION['usuario'], basename(__FILE__), 'ingresa sensor', 0, "INSERT INTO sensores_disponibles (tipo_sensor) VALUES ('".$tipo_sensor."')") ;

                } else {
                    echo "ERROR: No se pudo ejecutar la consulta. " . mysqli_error($link);
                }

                mysqli_stmt_close($stmt);
            }
            break;
        case 'eliminacion':
            
            $id = $_POST['id'];
            $sql = "DELETE FROM sensores_disponibles WHERE id = ?";
            trazabilidad($link, $_SESSION['usuario'], basename(__FILE__), 'elimina sensor', $id, $sql.$id);
            $stmt = mysqli_prepare($link, $sql);
            if ($stmt === false) {
                echo "ERROR: No se pudo preparar la consulta SQL. " . mysqli_error($link);
            } else {
                mysqli_stmt_bind_param($stmt, "i", $id);
                if (mysqli_stmt_execute($stmt)) {
                    echo "Sensor eliminada con éxito.";
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
