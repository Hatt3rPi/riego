<?php

include '/home/gestio10/procedimientos_almacenados/config_ayun.php';
header('Content-Type: application/json');

$sql =  "SELECT a.id, a.especie, a.ubicacion, " .
        "MAX(CASE WHEN b.tipo_sensor = 'humedad_sustrato' THEN b.pin ELSE NULL END) AS humedad_sustrato," .
        "MAX(CASE WHEN b.tipo_sensor = 'bomba_agua' THEN b.pin ELSE NULL END) AS bomba_agua " .
        "FROM plantas as a " .
        "LEFT JOIN recolectores_pines as b " .
        "ON a.id = b.id_planta " .
        "GROUP BY " .
        "a.id, a.especie, a.ubicacion";
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