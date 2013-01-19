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
})(window.jQuery);

