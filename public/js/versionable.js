
$(function(){

    tttjs.versiones = {


        /**
		 * Constructor
		 */
        init: function(settings){
            //extendemos propiedades en el inicializador
            tttjs.versiones.$settings = $.extend(tttjs.versiones.$settings, settings);

            
            $(".selector_versiones").click(function()
                {
                   var id_version       = $(this).data("version");
                   var form_element     = $(this).data("formelement");
                   var nombre_modulo    = $(this).data("module");

                   if(id_version != -1)
                   {
                        $.getJSON(BASE_URL + nombre_modulo + '/version/' + id_version,
                                     function(data) {
                                        if(data){
                                            $("#" + form_element).val(data.valor_nuevo);
                                        }
                                     });
                    }else{
                        $("#" + form_element).val($(this).data("content"));
                    }
                });
        }
    };
});