/**
 * Fichero que contiene los métodos básicos para el funcionamiento del panel de gestión
 */

/**
 * Metodo para el control de Seleccionar todos en los listados
 */
function select_all() {
    var form = $(this).parents('form:first');
    var items = form.find('.listado input.item:visible');
    var value = ($(this).prop('checked')) ? true : false;
    if (value){
        form.find('tbody tr').addClass('warning');
    }
    else {
        form.find('tbody tr').removeClass('warning');
    }
    items.prop('checked', value);
}

/**
 * Metodo para controlar el boton de select_all al seleccionar un item del listado
 */
function control_select_all(item) {
    var form = $(item).parents('form:first');
    if ($(item).prop('checked')) {
        $(item).parents('tr:first').addClass('warning');
        var items = form.find('.listado input.item:visible');
        var items_seleccionados = form.find('.listado input.item:visible:checked');
        if (items.length == items_seleccionados.length) {
            form.find('.select_all').prop('checked', true);
        }
    }
    else {
        $(item).parents('tr:first').removeClass('warning');
        form.find('.select_all').prop('checked', false);
    }
}

/**
 * Metodo para seleccionar un item al hacer click en el input
 */
function select_item() {
    control_select_all(this);
}

/**
 * Metodo para seleccionar un item al hacer click en una celda de la fila
 */
function select_item_fila(e) {
    // evitamos que se propague el evento (necesario cuando tenemos varios listados (ver)
    e.stopImmediatePropagation();
    var item = $(this).parents('tr:first').find('input.item:visible');
    var value = item.prop('checked') ? false : true;
    item.prop('checked', value);

    control_select_all(item);
}

/**
 * Metodo para ir al destino indicado
 */
function ir_a(dest) {
    var destino = (dest instanceof Object) ? dest.data.destino : dest;
    if (destino) {
        self.location = destino;
    }
}

/**
 * Metodo que elimina el valor por defecto del control al poner
 * el foco sobre el.
 */
function quita_valor_por_defecto(event) {
    var elemento = $(event.target);
    if (elemento.val() == elemento.attr('alt')) {
        elemento.val('');
    }
}

/**
 * Metodo que reestablece el valor por defecto del control al poner
 * el foco sobre el.
 */
function restablece_valor_por_defecto(event) {
    var elemento = $(event.target);
    var por_defecto = elemento.attr('alt');
    if (jQuery.trim(elemento.val()) == '') {
        elemento.val(por_defecto);
    }
}

/**
 * Metodo para configurar el autocompletar
 */
function autocompletar(datos) {
    var id = datos.id ? datos.id : 'autocompletar';
    $('#'+id).autocomplete({
        minLength: 2,
        //        appendTo: "#datos",
        //        position: { my : "right top", at: "right bottom" },
        source: function (request, response) {
            // configuramos los datos que se envian por post
            var datos_post = datos.datos_post ? datos.datos_post : {};
            datos_post.q = request.term;
            datos_post.id_aux = datos.id_aux;
            //console.log(datos);
            //console.log(request);
            //console.log(datos_post);

            // realizamos la llamada ajax
            $.ajax({
                type: 'post',
                url: datos.url,
                dataType: 'json',
                data: jQuery.param(datos_post),
                success: function( data ) {
                    if ( ! data || data.length == 0) {
                        var data = [{
                            id: -1,
                            label: 'No hay resultados',
                            value: ''
                        }];
                    }
                    // Pasamos los datos al metodo que trata la respuesta del autocompletar
                    response(data);
                }
            });
        },
        select: function (event, ui) {
            if (ui.item.id != -1) {
                seleccion_autocompletar(event, ui, datos);
            }
        }
    });
}

/**
 * Callback que se ejecuta al seleccionar un item de los sugeridos en el autocompletar
 */
function seleccion_autocompletar(e, ui, datos) {
    if (datos.callback) {
        datos.callback(e, ui, datos);
    }
    else {
        // si se indica destino vamos a la edicion
        if (datos.destino) {
            ir_a(datos.destino + ui.item.id + '/');
        }
    }
}

/**
 * Metodo para controlar la accion por lotes de los listados
 */
function accion_por_lotes(e) {
    // evitamos que se propague el evento (necesario cuando tenemos varios listados (ver)
    e.stopImmediatePropagation();
    // comprobamos la accion
    var accion = $(this).find('.selectAcciones select#acciones_por_lote').val();
    if (accion == 0) {
        alert('Debe seleccionar una acción.');
        return false;
    }

    // comprobamos que ha seleccionado un item
    var items_seleccionados = $(this).find('.listado input.item:visible:checked').length;
    if ( ! items_seleccionados) {
        alert('Debe seleccionar al menos un ítem del listado.');
        return false;
    }

    // comprobamos si es un borrado
    if (accion == 'accion_borrar') {
        bootbox.confirm('¿Seguro que desea eliminar los items seleccionados?', function(result){
            if(result){
                $('.listado').parents('form').unbind('submit', accion_por_lotes);
                //alert($('.listado').parents('form').serialize());
                $('.listado').parents('form').submit();
            }
        });
        return false;
    }else{
        return true;
    }
}

/**
 * Metodo para confirmar una accion
 */
function confirmar(e) {

			bootbox.confirm(
                                {
                                message: e.data.msg,
                                buttons: {
                                   confirm: {
                                        label: "Aceptar",
                                        className: "btn-success",
                                      },
                                      cancel: {
                                        label: "Cancelar",
                                        className: "btn-danger",
                                       }
                                    },
                                    callback:
                                        function(result){
                                        if(result){
                                                window.location =  e.data.destino;
                                        }
                                    }});
}

// Cambia estado de un item
function cambiar_estado(elemento, url_aplicacion_modulo){
    var container = elemento;
    //alert(container.text());
    if (container.attr('rel')=='off') {
        var msg='¿Desea activar este item?';
    } else {
        var msg='¿Desea desactivar este item?';
    }
    bootbox.confirm(
    {
        message: msg,
        buttons: {
            confirm: {
              label: "Aceptar",
              className: "btn-success",
            },
            cancel: {
              label: "Cancelar",
              className: "btn-danger",
            }
        },
        callback: function(result) {
            if(result){
            var array_id = elemento.attr('id').split("_");
            var id_element = array_id[array_id.length - 1];
            var params = {
                id: id_element
            }

            _cambiar_estado(container, params, url_aplicacion_modulo);
            }
    //return true;
        }
    });
}

// Cambia estado de un item
function cambiar_estado_relacionado(elemento, url_aplicacion_modulo){
    var container = elemento;
    //alert(container.text());
    if (container.attr('rel')=='off') {
        var msg='¿Desea activar este item?';
    } else {
        var msg='¿Desea desactivar este item?';
    }
    bootbox.confirm(msg,function(result) {
		if(result){
	      var array_id = elemento.attr('id').split("_");
        var id_elemento_destino = array_id[array_id.length - 1];
        var params = {
            referrer_item: $("#referrer_item").val(),
            referrer_modulo: $("#referrer_modulo").val(),
            id: id_elemento_destino
        }

        _cambiar_estado(container, params, url_aplicacion_modulo);

    }
//return true;
	});


}

function _cambiar_estado(container, parametros, url_aplicacion_modulo){
    var params = parametros;
    //eliminamos información del contendor
    container.removeClass('activo');
    container.removeClass('noActivo');
    container.removeAttr('rel');
    container.text('Cargando');

    var src,path;

    $.ajax({
        type: 'post',
        url: url_aplicacion_modulo,
        data: params,
        dataType: 'json',
        beforeSend: function(){
            container.addClass('cargando');
        }, // Icono procesando
        success: function(data){ // Guardamos en data el resultado
            datos=eval(data);
            container.removeClass('cargando');

            //extraemos el path a las imagenes del elemento
            if(datos.error)
            {
                alert(datos.message);
            }else{
                if(datos.visible == 1){
                    container.attr('rel', 'on');
                    container.attr('title', 'Desactivar elemento');
                    container.addClass('activo');
                    if(container.is("input")){
                        container.prop('checked', true);
                    }
                }else{
                    container.attr('rel', 'off');
                    container.attr('title', 'Activar elemento');
                    container.addClass('noActivo');
                    if(container.is("input")){
                        container.prop('checked', false);
                    }
                }
            }
        }
    }); //close $.ajax(
}

/**
 * Metodo para activar la pestanya correcta segun la 'ruta' indicada en el hash de la url
 */
function comprueba_ruta_hash() {
    // si hay hash
    if (location.hash) {
        // obtenemos la ruta
        var ruta = location.hash;
        rutas = ruta.split('-');

        var p1 = rutas[0].substring(1); // pestanya exterior
        var p2 = rutas[1]; // pestanya interior

        // activamos la pestanya
        t.tabs('select', p1);
        tI.tabs('select', p1 + '-' + p2);

        // comprobamos si es una nueva traduccion en un idioma
        if (p2 == 'nueva_traduccion' && rutas.length == 3) {
            $('#lang').val(rutas[2]);
        }
    }
}

/**
 * Callback que se encarga de ocultar los checkbox de la clase indicada como parametro.
 */
function ocultar_checkbox_listado(e) {
    var clase = e.data.clase;
    var acciones = e.data.acciones ? e.data.acciones : ['accion_borrar', 'accion_quitar'] ;
    var ocultar = false;

    // comprobamos si debemos ocultar o mostrar
    for (var a in acciones) {
        if ($(this).val() == acciones[a]) {
            ocultar = true;
        }
    }

    var form = $(this).parents('form:first');

    // ejecutamos o no dependiendo de si la accion se encuentra entre la pasadas como parametro
    if (ocultar) {
        // Comprobamos si todos son de sistema antes de ocultar, para ocultar tb el selector de todos
        if (form.find('.listadoElementos input[type="checkbox"].'+clase).length == form.find('.listadoElementos input[type="checkbox"]').length - 1) {
            form.find('.select_all').fadeOut();
        }
        form.find('.listadoElementos input[type="checkbox"].'+clase).prop('checked', false).fadeOut();
    }
    else {
        form.find('.listadoElementos input[type="checkbox"].'+clase).fadeIn();
        form.find('.select_all').prop('checked', false).fadeIn();
    }
}

/**
 * Metodo para realizar un envio de un formulario a traves de un enlace que se encuntra dentro
 * de dicho formulario. El 'action' del formulario para a ser el 'href' del enlace antes de
 * realizarse el envio.
 */
function submit_link(e) {
    e.preventDefault();
    var a = $(e.target);
    // Desactivamos la accion por lotes para que no salten los avisos
    a.parents('form:first').unbind('submit', accion_por_lotes);
    // Nota: si los enlaces tiene barra al final, quitar el +'/'
    a.parents('form:first').attr('action', a.attr('href')+'/').submit();
}

var tttjs = {};
jQuery(function($){

    $(document).on("click",'form#crud_permisos .selectAcciones input',function(e){
       e.preventDefault();
//        alert('hello');
        var form = $('form#crud_permisos');
        form.fadeOut(function(){

            var elemento_parrafo = $('<p></p>');
            var imagen_preload = $('<img>');
            imagen_preload.attr('src', BASE_URL + 'assets/gestion/images/ico_proceso.gif');
            elemento_parrafo.append(imagen_preload);

            form.parent().before(elemento_parrafo);

            tttjs.limpiaNotificacion();

            $.post(form.attr('action'), form.serialize(), function(data){
                form.fadeIn(function(){
                    elemento_parrafo.remove();
                    //añadimos la notificación al div correspondiente
                    $('#permisos').html(data.contenido);
                    var div_notificacion = $('<div id="notificacion"></div>');
                    var div_tipo_notificacion = $('<div class="' + data.mensaje.tipo + '"></div>');
                    div_tipo_notificacion.html('<p>' + data.mensaje.texto + '</p>');
                    div_notificacion.append(div_tipo_notificacion);
                    $('#migas').after(div_notificacion);
                });
            }, 'json');

        });
    });
    tttjs.init = function() {
        //controles de seleccion de items
        $(document).on("click",'.listado .select_all',select_all);
        $(document).on("click",'.listado .item',select_item);
        $(document).on("click",'.listado .td_click',select_item_fila);

		$(".submenu:has(.active)").show().parent().addClass("open");

//        $('.listado').parents('form').submit(accion_por_lotes);
        $('.listado').parents('form').bind('submit', accion_por_lotes);
        $('#filtros input[type=text], #autocompletar').focus(quita_valor_por_defecto);
        $('#filtros input[type=text], #autocompletar').blur(restablece_valor_por_defecto);

        $('#acciones_por_lote').change({
            clase: 'sistema'
        }, ocultar_checkbox_listado);
        $(".cambiar_estado, .cambiar_estado_relacionado").hover(function(){
            $(this).css("cursor", "pointer");
        });

        //tabs
        //$("#tabs").tabs();

        $('#tabs').tabs({
        select: function(event, ui) {
            var url = $.data(ui.tab, 'load.tabs');
            if( url ) {
                location.href = url;
                return false;
            }
            return true;
        }});
       $("#tabsI").tabs();

		//control de envio de formulario
        $('.submit_link').click(function(e){
            e.preventDefault();
            var a = $(this);
            // Desactivamos la accion por lotes para que no salten los avisos
            a.parents('form:first').unbind('submit', accion_por_lotes);
            if(a.attr('title') == 'Borrar' && ! confirm('¿Seguro que desea eliminar el elemento?')){
                return false;
            }
            // Nota: si los enlaces tiene barra al final, quitar el +'/'
//            a.parents('form:first').attr('action', a.attr('href')+'/').submit();
            a.parents('form:first').attr('action', a.attr('href')).submit();
        });



         //ocultar cliente
         $(".clienteOculto").hide();
         $("#cambiar").css("cursor","pointer");
         $("#cambiar").click(function(){
             $(".clienteOculto").slideToggle();

         });

		 //elementos disabled
		 //$('.fieldset :disabled').css("background-color","#ffffff");

		 //ocultar contraseña
		/*$("#cambiar").css("cursor","pointer");
                $("#cambiar").toggle(
                    function(){
                        $(".passOculto").slideToggle();
                        $(this).find('img').attr({src: BASE_URL +"assets/gestion/images/up.png"});
                    },
                    function() {
                        $(".passOculto").slideToggle();
                        $(this).find('img').attr({src: BASE_URL +"assets/gestion/images/down.png"});
                });*/

		//CAMPOS PRECIO MONEDA FUERA DE FORMULARIOS CON OPERACIONES
//		$("fieldset .precioMoneda").blur(function(){
//			 $(this).formatCurrency('es');
//		});
		//antes de enviar un form con campo precioMoneda pasamos a formato inglés
		/*
		$("form:has(.precioMoneda)").submit(function(e){
                    $('.precioMoneda').formatCurrency('en');
			 return true;
		});
		*/
                $('.precioMoneda').autonumericExtended('init', {aSep: '.', aDec: ',', aSign: '€', pSign:'s'});
		 //bootstrap
		$('[data-rel=popover]').popover({placement:"auto left",container:'body'});
		bootbox.setDefaults({locale: "es"});
		 $("input[type=file]").ace_file_input({
					no_file:'Sin archivo',
					btn_choose:'Elegir',
					btn_change:'Cambiar',
					droppable:false,
					onchange:null,
					thumbnail:false //| true | large
					//whitelist:'gif|png|jpg|jpeg'
					//blacklist:'exe|php'
					//onchange:''
					//
				});


    }
    tttjs.limpiaNotificacion = function (){
        $("#notificacion").remove();
    }
    tttjs.cancelEvent = function (evento){
        if (evento && evento.preventDefault)evento.preventDefault();
        return false;
    }
    tttjs.doOcultoDependiente = function (datos){
        var datos = datos.data ? datos.data : datos;
//        alert(datos.selectorCondicionante);

        if($('#' + datos.selectorCondicionante).val() == datos.valor){
            $('#' + datos.selectorCondicionadoUno).parents('label:first').show('slow');
            $('#' + datos.selectorCondicionadoDos).parents('label:first').hide('slow');
        }else{
            $('#' + datos.selectorCondicionadoUno).parents('label:first').hide('slow');
            $('#' + datos.selectorCondicionadoDos).parents('label:first').show('slow');
        }
        $('#' + datos.selectorCondicionante).unbind('change', tttjs.doOcultoDependiente);
        $('#' + datos.selectorCondicionante).change({
                valor: datos.valor,
                selectorCondicionante: datos.selectorCondicionante,
                selectorCondicionadoUno: datos.selectorCondicionadoUno,
                selectorCondicionadoDos: datos.selectorCondicionadoDos
            },
            tttjs.doOcultoDependiente
        );
    }
    tttjs.doOculto = function (sel,ocultar){
        if(ocultar){
            $(sel).hide();
            $(sel).prev().children("span").toggleClass("open close");
        }
        $(sel).prev().css("cursor","pointer").attr("title","Desplegar").click(function(){
            $(this).next(sel).slideToggle("slow");
            $(this).children("span").toggleClass("open close");
        });
    }
    tttjs.limitaCaracteres = function(e){
        var datos = e.data ? e.data : e;
        var container = $(this);
        var texto = container.val();
        if(texto.length > datos.limite) {
            texto = texto.substring(0, datos.limite);
            container.val(texto);
	}
    }
    tttjs.soloNumeros = function(e){
        var datos = e.data ? e.data : e;

        if( !(e.keyCode == 8                                // Botón borrar encima del intro
            || e.keyCode == 9                              // Tabulador
            || e.keyCode == 46                              // Botón delete
            || (e.keyCode >= 35 && e.keyCode <= 40)     // teclas de las flechas
            || (e.keyCode >= 48 && e.keyCode <= 57)     // números en el teclado normal
            || (e.keyCode >= 96 && e.keyCode <= 105))   // números teclado numérico
            ) {
            e.preventDefault();
            return false;
        }
    }
    tttjs.soloNumerosDecimales = function(e){
        var datos = e.data ? e.data : e;

        if( !(e.keyCode == 8                                // Botón borrar encima del intro
            || e.keyCode == 9                              // Tabulador
            || e.keyCode == 46                              // Botón delete
            || e.keyCode == 188                              // comma
            || (e.keyCode >= 35 && e.keyCode <= 40)     // teclas de las flechas
            || (e.keyCode >= 48 && e.keyCode <= 57)     // números en el teclado normal
            || (e.keyCode >= 96 && e.keyCode <= 105))   // números teclado numérico
            ) {
            //si se permite el punto decimal y no es dicho caracter lanzamos error
            if(! datos.permitir_punto_decimal){
                e.preventDefault();
                return false;
            }
            else{
                if(e.keyCode != 190){
                    e.preventDefault();
                    return false;
                }
            }
        }
    }
    tttjs.soloHorasMinutos = function(e){
        //alert(e.shiftKey && e.keyCode != 190);
        if( !(e.keyCode == 8                                // Botón borrar encima del intro
            || e.keyCode == 9                              // Tabulador
            || e.keyCode == 46                              // Botón delete
            || (e.keyCode >= 35 && e.keyCode <= 40)     // teclas de las flechas
            || (e.keyCode >= 48 && e.keyCode <= 57)     // números en el teclado normal
            || (e.keyCode >= 96 && e.keyCode <= 105)   // números teclado numérico
            || (e.keyCode == 190)         // 2 puntos Firefox
            || (e.shiftKey && e.keyCode != 190))         // 2 puntos Chrome
            ) {
            e.preventDefault();
            return false;
        }
    }




    $(document).ready(function() {
        tttjs.init();
    });
});
