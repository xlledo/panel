@extends('packages/ttt/panel/layout/panel_layout')
@section('migas')

<div class="breadcrumbs" id="breadcrumbs">
		<script type="text/javascript">
			try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}
		</script>
		<ul class="breadcrumb">
			<li>
				<i class="icon-home home-icon"></i>
				<a href="{{ action('Ttt\Panel\DashboardController@index') }}">Inicio</a>
			</li>
			<li>
				<a href="{{ action('Ttt\Panel\TraduccionesController@index') }}" title="Volver al listado">Traducciones</a>
			</li>
			<li>
			 <?php if ($action == 'create'): ?>
				Nuevo elemento
			<? else: ?>
				Editar <?php echo $item->clave; ?>
			<?php endif; ?>
			</li>
		</ul>
</div>
@stop

@section('tools')
	@if(Sentry::getUser()->hasAccess('traducciones::crear'))
		<a href="{{ action('Ttt\Panel\TraduccionesController@nuevo') }}" title="Nuevo Módulo" class="btn btn-sm btn-primary no-border"><i class="icon-file"></i> Nuevo</a></li>
	@endif
@stop
@section('page_header')
	@if($action == 'create')
		<h1>Nuevo elemento de <a href="{{ action('Ttt\Panel\TraduccionesController@index') }}" title="Volver al listado">Traducciones</a></h1>
	@else
		<h1><small><a href="{{ action('Ttt\Panel\TraduccionesController@index') }}" title="Volver al listado">Traducciones</a> <i class="icon-double-angle-right"></i></small> {{ $item->clave }}</h1>
	@endif
@stop
@section('content')
	<div class="row">
	    <div class="col-xs-12">
			<div id="tabs">
				<ul id="aux" class="mi">
				     <li><a href="#datos" title="datos"><i class="icon-list"></i>  Datos</a></li>
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
                                    @include('packages/ttt/panel/traducciones/partialform', array('action'=>$action, 'nueva_traduccion'=>false))
                            @else
                            
                            {{-- Creamos los forms de idiomas --}}
                            <?php $idioma_error =  (\Session::has('idioma_error')) ? \Session::get('idioma_error') : FALSE ?>
                            @foreach($item->traducciones()->get() as $trad)
                                @include('packages/ttt/panel/traducciones/partialform', array('trad'=>$trad,'idioma_error'=>$idioma_error, 'action'=>$action, 'nueva_traduccion' => false))
                            @endforeach
                            
                            {{-- Form Nueva traduccion si es necesario --}}
                            @if(count($item->traducciones()->get()) != count($todos_idiomas))
                                @include('packages/ttt/panel/traducciones/partialform', array('action'=>$action, 'nueva_traduccion' => true))
                            @endif
                    @endif
                </div>
            </div>
        </div>
                
	</div>
        </div>
	@if(Sentry::getUser()->hasAccess('traducciones::borrar'))
		@if ($action != 'create')
			<div class="space-6"></div>
			<div class="acciones">
				<a class="btn btn-minier btn-danger no-border" title="Eliminar ?" href="{{ action('Ttt\Panel\TraduccionesController@borrar', $item->id) }}"><i class="icon-trash"></i>Borrar</a>
			</div>
		@endif
	@endif
@stop

@section('inline_js')
            @parent
@stop