(function($){
    var defaults = {
        //Sufijo para el control renombrado
        nameSuffix: 'autonumeric',
        //Sufijo para el control oculto creado dinámicamente
        idSuffix: 'autonumeric',
        //Nombre de clase para el control renombrado, que es el que se ve
        ctrlClass: null,
        //Estilo en línea para el control renombrado, que es el que se ve
        ctrlStyle: {
            'text-align': 'right'
        }
    };
    
    function isEmpty(value) {
        return typeof (value) == 'undefined' || value == null;
    }
    
    var methods = {
        init: function(options){
            return this.each(function(){
                var $this = $(this);
                var settings = $this.data('autonumericExtended');
                if (settings) {
                    $.error('`autonumericExtended` ya está inicializado y no se puede inicializar 2 veces.');
                }else{
                    //llamamos al padre, que es al que extendemos
                    $this.autoNumeric(options);
                    
                    //establecemos la configuración
                    settings = $.extend({}, defaults, options);
                    
                    if(! isEmpty(settings.ctrlClass)){
                        $this.addClass(settings.ctrlClass);
                    }
                    if(! isEmpty(settings.ctrlStyle)){
                        $this.css(settings.ctrlStyle);
                    }
                    
                    var nombreOriginal = $this.attr('name');
                    var nuevoNombre = nombreOriginal + '_' + settings.nameSuffix;//para el control renombrado
                    var nuevoID = nombreOriginal + '_' + settings.idSuffix;//para el control creado
                    var valorDecimal = $this.autoNumeric('get');

                    var nuevoInput = $('<input type="hidden" id="' + nuevoID + '" name="' + nombreOriginal + '" value="' + valorDecimal + '" />');
                    $this.attr('name', nuevoNombre);
                    $this.after(nuevoInput);
                    
                    //cada vez que cambie el valor del control renombrado, actualizamos el creado
                    $this.bind('blur focusout keypress keyup', function () {
                        var demoGet = $(this).autoNumeric('get');
                        $(nuevoInput).val(demoGet);
                    });
                    
                    //guardamos configuración
                    $this.data('autonumericExtended', settings);
                }
            });
        },
        options: function(valor){
            var target = this;
            if (target.length == 1) {
                var $this = $(target[0]);
                var settings = $this.data('autonumericExtended');
                if (settings && ! isEmpty(settings[valor])) {
                    return settings[valor];
                }else{
                    $.error('`autonumericExtended` no está inicializado');
                }
            }else{
                $.error('No puede obtener el valor en múltiples elementos');
            }
        }
    };
    $.fn.autonumericExtended = function() {
        
        // Grab our method, sadly if we used function(method){}, it ruins it all
        var method = arguments[0];

        // Check if the passed method exists
        if(methods[method]) {
            // If the method exists, store it for use
            // Note: I am only doing this for repetition when using "each()", later.
            method = methods[method];

            //eliminamos el nombre del método de los argumentos
            arguments = Array.prototype.slice.call(arguments, 1);

        // If the method is not found, check if the method is an object (JSON Object) or one was not sent.
        } else if( typeof(method) == 'object' || !method ) {
                // If we passed parameters as the first object or no arguments, just use the "init" methods
                method = methods.init;
        } else {
            try {
                return $.fn.autoNumeric.apply(this, arguments);
            } catch (e) {
                $.error(e);
                // Not a method and not parameters, so return an error.  Something wasn't called correctly.
//                $.error( 'Method ' +  method + ' does not exist on jQuery.autonumericExtended' );
                return this;
            }
        }

        // Call our selected method
        // Once again, note how we move the "each()" from here to the individual methods
        return method.apply(this, arguments);
    };
})( jQuery );