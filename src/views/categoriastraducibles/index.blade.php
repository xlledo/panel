@extends('packages/ttt/panel/layout/panel_layout')
@section('tools')
	@if(Sentry::getUser()->hasAccess('categorias-traducibles::crear-arbol'))
		<a href="{{ action('Ttt\Panel\CategoriaTraducibleController@nuevoArbol') }}" title="Nuevo árbol de categorías" class="btn btn-sm btn-primary no-border"><i class="icon-file"></i> Nuevo árbol de categorías</a></li>
	@endif
@stop
@section('page_header')
	<h1>Árboles de categorías <small> <i class="icon-double-angle-right"></i> Listado</small></h1>
@stop
@section('content')
	<div class="row">
	    <div class="col-xs-12">
			@if($items->count() === 0)
				<div class="alert alert-info">Actualmente no hay elementos en la base de datos</div>
	        @else
                <fieldset>
                    <table class="table table-striped table-bordered table-hover listado" summary="Listado de árboles de categorías" border="0" cellpadding="0" cellspacing="1">
                        <thead>
                            <tr>
                                <th scope="col" width="40">Visible</th>
                                <th scope="col">{{ ordenable_link($currentUrl, 'nombre', 'Nombre', $params, $params[Config::get('panel::app.orderDir')]) }}</th>
                            </tr>
                        </thead>
                        <tbody>

							@foreach($items as $index => $item)
								<tr class="@if($index % 2 == 0) par @else impar @endif">
									<td class="center">
										@if($item->visible)
											<span class="label label-success">Sí</span>
										@else
											<span class="label label-danger">No</span>
										@endif
									</td>
									<td>
										@if(Sentry::getUser()->hasAccess('categorias-traducibles::editar-arbol'))
											{{ link_to('admin/categorias-traducibles/ver-arbol/' . $item->id, $item->nombre) }}
										@else
											{{ $item->nombre }}
										@endif
									</td>
								</tr>
							@endforeach
                        </tbody>
                    </table>
                    <div class="selectAcciones row">
                        <div class="elementos col-sm-6">
                            Número total de árboles de categorías {{ $items->count() }}
                        </div>
                    </div>
                </fieldset>
			@endif
		</div>
	</div>
@stop
