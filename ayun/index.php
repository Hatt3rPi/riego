<?php
include './assets/csrf_token.php';
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mi página de plantas</title>
        <link rel="stylesheet" href="./assets/css/style.css">
    
        <!-- CSS de Bootstrap 4 -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
        <!-- Estilos CSS de DataTables -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
        <!-- JS de DataTables -->
        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    
        <!-- JS de DataTables con soporte para Bootstrap 4 -->
        <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    </head>
<body>
    <header>
        <div class="logo-title-search-container">
            <div class="logo-title-container">
                <img src="./assets/img/logo2.png" alt="Logo" class="logo"/>
                <h1>Ayün Plantkeeper</h1>
            </div>
            <div class="search-container">
                <input type="search" placeholder="Buscar..." class="header-search">
            </div>
        </div>
    </header>
    
    <div class="container_fas">
        <aside class="sidebar">

            <ul>
                <li><a href="principal.php" class="">Principal</a></li>
                <li><a href="plantkeeper/plantkeeper.php" class="">Plantkeeper</a></li>
                <li><a href="plantkeeper/plantas.php" class="">Plantas</a></li>
                <li><a href="plantkeeper/zonas.php" class="">Zonas</a></li>
                <li><a href="plantkeeper/recolectores.php" class="">Recolectores</a></li>
                <li><a href="plantkeeper/relaciones.php" class="">Relaciones</a></li>
                <li><a href="plantkeeper/metricas.php" class="">Métricas</a></li>
            </ul>
        </aside>
        <main class="content">
            <!-- El contenido se cargará dinámicamente aquí -->
        </main>
    </div>
    <script src="./assets/js/scripts.js"></script>
</body>
</html>
