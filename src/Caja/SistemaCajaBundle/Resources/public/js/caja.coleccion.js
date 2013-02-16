/**
 * Created with JetBrains PhpStorm.
 * User: cacho
 * Date: 15/01/13
 * Time: 19:31
 * To change this template use File | Settings | File Templates.
 */

 
!function ($) {

    "use strict";


    $.fn.coleccion = function (opcion) {

        return this.each(function () {

            var $this = $(this),
                coleccion_id = $this.data('coleccion-add-btn'),
                data = $this.data('coleccion'),
                options = typeof opcion == 'object' ? opcion : {};
				
			opcion = typeof opcion == 'object' ? opcion.opcion : opcion;
				
            if (coleccion_id) {
                options.coleccion_id = coleccion_id;
            }
            else if ($this.closest(".control-group").attr('id')) {
                options.coleccion_id = '#' + $this.closest(".control-group").attr('id');
            }
            else {
                options.coleccion_id = this.id.length === 0 ? '' : '#' + this.id;
            }


            if (!data) {
                $this.data('coleccion', (data = new Coleccion(this, options)));
            }


            if (opcion == 'add') {
                data.add();
            }
            if (opcion == 'remove') {
                data.remove();
            }
        });
    };

	$.fn.coleccion.defaults = {
        coleccion_id:null,
        addcheckfunc:false,
        addfailedfunc:false,
		addpostfunc:false,
		rempostfunc:false
    };
	
	var Coleccion = function (element, options) {
		
        this.$element = $(element);
        this.options = $.extend({}, $.fn.coleccion.defaults, options);
		
        var embeddedForms = 'div' + this.options.coleccion_id +  ' .collection table tbody tr';
        this.options.index = $(embeddedForms).length - 1;
    };
	
    Coleccion.prototype = {
        constructor: Coleccion,
        add:function () {
            this.options.index = this.options.index + 1;
            var index = this.options.index;
            if ($.isFunction(this.options.addcheckfunc) && !this.options.addcheckfunc()) {
                if ($.isFunction(this.options.addfailedfunc)) {
                    this.options.addfailedfunc();
                }
                return;
            }
            this.addPrototype(index);
        },
        addPrototype:function (index) {

            var rowContent = $(".collection","div"+this.options.coleccion_id).attr('data-prototype').replace(/__name__/g, index);
            var row = $(rowContent);
            $('div' + this.options.coleccion_id +  ' .collection table tbody').append(row);
            //$('div' + this.options.coleccion_id + '> .controls').append(row);
            //$(this.options.coleccion_id).trigger('add.mopa-collection-item', [row]);
			if ($.isFunction(this.options.addpostfunc) ) {
				this.options.addpostfunc(index);
			}
				
        },
        remove:function () {
            if (this.$element.parents('tr').length !== 0) {
                var row = this.$element.closest('tr');

                row.find("td").fadeOut(600, function (){
                    row.remove();
                });
				if ($.isFunction(this.options.rempostfunc) ) {
					this.options.rempostfunc();
				}
				
                //  $(this.options.coleccion_id).trigger('remove.mopa-collection-item', [row]);
            }
        }

    }
	
	
	
   

	/***
	*/
    $.fn.coleccion.Constructor = Coleccion;

    $(function () {
		
        $('body').on('click', '[data-coleccion-add-btn]', function (e) {
            var $btn = $(e.target);
            if (!$btn.hasClass('btn')) {
                $btn = $btn.closest('.btn');
            }
            $btn.coleccion('add');
            e.preventDefault();
        });


		$('body').on('click', '[data-coleccion-remove-btn]', function ( e ) { 
             var $btn = $(e.target);
             if (!$btn.hasClass('btn')){
                $btn = $btn.closest('.btn');
             }
             $btn.coleccion('remove');
             e.preventDefault();
		});

    });
}(window.jQuery);


