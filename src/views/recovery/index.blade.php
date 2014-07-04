@extends('packages/ttt/panel/layout/login_layout')
@section('content')
	<div class="col-sm-10 col-sm-offset-1">
		<div class="login-container">

			<div class="position-relative">
				<div id="forgot-box" class="forgot-box widget-box no-border visible">
					<div class="widget-body">
						<div class="widget-main">
							<h4 class="header red lighter bigger">
								<i class="icon-key"></i>
								Recuperar contraseña
							</h4>

							<div class="space-6"></div>
							<p>Introduzca su email para recuperar su contraseña</p>
							<form action="{{ action('Ttt\Panel\RecoveryController@comprobar') }}" method="post">
								<fieldset>
									<label class="block clearfix">
										<span class="block input-icon input-icon-right">
											<input type="text" class="form-control" placeholder="Usuario" name="email" id="email" />
											<i class="icon-envelope"></i>
										</span>
									</label>

									<div class="clearfix">
										<input type="submit" class="width-35 pull-right btn btn-sm btn-danger" value="Enviar" name="submit" />
									</div>

								</fieldset>
							</form>
						</div><!-- /widget-main -->

                        @include('packages/ttt/panel/layout/flash_messages')

						<div class="toolbar center">
							<div>
								<a href="{{ action('Ttt\Panel\LoginController@index') }}" class="back-to-login-link" title="Volver a login">
									Volver a login
									<i class="icon-arrow-right"></i>
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
	$("#email").focus();
@stop
