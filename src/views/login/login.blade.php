@extends('packages/ttt/panel/layout/login_layout')
@section('content')
	<div class="col-sm-10 col-sm-offset-1">
		<div class="login-container">

			<div class="position-relative">
				<div id="login-box" class="login-box visible widget-box no-border">
					<div class="widget-body">
						<div class="widget-main">
							<h4 class="header blue lighter bigger">
								<i class="icon-coffee green"></i>
								Control de acceso modificado
							</h4>

							<div class="space-6"></div>

							<form action="{{ action('Ttt\Panel\LoginController@login') }}" method="post">
								<fieldset>
									<label class="block clearfix">
										<span class="block input-icon input-icon-right">
											<input type="text" class="form-control" placeholder="Usuario" value="{{ Input::old('email') }}" name="email" id="email" />
											<i class="icon-user"></i>
										</span>
									</label>

									<label class="block clearfix">
										<span class="block input-icon input-icon-right">
											<input type="password" class="form-control" placeholder="Password" name="password" id="password" />
											<i class="icon-lock"></i>
										</span>
									</label>

									<div class="space"></div>

									<div class="clearfix">
										<input type="submit" class="width-35 pull-right btn btn-sm btn-primary" value="Acceder" name="submit" />
									</div>

									<div class="space-4"></div>
								</fieldset>
							</form>
						</div><!-- /widget-main -->

                        @include('packages/ttt/panel/layout/flash_messages')

						<div class="toolbar clearfix">
							<div>
								<a href="{{ action('Ttt\Panel\RecoveryController@index') }}" class="forgot-password-link" title="Recuperar contraseña">
									<i class="icon-arrow-left"></i>
									¿Olvidó su contraseña?
								</a>
							</div>
						</div>
					</div><!-- /widget-body -->
				</div><!-- /login-box -->
			</div><!-- /position-relative -->
		</div>
	</div><!-- /.col -->
@stop

@section('inline_js')
	if($('#email').val() == '')
	{
		$('#email').focus();
	}else{
		$('#password').focus();
	}
@stop
