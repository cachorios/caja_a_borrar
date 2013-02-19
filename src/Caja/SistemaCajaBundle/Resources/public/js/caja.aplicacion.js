/**
 * Created with JetBrains PhpStorm.
 * User: cacho
 * Date: 17/01/13
 * Time: 14:07
 * To change this template use File | Settings | File Templates.
 */
!(function($){


    $("a[rel=popover]")
        .popover({
            trigger: 'hover',
            html:true,
            placement:"top",
            offset:5,
            delay:{ show:500, hide:100 },
           // template:'<div class="popover"><div class="popover-inner"><h2 class="popover-title"></h2><div class="popover-content"><div></div></div></div></div>',
            title:function () {
                return $(this).html();
            },
            content:function () {
                var contenido = $("#"+$(this).data('idcontenido')).html();

                return '<p>' + contenido ;
            }
        })
        .click(function(e) {
            e.preventDefault();
        });

//    $("a[rel=popover]")
//        .popover({
//            trigger: 'hover'
//        })
//        .click(function(e) {
//            e.preventDefault()
//        })

})(window.jQuery);

/*
 * Da formato a un número para su visualización
 *
 * numero (Number o String) - Número que se mostrará
 * decimales (Number, opcional) - Nº de decimales (por defecto, auto)
 * separador_decimal (String, opcional) - Separador decimal (por defecto, coma)
 * separador_miles (String, opcional) - Separador de miles (por defecto, ninguno)
 */
function formato_numero(numero, decimales, separador_decimal, separador_miles){ // v2007-08-06
    numero=parseFloat(numero);
    if(isNaN(numero)){
        return "";
    }

    if(decimales!==undefined){
        // Redondeamos
        numero=numero.toFixed(decimales);
    }

    // Convertimos el punto en separador_decimal
    numero=numero.toString().replace(".", separador_decimal!==undefined ? separador_decimal : ",");

    if(separador_miles){
        // Añadimos los separadores de miles
        var miles=new RegExp("(-?[0-9]+)([0-9]{3})");
        while(miles.test(numero)) {
            numero=numero.replace(miles, "$1" + separador_miles + "$2");
        }
    }

    return numero;
}