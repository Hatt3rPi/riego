$(document).ready(function () {
    // Carga el contenido de la página principal al cargar la página
    loadContent("principal.php");
    $('.sidebar a[href="principal.php"]').addClass('active');

    // Manejar clics en los enlaces del menú
    $(".sidebar a").on("click", function (e) {
        e.preventDefault();
        var url = $(this).attr("href");
        loadContent(url);

        // Actualizar la clase 'active'
        $(".sidebar a").removeClass("active");
        $(this).addClass("active");
    });
});

function loadContent(url) {
    $.ajax({
        url: url,
        type: "GET",
        dataType: "html",
        success: function (response) {
            $(".content").html(response);
            
        },
        error: function () {
            $(".content").html("<p>Error al cargar el contenido.</p>");
        },
    });
}
