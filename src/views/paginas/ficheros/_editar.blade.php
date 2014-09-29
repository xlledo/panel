@extends('packages/ttt/panel/layout/panel_layout')
@section('tools')
	@if(Sentry::getUser()->hasAccess('paginas::editar'))
		<a href="{{ action('Ttt\Panel\PaginasController@ver',$item_id) }}" title="Volver a  {{$_titulo}}" class="btn btn-sm btn-primary no-border"><i class="icon-double-angle-left"></i> Volver a Página</a></li>
	@endif
@stop
    @section('page_header')
            <h1>Editar Fichero <small> <i class="icon-double-angle-right"></i> {{$item->nombre }} </small></h1>
    @stop
@section('content')
    
    	<div class="row">
	    <div class="col-xs-12">
			<div id="tabs">
				<ul id="aux" class="mi">
				     <li><a href="#datos" title="datos"><i class="icon-list"></i>  Datos</a></li>
				</ul>
                                <div id="datos" class="">
                                        @include('packages/ttt/panel/paginas/ficheros/_partial_form')
                                </div>
                        </div>
            </div>
        </div>
    @include('packages/ttt/panel/ficheros/_partial_modal_seleccion', array('modulo'=>'paginas','add'=>true))
                                        
    
@stop

@section('inline_js')
	@parent
@stop
