@extends('packages/ttt/panel/layout/panel_layout')

@section('tools')
        <a href="{{ action('Ttt\Panel\PaginasController@index') }}" title="Volver al listado" class="btn btn-sm btn-primary no-border"><i class="icon-double-angle-left"></i> Volver al listado</a>
	@if(Sentry::getUser()->hasAccess('paginas::crear') && $action!='create')
		<a href="{{ action('Ttt\Panel\PaginasController@nuevo') }}" title="Nuevo elemento de {{ $_titulo }}" class="btn btn-sm btn-primary no-border"><i class="icon-file"></i> Nuevo</a></li>
	@endif
@stop

@section('page_header')
	@if($action == 'create')
		<h1>Nuevo elemento de <a href="{{ action('Ttt\Panel\PaginasController@index') }}" title="Volver al listado">Paginas</a></h1>
	@else
		<h1>Editando {{$item->titulo}}</h1>
	@endif
@stop
@section('content')
	<div class="row">
            <div class="col-xs-12">
			<div id="tabs">
				<ul id="aux" class="mi">
				     <li><a href="#datos" title="datos"><i class="icon-list"></i>  Datos</a></li>
                                     
                                    @if($action != 'create')
                                    
                                     <li><a href="#ficheros" title="ficheros"><i class="icon-list"></i> Ficheros</a></li>
                                     
                                     @endif
				</ul>
    <div id="datos" class="no-padding">
        <div id="tabsI" class="clearfix">
            <ul class="pestanias">
            @if($action != 'create')
                    @foreach($item->traducciones()->get() as $trad)
                        <li>
                            
                            <a href="#datos-{{$trad->idioma}}"> 
                                {{ Ttt\Panel\Repo\Idioma\Idioma::getByCodigoIso2($trad->idioma)->nombre }}
                            </a>
                            
                        </li>
                    @endforeach
                    @if(count($item->traducciones()->get())!= count($todos_idiomas))
                        <li><a href="#datos-nuevatraduccion"> Nueva Traducción </a></li>
                    @endif
            @else
                <li><a href="#datos-{{ $idioma_predeterminado->codigo_iso_2 }}">{{ ucfirst($idioma_predeterminado->nombre) }}</a></li>
            @endif
            </ul>            
                            {{-- Formularios --}}
                            @if($action == 'create')
                                    {{-- Form elemento nuevo --}}
                                    @include('packages/ttt/panel/paginas/partialform', array('action'=>$action, 'nueva_traduccion'=>false,'idioma_error'=>$idioma_predeterminado->codigo_iso_2))
                            @else
                            
                            {{-- Creamos los forms de idiomas --}}
                            @foreach($item->traducciones()->get() as $trad)
                                @include('packages/ttt/panel/paginas/partialform', array('trad'=>$trad, 'action'=>$action, 'nueva_traduccion' => false, 'idioma_error' => \Session::get('idioma_error', FALSE)))
                            @endforeach
                            
                            {{-- Form Nueva traduccion si es necesario --}}
                            @if(count($item->traducciones()->get()) != count($todos_idiomas))
                                @include('packages/ttt/panel/paginas/partialform', array('action'=>$action, 'nueva_traduccion' => true,'idioma_error' => \Session::get('idioma_error', FALSE)))
                            @endif
                    @endif
                </div>
            </div>
                            {{-- Ficheros --}}
                            @if($action != 'create')
                            <div id='ficheros'>
                                            <div class="acciones pull-right">
                                                <button data-toggle="modal" data-target="#modal_select_fichero"  class="btn btn-sm btn-success no-border">Añadir Fichero</button>
					    </div>
                                @include('packages/ttt/panel/ficheros/_partial_listado', array('modulo'=>'paginas'))
                                @include('packages/ttt/panel/ficheros/_partial_modal_seleccion', array('modulo'=>'paginas','add'=>true))
                            </div>
                            @endif                
        </div>
	</div>
        </div>
	@if(Sentry::getUser()->hasAccess('paginas::borrar'))
		@if ($action != 'create')
			<div class="space-6"></div>
			<div class="acciones">
                                <a href="#" title="Borrar Página" class="btn btn-minier btn-danger no-border btn_confirmacion" data-action="{{action('Ttt\Panel\PaginasController@borrar', $item->id) }}">Borrar Página</a>
			</div>
		@endif
	@endif
@stop

@section('inline_js')
            @parent
@stop