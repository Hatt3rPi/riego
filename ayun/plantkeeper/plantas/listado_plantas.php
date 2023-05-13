<?php

include '/home/gestio10/procedimientos_almacenados/config_ayun.php';
header('Content-Type: application/json');

$sql = "SELECT * FROM plantas";
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