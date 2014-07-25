$(function(){

    tttjs.usuarios = {

        $settings: {
            vista: 'listado'
        },
        /**
		 * Constructor
		 */
        init: function(settings){
            //extendemos propiedades en el inicializador
            tttjs.usuarios.$settings = $.extend(tttjs.usuarios.$settings, settings);
            
            if(tttjs.usuarios.$settings.vista == 'edicion'){

                tttjs.usuarios.togglePermisos();

                $('#grupo').on('change', tttjs.usuarios.togglePermisos);
            }
        },
        togglePermisos: function(){
            var grupo_seleccionado = $('#grupo option:selected').val();
            if(grupo_seleccionado == '1'){
                $('#permissionBox').hide();
            }else{
                $('#permissionBox').show();
            }
        }
    };
});
