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
            <th>Estado</th>
            <th>ID</th>
            <th>Tipo de sensor</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<div id="modalNuevaPlanta" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Añadir nuevo sensor</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="formulario_nuevo_sensor" class="minimalist-form">
          <div class="form-group">
            <label for="tipo_sensor">Tipo de Sensor:</label>
            <select class="form-control" id="tipo_sensor" required>
                <option value="">Seleccione el tipo de sensor</option>
                <option value="humedad_sustrato">Humedad Sustrato</option>
                <option value="bomba_agua">Bomba de agua</option>
            </select>
          </div>
          <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
          <button type="submit" class="btn btn-primary">Añadir sensor</button>
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
        }
    ],
   
    columns: [
        { width: "5%" }, // Checkbox
        { width: "30%" }, // estado
        { width: "10%" }, // id
        { width: "55%" } // tipo
    ],
    language: {
        url: "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
    }
});



function cargarPlantas() {
    $.getJSON("./plantkeeper/sensores/listado_sensores.php", function (data) {
        for (let planta of data) {
            agregarFilaATabla(planta);
        }
    });
}

function agregarFilaATabla(planta) {
    tablaPlantas.row.add([
        '<input type="checkbox" name="id[]" value="' + planta.id + '">',
        planta.estado,
        planta.id,
        planta.tipo_sensor
    ]).draw();
}

function eliminarFilaDeTabla(fila) {
    var idHtml = tablaPlantas.row(fila).data()[2]; // Obtiene el valor del ID de la fila
    var id = idHtml.replace(/<[^>]*>/g, ''); // Elimina las etiquetas HTML
    $.post("./plantkeeper/sensores/modifica_sensores.php", { id: id, accion_bbdd: 'eliminacion', csrf_token: $("input[name='csrf_token']").val() }, function (response) {
        tablaPlantas.row(fila).remove().draw();
    });
}


function recolectarDatosDelFormulario() {
    return {
        tipo_sensor: $("#tipo_sensor").val(),
        csrf_token: $("input[name='csrf_token']").val(),
        accion_bbdd: 'ingreso'
    };
}

function enviarFormulario(datos) {
    $.post("./plantkeeper/sensores/listado_sensores.php", datos, function (response) {
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