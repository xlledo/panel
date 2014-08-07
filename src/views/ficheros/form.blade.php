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
			<? else: ?>
				Editar <?php echo $item->nombre; ?>
			<?php endif; ?>
			</li>
		</ul>
</div>
@stop

@section('tools')
	@if(Sentry::getUser()->hasAccess('ficheros::crear'))
		<a href="{{ action('Ttt\Panel\FicherosController@nuevo') }}" title="Nuevo Módulo" class="btn btn-sm btn-primary no-border"><i class="icon-file"></i> Nuevo</a></li>
	@endif
@stop

@section('page_header')
	@if($action == 'create')
		<h1>Nuevo elemento de <a href="{{ action('Ttt\Panel\FicherosController@index') }}" title="Volver al listado">Ficheros</a></h1>
	@else
		<h1><small><a href="{{ action('Ttt\Panel\FicherosController@index') }}" title="Volver al listado">Ficheros</a> <i class="icon-double-angle-right"></i></small> {{ $item->clave }}</h1>
	@endif
@stop
@section('content')
	<div class="row">
	    <div class="col-xs-12">
			<div id="tabs">
				<ul id="aux">
				     <li><a href="#datos" title="datos"><i class="icon-list"></i>  Datos</a></li>
				</ul>
				<div id="datos">
					<form class="clearfix" action="<?php echo ($action == 'create') ? action('Ttt\Panel\FicherosController@crear') : action('Ttt\Panel\FicherosController@actualizar') ; ?>" method="post">
						@if($action != 'create')
							<input type="hidden" name="id" id="id" value="{{ $item->id }}" />
						@endif
					    <div class="acciones pull-right">
					        <input type="submit" value="Guardar" name="guardar" class="btn btn-sm btn-success no-border">
					    </div>
					    <div class="row">
					        <div class="col-xs-12">
					            <div class="widget-box transparent">
					                <div class="widget-header widget-header-small">
					                    <h4 class="smaller lighter">Datos</h4>
					                </div>
					                <div class="widget-body">
                                                            <div class=""> <!-- Form Ficheros -->
                                                                
                                                            </div>
                                                            
					                </div>
					            </div>
					        </div>
					    </div>
					    <div class="acciones pull-right">
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
                $(document).ready(function() {
                    tttjs.versiones.init();
                });
    @stop