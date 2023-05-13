<?php
include '/home/gestio10/procedimientos_almacenados/config_ayun.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['csrf_token']==$_SESSION['csrf_token']) {
    $especie = $_POST['especie'];
    $ubicacion = $_POST['ubicacion'];
    $humedad_min = $_POST['humedad_min'];
    $humedad_max = $_POST['humedad_max'];
    $macetero = $_POST['macetero'];

    $sql = "INSERT INTO plantas (especie, ubicacion, humedad_sustrato_minima, humedad_sustrato_maxima, macetero) VALUES (?, ?, ?, ?, ?)";
    
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssiii", $especie, $ubicacion, $humedad_min, $humedad_max, $macetero);
        
        if (mysqli_stmt_execute($stmt)) {
            echo "Planta añadida con éxito.";
        } else {
            echo "ERROR: No se pudo ejecutar $sql. " . mysqli_error($link);
        }
    }
    mysqli_stmt_close($stmt);
}
mysqli_close($link);
?>
