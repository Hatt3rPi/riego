<?php

include '/home/gestio10/procedimientos_almacenados/config_ayun.php';
header('Content-Type: application/json');

$sql = "SELECT a.id, a.pin, a.id_recolector, a.id_planta, a.id_sensor, a.tipo_sensor, b.estado FROM `recolectores_pines` as a left join sensores_disponibles as b on a.id_sensor=b.id";
$result = mysqli_query($link, $sql);

if (mysqli_num_rows($result) > 0) {
    $plantas = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $plantas[] = $row;
    }
    echo json_encode($plantas);
} else {
    echo json_encode([]);
}

mysqli_close($link);
?>