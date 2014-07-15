<?php
Route::get('/', function()
{
	return Panel::saluda();
	//return View::make('hello');
});




Route::group(array('prefix' => 'admin'), function()
{

	// Filtros de control de logueado o no
	Route::filter('notLogged', 'Ttt\Panel\Filters\Panel@notLogged');
	Route::filter('logged', 'Ttt\Panel\Filters\Panel@logged');

	Route::get('/', 'Ttt\Panel\LoginController@index');

	Route::post('/validar', 'Ttt\Panel\LoginController@login');

	Route::get('/cerrar-sesion', 'Ttt\Panel\LoginController@logout');

	Route::get('/recuperar-clave', 'Ttt\Panel\RecoveryController@index');

	Route::post('/validar-usuario-recuperacion', 'Ttt\Panel\RecoveryController@comprobar');

	Route::get('/cambiar-clave/{resetCode}/{email}', 'Ttt\Panel\RecoveryController@cambiar');

	Route::post('/validar-cambio-clave', 'Ttt\Panel\RecoveryController@validar');

	Route::get('/dashboard', 'Ttt\Panel\DashboardController@index');

	Route::get('/modulos', 'Ttt\Panel\ModuloController@index');
	Route::post('/modulos', 'Ttt\Panel\ModuloController@index');//los filtros
	Route::post('/modulos/acciones_por_lote', 'Ttt\Panel\ModuloController@accionesPorLote');//las acciones por lote

	Route::get('/modulos/nuevo', 'Ttt\Panel\ModuloController@nuevo');
	Route::get('/modulos/ver/{id}', 'Ttt\Panel\ModuloController@ver');
	Route::get('/modulos/borrar/{id}', 'Ttt\Panel\ModuloController@borrar');
	Route::post('/modulos/crear', 'Ttt\Panel\ModuloController@crear');
	Route::post('/modulos/actualizar', 'Ttt\Panel\ModuloController@actualizar');
	Route::post('/modulos/cambiar_estado', 'Ttt\Panel\ModuloController@visibleNoVisible');

        //Variables Globales
	Route::get('/variablesglobales', 'Ttt\Panel\VariablesglobalesController@index');
        Route::get('/variablesglobales/nuevo', 'Ttt\Panel\VariablesglobalesController@nuevo');
        Route::get('/variablesglobales/ver/{id}', 'Ttt\Panel\VariablesglobalesController@ver');        
        Route::get('/variablesglobales/borrar/{id}', 'Ttt\Panel\VariablesglobalesController@borrar');
        Route::post('/variablesglobales/crear', 'Ttt\Panel\VariablesglobalesController@crear');
        Route::post('/variablesglobales/actualizar', 'Ttt\Panel\VariablesglobalesController@actualizar');

	Route::post('/variablesglobales', 'Ttt\Panel\VariablesglobalesController@index');//los filtros
        
        
	Route::get('/hola', function()
	{
		return Panel::saluda();
	});
	/*
		Route::get('/superadmin', function()
		{
			try{
				$adminGroup = Sentry::findGroupByName('Superadmin');

				$superUser = Sentry::createUser(array(
					'email'      => 'ximo@trestristestigres.com',
					'password'   => 'j4k4rt4',
					'activated'  => TRUE,
					'first_name' => 'Ximo',
					'last_name'  => 'Lledó',
				));

				$superUser->addGroup($adminGroup);

				$message = 'Creado el usuario ' . $superUser['first_name'] . ' ' . $superUser['last_name'] . ' [' . $superUser['email'] . ']';
			}
			catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
			{
			    $message = 'Login field is required.';
			}
			catch (Cartalyst\Sentry\Users\PasswordRequiredException $e)
			{
			    $message = 'Password field is required.';
			}
			catch (Cartalyst\Sentry\Users\UserExistsException $e)
			{
			    $message = 'User with this login already exists.';
			}
			catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
			{
			    $message = 'Group was not found.';
			}

			Session::flash('message', $message);

			return Redirect::action('Xll\Admin\HomeController@inicio');
		});
	*/
});
