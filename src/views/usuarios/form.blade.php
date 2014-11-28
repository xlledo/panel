@extends('packages/ttt/panel/layout/panel_layout')

@section('tools')
        <a href="{{ action('Ttt\Panel\UsuarioController@index') }}" title="Volver al listado" class="btn btn-sm btn-primary no-border"><i class="icon-double-angle-left"></i> Volver al listado</a>
    @if(Sentry::getUser()->hasAccess('usuarios::crear') && $action!= 'create')
        <a href="{{ action('Ttt\Panel\UsuarioController@nuevo') }}" title="Nuevo elemento en {{ $_titulo }}" class="btn btn-sm btn-primary no-border"><i class="icon-file"></i> Nuevo</a></li>
    @endif
@stop
@section('page_header')
    @if($action == 'create')
        <h1>Nuevo elemento de <a href="{{ action('Ttt\Panel\UsuarioController@index') }}" title="Volver al listado">Usuarios</a></h1>
    @else
        <h1>Editando <em>{{$item->full_name}}</em></h1>
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
                <form class="clearfix" action="<?php echo ($action == 'create') ? action('Ttt\Panel\UsuarioController@crear') : action('Ttt\Panel\UsuarioController@actualizar'); ?>" method="post">
                    @if($action != 'create')
                    <input type="hidden" name="id" id="id" value="{{ $item->id }}" />
                    @endif
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
                                        <div class="col-md-3">
                                            <div class="form-group @if ($errors->first('first_name')) has-error @endif">
                                                <label for="first_name">Nombre *</label>
                                                <input type="text" tabIndex="1" class="form-control" name="first_name" id="first_name" value="{{{ $item->first_name }}}" size="20" />
                                                @if ($errors->first('first_name'))
                                                @foreach($errors->get('first_name') as $err)
                                                <span class="help-block">{{ $err }}</span>
                                                @endforeach
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label for="grupo">Grupo</label>
                                                <select name="grupo" tabIndex="4" id="grupo" class="form-control">
                                                    <option value="">- Seleccionar -</option>
                                                    @foreach($grupos as $grupo)
                                                    <option value="{{ $grupo->id }}"<?php if ($item->groups->count() && $item->groups->first()->id == $grupo->id): ?> selected="selected"<?php endif; ?>>{{ $grupo->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group @if ($errors->first('last_name')) has-error @endif">
                                                <label for="first_name">Apellidos *</label>
                                                <input type="text" tabIndex="2" class="form-control" name="last_name" id="last_name" value="{{{ $item->last_name }}}" size="20" />
                                                @if ($errors->first('last_name'))
                                                @foreach($errors->get('last_name') as $err)
                                                <span class="help-block">{{ $err }}</span>
                                                @endforeach
                                                @endif
                                            </div>
                                            <div class="form-group @if ($errors->first('password')) has-error @endif">
                                                <label for="password">Contraseña<?php if ($action == 'create'): ?> *<?php endif; ?></label>
                                                <input type="password" tabIndex="5" class="form-control" name="password" id="password" size="20" />
                                                @if ($errors->first('password'))
                                                @foreach($errors->get('password') as $err)
                                                <span class="help-block">{{ $err }}</span>
                                                @endforeach
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group @if ($errors->first('email')) has-error @endif">
                                                <label for="email">E-mail *</label>
                                                <input type="text" tabIndex="3" class="form-control" name="email" id="email" value="{{ $item->email }}" size="20" />
                                                @if ($errors->first('email'))
                                                @foreach($errors->get('email') as $err)
                                                <span class="help-block">{{ $err }}</span>
                                                @endforeach
                                                @endif
                                            </div>
                                            <div class="form-group @if ($errors->first('confirm_password')) has-error @endif">
                                                <label for="confirm_password">Repetir contraseña<?php if ($action == 'create'): ?> *<?php endif; ?></label>
                                                <input type="password" tabIndex="6" class="form-control" name="confirm_password" id="confirm_password" value="" size="20" />
                                                @if ($errors->first('confirm_password'))
                                                @foreach($errors->get('confirm_password') as $err)
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
                    {{-- Añadimos la caja de permisos --}}
                    <div class="row" id="permissionBox" style="display:none;">
                        <div class="col-xs-12">
                            <div class="widget-box transparent">
                                <div class="widget-header widget-header-small">
                                    <h4 class="smaller lighter">Permisos</h4>
                                </div>
                                <div class="widget-body">
                                    <div class="widget-main row">
                                        @foreach($configGroupsOrderedByKey as $moduloKey => $acciones)
                                        <table class="table table-bordered" id="tabla{{ ucfirst($moduloKey) }}" summary="{{ $moduloKey }}" border="0" cellpadding="0" cellspacing="1">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Acciones del módulo {{ ucfirst(str_replace('-', ' ', $moduloKey)) }}</th>
                                                    <th scope="col" width="150" class="center">Permitida</th>
                                                    <th scope="col" width="150" class="center">No Permitida</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($acciones as $actionKey => $metodos)
                                                <tr class="lineaPermiso" id="linea_{{ $moduloKey }}_{{ $actionKey }}">
                                                    <td>
                                                        <p><strong>{{ ucfirst($actionKey) }}</strong>:</p>
                                                        <p>{{ implode(', ', $metodos) }}</p>
                                                    </td>
                                                    <td class="center">
                                                        <input class="radioPermission radioPermissionSi" value="si" type="radio"<?php if ($item->hasAccess($moduloKey . '::' . $actionKey)): ?> checked="checked"<?php endif; ?> id="{{ $moduloKey }}_{{ $actionKey }}_si" name="{{ $moduloKey }}::{{ $actionKey }}" />
                                                    </td>
                                                    <td class="center">
                                                        <input class="radioPermission radioPermissionNo" value="no" type="radio"<?php if (!$item->hasAccess($moduloKey . '::' . $actionKey)): ?> checked="checked"<?php endif; ?> id="{{ $moduloKey }}_{{ $actionKey }}_no" name="{{ $moduloKey }}::{{ $actionKey }}" />
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div class="space-6"></div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="acciones pull-right">

                        <button type="submit" title="Guardar los cambios" class="btn btn-sm btn-success no-border"><i class="icon-save"></i> Guardar</button>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@if(Sentry::getUser()->hasAccess('usuarios::borrar'))
    @if ($action != 'create')
    <div class="space-6"></div>
    <div class="acciones">
        <a class="btn btn-minier btn-danger no-border btn-confirmacion" title="Eliminar ?" href="{{ action('Ttt\Panel\UsuarioController@borrar', $item->id) }}"><i class="icon-trash"></i>Borrar</a>
    </div>
    @endif
@endif
@stop
@section('inline_js')
    @parent
    $(document).ready(function() {
        tttjs.usuarios.init({
            vista: 'edicion',
            currentGroupPermission: <?php if ($item->groups->count() && $item->groups->first()->id == $grupo->id): ?>{}<?php else: ?><?php echo json_encode($item->groups->first()->permissions); ?><?php endif; ?>
        });
    });
@stop
