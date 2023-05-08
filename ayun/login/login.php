<?php
session_start();

// Incluir el archivo de conexión a la base de datos
require_once "/home/gestio10/procedimientos_almacenados/config_ayun.php";

// Función para escapar valores de cadenas de caracteres
function escape($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// Función para generar un token CSRF
function generateCSRFToken() {
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    return $token;
}

// Verificar si ya hay una sesión iniciada
if (isset($_SESSION['usuario'])) {
    // Si ya hay una sesión iniciada, redirigir al usuario a la página principal
    header("Location: ../index.php");
    exit();
}

// Verificar si el formulario de inicio de sesión ha sido enviado
if (isset($_POST['login'])) {
    // Obtener el nombre de usuario y la contraseña ingresados y filtrarlos
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    // Consulta preparada para buscar el usuario en la base de datos
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = :usuario");
    $stmt->execute(array('usuario' => $username));
    $usuario = $stmt->fetch();

    // Verificar si la contraseña es correcta utilizando password_verify()
    if ($usuario && password_verify($password, $usuario['contrasena'])) {
        // Generar un token CSRF y establecer la sesión
        $csrfToken = generateCSRFToken();
        $_SESSION['usuario'] = escape($usuario['usuario']);
        $_SESSION['csrf_token'] = $csrfToken;

        // Redirigir al usuario a la página principal
        header("Location: ../index.php");
        exit();
    } else {
        // La contraseña es incorrecta, mostrar un mensaje de error
        $error = "Nombre de usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container_login">
        <form method="POST">
            <h1>Iniciar Sesión</h1>
            <?php if (isset($error)) { ?>
                <p class="error"><?php echo escape($error); ?></p>
            <?php } ?>
            <label for="username">Usuario</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required>
            <input type="hidden" name="csrf_token" value="<?php echo escape($csrfToken); ?>">
            <input type="submit" name="login" value="Login">
        </form>
    </div>
</body>
</html>
