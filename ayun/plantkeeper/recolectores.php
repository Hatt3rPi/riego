<?php
session_start();
?>
<!-- Contenido de la página plantas -->
<h2>Lista de Plantas</h2>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

<div id="buttons">
    <button id="new-plant" class="btn btn-secondary" data-toggle="modal" data-target="#modalNuevaPlanta"><i class="fa-solid fa-leaf"></i></button>
    <button id="edit-plant" class="btn btn-secondary" disabled><i class="fas fa-pencil-alt"></i></button>
    <button id="delete-plant" class="btn btn-danger" disabled><i class="fas fa-trash"></i></button>
</div>

<table id="plantas-table" class="table table-striped table-bordered" style="width:100%">
    <thead>
        <tr>
            <th></th> <!-- Columna de checkbox -->
            <th>Especie</th>
            <th>Ubicación</th>
            <th>Humedad mínima</th>
            <th>Humedad máxima</th>
            <th>Macetero</th>
            <th style="display: none;">id</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<!-- Añadir nueva planta -->
<div id="modalNuevaPlanta" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Añadir nueva planta</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="formulario_nueva_planta" class="minimalist-form">
          <div class="form-row">
            <div class="form-group col-md-6">
                <label for="especie">Especie:</label>
                <input type="text" class="form-control" id="especie" required>
            </div>
            <div class="form-group col-md-6">
                <label for="ubicacion">Ubicación:</label>
                <select class="form-control" id="ubicacion" required>
                    <option value="">Seleccione la ubicación</option>
                    <option value="Oficina - repisa">Oficina - repisa</option>
                    <option value="Oficina - colgante">Oficina - colgante</option>
                    <option value="Living - mueble tv">Living - mueble tv</option>
                    <option value="Living - colgante">Living - colgante</option>
                    <option value="Living - ventanal jardín">Living - ventanal jardín</option>
                    <option value="Living - bajo lámparas">Living - bajo lámparas</option>
                    <option value="Living - muros">Living - muros</option>
                    <option value="Living - ventanal patio">Living - ventanal patio</option>
                    <option value="Living - gabinete">Living - gabinete</option>
                    <option value="Arrimo entrada">Arrimo entrada</option>
                    <option value="Patio">Patio</option>
                    <option value="Jardin">Jardin</option>
                </select>
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
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<script>
$(document).ready(function() {

    var tablaPlantas = $("#plantas-table").DataTable({
    columnDefs: [
        {
            targets: 0,
            render: function (data, type, row, meta) {
                return '<input type="checkbox" name="id[]" value="' + $('<div/>').text(data).html() + '">';
            }
        },
        {
            targets: [6], // Índice de la columna ID en base cero
            visible: false,
            searchable: false
        }
    ],
   
    columns: [
        { width: "5%" }, // Checkbox
        { width: "30%" }, // Especie
        { width: "14%" }, // Ubicación
        { width: "17%" }, // Humedad mínima
        { width: "17%" }, // Humedad máxima
        { width: "17%" }, // Macetero
        { width: "0%" }  // ID (oculto)
    ],
    language: {
        url: "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
    }
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
        '<input type="checkbox" name="id[]" value="' + planta.id + '">',
        planta.especie,
        planta.ubicacion,
        planta.humedad_sustrato_minima + '%',
        planta.humedad_sustrato_maxima + '%',
        planta.tamano + ' cm',
        '<td style="display: none;">' + planta.id + '</td>'
    ]).draw();
}

function eliminarFilaDeTabla(fila) {
    var idHtml = tablaPlantas.row(fila).data()[6]; // Obtiene el valor del ID de la fila
    var id = idHtml.replace(/<[^>]*>/g, ''); // Elimina las etiquetas HTML
    $.post("./plantkeeper/plantas/modifica_plantas.php", { id: id, accion_bbdd: 'eliminacion', csrf_token: $("input[name='csrf_token']").val() }, function (response) {
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

$('#delete-plant').on('click', function() {
    // Array para almacenar las filas que deben ser eliminadas
    var filasParaEliminar = [];

    // Recorre todas las filas de la tabla
    $('#plantas-table tbody tr').each(function() {
        // Comprueba si el checkbox de esta fila está marcado
        if ($(this).find('input[type="checkbox"]').is(':checked')) {
            // Si está marcado, añade la fila al array
            filasParaEliminar.push($(this));
        }
    });

    // Recorre el array de filas para eliminar y elimina cada fila
    for (let fila of filasParaEliminar) {
        eliminarFilaDeTabla(fila);
    }
});

$('#edit-plant').on('click', function() {
    // Código para editar la planta seleccionada
});

$("#formulario_nueva_planta").on("submit", function (e) {
    e.preventDefault();
    var datos = recolectarDatosDelFormulario();
    enviarFormulario(datos);
});

// Manejador de eventos para los checkboxes
$("#plantas-table").on("change", "input[type='checkbox']", function() {
    // Comprueba si hay al menos un checkbox marcado
    var isCheckboxChecked = $("#plantas-table input[type='checkbox']:checked").length > 0;

    // Activa o desactiva los botones en función de si hay un checkbox marcado
    $("#edit-plant, #delete-plant").prop("disabled", !isCheckboxChecked);
});

// Cargar datos desde el servidor
cargarPlantas();

});

</script>