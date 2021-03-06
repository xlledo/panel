
@if ($nueva_traduccion)
    <div id="datos-nuevatraduccion">
@elseif ($action != 'create')
    <div id="datos-{{ $trad->idioma  }}">
@else {{-- Al crear un nuevo Item siempre lo guardamos en el idioma predeterminado --}}
    <div id="datos-{{ $idioma_predeterminado->codigo_iso_2}}">
@endif

            @if ($nueva_traduccion)
                <form class="clearfix" action="<?php echo ($action == 'create') ? action('Ttt\Panel\TraduccionesController@crear') : action('Ttt\Panel\TraduccionesController@actualizar') ; ?>" method="post">
            @else
                <form class="clearfix" action="<?php echo ($action == 'create') ? action('Ttt\Panel\TraduccionesController@crear') : action('Ttt\Panel\TraduccionesController@actualizar') ; ?>" method="post">
            @endif
                            @if($action != 'create')
                                    <input type="hidden" name="id" id="id" value="{{ $item->id }}" />
                            @endif
                            <div class="acciones pull-right">
                                <button title="Guardar los cambios" type="submit" class="btn btn-sm btn-success no-border"><i class="icon-save"></i> Guardar</button>
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
                                                                <div class="form-group @if(($errors->first('idioma') && $action=='create') || ($errors->first('idioma') && $idioma_error==$trad->idioma) || ( isset($nueva_traduccion) && $errors->first('idioma'))) has-error @endif">
                                                                <label for="select_idioma">Idioma *</label>
                                                                <select name="idioma" id="select_idioma" class="form-control">
                                                                <option value="">- Seleccionar -</option>
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

                                                    <div class="col-md-3">
                                                        <div class="input-group @if(($errors->first('clave') && $action=='create') || ($errors->first('clave') && $idioma_error==$trad->idioma) ||( $errors->first('clave') && $nueva_traduccion ) ) has-error @endif">
                                                            <label for="clave">Clave *</label>
                                				<div class="input-group">
                                                                    <span class="input-group-addon"><i class="icon-flag"></i></span>
                                                                    <input type="text" class="form-control" name="clave" id="clave" 
                                                                           value="{{{ ($action=='create') ? $item->clave : ( ($nueva_traduccion) ? $item_nuevatraduccion->clave : (($idioma_error == $trad->idioma ) ? \Input::old('clave') : $item->clave )) }}}" 
                                                                           size="20" data-html="true" data-rel="popover" data-trigger="focus" 
                                                                           data-placement="left" data-content="Atención, modificar este dato afectará a todas las traducciones" title="<i class='icon-warning-sign'></i> Campo común"/>
                                                                </div>
                                                            @if(($errors->first('clave') && $action=='create') || ($errors->first('clave') && $idioma_error==$trad->idioma) || ($errors->first('clave') && $nueva_traduccion ))
                                                                        @foreach($errors->get('clave') as $err)
                                                                                <span class="help-block">{{ $err }}</span>
                                                                        @endforeach
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                            <div class="widget-box transparent @if(($errors->first('texto') && $action=='create') || ($errors->first('texto') && $idioma_error==$trad->idioma) || ($errors->first('texto') && $nueva_traduccion )) has-error @endif">
                                                <div class="widget-header widget-header-small">
                                                    <h4 class="smaller lighter">Texto</h4>
                                                </div>
                                                <div class="widget-body">
                                                    <textarea name="texto" class="mceEditor">{{ ($action=='create') ? $item->texto : ( ($nueva_traduccion) ? $item_nuevatraduccion->texto : (($idioma_error == $trad->idioma ) ? \Input::old('texto') : $trad->texto )) }}</textarea>
                                                </div>
                                                @if( ($errors->first('texto') && $action=='create') || ($errors->first('texto') && $idioma_error==$trad->idioma) || ($errors->first('texto') && $nueva_traduccion) )
                                                        @foreach($errors->get('texto') as $err)
                                                                <span class="help-block">{{ $err }}</span>
                                                        @endforeach
                                                @endif
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
                                        <button title="Guardar los cambios" type="submit" class="btn btn-sm btn-success no-border"><i class="icon-save"></i> Guardar</button>
                                </div>
                    </form>
                    @if($action != 'create')
                        @if($trad->idioma != $idioma_predeterminado->codigo_iso_2)
                            <div class="col-xs-6">
                                <a href="#" title="Borrar Traducción" class="btn btn-minier btn-danger no-border btn_confirmacion" data-action='{{ action('Ttt\Panel\TraduccionesController@borrarTraduccion' , $trad->id )  }}' ><i class="icon-trash"></i>Borrar traducción</a>
                            </div>
                        @endif
                    @endif
</div>
