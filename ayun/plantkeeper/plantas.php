
<!-- Contenido de la página plantas -->
<h2>Lista de Plantas</h2>
<table id="plantas-table" class="table table-striped table-bordered" style="width:100%">
    <thead>
        <tr>
            <th>Especie</th>
            <th>Ubicación</th>
            <th>Humedad mínima</th>
            <th>Humedad máxima</th>
            <th>Macetero</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<!-- Botón para mostrar el formulario -->
<button id="boton_agrega_plantas" class="btn btn-success">Ingresar nuevas plantas</button>

<!-- Añadir nueva planta -->
<div id="contenedor_nueva_planta" style="display: none;">
    <h2>Añadir nueva planta</h2>
    <form id="formulario_nueva_planta" class="minimalist-form">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="especie">Especie:</label>
                <input type="text" class="form-control" id="especie" required>
            </div>
            <div class="form-group col-md-6">
                <label for="ubicacion">Ubicación:</label>
                <input type="text" class="form-control" id="ubicacion" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="humedad-min">Humedad mínima:</label>
                <input type="number" class="form-control" id="humedad-min" required>
            </div>
            <div class="form-group col-md-6">
                <label for="humedad-max">Humedad máxima:</label>
                <input type="number" class="form-control" id="humedad-max" required>
            </div>
        </div>
        <div class="form row">
            <div class="form-group col-md-6">
                <label for="macetero">Macetero:</label>
                <input type="text" class="form-control" id="macetero" required>
            </div>
        </div>
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <button type="submit" class="btn btn-primary">Añadir planta</button>
    </form>
</div>
<script>
        function loadPlantas() {
            $.getJSON("./plantkeeper/plantas/listado_plantas.php", function (data) {
                for (let planta of data) {
                    plantasTable.row.add([
                        planta.especie,
                        planta.ubicacion, // Coma agregada aquí
                        planta.humedad_min + '%',
                        planta.humedad_max + '%',
                        planta.macetero,
                        '<button class="btn btn-primary">Editar</button> <button class="btn btn-danger">Eliminar</button>'
                    ]).draw();
                }
            });
        }
        // Mostrar el formulario cuando se hace clic en el botón
        $("#boton_agrega_plantas").on("click", function() {
            $("#contenedor_nueva_planta").toggle();
        });
        // Inicializar DataTable
        var plantasTable = $("#plantas-table").DataTable();

        // Cargar plantas desde el servidor
        loadPlantas();

        $("#plantas-table tbody").on("click", ".btn-danger", function() {
            plantasTable.row($(this).parents("tr")).remove().draw();
        });

        // Manejar el envío del formulario de nueva planta
        $("#formulario_nueva_planta").on("submit", function (e) {
            e.preventDefault();

            // Recolecta los datos del formulario
            const data = {
                especie: $("#especie").val(),
                ubicacion: $("#ubicacion").val(),
                humedad_min: $("#humedad-min").val(),
                humedad_max: $("#humedad-max").val(),
                macetero: $("#macetero").val(),
                csrf_token: $("input[name='csrf_token']").val(),
            };

            // Enviar datos al servidor
            $.post("./plantkeeper/plantas/agregar_plantas.php", data, function (response) {
                console.log(response);

                // Actualizar la tabla con la nueva planta
                plantasTable.row.add([
                    data.especie,
                    data.ubicacion,
                    data.humedad_min + '%',
                    data.humedad_max + '%',
                    data.macetero,
                    '<button class="btn btn-primary">Editar</button> <button class="btn btn-danger">Eliminar</button>',
                ]).draw();

                // Limpiar el formulario
                $("#formulario_nueva_planta")[0].reset();
            });
        });


</script>
