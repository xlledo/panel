<?php

return array(
    
    'tipos' => array( 
            /**
             * Configuracion de Imagenes
             * 
             */

        'imagen' => array(
                'validation' => 'mimes: jpeg, bmp, png, jpg',
                'desc' => 'jpeg, jpg, gif, png'
                    ),

            /**
             * Todos los ficheros
             * 
             */

        'fichero' => array(
                'validation' => 'mimes:*',
                'desc' => 'Todo tipo de ficheros'
                    ),
            ),
    
    /**
     * 
     * Opciones comunes
     * 
     */
    
    'common'    => array(
            'upload_folder' => 'uploads/',
            'size'          => 2048
                )
);
