@extends('packages/ttt/panel/layout/panel_layout')
@section('tools')
	<a href="{{ action('Ttt\Panel\UsuarioController@nuevo') }}" title="Nuevo Usuario" class="btn btn-sm btn-primary no-border"><i class="icon-file"></i> Nuevo</a></li>
@stop
@section('page_header')
	<h1>Usuarios <small> <i class="icon-double-angle-right"></i> Listado</small></h1>
@stop
@section('content')
	<div class="row">
	    <div class="col-xs-12">
			<div class="widget-box">
				<form method="POST" action="{{ url('admin/usuarios') }}">
					<div class="widget-header widget-header-small" data-toggle="collapse" data-target=".widget-body">
						<h4 class="smaller lighter"><i class="icon-filter"></i> Filtros</h4>

						<div class="widget-toolbar">
							<i class="icon-chevron-down"></i>
						</div>
					</div>

					<div class="widget-body collapse">
						<div class="widget-main row">

							<div class="col-md-3 form-group">
								<label for="filtro_nombre">Nombre</label>
								<input type="text" class="form-control" name="nombre" id="filtro_nombre" value="<?php if(isset($params['nombre'])): ?>{{ $params['nombre'] }}<?php endif; ?>" size="20" placeholder="Nombre" />
							</div>

							<div class="col-md-3 form-group">
								<label for="filtro_email">E-mail</label>
								<input type="text" class="form-control" name="email" id="filtro_email" value="<?php if(isset($params['email'])): ?>{{ $params['email'] }}<?php endif; ?>" size="20" placeholder="E-mail" />
							</div>

						</div>
						<div class="widget-toolbox padding-8 clearfix">
							<div class="pull-right">
								<a href="{{ action('Ttt\Panel\UsuarioController@index') }}" title="Mostrar Todos" class="btn btn-primary btn-xs" >Mostrar todos</a></li>
								<input type="submit" name="filtrar" value="Buscar" class="btn btn-success btn-xs"/>
							</div>
						</div>
					</div>
				</form>
			</div>

			<div class="space-12"></div>

			@if($items->count() === 0)
				<div class="alert alert-info">Actualmente no hay elementos en la base de datos</div>
	        @else
				<form action="{{ url('admin/modulos/acciones_por_lote') }}" method="post">
	                <fieldset>
	                    <table class="table table-striped table-bordered table-hover listado" summary="Listado de módulos" border="0" cellpadding="0" cellspacing="1">
	                        <thead>
	                            <tr>
	                                <th scope="col" width="40">{{ ordenable_link($currentUrl, 'id', '#ID', $params, $params[Config::get('panel::app.orderDir')]) }}</th>
									<th scope="col" width="300">{{ ordenable_link($currentUrl, 'nombre', 'Nombre', $params, $params[Config::get('panel::app.orderDir')]) }}</th>
	                                <th scope="col" width="300">{{ ordenable_link($currentUrl, 'email', 'E-mail', $params, $params[Config::get('panel::app.orderDir')]) }}</th>
									<th scope="col">Grupo</th>
	                            </tr>
	                        </thead>
	                        <tbody>

								@foreach($items as $index => $item)
									<tr class="@if($index % 2 == 0) par @else impar @endif">
										<td class="center">{{ $item->id }}</td>
										<td>{{ link_to('admin/usuarios/ver/' . $item->id, $item->full_name) }}</td>
										<td>{{ link_to('admin/usuarios/ver/' . $item->id, $item->email) }}</td>
										<td>@if($item->groups->count()) {{ $item->groups->first()->name }} @endif</td>
									</tr>
								@endforeach
	                        </tbody>
	                    </table>
	                    <div class="selectAcciones row">
							<div class="elementos col-sm-6">
								Mostrando de {{ $items->getFrom() }} a {{ $items->getTo() }} de un total de {{ $items->getTotal() }}
							</div>
						</div>
	                </fieldset>
				</form>
				<div class="center">
					@if($items->getLastPage() > 1)
						<ul class="pagination">
							<?php echo with(new Ttt\Panel\Pagination\TttPresenter($items))->render(); ?>
						</ul>
					@endif
				</div>
			@endif
		</div>
	</div>
@stop
