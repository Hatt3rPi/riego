<?php
session_start();
?>
<!-- Contenido de la página plantas -->
<h2>Lista de Plantas</h2>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

<div id="buttons">
    <button id="new-plant" class="btn btn-outline-success-sm" data-toggle="modal" data-target="#modalNuevaPlanta">Nueva Planta</button>
    <button id="edit-plant" class="btn btn-outline-secondary-sm"><i class="fas fa-pencil-alt"></i></button>
    <button id="delete-plant" class="btn btn-outline-danger-sm"><i class="fas fa-trash"></i></button>
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
            <th>Acciones</th>
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
          <!-- El resto de tu formulario va aquí -->
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
            targets: [7], // Índice de la columna ID en base cero
            visible: false,
            searchable: false
        }
    ],
   
    columns: [
        { width: "5%" }, // Checkbox
        { width: "29%" }, // Especie
        { width: "14%" }, // Ubicación
        { width: "14%" }, // Humedad mínima
        { width: "14%" }, // Humedad máxima
        { width: "14%" }, // Macetero
        { width: "10%" }, // Acciones
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
        planta.especie,
        planta.ubicacion,
        planta.humedad_sustrato_minima + '%',
        planta.humedad_sustrato_maxima + '%',
        planta.tamano + ' cm',
        '<button class="btn btn-outline-secondary-sm"><i class="fas fa-pencil-alt"></i></button> <button class="btn btn-outline-danger-sm"><i class="fas fa-trash"></i></button>',
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

$("#plantas-table tbody").on("click", ".btn-danger", function() {
    eliminarFilaDeTabla($(this).parents("tr"));
});
$('#edit-plant').on('click', function() {
    // Código para editar la planta seleccionada
});

$('#delete-plant').on('click', function() {
    // Código para eliminar la planta seleccionada
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