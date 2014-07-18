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
    'grupos' => array(
        'listar' => array('index'),
        'crear'  => array('nuevo', 'crear'),
        'editar' => array('ver', 'actualizar'),
        'borrar' => array('borrar')
    ),
    'modulos' => array(
        'listar' => array('index'),
        'crear'  => array('nuevo', 'crear'),
        'editar' => array('ver', 'actualizar', 'accionesPorLote:accion.visible', 'accionesPorLote:accion.noVisible', 'visibleNoVisible'),
        'borrar' => array('borrar', 'accionesPorLote:accion.delete')
    ),
    'variables-globales' => array(
        'listar' => array('index'),
        'crear'  => array('nuevo', 'crear'),
        'editar' => array('ver', 'actualizar'),
        'borrar' => array('borrar', 'accionesPorLote:accion.delete')
    ),

);
