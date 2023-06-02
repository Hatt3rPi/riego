<?php
session_start();
?>
<!-- Contenido de la página recolectores -->
<h2>Relaciones Planta-Pines</h2>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

<table id="recolectores-table" class="table table-striped table-bordered" style="width:100%">
    <thead>
        <tr>
            <th></th> <!-- Columna de checkbox -->
            <th>id</th>
            <th>nombre</th>
            <th>ubicacion</th>
            <th>sensor_humedad</th>
            <th>bomba_agua</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<script>
$(document).ready(function() {

    var tablaRecolectores = $("#recolectores-table").DataTable({
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
        { width: "5%" }, // id
        { width: "35%" }, // nombre
        { width: "25%" }, // ubicacion
        { width: "15%" }, // humedad_sustrato
        { width: "15%" } // bomba_agua

    ],
    language: {
        url: "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
    }
});
var listadoPines;
function agregarFilaATabla(recolector) {
    tablaRecolectores.row.add([
        '<input type="checkbox" name="id[]" value="' + recolector.id + '">',
        recolector.id,
        recolector.especie,
        recolector.ubicacion,
        crearSelector(recolector.humedad_sustrato, 'humedad_sustrato'),
        crearSelector(recolector.bomba_agua, 'bomba_agua')
    ]).draw();
}

function cargarRecolectores() {
    $.getJSON("./plantkeeper/sensores/relaciones_plantas.php", function (data) {
        console.log(data)
        for (let recolector of data) {
            agregarFilaATabla(recolector);
        }
    });
}


function crearOpcion(valor, etiqueta, desactivado, tachado, seleccionado) {
    let prefijo = "";
    if (desactivado) {
        prefijo = "🚫 ";
    } else if (tachado) {
        prefijo = "⛔ ";
    } else {
        prefijo = "✅ ";
    }
    if (seleccionado) {
        prefijo = "👍 ";
    }
    return '<option value="' + valor + '"' + 
           (desactivado ? ' disabled' : '') + 
           (seleccionado ? ' selected' : '') + 
           '>' + prefijo + etiqueta + '</option>';
}


function cargarPines() {
    return $.getJSON("./plantkeeper/sensores/listado_pines.php", function (data) {
        listadoPines = data;
    });
}
function crearSelector(pinSeleccionado, tipo_sensor) {
    var selectHtml = '<select class="form-control">';
    
    // Opción predeterminada
    selectHtml += '<option value=""' + (pinSeleccionado ? '' : ' selected') + '>Selecciona un PIN</option>';

    for (let pin of listadoPines) {
        var desactivado = pin.tipo_sensor !== tipo_sensor;
        var tachado = pin.tipo_sensor === tipo_sensor && pin.estado === "activo";
        var seleccionado = pin.pin === pinSeleccionado;

        selectHtml += crearOpcion(pin.pin, pin.pin, desactivado, tachado, seleccionado);
    }
    selectHtml += '</select>';
    return selectHtml;
}


cargarPines().done(function() {
        cargarRecolectores();
    });

});

</script>