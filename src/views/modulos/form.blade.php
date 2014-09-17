@extends('packages/ttt/panel/layout/panel_layout')

@section('tools')
	@if(Sentry::getUser()->hasAccess('modulos::crear'))
		<a href="{{ action('Ttt\Panel\ModuloController@nuevo') }}" title="Nuevo Módulo" class="btn btn-sm btn-primary no-border"><i class="icon-file"></i> Nuevo</a></li>
	@endif
@stop
@section('page_header')
	@if($action == 'create')
		<h1>Nuevo elemento de <a href="{{ action('Ttt\Panel\ModuloController@index') }}" title="Volver al listado">Módulos</a></h1>
	@else
		<h1><small><a href="{{ action('Ttt\Panel\ModuloController@index') }}" title="Volver al listado">Módulos</a> <i class="icon-double-angle-right"></i></small> {{ $item->nombre }}</h1>
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
					<form class="clearfix" action="<?php echo ($action == 'create') ? action('Ttt\Panel\ModuloController@crear') : action('Ttt\Panel\ModuloController@actualizar') ; ?>" method="post">
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
					                    <div class="widget-main row">
					                        <div class="col-md-3">
					                            <div class="checkbox">
					                                <label for="visible">
					                                    <input type="checkbox" class="ace ace-checkbox-2" name="visible" id="visible" <?php if($item->visible): ?>checked="checked" <?php endif; ?>value="1" />
					                                    <span class="lbl"> Visible</span>
					                                </label>
					                            </div>
					                        </div>
					                        <div class="col-md-3">
					                            <div class="form-group @if ($errors->first('nombre')) has-error @endif">
					                                <label for="nombre">Nombre *</label>
					                                <input type="text" class="form-control" name="nombre" id="nombre" value="{{ $item->nombre }}" size="20" />
													@if ($errors->first('nombre'))
														@foreach($errors->get('nombre') as $err)
															<span class="help-block">{{ $err }}</span>
														@endforeach
													@endif
					                            </div>
					                        </div>
											@if ($action != 'create')
						                        <div class="col-md-3">
						                            <div class="form-group">
						                                <label for="slug">Slug</label>
						                                <input type="text" class="form-control" readonly="readonly" name="slug" id="slug" value="{{ $item->slug }}" size="20" />
						                            </div>
						                        </div>
											@endif
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
	@if(Sentry::getUser()->hasAccess('modulos::borrar'))
		@if ($action != 'create')
			<div class="space-6"></div>
			<div class="acciones">
				<a class="btn btn-minier btn-danger no-border" title="Eliminar ?" href="{{ action('Ttt\Panel\ModuloController@borrar', $item->id) }}"><i class="icon-trash"></i>Borrar</a>
			</div>
		@endif
	@endif
@stop
