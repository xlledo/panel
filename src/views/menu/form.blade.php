@extends('packages/ttt/panel/layout/panel_layout')

@section('tools')
	@if(Sentry::getUser()->hasAccess('menu::listar'))
		<a href="{{ action('Ttt\Panel\MenuController@index') }}" title="Volver al árbol {{ $item->getRoot()->nombre }}" class="btn btn-sm no-border"><i class="icon-double-angle-left"></i> Volver al árbol</a>
	@endif
	@if($action == 'edit')
		@if(Sentry::getUser()->hasAccess('menu::crear'))
			<a href="{{ action('Ttt\Panel\MenuController@nuevo', $item->getRoot()->id) }}" title="Nueva opción de menú en {{ $item->getRoot()->nombre }}" class="btn btn-sm btn-primary no-border"><i class="icon-file"></i> Nueva opción</a>
		@endif
	@endif

@stop
@section('page_header')
	@if($action == 'create')
		<h1><small><a href="{{ action('Ttt\Panel\MenuController@index') }}" title="Volver al listado">{{ $item->getRoot()->nombre }}</a> <i class="icon-double-angle-right"></i></small>Nueva opción en {{ $item->parent()->get()->first()->isRoot() ? link_to('admin/menu/', $item->getRoot()->nombre, array('title' => $item->getRoot()->nombre)) : link_to('admin/menu/ver/' . $item->parent()->get()->first()->id . '/', $item->parent()->get()->first()->nombre, array('title' => $item->parent()->get()->first()->nombre)) }}</h1>
	@else
		<h1>Editando <em>{{ $item->nombre }}</em></h1>
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
					<form class="clearfix" action="<?php echo ($action == 'create') ? action('Ttt\Panel\MenuController@crear') : action('Ttt\Panel\MenuController@actualizar') ; ?>" method="post">
						@if($action != 'create')
							<input type="hidden" name="id" id="id" value="{{ $item->id }}" />
						@endif

						@if($action == 'create')
							<input type="hidden" name="parent_id" id="parent_id" value="{{ $item->getRoot()->id }}" />
						@endif
						<div class="acciones pull-right">
							<button type="submit" title="Guardar los cambios" class="btn btn-sm btn-success no-border"><i class="icon-save"></i> Guardar</button>
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
														<input type="checkbox" class="ace ace-checkbox-2" name="visible" id="visible" value="1"<?php if($item->visible): ?> checked="checked" <?php endif; ?>/>
														<span class="lbl"> Visible</span>
													</label>
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group @if ($errors->first('nombre')) has-error @endif">
													<label for="nombre">Nombre *</label>
													<input type="text" class="form-control" name="nombre" id="nombre" value="{{{ $item->nombre }}}" size="20" />
													@if ($errors->first('nombre'))
														@foreach($errors->get('nombre') as $err)
															<span class="help-block">{{ $err }}</span>
														@endforeach
													@endif
												</div>
												<div class="form-group @if ($errors->first('ruta')) has-error @endif">
													<label for="nombre">Ruta</label>
													<input type="text" class="form-control" name="ruta" id="ruta" value="{{ $item->ruta }}" size="20" />
													@if ($errors->first('ruta'))
														@foreach($errors->get('ruta') as $err)
															<span class="help-block">{{ $err }}</span>
														@endforeach
													@endif
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group @if ($errors->first('icono')) has-error @endif">
													<label for="nombre">Icono</label>
													<input type="text" class="form-control" name="icono" id="icono" value="{{ $item->icono }}" size="20" /> <a href="http://fontawesome.io/icons/" target="_blank">Consultar</a>
													@if ($errors->first('icono'))
														@foreach($errors->get('icono') as $err)
															<span class="help-block">{{ $err }}</span>
														@endforeach
													@endif
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<label for="modulo_id">Módulo</label>
													<select name="modulo_id" id="modulo_id" class="form-control">
														<option value="">- Seleccionar -</option>
														@foreach($modulos as $mod)
															<option value="{{ $mod->id }}"<?php if($item->modulo()->count() && $item->modulo->id == $mod->id): ?> selected="selected"<?php endif; ?>>{{ $mod->nombre }}</option>
														@endforeach
													</select>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="acciones pull-right">

							<button type="submit" title="Guardar los cambios" class="btn btn-sm btn-success no-border"><i class="icon-save"></i> Guardar</button>

						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	@if($action == 'edit' && Sentry::getUser()->hasAccess('menu::borrar'))
		<div class="space-6"></div>
		<div class="acciones">
			<a class="btn btn-minier btn-danger no-border btn-confirmacion" title="Eliminar ?" href="{{ action('Ttt\Panel\MenuController@borrar', $item->id) }}"><i class="icon-trash"></i>Borrar</a>
		</div>
	@endif
@stop
@section('inline_js')
	@parent
@stop
