
$(function(){

    tttjs.versiones = {


        /**
		 * Constructor
		 */
        init: function(settings){
            //extendemos propiedades en el inicializador
            tttjs.versiones.$settings = $.extend(tttjs.versiones.$settings, settings);

            
            $(".selector_versiones").click(function(event)
                {
                   
                   console.log('click versiones');
                    
                   var id_version       = $(this).data("version");
                   var form_element     = $(this).data("formelement");
                   var nombre_modulo    = $(this).data("module");
                   var es_tiny          = $(this).data("tinymce");

                   console.log('Es tiny -> ' + es_tiny);

                   if(id_version != -1)
                   {
                        $.getJSON(BASE_URL + nombre_modulo + '/version/' + id_version,
                                     function(data) {
                                        if(data){
                                            if(!es_tiny) //Los tiny no se pueden rellenar con .val
                                            {
                                                $("#" + form_element).val(data.valor_nuevo);
                                            }else{
                                                tinymce.get(form_element).setContent(data.valor_nuevo);
                                            }
                                        }
                                     });
                    }else{
                        //$("#" + form_element).val($(this).data("content"));
                        if(!es_tiny)
                        {
                            $("#" + form_element).val($(this).data("content"));
                        }else{
                            tinymce.get(form_element).setContent($(this).data("content"));
                        }
                        
                    }
                });
        }
    };
});