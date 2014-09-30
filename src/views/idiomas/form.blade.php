@extends('packages/ttt/panel/layout/panel_layout')

@section('tools')
                <a href="{{ action('Ttt\Panel\IdiomaController@index') }}" title="Volver al listado" class="btn btn-sm btn-primary no-border"><i class="icon-double-angle-left"></i> Volver al listado</a>

	@if(Sentry::getUser()->hasAccess('idiomas::crear') && $action != 'create')
		<a href="{{ action('Ttt\Panel\IdiomaController@nuevo') }}" title="Nuevo elemento en {{$_titulo }}" class="btn btn-sm btn-primary no-border"><i class="icon-file"></i> Nuevo</a></li>

	@endif
@stop
@section('page_header')
	@if($action == 'create')
		<h1>Nuevo elemento de <a href="{{ action('Ttt\Panel\IdiomaController@index') }}" title="Volver al listado">Idiomas</a></h1>
	@else
		<h1>Editando {{$item->nombre}}</h1>
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
					<form class="clearfix" action="<?php echo ($action == 'create') ? action('Ttt\Panel\IdiomaController@crear') : action('Ttt\Panel\IdiomaController@actualizar') ; ?>" method="post">
						@if($action != 'create')
							<input type="hidden" name="id" id="id" value="{{ $item->id }}" />
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
					                                    <input type="checkbox" class="ace ace-checkbox-2" name="visible" id="visible" <?php if($item->visible): ?>checked="checked" <?php endif; ?>value="1" />
					                                    <span class="lbl"> Visible</span>
					                                </label>
					                            </div>
												<div class="checkbox">
													<label for="principal">
														<input type="checkbox" class="ace ace-checkbox-2" name="principal" id="principal" <?php if($item->principal): ?>checked="checked" <?php endif; ?>value="1" />
														<span class="lbl"> Principal</span>
													</label>
												</div>
					                        </div>
					                        <div class="col-md-3">
					                            <div class="form-group @if($errors->first('nombre')) has-error @endif">
					                                <label for="nombre">Nombre *</label>
					                                <input type="text" class="form-control" name="nombre" id="nombre" value="{{ $item->nombre }}" size="20" />
													@if ($errors->first('nombre'))
														@foreach($errors->get('nombre') as $err)
															<span class="help-block">{{ $err }}</span>
														@endforeach
													@endif
					                            </div>
					                        </div>
											<div class="col-md-3">
												<div class="form-group @if ($errors->first('codigo_iso_2')) has-error @endif">
													<label for="codigo_iso_2">Código ISO 2 *</label>
													<input type="text" class="form-control" name="codigo_iso_2" id="codigo_iso_2" value="{{ $item->codigo_iso_2 }}" size="20" />
													@if ($errors->first('codigo_iso_2'))
														@foreach($errors->get('codigo_iso_2') as $err)
															<span class="help-block">{{ $err }}</span>
														@endforeach
													@endif
												</div>
												<div class="form-group @if ($errors->first('codigo_iso_3')) has-error @endif">
													<label for="codigo_iso_3">Código ISO 3</label>
													<input type="text" class="form-control" name="codigo_iso_3" id="codigo_iso_3" value="{{ $item->codigo_iso_3 }}" size="20" />
													@if ($errors->first('codigo_iso_3'))
														@foreach($errors->get('codigo_iso_3') as $err)
															<span class="help-block">{{ $err }}</span>
														@endforeach
													@endif
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
	@if(Sentry::getUser()->hasAccess('idiomas::borrar'))
		@if ($action != 'create')
			<div class="space-6"></div>
			<div class="acciones">
				<a class="btn btn-minier btn-danger no-border btn-confirmacion" title="Eliminar ?" href="{{ action('Ttt\Panel\IdiomaController@borrar', $item->id) }}"><i class="icon-trash"></i>Borrar</a>
			</div>
		@endif
	@endif
@stop
