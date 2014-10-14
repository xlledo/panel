@if ($action_fichero!='edit')
    <form class="clearfix" action="{{ action('Ttt\Panel\PaginasController@crearFichero');  }}" method="post"  enctype="multipart/form-data">
@else
    <form class="clearfix" action="{{ action('Ttt\Panel\PaginasController@actualizarFichero');  }}" method="post"  enctype="multipart/form-data">
@endif
    					    <div class="acciones pull-right">
                                                <button type="submit" class="btn btn-sm btn-success no-border"><i class="icon-save"></i> Guardar</button>
					    </div>
                                                @if($action_fichero=='edit')
                                                <div class="row">
					        <div class="col-xs-12">
					            <div class="widget-box transparent">
                                                        
                                                            <div class='alert alert-block alert-info'>
                                                                <span>La ruta del fichero es: {{\URL::to('/') . '/' .$item->ruta . $item->fichero}}</span>
                                                            </div>
                                                        
					                <div class="widget-header widget-header-small">
					                    <h4 class="smaller lighter">Datos</h4>
					                </div>
					                <div class="widget-body">
                                                            <div class="widget-main row"> <!-- Form Ficheros -->
                                                                <div class="col-md-8">
                                                                    <div class="input-group">
                                                                        
                                                                        <input id='fichero'  type="text" class="form-control search-query" placeholder=" {{ $item->nombre }}" name="nombre" value='{{ $item->nombre }}'>
                                                                            <span class="input-group-btn">
                                                                            <button data-toggle="modal" data-target="#modal_select_fichero"  class="btn btn-sm btn-success no-border">Seleccionar Fichero</button>
                                                                                            <i class="ace-icon fa fa-search icon-on-right bigger-110"></i>
                                                                                    </button>
                                                                            </span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    @if($action_fichero =='edit' && $item->esImagen())
                                                                    <h4>Preview</h4>
                                                                        <div>
                                                                                <img src="{{ \URL::to('/') . '/' . $item->ruta . $item->fichero }}" />
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
					                </div>
					            </div>
					        </div>
                                                </div>
                                                @endif
                                                <!-- Datos especificos de la relacion -->
                                                <div class="row">
                                                    <div class="col-xs-12">
                                                        <div class="widget-box transparent">
                                                            <div class="widget-header widget-header-small">
                                                                <h4 class="smaller lighter">Datos de la relación</h4>
                                                            </div>
                                                            <div class="widget-main row">
                                                                <div class="col-xs-6">
                                                                    <div class="form-group">
                                                                        <label for="idioma">Idioma</label>
                                                                        <select name="idioma" class="form-control">
                                                                            <option value="-1" {{ @($idioma==-1) ? 'selected' : '' }}>- Todos -</option>
                                                                            <?php foreach($_todos_idiomas as $id): ?>
                                                                                <option value="{{$id->codigo_iso_2 }}" {{ ($id->codigo_iso_2 == $idioma)? 'selected' :'' }}>{{$id->nombre}}</option>
                                                                            <?php endforeach; ?>
                                                                        </select>
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
                                                            <h4 class="smaller lighter">Datos del fichero</h4>
                                                        </div>
                                                        <div class="widget-main row">
                                                            <div class="col-md-6">
                                                                <div class="form-group @if($errors->first('titulo_defecto')) has-error  @endif">
                                                                    <label for="titulo">Titulo</label>
                                                                    <input type="text" name="titulo_defecto" class="form-control"
                                                                           value ="{{ ($action_fichero == 'edit') ? $item->titulo_defecto : ''}}"
                                                                           id='titulo'>
                                                                    @if($errors->first('titulo_defecto'))
                                                                        @foreach($errors->get('titulo_defecto') as $err)
                                                                        <span class="help-block">{{$err}}</span>
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group  @if($errors->first('alt_defecto')) has-error @endif  ">
                                                                    <label for="alt">Alt</label>
                                                                    <input type="text" name="alt_defecto" class="form-control"
                                                                           value="{{($action_fichero == 'edit') ? $item->alt_defecto : ''}}"
                                                                           id='alt'>
                                                                    @if($errors->first('alt_defecto'))
                                                                        @foreach($errors->get('alt_defecto') as $err)
                                                                        <span class="help-block">{{$err}}</span>
                                                                        @endforeach
                                                                    @endif                                                                              
                                                                    
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group @if($errors->first('descripcion_defecto')) has-error @endif ">   
                                                                    <label for="descripcion">Descripción</label>
                                                                    <textarea id="descripcion" name="descripcion_defecto" class="mceEditor">{{($action_fichero == 'edit') ? $item->descripcion_defecto: ''}}</textarea>
                                                                    @if($errors->first('descripcion_defecto'))
                                                                        @foreach($errors->get('descripcion_defecto') as $err)
                                                                        <span class="help-block">{{$err}}</span>
                                                                        @endforeach
                                                                    @endif
                                                                    </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
					    </div>
                                                @if($action_fichero =='edit')
                                                    <!-- Datos del Fichero  -->
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
                                                                        <strong>Tamaño: </strong>{{ number_format($item->peso/1000, 1) }} kb <br/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>                         
                                                @endif
    					    <div class="acciones pull-right">
                                            <input type="hidden" name="asociar" value="1" />
                                            <input type="hidden" name="from_id" value="{{$item_id}}" />
                                            
                                            @if ($action_fichero == 'edit')
                                                <input type="hidden" name='id' value='{{$item->id}}' />
                                                <input id='fichero_id' type='hidden' name='fichero_id' value='{{$item->id}}' />
                                                <input type='hidden' name='pivot_id' value='{{$pivot_id }}'/>
                                                <input type='hidden' name='item_id' value='{{$item_id}}' />
                                            @endif
                                            
                                            <button type="submit" class="btn btn-sm btn-success no-border"><i class="icon-save"></i> Guardar</button>

            </form>
</div>
                                                
<script type='text/javascript'>
$(document).ready(function()
{
   $('.btn_asociar_fichero').click(function( event ){
      //event.stopImmediatePropagation();

      var idFichero = $(this).data('id-fichero');
      var nombre_fichero = $(this).data('fichero');
      
      $("#fichero_id").val(idFichero);
      $("#fichero").val(nombre_fichero);
      
      $("#modal_select_fichero").modal('hide');
   });
});                                                  
</script>