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
	Route::filter('hasPermission', 'Ttt\Panel\Filters\Panel@hasPermission');

	Route::get('/', 'Ttt\Panel\LoginController@index');

	Route::post('/validar', 'Ttt\Panel\LoginController@login');

	Route::get('/cerrar-sesion', 'Ttt\Panel\LoginController@logout');

	Route::get('/recuperar-clave', 'Ttt\Panel\RecoveryController@index');

	Route::post('/validar-usuario-recuperacion', 'Ttt\Panel\RecoveryController@comprobar');

	Route::get('/cambiar-clave/{resetCode}/{email}', 'Ttt\Panel\RecoveryController@cambiar');

	Route::post('/validar-cambio-clave', 'Ttt\Panel\RecoveryController@validar');

	Route::get('/dashboard', 'Ttt\Panel\DashboardController@index');

	//Gestión de módulos
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
        Route::post('/variablesglobales/acciones_por_lote', 'Ttt\Panel\VariablesglobalesController@accionesPorLote');//las acciones por lote
       
        Route::get('/variablesglobales/version/{id}', 'Ttt\Panel\VariablesglobalesController@getVersion'); //Las versiones
        
        //Versiones
        Route::get('/version/{id}', 'Ttt\Panel\VersionesController@getVersion');

        //Gestión de grupos
	Route::get('/grupos', 'Ttt\Panel\GrupoController@index');//listado
	Route::get('/grupos/nuevo', 'Ttt\Panel\GrupoController@nuevo');//formulario de nuevo grupo
	Route::get('/grupos/ver/{id}', 'Ttt\Panel\GrupoController@ver');//formulario de edición
	Route::get('/grupos/borrar/{id}', 'Ttt\Panel\GrupoController@borrar');//borrar

	Route::post('/grupos/crear', 'Ttt\Panel\GrupoController@crear');//creación de un nuevo grupo
	Route::post('/grupos/actualizar', 'Ttt\Panel\GrupoController@actualizar');//actualización de un grupo

	//Gestión de usuarios
	Route::get('/usuarios', 'Ttt\Panel\UsuarioController@index');//listado
	Route::post('/usuarios', 'Ttt\Panel\UsuarioController@index');//los filtros
	Route::get('/usuarios/nuevo', 'Ttt\Panel\UsuarioController@nuevo');//formulario de nuevo usuario
	Route::get('/usuarios/ver/{id}', 'Ttt\Panel\UsuarioController@ver');//formulario de edición
	Route::get('/usuarios/borrar/{id}', 'Ttt\Panel\UsuarioController@borrar');//borrar

	Route::post('/usuarios/crear', 'Ttt\Panel\UsuarioController@crear');//creación de un nuevo usuario
	Route::post('/usuarios/actualizar', 'Ttt\Panel\UsuarioController@actualizar');//actualización de un usuario
	Route::get('/preferencias', 'Ttt\Panel\UsuarioController@verPreferencias');//ver las preferencias
	Route::post('/preferencias', 'Ttt\Panel\UsuarioController@actualizarPreferencias');//actualizar las preferencias

        //Traducciones
        Route::get('/traducciones', 'Ttt\Panel\TraduccionesController@index');
        Route::get('/traducciones/nuevo', 'Ttt\Panel\TraduccionesController@nuevo');
        Route::get('/traducciones/borrar/{id}','Ttt\Panel\TraduccionesControllers@borrar');
        Route::get('/traducciones/ver/{id}','Ttt\Panel\TraduccionesController@ver');
        Route::get('/traducciones/borrar/{id}', 'Ttt\Panel\TraduccionesController@borrar');
        Route::get('/traducciones/borrarTraduccion/{id}', 'Ttt\Panel\TraduccionesController@borrarTraduccion');

        Route::post('/traducciones/acciones_por_lote','Ttt\Panel\TraduccionesController@accionesPorLote');
        Route::post('/traducciones/crear','Ttt\Panel\TraduccionesController@crear');
        Route::post('/traducciones/actualizar', 'Ttt\Panel\TraduccionesController@actualizar');
        Route::post('/traducciones/', 'Ttt\Panel\TraduccionesController@index');
        
        
	//Gestión de categorías
	Route::get('/categorias', 'Ttt\Panel\CategoriaController@index');

	Route::get('/categorias/nuevo-arbol', 'Ttt\Panel\CategoriaController@nuevoArbol');
	Route::post('/categorias/crear-arbol', 'Ttt\Panel\CategoriaController@crearArbol');
	Route::get('/categorias/ver-arbol/{id}', 'Ttt\Panel\CategoriaController@verArbol');//drag-and-drop de todo el árbol
	Route::get('/categorias/ordenar-arbol/{id}', 'Ttt\Panel\CategoriaController@ordenarAlfabeticamente');//Ordena alfabéticamente un árbol
	Route::post('/categorias/ordenar/', 'Ttt\Panel\CategoriaController@ordenar');//Ordena alfabéticamente un árbol
	Route::get('/categorias/ver-raiz/{id}', 'Ttt\Panel\CategoriaController@verRaiz');//formulario
	Route::post('/categorias/actualizar-raiz', 'Ttt\Panel\CategoriaController@actualizarRaiz');//post para actualizar árbol

	Route::get('/categorias/ver/{id}', 'Ttt\Panel\CategoriaController@ver');
	Route::get('/categorias/nuevo/{id}', 'Ttt\Panel\CategoriaController@nuevo');
	Route::post('/categorias/crear', 'Ttt\Panel\CategoriaController@crear');
	Route::post('/categorias/actualizar', 'Ttt\Panel\CategoriaController@actualizar');

	Route::get('/categorias/borrar-arbol/{id}', 'Ttt\Panel\CategoriaController@borrarArbol');
	Route::get('/categorias/borrar/{id}', 'Ttt\Panel\CategoriaController@borrar');

	//Gestión de idiomas
	Route::get('/idiomas', 'Ttt\Panel\IdiomaController@index');
	Route::post('/idiomas', 'Ttt\Panel\IdiomaController@index');//los filtros
	Route::post('/idiomas/acciones_por_lote', 'Ttt\Panel\IdiomaController@accionesPorLote');//las acciones por lote
	Route::get('/idiomas/nuevo', 'Ttt\Panel\IdiomaController@nuevo');
	Route::get('/idiomas/ver/{id}', 'Ttt\Panel\IdiomaController@ver');
	Route::get('/idiomas/borrar/{id}', 'Ttt\Panel\IdiomaController@borrar');
	Route::post('/idiomas/crear', 'Ttt\Panel\IdiomaController@crear');
	Route::post('/idiomas/actualizar', 'Ttt\Panel\IdiomaController@actualizar');
	Route::post('/idiomas/cambiar_estado', 'Ttt\Panel\IdiomaController@visibleNoVisible');

        //Gestion Ficheros
        Route::get('/ficheros', 'Ttt\Panel\FicherosController@index');
        Route::get('/ficheros/nuevo', 'Ttt\Panel\FicherosController@nuevo');
        Route::post('/ficheros/crear', 'Ttt\Panel\FicherosController@crear');
        Route::get('/ficheros/ver/{id}','Ttt\Panel\FicherosController@ver');
      	Route::post('/ficheros/actualizar', 'Ttt\Panel\FicherosController@actualizar');
        Route::get('/ficheros/borrar/{id}','Ttt\Panel\FicherosController@borrar');
        
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
