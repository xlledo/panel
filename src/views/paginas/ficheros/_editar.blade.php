@extends('packages/ttt/panel/layout/panel_layout')
@section('tools')
	@if(Sentry::getUser()->hasAccess('paginas::editar'))
		<a href="{{ action('Ttt\Panel\PaginasController@nuevo') }}" title="Nuevo elemento de {{$_titulo}}" class="btn btn-sm btn-primary no-border"><i class="icon-file"></i> Nuevo</a></li>
	@endif
@stop
    @section('page_header')
            <h1>Editar Fichero <small> <i class="icon-double-angle-right"></i> {{$item->nombre }} </small></h1>
    @stop
@section('content')
    @include('packages/ttt/panel/paginas/ficheros/_partial_form')
    @include('packages/ttt/panel/ficheros/_partial_modal_seleccion', array('modulo'=>'paginas','add'=>true))
@stop

@section('inline_js')
	@parent
@stop
