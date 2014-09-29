@extends('packages/ttt/panel/layout/panel_layout')
@section('tools')
	@if(Sentry::getUser()->hasAccess('variables-globales::crear'))
		<a href="{{ action('Ttt\Panel\VariablesglobalesController@nuevo') }}" title="Nuevo elemento de {{$_titulo }}" class="btn btn-sm btn-primary no-border"><i class="icon-file"></i> Nuevo</a></li>
	@endif
@stop
@section('page_header')
	<h1>Listado de {{$_titulo }}</h1>
@stop
@section('content')
	<div class="row">
	    <div class="col-xs-12">
	        <div class="widget-box">
	            <form method="POST" action="{{ url('admin/variablesglobales') }}">
	                <div class="widget-header widget-header-small" data-toggle="collapse" data-target=".widget-body">
	                    <h4 class="smaller lighter"><i class="icon-filter"></i> Filtros</h4>

	                    <div class="widget-toolbar">
	                        <i class="icon-chevron-down"></i>
	                    </div>
	                </div>

	                <div class="widget-body <?php if( ! $params['clave'] ): ?> collapse <?php endif; ?>">
	                    <div class="widget-main row">

	                        <div class="col-md-3 form-group">
	                            <label for="filtro_nombre">Clave</label>
	                            <input type="text" class="form-control" name="clave" id="filtro_nombre" value="<?php if(isset($params['clave'])): ?>{{ $params['clave'] }}<?php endif; ?>" size="20" placeholder="Valor" />
	                        </div>

	                    </div>
	                    <div class="widget-toolbox padding-8 clearfix">
	                        <div class="pull-right">
	                            <a href="{{ action('Ttt\Panel\VariablesglobalesController@index') }}" title="Mostrar Todos" class="btn btn-primary btn-xs" >Mostrar todos</a></li>
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

	            <form action="{{ url('admin/variablesglobales/acciones_por_lote') }}" method="post">
	                <fieldset>
	                    <table class="table table-striped table-bordered table-hover listado" summary="Listado de Variablesglobales" border="0" cellpadding="0" cellspacing="1">
	                        <thead>
	                            <tr>

	                                <th scope="col">{{ ordenable_link($currentUrl, 'clave', 'Clave', $params, $params[Config::get('panel::app.orderDir')]) }}</th>
									<th scope="col">{{ ordenable_link($currentUrl, 'creado_por', 'Creado por', $params, $params[Config::get('panel::app.orderDir')]) }}</th>
									<th scope="col">Actualizado por</th>
									@if(Sentry::getUser()->hasAccess(array('variables-globales::editar', 'variables-globales::borrar'), FALSE))
	                                	<th scope="col" width="30"><input type="checkbox" class="select_all"/></th>
									@endif
	                            </tr>
	                        </thead>
	                        <tbody>
								@foreach($items as $index => $item)
									<tr class="@if($index % 2 == 0) par @else impar @endif">
										<td class="td_click">
											@if(Sentry::getUser()->hasAccess('variables-globales::editar'))
												{{ link_to('admin/variablesglobales/ver/' . $item->id, $item->clave) }}
											@else
												{{ $item->clave }}
											@endif
										</td>
										<td class="td_click">{{ $item->maker->first_name . ' ' . $item->maker->last_name }}</td>
										<td class="td_click">{{ $item->updater->first_name . ' ' . $item->updater->last_name }}</td>
										@if(Sentry::getUser()->hasAccess(array('variables-globales::editar', 'variables-globales::borrar'), FALSE))
											<td><input class="item" type="checkbox" name="item[]" value="{{ $item->id }}" /></td>
										@endif
									</tr>
								@endforeach
	                        </tbody>
	                    </table>
	                    -<div class="selectAcciones row">
	                        <div class="elementos col-sm-6">
	                            Mostrando de {{ $items->getFrom() }} a {{ $items->getTo() }} de un total de {{ $items->getTotal() }}
	                        </div>
							@if(count($accionesPorLote))
		                        <div class="acciones col-sm-6">
		                            <div class="pull-right form-inline selectAcciones">
		                                <label for="acciones_por_lote">Acción:</label>
		                                <select id="acciones_por_lote" name="accion" class="input-medium input-sm">
		                                    <option value="0" selected="selected">- Seleccionar -</option>
											@foreach($accionesPorLote as $key => $apl)
												<option value="{{ $key }}">{{ $apl }}</option>
											@endforeach
		                                </select>
		                                <input type="submit" name="ejecutar" class="btn btn-success btn-xs" value="Enviar" />
		                            </div>
		                        </div>
							@endif
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

@section('inline_js')
	@parent
    $(".cambiar_estado").click(function(e){
		 e.stopPropagation();
		 e.preventDefault();
		if($(this).is(":checked")){
			$(this).prop('checked', false);
		}else{
			$(this).prop('checked', true);
		}
        cambiar_estado($(this),'{{ url('admin/modulos/cambiar_estado') }}');
    });
@stop
