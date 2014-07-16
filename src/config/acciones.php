<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Acciones para la gestión de módulos
	|--------------------------------------------------------------------------
	|
	| Se mappean las acciones del módulo con las diferentes urls que pueden llegar Request::segment(3)
	|
	*/

    'modulos' => array(
        'crear'  => array('nuevo', 'crear'),
        'editar' => array('ver', 'actualizar', 'visible', 'noVisible', 'cambiar_estado'),
        'listar' => array('index'),
        'borrar' => array('borrar', 'delete')
    ),

);
