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
								Error {{ $codigo }}
							</h4>
						</div><!-- /widget-main -->

                        <div id="notificacion">
			                <div class="alert alert-block danger">
			                    <p>{{ $mensaje }}</p>
			                </div>
					    </div>

						<div class="toolbar clearfix">
							<div>
								@if(Sentry::getUser())
									<a href="{{ action('Ttt\Panel\DashboardController@index') }}" class="forgot-password-link" title="Ir a la página de Inicio">
										<i class="icon-arrow-left"></i>
										Ir a la página de inicio
									</a>
								@else
									<a href="{{ action('Ttt\Panel\LoginController@index') }}" class="forgot-password-link" title="Iniciar sesión">
										<i class="icon-arrow-left"></i>
										Iniciar sesión
									</a>
								@endif
							</div>
						</div>
					</div><!-- /widget-body -->
				</div><!-- /login-box -->
			</div><!-- /position-relative -->
		</div>
	</div><!-- /.col -->
@stop
