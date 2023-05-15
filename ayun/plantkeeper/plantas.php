<?php
session_start();
?>
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
            <th style="display: none;">id</th>
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
                <input type="number" class="form-control" id="humedad-min" value="20" required>
            </div>
            <div class="form-group col-md-6">
                <label for="humedad-max">Humedad máxima:</label>
                <input type="number" class="form-control" id="humedad-max" value="60" required>
            </div>
        </div>
        <div class="form row">
        <div class="form-group col-md-6">
            <label for="macetero">Macetero:</label>
            <select class="form-control" id="macetero" required>
                <option value="">Seleccione el tamaño del macetero</option>
                <option value=4>4 cm</option>
                <option value=6>6 cm</option>
                <option value=8>8 cm</option>
                <option value=10 selected>10 cm</option>
                <option value=16>16 cm</option>
                <option value=20>20 cm</option>
                <option value=24>24 cm</option>
            </select>
        </div>
    </div>

        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <button type="submit" class="btn btn-primary">Añadir planta</button>
    </form>
</div>
<script>
$(document).ready(function() {

var tablaPlantas = $("#plantas-table").DataTable({
    columnDefs: [
        {
            targets: [6], // Índice de la columna ID en base cero
            visible: false,
            searchable: false
        }
    ]
});


function cargarPlantas() {
    $.getJSON("./plantkeeper/plantas/listado_plantas.php", function (data) {
        for (let planta of data) {
            agregarFilaATabla(planta);
        }
    });
}

function agregarFilaATabla(planta) {
    tablaPlantas.row.add([
        planta.especie,
        planta.ubicacion,
        planta.humedad_sustrato_minima + '%',
        planta.humedad_sustrato_maxima + '%',
        planta.tamano + ' cm',
        '<button class="btn btn-primary"><i class="fas fa-pencil-alt"></i></button> <button class="btn btn-danger"><i class="fas fa-trash"></i></button>',
        '<td style="display: none;">' + planta.id + '</td>'
    ]).draw();
}

function eliminarFilaDeTabla(fila) {
    var id = tablaPlantas.row(fila).data()[6]; // Obtiene el valor del ID de la fila
    $.post("./plantkeeper/plantas/modifica_plantas.php", { id: id, accion_bbdd: 'eliminacion' }, function (response) {
        tablaPlantas.row(fila).remove().draw();
    });
}

function recolectarDatosDelFormulario() {
    return {
        especie: $("#especie").val(),
        ubicacion: $("#ubicacion").val(),
        humedad_sustrato_minima: $("#humedad-min").val(),
        humedad_sustrato_maxima: $("#humedad-max").val(),
        tamano: $("#macetero").val(),
        csrf_token: $("input[name='csrf_token']").val(),
        accion_bbdd: 'ingreso'
    };
}

function enviarFormulario(datos) {
    $.post("./plantkeeper/plantas/modifica_plantas.php", datos, function (response) {
        agregarFilaATabla(datos);
        $("#formulario_nueva_planta")[0].reset();
    });
}

// Manejadores de eventos
$("#boton_agrega_plantas").on("click", function() {
    $("#contenedor_nueva_planta").toggle();
});

$("#plantas-table tbody").on("click", ".btn-danger", function() {
    eliminarFilaDeTabla($(this).parents("tr"));
});

$("#formulario_nueva_planta").on("submit", function (e) {
    e.preventDefault();
    var datos = recolectarDatosDelFormulario();
    enviarFormulario(datos);
});

// Cargar datos desde el servidor
cargarPlantas();

});

</script>
