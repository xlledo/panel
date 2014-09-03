$(function(){
    tttjs.categorias = {

        $settings: {
			url: BASE_URL,
            clave: 'categorias'
		},

        /**
		 * Constructor
		 */
        init: function(settings){
            //extendemos propiedades en el inicializador
            tttjs.categorias.$settings = $.extend(tttjs.categorias.$settings, settings);
			$('.dd').nestable().on('change',function(){
                    //console.log({data:window.JSON.stringify($(".dd").nestable('serialize'))});
                    var allTree = window.JSON.stringify($(".dd").nestable('serialize'));
                    var rootId = $('#root_id').data('id');

				    $.ajax({
    					type: 'post',
    					url: tttjs.categorias.$settings.url + tttjs.categorias.$settings.clave + "/ordenar",
    					data: {
                            allTree: allTree,
                            root_id: rootId
                        },
    					dataType: 'json',
    					beforeSend: function(){
    					},
    					success: function(datos){
                            if(datos.error)
                            {
                                alert(datos.message);
                            }else{
                                alert(datos.message);
                            }
                        }
    				}); //close $.ajax(

			});
        }
    };
});
