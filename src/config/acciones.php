<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Acciones para la gestión de módulos
	|--------------------------------------------------------------------------
	|
	| Se mappean las acciones del módulo con los diferentes métodos existentes en cada controlador
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
    'usuarios' => array(
        'listar' => array('index'),
        'crear'  => array('nuevo', 'crear'),
        'editar' => array('ver', 'actualizar'),
        'borrar' => array('borrar'),
        'editar-preferencias' => array('verPreferencias', 'actualizarPreferencias')
    ),
    'variables-globales' => array(
        'listar' => array('index'),
        'crear'  => array('nuevo', 'crear'),
        'editar' => array('ver', 'actualizar'),
        'borrar' => array('borrar', 'accionesPorLote:accion.delete')
    ),
    'categorias' => array(
        'listar' => array('index'),//muestra el listado de árboles existentes
        'crear-arbol'  => array('nuevoArbol', 'crearArbol'),//crea un nuevo árbol
        'crear'  => array('nuevo', 'crear'),//crea un nuevo nodo dentro del árbol
        'editar-arbol' => array('verArbol', 'editarRaiz', 'actualizarRaiz', 'ordenarAlfabeticamente', 'ordenar'),//edita el árbol o la raíz
        'editar' => array('ver', 'actualizar'),//edita un nodo dentro del árbol
        'borrar-arbol' => array('borrarArbol'),//borra la raíz del árbol y todo su contenido
        'borrar' => array('borrar')//borra cualquier nodo del árbol y todo lo que cuelga de él
    ),
 'traducciones' => array(
        'listar' => array('index'),
        'crear'  => array('nuevo', 'crear'),
        'editar' => array('ver', 'actualizar'),
        'borrar' => array('borrar', 'accionesPorLote:accion.delete')
    ),
    'idiomas' => array(
        'listar' => array('index'),
        'crear'  => array('nuevo', 'crear'),
        'editar' => array('ver', 'actualizar', 'accionesPorLote:accion.visible', 'accionesPorLote:accion.noVisible', 'visibleNoVisible'),
        'borrar' => array('borrar', 'accionesPorLote:accion.delete')
    ),
    'ficheros' => array(
        'listar' => array('index'),
        'borrar' => array('borrar')
    )
);
