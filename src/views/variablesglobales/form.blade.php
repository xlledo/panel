@extends('packages/ttt/panel/layout/panel_layout')

@section('tools')
        <a href="{{ action('Ttt\Panel\VariablesglobalesController@index') }}" title="Volver al listado" class="btn btn-sm no-border"><i class="icon-double-angle-left"></i> Volver al listado</a>
	@if(Sentry::getUser()->hasAccess('variables-globales::crear') && $action != 'create')
                
		<a href="{{ action('Ttt\Panel\VariablesglobalesController@nuevo') }}" title="Nuevo elemento de {{$_titulo}}" class="btn btn-sm btn-primary no-border"><i class="icon-file"></i> Nuevo</a></li>
	
        @endif
@stop
@section('page_header')
	@if($action == 'create')
		<h1>Nuevo elemento de <a href="{{ action('Ttt\Panel\VariablesglobalesController@index') }}" title="Volver al listado">Variables globales</a></h1>
	@else
                <h1>Editando <em>{{$item->clave}}</em></h1>
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
					<form class="clearfix" action="<?php echo ($action == 'create') ? action('Ttt\Panel\VariablesglobalesController@crear') : action('Ttt\Panel\VariablesglobalesController@actualizar') ; ?>" method="post">
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
					                            <div class="input-group @if ($errors->first('clave')) has-error @endif">
					                                <label for="clave">Clave *</label>
					                                <input type="text" class="form-control" name="clave" id="clave" value="{{{ $item->clave }}}" size="20" />
                                                                        
										@if ($errors->first('clave'))
                                                                                        @foreach($errors->get('clave') as $err)
                                                                                                <span class="help-block">{{ $err }}</span>
                                                                                        @endforeach
										@endif
					                            </div>
					                        </div>
					                        <div class="col-md-3">
					                            <div class="input-group @if($errors->first('valor')) has-error @endif">
					                                <label for="valor">Valor *</label>
					                                <input type="text" class="form-control" name="valor" id="valor" value="{{{ $item->valor }}}" size="20" />

                                                                            @if ($errors->first('valor'))
                                                                                    @foreach($errors->get('valor') as $err)
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
					        <input type="submit" value="Guardar" class="boton btn btn-sm btn-success no-border" name="guardar"></li>
					    </div>
					</form>
				</div>
			</div>
		</div>
	</div>
	@if(Sentry::getUser()->hasAccess('variables-globales::borrar'))
		@if ($action != 'create')
			<div class="space-6"></div>
			<div class="acciones">
                                <a href="#" title="Borrar Elemento" class="btn btn-minier btn-danger no-border btn_confirmacion" data-action='{{ action('Ttt\Panel\VariablesglobalesController@borrar' , $item->id )  }}' ><i class="icon-trash"></i>Borrar Elemento</a>
			</div>
		@endif

	@endif
@stop
    @section('inline_js')
                @parent
                $(document).ready(function() {
                    tttjs.versiones.init();
                });
    @stop
