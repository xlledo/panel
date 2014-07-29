$(function(){
    tttjs.categorias = {

        $settings: {
			url: BASE_URL
		},

        /**
		 * Constructor
		 */
        init: function(settings){
            //extendemos propiedades en el inicializador
            tttjs.categorias.$settings = $.extend(tttjs.categorias.$settings, settings);
			$('.dd').nestable().on('change',function(){
				    $.ajax({
					type: 'post',
					url: tttjs.categorias.$settings.url+"categorias/ordenar/",
					data: {data:window.JSON.stringify($(".dd").nestable('serialize'))},
					dataType: 'json',
					beforeSend: function(){
					},
					success: function(data){console.log(ok);}
				}); //close $.ajax(

			});
        }
    };
});
