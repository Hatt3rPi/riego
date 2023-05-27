<?php

require_once '/home/gestio10/procedimientos_almacenados/config_ayun.php';

// Configura tu token de API de Telegram aquí
$telegramApiToken = '6101845697:AAHTNNoqlhkAsLUAJQkkN_2vqTLuxC8Fk_4';
// Configura tu chat ID aquí
$telegramChatId = '1567062024';


function enviarMensajeTelegram($telegramApiToken, $telegramChatId, $texto)
{
    $apiUrl = "https://api.telegram.org/bot{$telegramApiToken}/sendMessage";
    $params = [
        'chat_id' => $telegramChatId,
        'text' => $texto,
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error al enviar mensaje por Telegram. Error cURL: ' . curl_error($ch);
    }

    curl_close($ch);

    $responseData = json_decode($response, true);

    if (!$responseData['ok']) {
        echo "Error al enviar mensaje por Telegram. Código de error: {$responseData['error_code']}. Descripción: {$responseData['description']}";
    }

    return $responseData['ok'];
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtén el mensaje del POST
    $tipo_mensaje = $_POST['tipo'];
    $texto = $_POST['mensaje'];

    enviarMensajeTelegram($telegramApiToken, $telegramChatId, $texto);
}
