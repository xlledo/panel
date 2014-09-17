@extends('packages/ttt/panel/layout/panel_layout')

@section('page_header')
	<h1><small>Mis preferencias <i class="icon-double-angle-right"></i></small> {{ $item->full_name }}</h1>
@stop
@section('content')
	<div class="row">
	    <div class="col-xs-12">
			<div id="tabs">
				<ul id="aux">
				     <li><a href="#datos" title="datos"><i class="icon-list"></i>  Datos</a></li>
				</ul>

				<div id="datos">
					<form class="clearfix" action="<?php action('Ttt\Panel\UsuarioController@actualizarPreferencias') ; ?>" method="post">
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
					                            <div class="form-group @if ($errors->first('first_name')) has-error @endif">
					                                <label for="first_name">Nombre *</label>
					                                <input type="text" class="form-control" name="first_name" id="first_name" value="{{ $item->first_name }}" size="20" />
													@if ($errors->first('first_name'))
														@foreach($errors->get('first_name') as $err)
															<span class="help-block">{{ $err }}</span>
														@endforeach
													@endif
					                            </div>
												<div class="form-group @if ($errors->first('last_name')) has-error @endif">
													<label for="first_name">Apellidos *</label>
													<input type="text" class="form-control" name="last_name" id="last_name" value="{{ $item->last_name }}" size="20" />
													@if ($errors->first('last_name'))
														@foreach($errors->get('last_name') as $err)
															<span class="help-block">{{ $err }}</span>
														@endforeach
													@endif
												</div>
					                        </div>
											<div class="col-md-3">
												<div class="form-group @if ($errors->first('email')) has-error @endif">
													<label for="email">E-mail *</label>
													<input type="text" class="form-control" name="email" id="email" value="{{ $item->email }}" size="20" />
													@if ($errors->first('email'))
														@foreach($errors->get('email') as $err)
															<span class="help-block">{{ $err }}</span>
														@endforeach
													@endif
												</div>
											</div>

											<div class="col-md-3">
												<div class="form-group @if ($errors->first('password')) has-error @endif">
													<label for="password">Password *</label>
													<input type="password" class="form-control" name="password" id="password" size="20" />
													@if ($errors->first('password'))
														@foreach($errors->get('password') as $err)
															<span class="help-block">{{ $err }}</span>
														@endforeach
													@endif
												</div>
												<div class="form-group @if ($errors->first('confirm_password')) has-error @endif">
													<label for="confirm_password">Repetir password *</label>
													<input type="password" class="form-control" name="confirm_password" id="confirm_password" value="" size="20" />
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
						<div class="acciones pull-right">

							<input type="submit" value="Guardar" class="boton btn btn-sm btn-success no-border" name="guardar"></li>

						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
@stop
