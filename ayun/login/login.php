<?php
    // Incluir el archivo de conexión a la base de datos
    require_once "/home/gestio10/procedimientos_almacenados/config_ayun.php";
    
    try {
        // Attempt to connect to MySQL database using PDO
        $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
        // Set the PDO error mode to exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e){
        // If connection fails, show error message and stop script
        die("ERROR: No se pudo conectar a la base de datos. " . $e->getMessage());
    }

    // Verificar si ya hay una sesión iniciada
    if (isset($_SESSION['usuario'])) {
        // Si ya hay una sesión iniciada, redirigir al usuario a la página principal
        header("Location: index.php");
        exit();
    }

    // Verificar si el formulario de inicio de sesión ha sido enviado
    if (isset($_POST['login'])) {
        // Obtener el nombre de usuario y la contrasena ingresados y filtrarlos
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

        // Consulta preparada para buscar el usuario en la base de datos
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = :usuario");
        $stmt->execute(array('usuario' => $username));
        $usuario = $stmt->fetch();

        // Verificar si la contrasena es correcta utilizando password_verify()
        if ($usuario && password_verify($password, $usuario['contrasena'])) {
            // Iniciar sesión
            session_start();
            // La contrasena es correcta, establecer la sesión
            $_SESSION['usuario'] = htmlspecialchars($usuario['usuario']);
            
            header("Location: ../index.php");
            exit();
        } else {
            // La contrasena es incorrecta, mostrar un mensaje de error
            $error = "Nombre de usuario o contrasena incorrectos.";
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container_login">
        <form method="POST">
            <h1>Login</h1>
            <?php if (isset($error)) { ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php } ?>
            <label for="username">Username</label>
            <input type="text" id="username" name="username">
            <label for="password">Password</label>
            <input type="password" id="password" name="password">
            <input type="submit" name="login" value="Login">
        </form>
    </div>
</body>
</html>
