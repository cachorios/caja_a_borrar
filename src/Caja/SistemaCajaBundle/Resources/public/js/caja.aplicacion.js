/**
 * Created with JetBrains PhpStorm.
 * User: cacho
 * Date: 17/01/13
 * Time: 14:07
 * To change this template use File | Settings | File Templates.
 */
!(function ($) {


    $("a[rel=popover]")
        .popover({
            trigger: 'hover',
            html: true,
            placement: "top",
            offset: 5,
            delay: { show: 500, hide: 100 },
            // template:'<div class="popover"><div class="popover-inner"><h2 class="popover-title"></h2><div class="popover-content"><div></div></div></div></div>',
            title: function () {
                return $(this).html();
            },
            content: function () {
                var contenido = $("#" + $(this).data('idcontenido')).html();

                return '<p>' + contenido;
            }
        })
        .click(function (e) {
            e.preventDefault();
        });

//    $("a[rel=popover]")
//        .popover({
//            trigger: 'hover'
//        })
//        .click(function(e) {
//            e.preventDefault()
//        })

    //Evitar el enter
    var keyStop = {
        8: ":not(input:text, textarea, input:file, input:password)", // stop backspace = back
        13: "input", // stop enter = submit
        //13: "input:number,input:text, input:password", // stop enter = submit

        end: null
    };
    $(document).bind("keypress", function (event) {
        var selector = keyStop[event.which];


        if (selector !== undefined && $(event.target).is(selector)) {
            event.preventDefault(); //stop event
            //return false;
        }
        return true;
    });

    $(".ayuda a").click(function (e) {
        e.preventDefault();

        $.ajax(
            {   url: $(this).data("ayuda"),
                async: true,
                dataType: "html"})
            .success(function (dato,a,b){

                var $html = $( dato );
                var $html = (( $html.children('body').length ) ? $html.children('body').contents() :
                    ( $html.filter('body').length ) ? $html.filter('body').contents() :
                        $html).not('style,title,script,head,meta');

                $(".modal-body","#myHelp").html($html);
                $("#myHelp").modal();

            })
            .error(function () {
                contenido.html("No existe la pagina");
            })
        ;


    })
//    $("form").keypress(function(e) {
//        if (e.which == 13) {
//            return false;
//        }
//    });

})(window.jQuery);


/*
 * Da formato a un número para su visualización
 *
 * numero (Number o String) - Número que se mostrará
 * decimales (Number, opcional) - Nº de decimales (por defecto, auto)
 * separador_decimal (String, opcional) - Separador decimal (por defecto, coma)
 * separador_miles (String, opcional) - Separador de miles (por defecto, ninguno)
 */
function formato_numero(numero, decimales, separador_decimal, separador_miles) { // v2007-08-06
    numero = parseFloat(numero);
    if (isNaN(numero)) {
        return "";
    }

    if (decimales !== undefined) {
        // Redondeamos
        numero = numero.toFixed(decimales);
    }

    // Convertimos el punto en separador_decimal
    numero = numero.toString().replace(".", separador_decimal !== undefined ? separador_decimal : ",");

    if (separador_miles) {
        // Añadimos los separadores de miles
        var miles = new RegExp("(-?[0-9]+)([0-9]{3})");
        while (miles.test(numero)) {
            numero = numero.replace(miles, "$1" + separador_miles + "$2");
        }
    }

    return numero;
}

/**
 * Genera retardo
 * @param millis
 */
function pausa(millis)
{
    var date = new Date();
    var curDate = null;
    do { curDate = new Date(); }
    while(curDate-date < millis);
}

/**
 * Manda a la impresora fiscal.
 * @param data
 */

imprimir_Serial = function (data) {
    var pser = document.SerialLar;
    var maxChar = 80;
    try {
        //Particiono la variable tk recibida, para evitar el llenado del buffer:
        for (var i = 0; i < data.length; i += maxChar) {
            //t = new Date().getTime();
//            while ((new Date().getTime()) - t < maxTime) {
//                //alert('timer');
//            }
            wait(500);

            pser.escribir(data.substr(i, maxChar));
        }
        $.unblockUI();
    } catch (e) {
        $.unblockUI();
        alert('Error de tipo: ' + e);
    }
}

wait = function(tiempo) {
    var t;
    t = new Date().getTime();
    while ((new Date().getTime()) - t < tiempo) {

    }

}
