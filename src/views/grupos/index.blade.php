@extends('packages/ttt/panel/layout/panel_layout')
@section('tools')
	@if(Sentry::getUser()->hasAccess('grupos::crear'))
		<a href="{{ action('Ttt\Panel\GrupoController@nuevo') }}" title="Nuevo Grupo" class="btn btn-sm btn-primary no-border"><i class="icon-file"></i> Nuevo</a></li>
	@endif
@stop
@section('page_header')
	<h1>Listado de {{ $_titulo }}</h1>
@stop
@section('content')
	<div class="row">
	    <div class="col-xs-12">
			@if($items->count() === 0)
				<div class="alert alert-info">Actualmente no hay elementos en la base de datos</div>
	        @else
                <fieldset>
                    <table class="table table-striped table-bordered table-hover listado" summary="Listado de módulos" border="0" cellpadding="0" cellspacing="1">
                        <thead>
                            <tr>
                                <th scope="col">{{ ordenable_link($currentUrl, 'name', 'Nombre', $params, $params[Config::get('panel::app.orderDir')]) }}</th>
                            </tr>
                        </thead>
                        <tbody>

							@foreach($items as $index => $item)
								<tr class="@if($index % 2 == 0) par @else impar @endif">
									<td>
										@if(Sentry::getUser()->hasAccess('grupos::editar'))
											{{ link_to('admin/grupos/ver/' . $item->id, $item->name) }}
										@else
											{{ $item->name }}
										@endif
									</td>
								</tr>
							@endforeach
                        </tbody>
                    </table>
                    <div class="selectAcciones row">
                        <div class="elementos col-sm-6">
                            Número total de grupos {{ $items->count() }}
                        </div>
                    </div>
                </fieldset>
			@endif
		</div>
	</div>
@stop
