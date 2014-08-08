<?php

return array(
    
    /**
     * Configuracion de Imagenes
     * 
     */
    
    'imagenes' => array(
            'validation' => 'mimes:jpeg,jpg,png,gif'
                ),
    
    /**
     * Todos los ficheros
     * 
     */
    
    'ficheros' => array(
            'validation' => 'mimes:*'
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
