@extends('packages/ttt/panel/layout/panel_layout')
@section('tools')
	@if(Sentry::getUser()->hasAccess('paginas::editar'))
		<a href="{{ action('Ttt\Panel\PaginasController@nuevo') }}" title="Nuevo Módulo" class="btn btn-sm btn-primary no-border"><i class="icon-file"></i> Nuevo</a></li>
	@endif
@stop
    @section('page_header')
            <h1>Editar Fichero <small> <i class="icon-double-angle-right"></i> Listado</small></h1>
    @stop
@section('content')
    @include('packages/ttt/panel/paginas/ficheros/_partial_form')
@stop

@section('inline_js')
	@parent
@stop
