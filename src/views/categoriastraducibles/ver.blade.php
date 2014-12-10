@extends('packages/ttt/panel/layout/panel_layout')

@section('tools')
	@if(Sentry::getUser()->hasAccess('categorias-traducibles::listar'))
		<a href="{{ action('Ttt\Panel\CategoriaTraducibleController@index') }}" title="Volver al listado" class="btn btn-sm no-border"><i class="icon-double-angle-left"></i> Volver al listado</a>
	@endif
	@if(Sentry::getUser()->hasAccess('categorias-traducibles::crear'))
		<a href="{{ action('Ttt\Panel\CategoriaTraducibleController@nuevo', $root->id) }}" title="Nueva subcategoría en {{ $root->nombre }}" class="btn btn-sm btn-primary no-border"><i class="icon-file"></i> Nueva subcategoría</a>
	@endif
@stop
@section('page_header')
	<h1>Árbol de {{ $root->nombre }}</h1>
@stop
@section('content')
	<div class="row">
	    <div class="col-xs-12 widget-container-span">
			<div class="dd dd-draghandle">
				<ol class="dd-list">
					{{ toNestable($tree, 'categorias-traducibles') }}
				</ol>
			</div>
			@if(Sentry::getUser()->hasAccess('categorias-traducibles::editar-arbol'))
				<a href="{{ action('Ttt\Panel\CategoriaTraducibleController@ordenarAlfabeticamente', $root->id) }}" title="Ordenar alfabéticamente este árbol" class="btn btn-sm btn-primary no-border"><i class="icon-list"></i> Ordenar alfabéticamente</a>
			@endif
		</div>
	</div>
@stop
@section('inline_js')
	@parent
	@if(Sentry::getUser()->hasAccess('categorias-traducibles::editar-arbol'))
	    $(document).ready(function() {

	        tttjs.categorias.init({
				clave: 'categorias-traducibles'
			});

	    });
	@endif
@stop
