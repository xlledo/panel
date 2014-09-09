        <form class="clearfix" action="{{ action('Ttt\Panel\FicherosController@crear');  }}" method="post"  enctype="multipart/form-data">
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
                                                                <div class="col-md-6">
                                                                    <div class="form-group @if($errors->first('nombre')) has-error @endif">
                                                                        <label for='nombre'>Nombre *</label>
                                                                        <input type='text' class='form-control' name='nombre' id='nombre' value='{{$item->nombre}}' size='20' />
                                                                            @if ($errors->first('nombre'))
                                                                                @foreach($errors->get('nombre') as $err)
                                                                                    <span class="help-block">{{ $err }}</span>
                                                                                @endforeach
                                                                            @endif                                                                        
                                                                     </div>
                                                                </div>
                                                                <div class='col-md-6'>
                                                                    <div class='form-group'>
                                                                        <label for='fichero'>Fichero</label>
                                                                        <input type="file" name='fichero' class='form-cotrol' />
                                                                    </div>
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
                                                                           value =""
                                                                           >
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="alt_defecto">Alt</label>
                                                                    <input type="text" name="alt_defecto" class="form-control"
                                                                           value=""
                                                                           >
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="descripcion_defecto">Descripcion</label>
                                                                    <input type="text" name="descripcion_defecto" class="form-control"
                                                                           value=""
                                                                           >
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="enlace_defecto">Enlace</label>
                                                                    <input type="text" name="enlace_defecto" class="form-control"
                                                                           value=""
                                                                           >
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
					    </div>
					    <div class="acciones pull-right">
                                                
                                                
                                            <input type="hidden" name="from_url" value="admin/paginas" />
                                            <input type="hidden" name="asociar" value="1" />
                                            <input type="hidden" name="accion_asociar" value="admin/paginas/asociar_fichero/" />
                                            <input type="hidden" name="from_id" value="{{$item_id}}" />
                                            <input type="submit" value="Guardar" class="boton btn btn-sm btn-success no-border" name="guardar">
                                            
</div>
