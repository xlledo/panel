

	<div class="row">
	    <div class="col-xs-12">
                
                
                
                <!--
                    <div class="widget-box">
                        <form method="POST" action="{{ url('admin/ficheros') }}">
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

                                </div>
                                <div class="widget-toolbox padding-8 clearfix">
                                    <div class="pull-right">
                                        <a href="{{ action('Ttt\Panel\FicherosController@index') }}" title="Mostrar Todos" class="btn btn-primary btn-xs" >Mostrar todos</a></li>
                                        <input type="submit" name="filtrar" value="Buscar" class="btn btn-success btn-xs"/>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                -->
                
	        <div class="space-12"></div>
			@if($ficheros->count() === 0)
				<div class="alert alert-info">Actualmente no hay elementos en la base de datos</div>
        	        @else
                <form action="{{ url('admin/ficheros/acciones_por_lote') }}" method="post">
	                <fieldset>
	                    <table class="table table-striped table-bordered table-hover listado" summary="Listado de Variablesglobales" border="0" cellpadding="0" cellspacing="1">
	                        <thead>
	                            <tr>
	                                <th scope="col">Nombre</th>
                                        <th scope="col">Preview</th>
					<th scope="col">Creado por </th>
                                        <th scope="col">Actualizado por </th>
					@if(Sentry::getUser()->hasAccess(array('variables-globales::editar', 'variables-globales::borrar'), FALSE))
                                            <th scope="col" width="30"><input type="checkbox" class="select_all"/></th>
                                        @endif
	                            </tr>
	                        </thead>
	                        <tbody>
								@foreach($ficheros->getResults() as $index => $item)
									<tr class="@if($index % 2 == 0) par @else impar @endif">
										<td class="td_click">
											@if(Sentry::getUser()->hasAccess('ficheros::editar'))
												{{ link_to('admin/ficheros/ver/' . $item->id, $item->nombre) }}
											@else
												{{ $item->nombre }}
											@endif
										</td>
                                                                                <td class="td_click">
                                                                                    @if($item->tipo == 'imagen')
                                                                                        <img src='{{URL::to('/') . '/' . $item->ruta . $item->fichero}}' width="100" />
                                                                                    @else
                                                                                        No disponible
                                                                                    @endif
                                                                                </td>
										<td class="td_click">{{ $item->maker->first_name }}</td>
										<td class="td_click">{{ $item->updater->first_name }}</td>
										@if(Sentry::getUser()->hasAccess(array('variables-globales::editar', 'variables-globales::borrar'), FALSE))
											<td><input class="item" type="checkbox" name="item[]" value="{{ $item->id }}" /></td>
										@endif
									</tr>
								@endforeach
	                        </tbody>
	                    </table>
	                    -<div class="selectAcciones row">
	                     paginador
	                    </div>
	                </fieldset>
	            </form>
				<div class="center">
					paginador 2
				</div>
			@endif
		</div>
	</div>