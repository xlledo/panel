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
				<a href="{{ action('Ttt\Panel\FicherosController@index') }}" title="Volver al listado">Ficheros</a>
			</li>
			<li>
                           <?php if ($action == 'create'): ?>
                                   Nuevo elemento
                           <?php else: ?>
                                   Editar <?php  echo $item->fichero; ?>
                           <?php endif; ?>
			</li>
		</ul>
</div>
@stop

@section('tools')
	@if(Sentry::getUser()->hasAccess('ficheros::crear'))
		<!-- <a href="{{ action('Ttt\Panel\FicherosController@nuevo') }}" title="Nuevo Fichero" class="btn btn-sm btn-primary no-border"><i class="icon-file"></i> Nuevo</a></li> -->
	@endif
@stop

@section('page_header')
	@if($action == 'create')
		<h1>Nuevo elemento de <a href="{{ action('Ttt\Panel\FicherosController@index') }}" title="Volver al listado">Ficheros</a></h1>
	@else
		<h1><small><a href="{{ action('Ttt\Panel\FicherosController@index') }}" title="Volver al listado">Ficheros</a> <i class="icon-double-angle-right"></i></small> {{ $item->nombre }}</h1>
	@endif
@stop
@section('content')
	<div class="row">
	    <div class="col-xs-12">
			<div id="tabs">
				<ul id="aux">
				     <li><a href="#datos" title="datos">
                                             <i class="icon-list"></i>  Datos</a>
                                     </li>
				</ul>

				<div id="datos">
                                    <form class="clearfix" action="<?php echo ($action == 'create') ? action('Ttt\Panel\FicherosController@crear') : action('Ttt\Panel\FicherosController@actualizar') ; ?>" method="post"  enctype="multipart/form-data">
						@if($action != 'create')
							<input type="hidden" name="id" id="id" value="{{ $item->id }}" />
						@endif
					    <div class="acciones pull-right">
					        <input type="submit" value="Guardar" name="guardar" class="btn btn-sm btn-success no-border">
					    </div>
                                   	    <div class="row">
					        <div class="col-xs-12">
					            <div class="widget-box transparent">
                                                        @if($action != 'create')
                                                            <div class='alert alert-block alert-info'>
                                                                <span>La ruta del fichero es: {{$item->ruta . $item->fichero}}</span>
                                                            </div>
                                                        @endif
					                <div class="widget-header widget-header-small">
					                    <h4 class="smaller lighter">Datos</h4>
					                </div>
					                <div class="widget-body">
                                                            <div class="widget-main row"> <!-- Form Ficheros -->
                                                                <div class="col-md-4">
                                                                    <div class="form-group @if($errors->first('nombre')) has-error @endif">
                                                                        <label for='nombre'>Nombre *</label>
                                                                        <input type='text' class='form-control' name='nombre' id='nombre' value='{{$item->nombre}}' size='20' />
                                                                            @if ($errors->first('nombre'))
                                                                                @foreach($errors->get('nombre') as $err)
                                                                                    <span class="help-block">{{ $err }}</span>
                                                                                @endforeach
                                                                            @endif                                                                        
                                                                     </div>
                                                                </div>

                                                                
                                                                <div class='col-md-4'>
                                                                    <div class='form-group'>
                                                                        <label for='fichero'>Fichero</label>
                                                                        <input type="file" name='fichero' class='form-cotrol' />
                                                                    </div>
                                                                </div>
                                                            </div>
					                </div>
					            </div>
					        </div>
                                                
                                                <div class="col-xs-12">
                                                    <div class="widget-box transparent">
                                                        <div class="widget-header widget-header-small">
                                                            <h4 class="smaller lighter">Datos Opcionales</h4>
                                                        </div>
                                                        <div class="widget-main row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="titulo_defecto">Titulo</label>
                                                                    <input type="text" name="titulo_defecto" class="form-control" 
                                                                           value ="{{$item->titulo_defecto }}"
                                                                           >
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="alt_defecto">Alt</label>
                                                                    <input type="text" name="alt_defecto" class="form-control"
                                                                           value="{{$item->alt_defecto}}"
                                                                           >
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="descripcion_defecto">Descripcion</label>
                                                                    <input type="text" name="descripcion_defecto" class="form-control"
                                                                           value="{{$item->descripcion_defecto}}"
                                                                           >
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="enlace_defecto">Enlace</label>
                                                                    <input type="text" name="enlace_defecto" class="form-control"
                                                                           value="{{$item->enlace_defecto}}"
                                                                           >
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
					    </div>
					    <div class="acciones pull-right">
                                                <input type="hidden" value="{{$from_url}}" name="from_url"/>
					        <input type="submit" value="Guardar" class="boton btn btn-sm btn-success no-border" name="guardar"></li>
					    </div>
					</form>
				</div>
			</div>
		</div>
	</div>
	@if(Sentry::getUser()->hasAccess('ficheros::borrar'))
		@if ($action != 'create')
			<div class="space-6"></div>
			<div class="acciones">
				<a class="btn btn-minier btn-danger no-border" title="Eliminar ?" href="{{ action('Ttt\Panel\FicherosController@borrar', $item->id) }}"><i class="icon-trash"></i>Borrar</a>
			</div>
		@endif

	@endif
@stop
    @section('inline_js')
                @parent
    @stop