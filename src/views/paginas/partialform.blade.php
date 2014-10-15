
@if ($nueva_traduccion)
    <div id="datos-nuevatraduccion">
@elseif ($action != 'create')        
    <div id="datos-{{ $trad->idioma  }}">
@else {{-- Al crear un nuevo Item siempre lo guardamos en el idioma predeterminado --}}
    <div id="datos-{{ $idioma_predeterminado->codigo_iso_2}}">
@endif

            @if ($nueva_traduccion)
                <form class="clearfix" action="<?php echo ($action == 'create') ? action('Ttt\Panel\PaginasController@crear') : action('Ttt\Panel\PaginasController@actualizar') ; ?>" method="post">
            @else
                <form class="clearfix" action="<?php echo ($action == 'create') ? action('Ttt\Panel\PaginasController@crear') : action('Ttt\Panel\PaginasController@actualizar') ; ?>" method="post">
            @endif
                            @if($action != 'create')
                                    <input type="hidden" name="id" id="id" value="{{ $item->id }}" />
                            @endif
                            <div class="acciones pull-right">
                                <button type="submit" class="btn btn-sm btn-success no-border"><i class="icon-save"></i> Guardar</button>
                            </div>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="widget-box transparent">
                                            <div class="widget-header widget-header-small">
                                                <h4 class="smaller lighter">Datos</h4>
                                            </div>
                                            <div class="widget-body">
                                                <div class="widget-main row">
                                                    @if($nueva_traduccion)
                                                            <div class="col-md-3">
                                                                <div class="form-group @if(($errors->first('idioma') && $action=='create') || ($errors->first('idioma') && $idioma_error==$trad->idioma) || (isset($nueva_traduccion) && $errors->first('idioma'))) has-error @endif">
                                                                <label for="select_idioma">Idioma *</label>
                                                                <select name="idioma" id="select_idioma" class="form-control">
                                                                <option value=""> - Seleccionar - </option>
                                                                    @foreach($todos_idiomas as $id) {{-- Cogemos todos los idiomas disponibles  --}}
                                                                        @if( ! $item->traduccion($id->codigo_iso_2)) {{-- Solo muestra las traducciones que no existan en el item --}}
                                                                            <option value="{{$id->codigo_iso_2 }}">{{ ucfirst($id->nombre) }}</option>
                                                                        @endif
                                                                    @endforeach
                                                                    </select>
                                                                     @if(($errors->first('idioma') && $action=='create') || ($errors->first('idioma') && $idioma_error==$trad->idioma) || (isset($nueva_traduccion) && $errors->first('idioma')))
                                                                        @foreach($errors->get('idioma') as $err)
                                                                                <span class="help-block">{{ $err }}</span>
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                    @endif
                                                    <div class="col-md-9">
                                                        <div class="input-group @if(($errors->first('titulo') && $action=='create') || ($errors->first('titulo') && $idioma_error==$trad->idioma) || ($errors->first('titulo') && $nueva_traduccion)  ) has-error @endif">
                                                            <label for="titulo">Titulo *</label>
                                				<div class="input-group">
                                                                    <input type="text" class="form-control" name="titulo" id="titulo" value='{{ ($action=='create') ? $item->titulo : ( ($nueva_traduccion) ? $item_nuevatraduccion->titulo : (($idioma_error == $trad->idioma ) ? \Input::old('titulo') : $trad->titulo )) }}' size="60" />
                                                                </div>
                                                            @if(($errors->first('titulo') && $action=='create') || ($errors->first('titulo') && $idioma_error==$trad->idioma) || ($errors->first('titulo') && $nueva_traduccion ))
                                                                @foreach($errors->get('titulo') as $err)
                                                                                <span class="help-block">{{ $err }}</span>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                            <div class="widget-box transparent @if(($errors->first('texto') && $action=='create') || ($errors->first('texto') && $idioma_error==$trad->idioma) || ($errors->first('texto') && $nueva_traduccion ) ) has-error @endif ">
                                                <div class="widget-header widget-header-small">
                                                    <h4 class="smaller lighter">Texto</h4>
                                                </div>
                                                <div class="widget-body">
                                                    <textarea name="texto" class="mceEditor">{{ ($action=='create') ? $item->texto : ( ($nueva_traduccion) ? $item_nuevatraduccion->texto : (($idioma_error == $trad->idioma ) ? \Input::old('texto') : $trad->texto )) }}</textarea>
                                                </div>
                                                @if(($errors->first('texto') && $action=='create') || ($errors->first('texto') && $idioma_error==$trad->idioma) || ($errors->first('texto') && $nueva_traduccion ) ) 
                                                        @foreach($errors->get('texto') as $err)
                                                                <span class="help-block">{{ $err }}</span>
                                                        @endforeach
                                                @endif
                                            </div>
                                    </div>
                                </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="widget-box transparent">
                                        <div class="widget-header widget-header-small">
                                            <h4 class="smaller lighter">Opciones avanzadas</h4>
                                        </div>
                                        <div class="widget-body">
                                        <div class="widget-main row">
                                                <div class="input-group @if(($errors->first('slug') && $action=='create') || ($errors->first('slug') && $idioma_error==$trad->idioma) || ($errors->first('slug') && $nueva_traduccion  ) ) has-error @endif">
                                                    <label for="slug"> Clave </label>
                                                    <input name="slug" type="text" class="form-control" value="{{ ($nueva_traduccion || $action=='create') ? '' : $trad->slug }}" />
                                                </div>
                                            
                                            @if(($errors->first('slug') && $action=='create') || ($errors->first('slug') && $idioma_error==$trad->idioma) || ($errors->first('slug') && $nueva_traduccion ) )
                                                    @foreach($errors->get('slug') as $err)
                                                            <span class="help-block">{{ $err }}</span>
                                                    @endforeach
                                            @endif
                                            </div>
                                        </div>                               
                                    </div>
                                </div>
                            </div>
                                <div class="acciones pull-right">
                                        @if(! $nueva_traduccion && $action!='create')
                                            <input type="hidden" name="idioma" value="{{ $trad->idioma }}" />
                                        @endif
                                        @if($action=='create')
                                            <input type="hidden" name="idioma" value="{{ $idioma_predeterminado->codigo_iso_2 }}" />
                                        @endif
                                        @if($action != 'create')
                                            <input type="hidden" name="item_id" value="{{$item->id }}"/>
                                        @endif
                                    <button type="submit" class="btn btn-sm btn-success no-border"><i class="icon-save"></i> Guardar</button>
                                </div>
                    </form>
                    @if($action != 'create')
                        @if($trad->idioma != $idioma_predeterminado->codigo_iso_2)
                            <div class="col-xs-6">
                                <a href="#" title="Borrar Traducción" class="btn btn-minier btn-danger no-border btn_confirmacion" data-action="{{ action('Ttt\Panel\PaginasController@borrarTraduccion' , $trad->id )  }}"><i class="icon-trash"></i>Borrar traducción de {{$_titulo}}</a>
                            </div>
                        @endif
                    @endif
</div>