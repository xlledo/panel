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
				<a href="{{ action('Ttt\Panel\GrupoController@index') }}" title="Volver al listado">Grupos</a>
			</li>
			<li>
			 <?php if ($action == 'create'): ?>
				Nuevo elemento
			<? else: ?>
				Editar <?php echo $item->name; ?>
			<?php endif; ?>
			</li>
		</ul>
</div>
@stop

@section('tools')
	<a href="{{ action('Ttt\Panel\GrupoController@nuevo') }}" title="Nuevo Grupo" class="btn btn-sm btn-primary no-border"><i class="icon-file"></i> Nuevo</a></li>
@stop
@section('page_header')
	@if($action == 'create')
		<h1>Nuevo elemento de <a href="{{ action('Ttt\Panel\GrupoController@index') }}" title="Volver al listado">Grupos</a></h1>
	@else
		<h1><small><a href="{{ action('Ttt\Panel\GrupoController@index') }}" title="Volver al listado">Grupo</a> <i class="icon-double-angle-right"></i></small> {{ $item->name }}</h1>
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
					<form class="clearfix" action="<?php echo ($action == 'create') ? action('Ttt\Panel\GrupoController@crear') : action('Ttt\Panel\GrupoController@actualizar') ; ?>" method="post">
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
					                            <div class="form-group @if ($errors->first('name')) has-error @endif">
					                                <label for="nombre">Nombre *</label>
					                                <input type="text" class="form-control" name="name" id="name" value="{{ $item->name }}" size="20" />
					                            </div>
					                        </div>
					                    </div>
					                </div>
					            </div>
					        </div>
					    </div>
						{{-- Añadimos la caja de permisos --}}
						@if($item->name != 'Superadmin')
							<div class="row">
								<div class="col-xs-12">
									<div class="widget-box transparent">
										<div class="widget-header widget-header-small">
											<h4 class="smaller lighter">Permisos</h4>
										</div>
										<div class="widget-body">
											<div class="widget-main row">
												@foreach(Config::get('panel::acciones') as $moduloKey => $acciones)
													<table class="table table-striped table-bordered" id="tabla{{ ucfirst($moduloKey) }}" summary="{{ $moduloKey }}" border="0" cellpadding="0" cellspacing="1">
												        <thead>
												            <tr>
												                <th scope="col">Acciones del módulo {{ ucfirst(str_replace('-', ' ', $moduloKey)) }}</th>
												                <th scope="col" width="150" class="center">Permitida</th>
																<th scope="col" width="150" class="center">No Permitida</th>
												            </tr>
												        </thead>
														<tbody>
															@foreach($acciones as $actionKey => $metodos)
																<tr>
																	<td>
																		<p><strong>{{ ucfirst($actionKey) }}</strong>:</p>
																		<p>{{ implode(', ', $metodos) }}</p>
																	</td>
																	<td class="center">
																		<input value="si" type="radio"<?php if($item->hasAccess($moduloKey . '::' . $actionKey)): ?> checked="checked"<?php endif; ?> id="{{ $moduloKey }}.{{ $actionKey }}.si" name="{{ $moduloKey }}::{{ $actionKey }}" />
																	</td>
																	<td class="center">
																		<input value="no" type="radio"<?php if(! $item->hasAccess($moduloKey . '::' . $actionKey)): ?> checked="checked"<?php endif; ?> id="{{ $moduloKey }}.{{ $actionKey }}.no" name="{{ $moduloKey }}::{{ $actionKey }}" />
																	</td>
																</tr>
															@endforeach
														</tbody>
													</table>
													<div class="space-6"></div>
												@endforeach
											</div>
										</div>
									</div>
								</div>
							</div>
						@endif
						<div class="acciones pull-right">

							<input type="submit" value="Guardar" class="boton btn btn-sm btn-success no-border" name="guardar"></li>

						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	@if ($action != 'create')
		@if($item->name != 'Superadmin')
			<div class="space-6"></div>
			<div class="acciones">
				<a class="btn btn-minier btn-danger no-border" title="Eliminar ?" href="{{ action('Ttt\Panel\GrupoController@borrar', $item->id) }}"><i class="icon-trash"></i>Borrar</a>
			</div>
		@endif
	@endif
@stop
