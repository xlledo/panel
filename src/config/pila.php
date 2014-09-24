<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Mapeos de módulos a elementos de la pila
	|--------------------------------------------------------------------------
	|
	|
	*/
    'modulos' => array(
        'eloquent'       => 'Ttt\Panel\Repo\Modulo\ModuloInterface',
        'metodo'         => 'byId',
        'param'          => 'id',
        'readFields'     => array('nombre'),
        'tituloCanonico' => 'Módulos'
    ),
    'grupos' => array(
        'eloquent'       => 'Ttt\Panel\Repo\Grupo\GrupoInterface',
        'metodo'         => 'findById',
        'param'          => 'id',
        'readFields'     => array('name'),
        'tituloCanonico' => 'Grupos'
    ),
    'idiomas' => array(
        'eloquent'       => 'Ttt\Panel\Repo\Idioma\IdiomaInterface',
        'metodo'         => 'byId',
        'param'          => 'id',
        'readFields'     => array('nombre'),
        'tituloCanonico' => 'Idiomas'
    ),
    'usuarios' => array(
        'eloquent'       => 'Ttt\Panel\Repo\Usuario\UsuarioInterface',
        'metodo'         => 'findById',
        'param'          => 'id',
        'readFields'     => array('fullName'),
        'tituloCanonico' => 'Usuarios'
    ),
    'variables-globales' => array(
        'eloquent'       => 'Ttt\Panel\Repo\Variablesglobales\VariablesglobalesInterface',
        'metodo'         => 'byId',
        'param'          => 'id',
        'readFields'     => array('clave'),
        'tituloCanonico' => 'Variables globales'
    ),
    'traducciones' => array(
        'eloquent'       => 'Ttt\Panel\Repo\Traducciones\TraduccionesInterface',
        'metodo'         => 'byId',
        'param'          => 'id',
        'readFields'     => array('clave'),
        'tituloCanonico' => 'Traducciones'
    ),
    'menu' => array(
        'eloquent'       => 'Ttt\Panel\Repo\Menu\MenuInterface',
        'metodo'         => 'byId',
        'param'          => 'id',
        'readFields'     => array('nombre'),
        'tituloCanonico' => 'Menú'
    ),
    'categorias' => array(
        'eloquent'       => 'Ttt\Panel\Repo\Categoria\CategoriaInterface',
        'metodo'         => 'byId',
        'param'          => 'id',
        'readFields'     => array('nombre'),
        'tituloCanonico' => 'Categorías'
    ),
    'categorias-traducibles' => array(
        'eloquent'       => 'Ttt\Panel\Repo\Categoriatraducible\CategoriaInterface',
        'metodo'         => 'byId',
        'param'          => 'id',
        'readFields'     => array('nombre'),
        'tituloCanonico' => 'Categorías traducibles'
    ),
    'ficheros' => array(
        'eloquent'       => 'Ttt\Panel\Repo\Fichero\FicheroInterface',
        'metodo'         => 'byId',
        'param'          => 'id',
        'readFields'     => array('nombre'),
        'tituloCanonico' => 'Ficheros'
    ),
    'paginas' => array(
        'eloquent'      => 'Ttt\Panel\Repo\Paginas\PaginasInterface',
        'metodo'        => 'byId',
        'param'         => 'id',
        'readFields'    => array("titulo"),
        'tituloCanonico'=> 'Paginas'
    )
);
