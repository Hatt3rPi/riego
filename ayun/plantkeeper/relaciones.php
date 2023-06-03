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
            <th>ID</th>
            <th>Nombre</th>
            <th>Ubicación</th>
            <th>Sensor Humedad</th>
            <th>Bomba Agua</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<script>
$(document).ready(function() {

    function verificarPinSeleccionado(selectElement) {
    var selectedPin = selectElement.value;

    // Verificar si el pin seleccionado ya está seleccionado por otra planta
    var pinSeleccionadoPorOtraPlanta = false;
    tablaRecolectores.column(4).nodes().each(function(cell, index) {
        if (cell.querySelector('select').value === selectedPin) {
            var rowData = tablaRecolectores.row(index).data();
            if (rowData[1] !== selectedPin) {
                pinSeleccionadoPorOtraPlanta = true;
                return false; // Salir del bucle
            }
        }
    });

    if (pinSeleccionadoPorOtraPlanta) {
        // Mostrar advertencia y solicitar confirmación
        if (!confirm('Este pin ya está seleccionado por otra planta. ¿Deseas continuar?')) {
            // Revertir la selección del pin
            selectElement.value = '';
        }
    }
}
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
    // Obtiene todas las posibles ubicaciones (asumiendo que son las ubicaciones existentes en la tabla)
var ubicaciones = [];
tablaRecolectores.column(3).data().unique().sort().each(function(d, j) {
    ubicaciones.push('<option value="' + d + '">' + d + '</option>');
});

// Añade el select al HTML
$('#recolectores-table_wrapper').prepend('<div id="ubicacion-filter">Ubicacion: <select id="ubicacion-search"><option value="">Todas las ubicaciones</option>' + ubicaciones.join('') + '</select></div>');

// Agrega el evento 'change' al select
$('#ubicacion-search').on('change', function() {
    // Busca en la 4ta columna (0-indexada, por lo tanto "3" para la columna "ubicacion")
    tablaRecolectores.column(3).search(this.value).draw();
});
var listadoPines;



function agregarFilaATabla(recolector) {
    var selectHtml = crearSelector(recolector.humedad_sustrato, 'humedad_sustrato');

    // Agrega el evento "change" al selector de pines
    selectHtml = selectHtml.replace('<select', '<select onchange="verificarPinSeleccionado(this)"');

    tablaRecolectores.row.add([
        '<input type="checkbox" name="id[]" value="' + recolector.id + '">',
        recolector.id,
        recolector.especie,
        recolector.ubicacion,
        selectHtml,
        crearSelector(recolector.bomba_agua, 'bomba_agua')
    ]).draw();
}


function cargarRecolectores() {
    $.getJSON("./plantkeeper/sensores/relaciones_plantas.php", function (data) {
        //console.log(data);
        tablaRecolectores.clear().draw(); // Limpiar la tabla antes de agregar las filas

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