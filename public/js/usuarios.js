$(function(){

    tttjs.usuarios = {

        $settings: {
            vista: 'listado',
            currentGroupPermission: {},
            inited: false
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
                
                $('#permissionBox').on('change', 'input.radioPermission:radio', function (e) {
                    //alert($(this).attr('id'));
                    tttjs.usuarios.updateLinesPermission();
                });
            }
        },
        togglePermisos: function(){
            var grupo_seleccionado = $('#grupo option:selected').val();
            if(grupo_seleccionado == '1'){
                $('#permissionBox').hide();
                if(tttjs.usuarios.$settings.inited)
                {
                    tttjs.usuarios.$settings.currentGroupPermission = {};
                    tttjs.usuarios.updateRadiosPermission();
                }
            }else{
                $('#permissionBox').show();
                if(tttjs.usuarios.$settings.inited)
                {
                    if(grupo_seleccionado == '')
                    {
                        //reseteamos todos los radios
                        tttjs.usuarios.$settings.currentGroupPermission = {};
                        tttjs.usuarios.updateRadiosPermission();
                    }else{
                        //debemos hacer una petición para recuperar los permisos que tiene el grupo que se ha seleccionado
                        var url = BASE_URL + 'grupos/permisos';//recogemos los permisos del grupo que se ha seleccionado
                        $.ajax({
                            type: 'post',
                            url: url,
                            dataType: 'json',
                            data: {
                                id: grupo_seleccionado
                            },
                            success: function( data ) {
                                var datos = eval(data);
                                if(datos.error)
                                {
                                    bootbox.alert(datos.mensaje);
                                }else{
                                    //console.log(JSON.stringify(data));
                                    tttjs.usuarios.$settings.currentGroupPermission = data.permission;
                                    tttjs.usuarios.updateRadiosPermission();
                                }
                            }
                        });
                    }
                }else{
                    tttjs.usuarios.$settings.inited = true;
                    tttjs.usuarios.updateLinesPermission();
                }
            }
        },
        updateRadiosPermission: function()
        {
            $('input.radioPermissionNo').prop('checked', true);
            $.each(tttjs.usuarios.$settings.currentGroupPermission, function(indx, vlue) {
                var idCheck = indx.replace( /::/g, "_" );
                //alert(idCheck);
                $('input#' + idCheck + '_si').prop('checked', true);
            });
            
            //marcamos las líneas que procedan para saber que permiso es personalizado y cual del grupo
            tttjs.usuarios.updateLinesPermission();
        },
        updateLinesPermission: function()
        {
            $('tr.lineaPermiso').removeClass('alert-danger');
            $('tr.lineaPermiso').removeClass('alert-success');
            var grupo_seleccionado = $('#grupo option:selected').val();
            if(grupo_seleccionado != '' && grupo_seleccionado != '1')
            {
                $.each($('input.radioPermission:radio'), function()
                {
                    if($(this).is(':checked'))
                    {
                        var nameRadio = $(this).attr('name');
                        var valRadio = $(this).val();
                        var idCheck = nameRadio.replace( /::/g, "_" );
                        if(valRadio == 'si')
                        {
                            //si el permiso existe en la pila de tttjs.usuarios.$settings.currentGroupPermission, ES DEL GRUPO
                            if(tttjs.usuarios.$settings.currentGroupPermission.hasOwnProperty(nameRadio))
                            {
                                $('tr#linea_' + idCheck).addClass('alert-success');
                            }else{
                                $('tr#linea_' + idCheck).addClass('alert-danger');
                            }
                        }else{
                            //si el permiso NO existe en la pila de tttjs.usuarios.$settings.currentGroupPermission, ES DEL GRUPO
                            if(! tttjs.usuarios.$settings.currentGroupPermission.hasOwnProperty(nameRadio))
                            {
                                $('tr#linea_' + idCheck).addClass('alert-success');
                            }else{
                                $('tr#linea_' + idCheck).addClass('alert-danger');
                            }
                        }
                    }
                });
            }
        }
    };
});
