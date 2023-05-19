<?php
session_start();
?>
<!-- Contenido de la página recolectores -->
<h2>Lista de Recolectores</h2>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

<div id="buttons">
    <button id="new-recolector" class="btn btn-secondary" data-toggle="modal" data-target="#modalNuevarecolector"><i class="fa-solid fa-leaf"></i></button>
    <button id="edit-recolector" class="btn btn-secondary" disabled><i class="fas fa-pencil-alt"></i></button>
    <button id="delete-recolector" class="btn btn-danger" disabled><i class="fas fa-trash"></i></button>
</div>

<table id="recolectores-table" class="table table-striped table-bordered" style="width:100%">
    <thead>
        <tr>
            <th></th> <!-- Columna de checkbox -->
            <th>Nombre</th>
            <th>Ubicación</th>
            <th>puerto</th>
            <th style="display: none;">id</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<!-- Añadir nueva recolector -->
<div id="modalNuevarecolector" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Añadir nueva recolector</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="formulario_nuevo_recolector" class="minimalist-form">
          <div class="form-row">
            <div class="form-group col-md-6">
                <label for="nombre">Especie:</label>
                <input type="text" class="form-control" id="nombre" required>
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
                    <option value="Living - mesa de centro">Living - mesa de centro</option>
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
          <button type="submit" class="btn btn-primary">Añadir recolector</button>
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

    var tablaRecolectores = $("#recolectores-table").DataTable({
    columnDefs: [
        {
            targets: 0,
            render: function (data, type, row, meta) {
                return '<input type="checkbox" name="id[]" value="' + $('<div/>').text(data).html() + '">';
            }
        },
        {
            targets: [4], // Índice de la columna ID en base cero
            visible: false,
            searchable: false
        }
    ],
   
    columns: [
        { width: "5%" }, // Checkbox
        { width: "40%" }, // nombre
        { width: "30%" }, // Ubicación
        { width: "25%" }, // puerto
        { width: "0%" }  // ID (oculto)
    ],
    language: {
        url: "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
    }
});



function cargarRecolectores() {
    $.getJSON("./plantkeeper/recolectores/listado_recolectores.php", function (data) {
        for (let recolector of data) {
            agregarFilaATabla(recolector);
        }
    });
}

function agregarFilaATabla(recolector) {
    tablaRecolectores.row.add([
        '<input type="checkbox" name="id[]" value="' + recolector.id + '">',
        recolector.nombre,
        recolector.ubicacion,
        recolector.puerto,
        '<td style="display: none;">' + recolector.id + '</td>'
    ]).draw();
}

function eliminarFilaDeTabla(fila) {
    var idHtml = tablaRecolectores.row(fila).data()[5]; // Obtiene el valor del ID de la fila
    var id = idHtml.replace(/<[^>]*>/g, ''); // Elimina las etiquetas HTML
    $.post("./plantkeeper/recolectores/modifica_recolectores.php", { id: id, accion_bbdd: 'eliminacion', csrf_token: $("input[name='csrf_token']").val() }, function (response) {
        tablaRecolectores.row(fila).remove().draw();
    });
}


function recolectarDatosDelFormulario() {
    return {
        nombre: $("#nombre").val(),
        ubicacion: $("#ubicacion").val(),
        tamano: $("#puerto").val(),
        csrf_token: $("input[name='csrf_token']").val(),
        accion_bbdd: 'ingreso'
    };
}

function enviarFormulario(datos) {
    $.post("./plantkeeper/recolectores/modifica_recolectores.php", datos, function (response) {
        agregarFilaATabla(datos);
        $("#formulario_nuevo_recolector")[0].reset();
    });
}

// Manejadores de eventos

$('#delete-recolector').on('click', function() {
    // Array para almacenar las filas que deben ser eliminadas
    var filasParaEliminar = [];

    // Recorre todas las filas de la tabla
    $('#recolectores-table tbody tr').each(function() {
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

$('#edit-recolector').on('click', function() {
    // Código para editar la recolector seleccionada
});

$("#formulario_nuevo_recolector").on("submit", function (e) {
    e.preventDefault();
    var datos = recolectarDatosDelFormulario();
    enviarFormulario(datos);
});

// Manejador de eventos para los checkboxes
$("#recolectores-table").on("change", "input[type='checkbox']", function() {
    // Comprueba si hay al menos un checkbox marcado
    var isCheckboxChecked = $("#recolectores-table input[type='checkbox']:checked").length > 0;

    // Activa o desactiva los botones en función de si hay un checkbox marcado
    $("#edit-recolector, #delete-recolector").prop("disabled", !isCheckboxChecked);
});

// Cargar datos desde el servidor
cargarRecolectores();

});

</script>