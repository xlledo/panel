@extends('packages/ttt/panel/layout/panel_layout')

@section('tools')
	@if(Sentry::getUser()->hasAccess('ficheros::crear'))
		<!-- <a href="{{ action('Ttt\Panel\FicherosController@nuevo') }}" title="Nuevo Fichero" class="btn btn-sm btn-primary no-border"><i class="icon-file"></i> Nuevo</a></li> -->
	@endif
@stop

@section('page_header')
	@if($action == 'create')
		<h1>Nuevo elemento de <a href="{{ action('Ttt\Panel\FicherosController@index') }}" title="Volver al listado">Ficheros</a></h1>
	@else
                <h1>Editando <em>{{ $item->nombre }}</em></h1>
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
                                    <form class="clearfix" action="<?php echo ($action == 'create') ? action('Ttt\Panel\FicherosController@crear') : action('Ttt\Panel\FicherosController@actualizar') ; ?>" method="post"  enctype="multipart/form-data">
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
                                                            <div class="widget-main row"> <!-- Form Ficheros -->
                                                                <div class="col-md-4">
                                                                    <div class="form-group @if($errors->first('nombre')) has-error @endif">
                                                                        <label for='nombre'>Nombre </label>
                                                                        <input type='text' class='form-control' name='nombre' id='nombre' value='{{{$item->nombre}}}' size='20' />
                                                                            @if ($errors->first('nombre'))
                                                                                @foreach($errors->get('nombre') as $err)
                                                                                    <span class="help-block">{{ $err }}</span>
                                                                                @endforeach
                                                                            @endif                                                                        
                                                                     </div>
                                                                </div>

                                                                
                                                                <div class='col-md-4'>
                                                                    <div class='form-group'>
                                                                        <label for='fichero'>Fichero</label>
                                                                        <input type="file" name='fichero' class='form-cotrol' />
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="col-md-4">
                                                                    @if($action!='create')
                                                                        <h4>Preview</h4>
                                                                            <div>
                                                                                @if($item->esImagen())
                                                                                    <img src="{{ \URL::to('/') . '/' . $item->ruta . $item->fichero }}" style='max-width: 250px;'/>
                                                                                @endif
                                                                            </div>
                                                                    @endif
                                                                </div>
                                                            </div>
					                </div>
					            </div>
					        </div>
                                                
                                                <div class="col-xs-12">
                                                    <div class="widget-box transparent">
                                                        <div class="widget-header widget-header-small">
                                                            <h4 class="smaller lighter">Datos Opcionales</h4>
                                                        </div>
                                                        <div class="widget-main row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="titulo_defecto">Titulo</label>
                                                                    <input type="text" name="titulo_defecto" class="form-control" 
                                                                           value ="{{{$item->titulo_defecto }}}"
                                                                           >
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="alt_defecto">Alt</label>
                                                                    <input type="text" name="alt_defecto" class="form-control"
                                                                           value="{{{$item->alt_defecto}}}"
                                                                           >
                                                                </div>
                                                            </div>
                                                            <!-- ENLACE
                                                                                                                            <div class="col-md-12">
                                                                                                                                <div class="form-group">
                                                                                                                                    <label for="enlace_defecto">Enlace</label>
                                                                                                                                    <input type="text" name="enlace_defecto" class="form-control"
                                                                                                                                           value="{{$item->enlace_defecto}}"
                                                                                                                                           >
                                                                                                                                </div>
                                                                                                                            </div>                                                        
                                                                                                                            -->
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="descripcion_defecto">Descripcion</label>
                                                                    <textarea id="descripcion_defecto" name="descripcion_defecto" class="mceEditor">{{$item->descripcion_defecto}}</textarea>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
					    </div>
                                            
                                            
                                                
                                            <?php if( $action == 'edit' ): ?>
                                            <div class="col-xs-12">
                                                <div class="widget-box transparent">
                                                    <div class="widget-header widget-header-small">
                                                    <h4 class="smaller lighter">Datos del Fichero</h4>
                                                    </div>
                                                    <div class="widget-body">
                                                        <div class="widget-main row">
                                                            <div class="col-md-4">
                                                                <strong>Nombre: </strong> {{$item->nombre }} <br/>
                                                                <strong>Tipo: </strong> {{$item->mime }} <br/>
                                                                <strong>Tamaño: </strong>{{ number_format($item->peso/1000, 0) }} kb <br/>
                                                                <strong>Enlace: </strong>{{\URL::to('/') . '/' .$item->ruta . $item->fichero}}<br/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                                
					    <div class="acciones pull-right">
                                                <input type="hidden" value="{{$from_url}}" name="from_url"/>
					        <input type="submit" value="Guardar" class="boton btn btn-sm btn-success no-border" name="guardar"></li>
					    </div>
					</form>
				</div>
			</div>
		</div>
	</div>
	@if(Sentry::getUser()->hasAccess('ficheros::borrar'))
		@if ($action != 'create')
			<div class="space-6"></div>
			<div class="acciones">
                                <a href="#" title="Eliminar ?" class="btn btn-minier btn-danger no-border btn_confirmacion" 
                                    data-action="{{ action('Ttt\Panel\FicherosController@borrar' , $item->id )  }}"><i class="icon-trash"></i>Borrar</a>                                
			</div>
		@endif

	@endif
@stop
    @section('inline_js')
                @parent
    @stop