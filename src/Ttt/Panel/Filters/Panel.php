<?php
namespace Ttt\Panel\Filters;


use \Sentry;

class Panel{

	public function logged($route, $request)
	{
		if(Sentry::check())
		{
			return \Redirect::guest(\Config::get('panel::app.access_url') . '/dashboard');
		}
	}


	public function notLogged()
	{
		if(! Sentry::check())
		{
			\Session::flash('messages', array(
				array(
					'class' => 'alert-danger',
					'msg'   => 'Debe estar registrado para poder acceder a la aplicación'
				)
			));
			return \Redirect::guest(\Config::get('panel::app.access_url'));
		}
	}

	public function hasPermission($route, $request)
	{
		//cogemos el full qualifid name y la acción
		//$routeAction = \Route::currentRouteAction();//p.e: Ttt\Panel\LoginController@index
		//$routeAction = $route->currentRouteAction();//p.e: Ttt\Panel\LoginController@index
		$routeAction = $route->getActionName();//p.e: Ttt\Panel\LoginController@index

		//separamos Clase de Método
		$routeActionArray = explode('@', $routeAction);
		if(count($routeActionArray) !== 2)
		{
			\Session::flash('messages', array(
				array(
					'class' => 'alert-danger',
					'msg'   => 'La acción ' . $routeAction . ' no contiene la estructura correcta. P.e: Ttt\Panel\LoginController@index'
				)
			));
			return \Redirect::guest(\Config::get('panel::app.access_url'));
		}

		$fullNameClass = '\\' . ltrim($routeActionArray[0], '\\');
		$methodName    = $routeActionArray[1];
		$moduleSlug = $fullNameClass::$moduleSlug;

		//si es nula o el método es del tipo referenciaXXXX se trata de un módulo permisible (accesible por cualquiera)
		if(! is_null($moduleSlug) && ! preg_match('/^referencia(.+)$/', $methodName))
		{
			//que empiece la fiesta
			$user = \Sentry::getUser();
			//starts_with($mergedPermission, $checkPermission)
			$permission = $this->guessPermission($fullNameClass, $methodName, $moduleSlug);

//			return $permission;

			if(! $user->hasAccess($permission))
			{
				//caso especial
				if($request->ajax())
				{
					return array(
						'error'   => TRUE,
						'message' => 'No tiene suficientes privilegios para acceder [' . $permission . ']'
					);
				}

				\Session::flash('messages', array(
					array(
						'class' => 'alert-danger',
						'msg'   => 'No tiene suficientes privilegios para acceder [' . $permission . ']'
					)
				));
				return \Redirect::guest(\Config::get('panel::app.access_url') . '/dashboard');
			}

		}
	}

	/**
	* Resuelve el permiso necesario para comparar con el usuario
	*
	* @param $fullNameClass string
	* @param $methodName string
	* @param $moduleSlug string
	*
	* @return @permission string
	*/
	protected function guessPermission($fullNameClass, $methodName, $moduleSlug)
	{
		$permission = 'vacio';

		$allAllowedActions = \Panel::getConfigMergedForFile();
		foreach($allAllowedActions as $moduloKey => $acciones)
		{
			if($moduleSlug !== $moduloKey)
			{
				continue;
			}
			$permission = $moduloKey;
			foreach($acciones as $actionKey => $metodos)
			{
				foreach($metodos as $metodo)
				{
					$metodoArray = explode(':', $metodo);

					if($methodName == $metodoArray[0])
					{
						//2 opciones
						//1 sin parámetros
						if(count($metodoArray) == 1)
						{
							$permission .= '::' . $actionKey;
							break 3;
						}
						//2 se esperan parámetros del tipo accionesPorLote:accion.noVisible
						else
						{
							$methodParams = $metodoArray[1];
							list($param, $value) = explode('.', $methodParams);
							if(\Input::get($param) == $value)
							{
								$permission .= '::' . $actionKey;
								break 3;
							}
						}
					}
				}
			}
		}
		return $permission;
	}

	/**
	* Se encarga de controlar la Pila usada para las referencias y construir las migas
	*/
	public function pila($route, $request)
	{
		//solo queremos tratar la pila si no es una petición AJAX
		if($request->ajax())
		{
			return;
		}
		$routeAction = $route->getActionName();//p.e: Ttt\Panel\LoginController@index
		//extraemos Controller y método
		list($controller, $method) = explode('@', $routeAction);

		/*comprobamos si tenemos que trabajar con la Pila
		1 - Si controlador es Dashboard: Reseteamos Pila
		2 - Si controlador es Login y método es logout: Vaciamos Pila
		3 - Si controlador no es Login ni Dashboard
		*/
		$tratamosPila = FALSE;
		switch($controller)
		{
			case 'Ttt\Panel\DashboardController':
				//Inicializamos Pila con el Dashboard
				\Pila::reset();
				return;
				break;
			case 'Ttt\Panel\LoginController':
				if($method === 'logout')
				{
					//Limpiamos la pila y la dejamos vacía
					\Pila::clean();
					return;
				}
				break;
			case 'Ttt\Panel\RecoveryController':
				return;
				break;
			default:
				//cualquier otro controlador, vemos si está llamando a un método que debe tratar la Pila
				if(in_array($method, array(
					'ver', 'nuevo', 'index', 'verFichero', 'nuevoFichero'
				)) || preg_match('/^referencia(.+)$/', $method, $matches))
				{
					$tratamosPila = TRUE;
				}
				break;
		}

		if(! $tratamosPila)
		{
			return;
		}
		$moduleSlug = $controller::$moduleSlug;//propiedad del controlador

		$configPila = \Panel::getConfigMergedForFile('pila');
		if(! isset($configPila[$moduleSlug]))
		{
			\Session::flash('messages', array(
				array(
					'class' => 'alert-danger',
					'msg'   => 'Falta información en la configuración de la Pila [' . $moduleSlug . ']'
				)
			));
			return \Redirect::guest(\Config::get('panel::app.access_url') . '/dashboard');
		}

		try{
			\Pila::guessStructure(
				$controller,
				$method,
				$configPila[$moduleSlug],
				$route->parameters(),
				$moduleSlug
			)->store();
		}
		catch(\Ttt\Panel\Exception\TttException $e)
		{
			\Session::flash('messages', array(
				array(
					'class' => 'alert-danger',
					'msg'   => $e->getMessage()
				)
			));
			return \Redirect::guest(\Config::get('panel::app.access_url') . '/dashboard');
		}

		/*echo '<pre>';
		print_r(array_keys($route->getAction()));
		echo '</pre>';exit;

		echo '<pre>';
		var_dump($route->parameter('id'));exit;
		echo '</pre>';
		echo $routeAction;exit;*/
	}
}
