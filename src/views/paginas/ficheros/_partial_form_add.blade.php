
    <form class="clearfix" action="{{ action('Ttt\Panel\PaginasController@crearFichero');  }}" method="post"  enctype="multipart/form-data">

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
                                                </div>

                                                <div class='row'>
                                                <div class="col-xs-12">
                                                    <div class="widget-box transparent">
                                                        <div class="widget-header widget-header-small">
                                                            <h4 class="smaller lighter">Datos Opcionales</h4>
                                                        </div>
                                                        <div class="widget-main row">
                                                            <div class="col-md-6">
                                                                <div class="form-group @if($errors->first('titulo')) has-errors  @endif">
                                                                    <label for="titulo">Titulo</label>
                                                                    <input type="text" name="titulo" class="form-control"
                                                                           value ="{{ ($action_fichero == 'edit') ? $titulo : ''}}"
                                                                           id='titulo'>
                                                                    @if($errors->first('titulo'))
                                                                        @foreach($errors->get('titulo') as $err)
                                                                        <span class="help-block">{{$err}}</span>
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="alt">Alt</label>
                                                                    <input type="text" name="alt" class="form-control"
                                                                           value="{{($action_fichero == 'edit') ? $alt : ''}}"
                                                                           id='alt'>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="descripcion">Descripcion</label>
                                                                    <input type="text" name="descripcion" class="form-control"
                                                                           value="{{($action_fichero == 'edit') ? $descripcion: ''}}"
                                                                           id='descripcion'>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="enlace">Enlace</label>
                                                                    <input type="text" name="enlace" class="form-control"
                                                                           value="{{($action_fichero == 'edit') ? $enlace: ''}}"
                                                                           id='enlace'>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
					    </div>
					    <div class="acciones pull-right">
                                            <input type="hidden" name="asociar" value="1" />
                                            <input type="hidden" name="from_id" value="{{$item_id}}" />
                                            
                                            <input type="submit" value="Guardar" class="boton btn btn-sm btn-success no-border" name="guardar">
            </form>
</div>
