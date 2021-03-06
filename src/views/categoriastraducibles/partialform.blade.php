
@if ($nueva_traduccion)
    <div id="datos-new">
@elseif ($action != 'create' && $action != 'createArbol')
    <div id="datos-{{ $trad->idioma  }}">
@else {{-- Al crear un nuevo Item siempre lo guardamos en el idioma predeterminado --}}
    <div id="datos-{{ $idioma_predeterminado->codigo_iso_2}}">
@endif

@if($item->isRoot())
    <form class="clearfix" action="<?php echo ($action == 'createArbol') ? action('Ttt\Panel\CategoriaTraducibleController@crearArbol') : action('Ttt\Panel\CategoriaTraducibleController@actualizarRaiz') ; ?>" method="post">
@else
    <form class="clearfix" action="<?php echo ($action == 'create') ? action('Ttt\Panel\CategoriaTraducibleController@crear') : action('Ttt\Panel\CategoriaTraducibleController@actualizar') ; ?>" method="post">
@endif

@if($action != 'create' && $action != 'createArbol')
    <input type="hidden" name="id" id="id" value="{{ $item->id }}" />
@endif

@if($action == 'create')
    <input type="hidden" name="parent_id" id="parent_id" value="{{ $item->getRoot()->id }}" />
@endif

    <input type="hidden" name="nueva_traduccion" id="nueva_traduccion" value="<?php echo $nueva_traduccion ? 1 : 0; ?>" />

    <div class="acciones pull-right">
        <button type="submit" title="Guardar los cambios" class="btn btn-sm btn-success no-border"><i class="icon-save"></i> Guardar</button>
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
                                <div class="col-md-2">
                                    <div class="form-group @if( Input::old('idioma_' . $clave_idioma) == $trad->idioma || (Input::old('nueva_traduccion') && $nueva_traduccion) && $errors->first('idioma') ) has-error @endif">
                                    <label for="select_idioma">Idioma *</label>
                                    <select name="idioma_{{ $clave_idioma }}" tabIndex="1" id="select_idioma" class="form-control">
                                    <option value="">- Seleccionar -</option>
                                        @foreach($todos_idiomas as $id) {{-- Cogemos todos los idiomas disponibles  --}}
                                            @if( ! $item->hasTranslation($id->codigo_iso_2)) {{-- Solo muestra las traducciones que no existan en el item --}}
                                                <option value="{{$id->codigo_iso_2 }}"<?php if(! is_null(Input::old('idioma_' . $clave_idioma)) && Input::old('idioma_' . $clave_idioma) == $id->codigo_iso_2): ?> selected="selected"<?php endif; ?>>{{ ucfirst($id->nombre) }}</option>
                                            @endif
                                        @endforeach
                                        </select>
                                         @if( Input::old('idioma_' . $clave_idioma) == $trad->idioma || (Input::old('nueva_traduccion') && $nueva_traduccion) && $errors->first('idioma') )
                                            @foreach($errors->get('idioma') as $err)
                                                    <span class="help-block">{{ $err }}</span>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                        @else
                            <input type="hidden" name="idioma_{{ $clave_idioma }}" id="hidden_idioma" value="{{ $clave_idioma }}">
                        @endif
                        <input type="hidden" name="clave_idioma_campos" id="clave_idioma_campos" value="{{ $clave_idioma }}">
                        <div class="col-md-3">
                            <div class="checkbox">
                                <label for="visible_{{ $clave_idioma }}">
                                    <input type="checkbox" tabIndex="2" class="ace ace-checkbox-2" name="visible_{{ $clave_idioma }}" id="visible_{{ $clave_idioma }}" value="1"<?php if($item->visible): ?> checked="checked" <?php endif; ?> data-html="true" data-rel="popover" data-trigger="focus" data-placement="left" data-content="Atención, modificar este dato afectará a todas las traducciones" title="<i class='icon-warning-sign'></i> Campo común" />
                                    <span class="lbl"> <i class="icon-flag"></i> Visible</span>
                                </label>
                            </div>
                            @if($item->isRoot())
                                <div class="checkbox">
                                    <label for="protegida_{{ $clave_idioma }}">
                                        <input type="checkbox" tabIndex="3" class="ace ace-checkbox-2" name="protegida_{{ $clave_idioma }}" id="protegida_{{ $clave_idioma }}" value="1"<?php if($item->protegida): ?> checked="checked" <?php endif; ?> data-html="true" data-rel="popover" data-trigger="focus" data-placement="left" data-content="Atención, modificar este dato afectará a todas las traducciones" title="<i class='icon-warning-sign'></i> Campo común" />
                                        <span class="lbl"><i class="icon-flag"></i>Protegida</span>
                                    </label>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-3">
                            <div class="form-group @if( (Input::old('idioma_' . $clave_idioma) == $trad->idioma || (Input::old('nueva_traduccion') && $nueva_traduccion) ) && $errors->first('nombre') ) has-error @endif">
                                <label for="nombre_{{ $clave_idioma }}">Nombre *</label>
                                <input type="text" tabIndex="4" class="form-control" name="nombre_{{ $clave_idioma }}" id="nombre_{{ $clave_idioma }}" value="{{{ $trad->nombre }}}" size="20" />
                                @if( Input::old('idioma_' . $clave_idioma) == $trad->idioma || (Input::old('nueva_traduccion') && $nueva_traduccion) && $errors->first('nombre') )
                                    @foreach($errors->get('nombre') as $err)
                                        <span class="help-block">{{ $err }}</span>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        @if(Sentry::getUser()->isSuperUser() && ! in_array($action, array('create', 'createArbol')))
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="slug">Slug</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-flag"></i></span>
                                        <input type="text" tabIndex="5" class="form-control" name="slug_{{ $clave_idioma }}" id="slug_{{ $clave_idioma }}" value="{{ $item->slug }}" readonly="readonly" size="20" data-html="true" data-rel="popover" data-trigger="focus" data-placement="left" data-content="Atención, modificar este dato afectará a todas las traducciones" title="<i class='icon-warning-sign'></i> Campo común"/>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if(!$item->isRoot())
                            <div class="col-md-3">
                                <div class="form-group @if( (Input::old('idioma_' . $clave_idioma) == $trad->idioma || (Input::old('nueva_traduccion') && $nueva_traduccion)) && $errors->first('valor') ) has-error @endif">
                                    <label for="valor_{{ $clave_idioma }}">Valor *</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-flag"></i></span>
                                        <input type="text" tabIndex="6" class="form-control" name="valor_{{ $clave_idioma }}" id="valor_{{ $clave_idioma }}" value="{{ $item->valor }}" size="20"  data-html="true" data-rel="popover" data-trigger="focus" data-placement="left" data-content="Atención, modificar este dato afectará a todas las traducciones" title="<i class='icon-warning-sign'></i> Campo común" />
                                    </div>
                                    @if( Input::old('idioma_' . $clave_idioma) == $trad->idioma || (Input::old('nueva_traduccion') && $nueva_traduccion) && $errors->first('valor') )
                                        @foreach($errors->get('valor') as $err)
                                            <span class="help-block">{{ $err }}</span>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="acciones pull-right">
        <button type="submit" title="Guardar los cambios" class="btn btn-sm btn-success no-border"><i class="icon-save"></i> Guardar</button>
    </div>
</form>
    @if($action != 'create' && $action != 'createArbol' && $clave_idioma != 'new')
        @if($trad->idioma != $idioma_predeterminado->codigo_iso_2 && Sentry::getUser()->hasAccess('categorias-traducibles::borrarTraduccion'))
            <div class="col-xs-6">
                <a href="{{ action('Ttt\Panel\CategoriaTraducibleController@borrarTraduccion', array($item->id, $trad->idioma) )  }}" title="Borrar Traducción" class="btn btn-minier btn-danger no-border btn-confirmacion" data-mensaje="¿Seguro que deseas borrar la traducción?">Borrar Traduccion</a>
            </div>
        @endif
    @endif
</div>
