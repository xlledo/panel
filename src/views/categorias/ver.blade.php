@extends('packages/ttt/panel/layout/panel_layout')

@section('tools')
	@if(Sentry::getUser()->hasAccess('categorias::crear'))
		<a href="{{ action('Ttt\Panel\CategoriaController@nuevo', $root->id) }}" title="Nueva subcategoría en {{ $root->nombre }}" class="btn btn-sm btn-primary no-border"><i class="icon-file"></i> Nueva subcategoría</a>
	@endif
	@if(Sentry::getUser()->hasAccess('categorias::editar-arbol'))
		<a href="{{ action('Ttt\Panel\CategoriaController@ordenarAlfabeticamente', $root->id) }}" title="Ordenar alfabéticamente este árbol" class="btn btn-sm btn-primary no-border"><i class="icon-list"></i> Ordenar alfabéticamente</a>
	@endif
@stop
@section('page_header')
	<h1><small><a href="{{ action('Ttt\Panel\CategoriaController@index') }}" title="Volver al listado">Categorías</a> <i class="icon-double-angle-right"></i></small>Estructura del árbol de categorías {{ $root->nombre }}</h1>
@stop
@section('content')
	<div class="row">
	    <div class="col-xs-12 widget-container-span">
			<div class="dd dd-draghandle">
				<ol class="dd-list">
					{{ toNestable($tree, 'categorias') }}
				</ol>
			</div>
		</div>
	</div>
@stop
@section('inline_js')
	@parent
	@if(Sentry::getUser()->hasAccess('categorias::editar-arbol'))
	    $(document).ready(function() {

	        tttjs.categorias.init();

	    });
	@endif
@stop
