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
                            No existen ficheros relacionados
        	        @else
                <form action="{{ url('admin/' . $modulo . '/acciones_por_lote') }}" method="post">
	                <fieldset>
	                    <table class="table table-striped table-bordered table-hover listado" summary="Listado de Variablesglobales" border="0" cellpadding="0" cellspacing="1">
	                        <thead>
	                            <tr>
                                            <th scope="col">Preview</th>
	                                    <th scope="col">Nombre</th>
                                            <th scope="col">Ruta</th>
                                            <th scope="col">Subido por</th>
                                        
					@if(Sentry::getUser()->hasAccess(array('variables-globales::editar', 'variables-globales::borrar'), FALSE))
                                            <th scope="col" width="30"><input type="checkbox" class="select_all"/></th>
                                        @endif
	                            </tr>
	                        </thead>
	                        <tbody>				
							@foreach($ficheros->getResults() as $index => $item)
									<tr class="@if($index % 2 == 0) par @else impar @endif">
                                                                                <td class="td_click">
                                                                                    @if($item->esImagen())
                                                                                        <img src="{{ $item->getStreamBase64($item->getSize(100))}}" style="max-width: 300px;"/>
                                                                                    @else
                                                                                        <i class="icon-file-text"></i>
                                                                                    @endif
                                                                                </td>
										<td class="td_click">
											@if(Sentry::getUser()->hasAccess('ficheros::editar'))
												{{ link_to('admin/' . $modulo. '/ver_fichero/' . $item->pivot->id, $item->nombre) }}
											@else
												{{ $item->nombre }}
											@endif
										</td>
                                                           
                                                                                <td class="td_click">
                                                                                    <input type="text" value="{{ \URL::to('/').'/'.$item->ruta.$item->fichero}}"  size="70"/>
                                                                                </td>
                                                                                <td class='td_click'>
                                                                                    {{ $item->maker->first_name . ' ' . $item->maker->last_name }}
                                                                                </td>
                                                                                    @if(Sentry::getUser()->hasAccess(array('ficheros::editar', 'ficheros::borrar'), FALSE))
                                                                                            <td><input class="item" type="checkbox" name="item[]" value="{{ $item->id }}" /></td>
                                                                                    @endif

									</tr>
								@endforeach                                                                
	                        </tbody>
	                    </table>
	                    <div class="selectAcciones row">
	                     
                                    <div class="acciones col-sm-12">
                                        <div class="pull-right form-inline selectAcciones">
                                            <label for="acciones_por_lote">Accion:</label>
                                            <select id="acciones_por_lote" name="accion" class="input-medium input-sm">
                                                <option value="0" selected="selected"> - Seleccionar - </option>
                                                    @foreach($acciones_por_lote_ficheros as $key => $apl)
                                                        <option value="{{$key}}">{{$apl}}</option>
                                                    @endforeach
                                            </select>
                                            <input type="submit" name="ejecutar" class="btn btn-success btn-xs" value="Enviar" />
                                        </div>
                                        
                                    </div>
                                    
	                    </div>
	                </fieldset>
                    <input name="from" type="hidden" value="{{$item_id}}" />
	            </form>
				<div class="center">
					
				</div>
			@endif
		</div>
	</div>