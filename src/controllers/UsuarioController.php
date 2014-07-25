<?php
namespace Ttt\Panel;

use \Config;
use \Input;
use \Paginator;
use \Sentry;
use \View;
use Ttt\Panel\Repo\Usuario\UsuarioInterface;
use Ttt\Panel\Service\Form\Usuario\UsuarioForm;
use Ttt\Panel\Repo\Grupo\GrupoInterface;
use Ttt\Panel\Core\AbstractCrudController;

class UsuarioController extends AbstractCrudController{

	protected $_views_dir = 'usuarios';
	protected $_titulo = 'Usuarios';

	public static $moduleSlug = 'usuarios';

	protected $usuario;
	protected $usuarioForm;
	protected $grupo;

	protected $allowed_url_params = array(
		'nombre', 'email', 'ordenPor', 'ordenDir', 'creado_por'
	);

	public function __construct(UsuarioInterface $usuario, GrupoInterface $grupo, UsuarioForm $usuarioForm)
	{
		parent::__construct();

		$this->usuario     = $usuario;
		$this->usuarioForm = $usuarioForm;
		$this->grupo       = $grupo;
	}

	protected function _setDefaultAssets()
	{
		parent::_setDefaultAssets();

		$assets = \View::shared('assets');
		$assets['js'][] = asset('packages/ttt/panel/js/usuarios.js');

		\View::share('assets', $assets);
	}

	public function index()
	{
		/*echo '<pre>';
		print_r(Config::get('panel::acciones'));
		echo '</pre>';exit;*/

		View::share('title', 'Listado de Usuarios');

		//recogemos la página
		$pagina  = Input::get(Config::get('panel::app.pageName', 'pg'), 1);
		$perPage = Config::get('panel::app.perPage', 1);

		$input = Input::only($this->allowed_url_params);

		$input[Config::get('panel::app.orderBy')]  = !is_null($input[Config::get('panel::app.orderBy')]) ? $input[Config::get('panel::app.orderBy')] : 'nombre';
		$input[Config::get('panel::app.orderDir')] = !is_null($input[Config::get('panel::app.orderDir')]) ? $input[Config::get('panel::app.orderDir')] : 'asc';

		//recogemos la paginación
		$pageData = $this->usuario->byPage($pagina, $perPage, $input);

		$usuarios = Paginator::make(
			$pageData->items,
			$pageData->totalItems,
			$perPage
		);
		//debemos añadir los parámetros de la url
		$usuarios->appends($input);

		View::share('items', $usuarios);
		return View::make('panel::usuarios.index')
									->with('currentUrl', \URL::current())
									->with('params', $input);
	}

	/**
	* Muestra el formulario de creación
	* @return void
	*/
	public function nuevo()
	{
		$item = $this->usuario->createModel();
		$item->first_name    = Input::old('first_name') ? Input::old('first_name') : '';
		$item->last_name     = Input::old('last_name') ? Input::old('last_name') : '';
		$item->email         = Input::old('email') ? Input::old('email') : '';
		if(Input::old('grupo') && Input::old('grupo') != '')
		{
			$coll = new \Illuminate\Database\Eloquent\Collection;
			$coll->add($this->grupo->findById(Input::old('grupo')));
			$item->groups = $coll;
		}

		//construimos permisos
		$permisos = array();
		foreach(Config::get('panel::acciones') as $moduloKey => $acciones)
		{
			foreach($acciones as $actionKey => $metodos)
			{
				$tmpPermiso = $moduloKey . '::' . $actionKey;
				$permisos[$tmpPermiso] = (Input::old($tmpPermiso) && Input::old($tmpPermiso) == 'si')  ? 1 : 0;//valor por defecto
			}
		}
		$item->permissions = $permisos;

		View::share('title', 'Creación de nuevo usuario.');
		return View::make('panel::usuarios.form')
								->with('item', $item)
								->with('grupos', $this->grupo->findAllBy(array('name', 'asc')))
								->with('action', 'create');
	}

	/**
	* Intenta crear un nuevo elemento
	* @return void
	*/
	public function crear()
	{
		$message = 'Usuario creado correctamente.';
		try
		{
			$data =  Input::only(array('first_name', 'last_name', 'email', 'password', 'confirm_password'));
			$data['activated'] = TRUE;//forzamos a que esté activo

			$tmp_grupo = Input::get('grupo');

			if($tmp_grupo != 1)
			{
				$permisos = array();
				foreach(Config::get('panel::acciones') as $moduloKey => $acciones)
				{
					foreach($acciones as $actionKey => $metodos)
					{
						$tmpPermiso = $moduloKey . '::' . $actionKey;
						$permisos[$tmpPermiso] = (Input::get($tmpPermiso) && Input::get($tmpPermiso) == 'si')  ? 1 : 0;//valor por defecto
					}
				}
				$data['permissions'] = $permisos;
			}


			$usuario = $this->usuarioForm->create($data);

			//si hemos llegado aquí ya tenemos usuario creado, por lo tanto asignamos grupo
			if($tmp_grupo != '')
			{
				$grupoUsuario = $this->grupo->findById($tmp_grupo);
				$usuario->addGroup($grupoUsuario);
			}

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => $message
				)
			));

			return \Redirect::action('Ttt\Panel\UsuarioController@ver', $usuario->id);
		}
		catch(\Ttt\Panel\Exception\TttException $e)
		{
			$message = $e->getMessage();
		}
		catch (\Cartalyst\Sentry\Users\UserExistsException $e)
		{
		    $message = 'Ya existe un usuario con ese email.';
		}
		catch (\Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
		    $message = 'El usuario indicado no existe.';
		}

		\Session::flash('messages', array(
			array(
				'class' => 'alert-danger',
				'msg'   => $message
			)
		));

		return \Redirect::action('Ttt\Panel\UsuarioController@nuevo')
									->withInput()
									->withErrors($this->usuarioForm->errors());
	}

	/**
	* Muestra el formulario de edición
	* @return void
	*/
	public function ver($id = null)
	{
		$message = '';
		try
		{
			/*echo '<pre>';
			print_r(Input::old());
			echo '</pre>';exit;*/
			$item = $this->usuario->findById($id);
			$item->first_name   = ! is_null(Input::old('first_name')) ? Input::old('first_name') : $item->first_name;
			$item->last_name    = ! is_null(Input::old('last_name')) ? Input::old('last_name') : $item->last_name;
			$item->email        = ! is_null(Input::old('email')) ? Input::old('email') : $item->email;


			foreach(Config::get('panel::acciones') as $moduloKey => $acciones)
			{
				foreach($acciones as $actionKey => $metodos)
				{
					$tmpPermiso = $moduloKey . '::' . $actionKey;
					$permisos[$tmpPermiso] = (int)(Input::old($tmpPermiso) == 'si');
					if(Input::old($tmpPermiso))
					{
						$permisos[$tmpPermiso] = (int)(Input::old($tmpPermiso) == 'si');
					}else{
						$permisos[$tmpPermiso] = (int)$item->hasAccess($tmpPermiso);
					}
				}
			}
			$item->permissions = $permisos;

			//puede que tengamos algún grupo porque viene de un error al actualizar
			if(Input::old('grupo') && Input::old('grupo') != '')
			{
				$coll = new \Illuminate\Database\Eloquent\Collection;
				$coll->add($this->grupo->findById(Input::old('grupo')));
				$item->groups = $coll;
			}


			View::share('title', 'Edición del usuario ' . $item->full_name);
			return View::make('panel::usuarios.form')
									->with('action', 'edit')
									->with('grupos', $this->grupo->findAllBy(array('name', 'asc')))
									->with('item', $item);

		}
		catch(\Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
			$message = $e->getMessage();
		}

		\Session::flash('messages', array(
			array(
				'class' => 'alert-danger',
				'msg'   => $message
			)
		));

		return \Redirect::action('Ttt\Panel\UsuarioController@index');
	}

	/**
	* Intenta actualizar un elemento existente
	* @return void
	*/
	public function actualizar()
	{
		$message = 'Usuario actualizado correctamente.';
		try
		{
			$ent = $this->usuario->findById(Input::get('id'));

			$data =  Input::only(array('first_name', 'last_name', 'email', 'password', 'confirm_password'));

			$tmp_grupo = Input::get('grupo');

			$permisos = array();
			if($tmp_grupo != 1)
			{
				foreach(Config::get('panel::acciones') as $moduloKey => $acciones)
				{
					foreach($acciones as $actionKey => $metodos)
					{
						$tmpPermiso = $moduloKey . '::' . $actionKey;
						$permisos[$tmpPermiso] = (Input::get($tmpPermiso) && Input::get($tmpPermiso) == 'si')  ? 1 : -1;//valor por defecto
					}
				}
			}
			$ent->permissions = $permisos;
			$ent->cleanGroups();
			$this->usuarioForm->update($data, $ent);

			//si hemos llegado aquí ya tenemos usuario creado, por lo tanto asignamos grupo
			if($tmp_grupo != '')
			{
				$grupoUsuario = $this->grupo->findById($tmp_grupo);
				$ent->addGroup($grupoUsuario);
			}

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => $message
				)
			));

			return \Redirect::action('Ttt\Panel\UsuarioController@ver', $ent->id);

		}
		catch(\Ttt\Panel\Exception\TttException $e)
		{
			$message = 'No se han podido guardar los cambios en el usuario.';
		}
		catch (\Cartalyst\Sentry\Users\UserExistsException $e)
		{
			$message = 'Ya existe un usuario con ese email.';
		}
		catch (\Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
			$message = $e->getMessage();
		}

		\Session::flash('messages', array(
			array(
				'class' => 'alert-danger',
				'msg'   => $message
			)
		));

		return \Redirect::action('Ttt\Panel\UsuarioController@ver', $ent->id)
																		->withInput()
																		->withErrors($this->usuarioForm->errors());
	}

	/**
	* Intenta actualizar un elemento existente
	* @return void
	*/
	public function borrar($id = null)
	{
		$message = 'Usuario eliminado correctamente.';
		try
		{
			$user = $this->usuario->findById($id);

			$user->delete();

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => $message
				)
			));

			return \Redirect::action('Ttt\Panel\UsuarioController@index');

		}
		catch (\Cartalyst\Sentry\Groups\UserNotFoundException $e)
		{
			$message = $e->getMessage();
		}

		\Session::flash('messages', array(
			array(
				'class' => 'alert-danger',
				'msg'   => $message
			)
		));

		return \Redirect::action('Ttt\Panel\UsuarioController@ver', $user->id);
	}
}
