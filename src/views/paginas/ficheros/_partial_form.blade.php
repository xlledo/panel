@if ($action_fichero!='edit')
    <form class="clearfix" action="{{ action('Ttt\Panel\PaginasController@crearFichero');  }}" method="post"  enctype="multipart/form-data">
@else
    <form class="clearfix" action="{{ action('Ttt\Panel\PaginasController@actualizarFichero');  }}" method="post"  enctype="multipart/form-data">
@endif
    					    <div class="acciones pull-right">
					        <input type="submit" value="Guardar" name="guardar" class="btn btn-sm btn-success no-border">
					    </div>
                                                @if($action_fichero=='edit')
                                                <div class="row">
					        <div class="col-xs-12">
					            <div class="widget-box transparent">
					                <div class="widget-header widget-header-small">
					                    <h4 class="smaller lighter">Datos</h4>
					                </div>
					                <div class="widget-body">
                                                            <div class="widget-main row"> <!-- Form Ficheros -->
                                                                <div class="col-md-6">
                                                                    <div class="input-group">
                                                                        
                                                                        <input id='fichero'  type="text" class="form-control search-query" placeholder="{{$item->nombre }}" readonly="readonly">
                                                                            <span class="input-group-btn">
                                                                            <button data-toggle="modal" data-target="#modal_select_fichero"  class="btn btn-sm btn-success no-border">Seleccionar Fichero</button>
                                                                                            <i class="ace-icon fa fa-search icon-on-right bigger-110"></i>
                                                                                    </button>
                                                                            </span>
                                                                    </div>
                                                                </div>
                                                            </div>
					                </div>
					            </div>
					        </div>
                                                </div>
                                                @endif
                                                <div class='row'>
                                                <div class="col-xs-12">
                                                    <div class="widget-box transparent">
                                                        <div class="widget-header widget-header-small">
                                                            <h4 class="smaller lighter">Datos Opcionales</h4>
                                                        </div>
                                                        <div class="widget-main row">
                                                            <div class="col-md-6">
                                                                <div class="form-group @if($errors->first('titulo')) has-error  @endif">
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
                                                                <div class="form-group  @if($errors->first('alt')) has-error @endif  ">
                                                                    <label for="alt">Alt</label>
                                                                    <input type="text" name="alt" class="form-control"
                                                                           value="{{($action_fichero == 'edit') ? $alt : ''}}"
                                                                           id='alt'>
                                                                    @if($errors->first('alt'))
                                                                        @foreach($errors->get('alt') as $err)
                                                                        <span class="help-block">{{$err}}</span>
                                                                        @endforeach
                                                                    @endif                                                                              
                                                                    
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group @if($errors->first('descripcion')) has-error @endif ">
                                                                    <label for="descripcion">Descripcion</label>
                                                                    <input type="text" name="descripcion" class="form-control"
                                                                           value="{{($action_fichero == 'edit') ? $descripcion: ''}}"
                                                                           id='descripcion'>
                                                                    @if($errors->first('descripcion'))
                                                                        @foreach($errors->get('descripcion') as $err)
                                                                        <span class="help-block">{{$err}}</span>
                                                                        @endforeach
                                                                    @endif                                                                              
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group @if($errors->first('enlace')) has-error @endif ">
                                                                    <label for="enlace">Enlace</label>
                                                                    <input type="text" name="enlace" class="form-control"
                                                                           value="{{($action_fichero == 'edit') ? $enlace: ''}}"
                                                                           id='enlace'>
                                                                    @if($errors->first('enlace'))
                                                                        @foreach($errors->get('enlace') as $err)
                                                                        <span class="help-block">{{$err}}</span>
                                                                        @endforeach
                                                                    @endif                                                                              
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
					    </div>
					    <div class="acciones pull-right">
                                            <input type="hidden" name="asociar" value="1" />
                                            <input type="hidden" name="from_id" value="{{$item_id}}" />
                                            
                                            @if ($action_fichero == 'edit')
                                                <input type="hidden" name='id' value='{{$item->id}}' />
                                                <input id='fichero_id' type='hidden' name='id' value='{{$item->id}}' />
                                                <input type='hidden' name='pivot_id' value='{{$pivot_id }}'/>
                                                <input type='hidden' name='item_id' value='{{$item_id}}' />
                                            @endif
                                            
                                            <input type="submit" value="Guardar" class="boton btn btn-sm btn-success no-border" guardar">
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