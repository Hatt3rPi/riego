<?php
$url = "https://www.gestionipn.cl/ayun/data_mediciones_plantas.php";
$token = "=goaWJ/d7r=obtd1d=kYlYi17ShkdVwsBn0ooG3DdiyhhQLqwpbWUvbGpcnVe5Ds";
$fecha_desde = date("Y-m-d H:i:s", strtotime("-120 days"));  // Últimos 30 días

$data = array('fecha_desde' => $fecha_desde, 'token' => $token);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);
if ($result === FALSE) {
    die("Error al obtener los datos.");
}

curl_close($ch);

$resultados = json_decode($result, true);

// Ordena los datos por fecha_hora
usort($resultados, function($a, $b) {
    return strtotime($a['fecha_hora']) - strtotime($b['fecha_hora']);
});

// Agrupa y calcula la media móvil por planta
$plantas = [];
foreach ($resultados as $key => $value) {
    $plantas[$value['especie']][] = $value;
}

foreach ($plantas as &$planta) {
    for ($key = 0; $key < count($planta); $key++) {
        if ($key >= 4) {
            $sum = 0;
            for ($i = 0; $i < 5; $i++) {
                $sum += $planta[$key - $i]['humedad_suelo'];
            }
            $planta[$key]['media_movil'] = $sum / 5;
        } else {
            $planta[$key]['media_movil'] = null;
        }
    }
}

// Codificar los datos en JSON
$data_json = json_encode($plantas);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Gráfico de Humedad del Suelo</title>


</head>
<body>

<canvas id="myChart"></canvas>

<script>
// Los datos del servidor
var data = <?php echo $data_json; ?>;

// Crea un contexto de dibujo en el canvas
var ctx = document.getElementById('myChart').getContext('2d');

// Colores para las diferentes plantas
var colors = ['rgba(0, 123, 255, 0.5)', 'rgba(220, 53, 69, 0.5)', 'rgba(40, 167, 69, 0.5)', 'rgba(255, 193, 7, 0.5)', 'rgba(23, 162, 184, 0.5)', 'rgba(52, 58, 64, 0.5)'];

// Crea un conjunto de datos para cada planta
var datasets = Object.keys(data).map(function(especie, index) {
    return {
        label: especie,
        data: data[especie].map(datum => datum.media_movil),  // Usa la media móvil como datos
        backgroundColor: colors[index % colors.length],
        borderColor: colors[index % colors.length],
        fill: false
    };
});

// Crea un nuevo gráfico usando la biblioteca Chart.js
var myChart = new Chart(ctx, {
    type: 'line',  // Tipo de gráfico
    data: {
        labels: data[Object.keys(data)[0]].map(datum => datum.fecha_hora),  // Usa las fechas como etiquetas
        datasets: datasets
    },
    options: {
        scales: {
            x: {
                type: 'time',
                min: '00:00',
                max: '21:00',
                adapters: {
                    date: {
                        library: 'moment'
                    }
                },
                tick: {
                    autoSkip: true,
                    source: 'data',
                    major: {
                        enabled: true,
                        interval: 3 * 60 * 60 * 1000 // cada 3 horas
                    }
                }
            },
            y: {
                min: 200,  // rango mínimo de y
                max: 520  // rango máximo de y
            }
        }
    }
});


</script>

</body>
</html>
