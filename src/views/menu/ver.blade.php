@extends('packages/ttt/panel/layout/panel_layout')

@section('tools')
	@if(Sentry::getUser()->hasAccess('menu::crear'))
		<a href="{{ action('Ttt\Panel\MenuController@nuevo', $root->id) }}" title="Nueva opción de menú en {{ $root->nombre }}" class="btn btn-sm btn-primary no-border"><i class="icon-file"></i> Nueva opción</a>
	@endif
	@if(Sentry::getUser()->hasAccess('menu::editar-arbol'))
		<a href="{{ action('Ttt\Panel\MenuController@ordenarAlfabeticamente', $root->id) }}" title="Ordenar alfabéticamente este árbol" class="btn btn-sm btn-primary no-border"><i class="icon-list"></i> Ordenar alfabéticamente</a>
	@endif
@stop
@section('page_header')
	<h1>Estructura de navegación {{ $root->nombre }}</h1>
@stop
@section('content')
	<div class="row">
	    <div class="col-xs-12 widget-container-span">
			<input type="hidden" id="root_id" data-id="{{ $root->id }}" />
			<div class="dd dd-draghandle">
				<ol class="dd-list">
					{{ toNestable($tree, 'menu') }}
				</ol>
			</div>
		</div>
	</div>
@stop
@section('inline_js')
	@parent
	@if(Sentry::getUser()->hasAccess('menu::editar-arbol'))
	    $(document).ready(function() {

	        tttjs.categorias.init({
				clave: 'menu'
			});

	    });
	@endif
@stop
