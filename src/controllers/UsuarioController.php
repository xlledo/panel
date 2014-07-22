<?php
namespace Ttt\Panel;

use \Config;
use \Input;
use \Paginator;
use \Sentry;
use \View;
use Ttt\Panel\Repo\Usuario\UsuarioInterface;
use Ttt\Panel\Repo\Grupo\GrupoInterface;
use Ttt\Panel\Core\AbstractCrudController;

class UsuarioController extends AbstractCrudController{

	protected $_views_dir = 'usuarios';
	protected $_titulo = 'Usuarios';

	protected $usuario;
	protected $grupo;

	protected $allowed_url_params = array(
		'nombre', 'email', 'ordenPor', 'ordenDir', 'creado_por'
	);

	public function __construct(UsuarioInterface $usuario, GrupoInterface $grupo)
	{
		parent::__construct();

		$this->usuario     = $usuario;
		$this->grupo       = $grupo;
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

		$input = array_merge(Input::only($this->allowed_url_params));

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
		$item = $this->grupo->createModel();
		$item->name    = Input::old('name') ? Input::old('name') : '';
		/*echo '<pre>';
		print_r(Input::old());
		echo '</pre>';exit;*/
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

		View::share('title', 'Creación de nuevo grupo.');
		return View::make('panel::grupos.form')
								->with('item', $item)
								->with('action', 'create');
	}

	/**
	* Intenta crear un nuevo elemento
	* @return void
	*/
	public function crear()
	{
		$message = 'Grupo creado correctamente.';
		try
		{
			$data = array(
				'name'        => Input::get('name')
			);
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

			$grupo = $this->grupo->create($data);

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => $message
				)
			));
			return \Redirect::action('Ttt\Panel\GrupoController@ver', $grupo->id);
		}
		catch (\Cartalyst\Sentry\Groups\NameRequiredException $e)
		{
		    $message = 'El campo nombre es obligatorio.';
		}
		catch (\Cartalyst\Sentry\Groups\GroupExistsException $e)
		{
		    $message = 'Ya existe un grupo con ese nombre y los nombres han de ser únicos.';
		}

		\Session::flash('messages', array(
			array(
				'class' => 'alert-danger',
				'msg'   => $message
			)
		));

		return \Redirect::action('Ttt\Panel\GrupoController@nuevo')
									->withInput();
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
			$item = $this->grupo->findById($id);
			$item->name   = ! is_null(Input::old('name')) ? Input::old('name') : $item->name;
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

			View::share('title', 'Edición del grupo ' . $item->name);
			return View::make('panel::grupos.form')
									->with('action', 'edit')
									->with('item', $item);

		}
		catch(\Cartalyst\Sentry\Groups\GroupNotFoundException $e)
		{
			$message = $e->getMessage();
		}

		\Session::flash('messages', array(
			array(
				'class' => 'alert-danger',
				'msg'   => $message
			)
		));

		return \Redirect::action('Ttt\Panel\GrupoController@index');
	}

	/**
	* Intenta actualizar un elemento existente
	* @return void
	*/
	public function actualizar()
	{
		$message = 'Grupo actualizado correctamente.';
		try
		{
			$ent = $this->grupo->findById(Input::get('id'));

			$ent->name = Input::get('name');
			$permisos = array();
			foreach(Config::get('panel::acciones') as $moduloKey => $acciones)
			{
				foreach($acciones as $actionKey => $metodos)
				{
					$tmpPermiso = $moduloKey . '::' . $actionKey;
					$permisos[$tmpPermiso] = (Input::get($tmpPermiso) && Input::get($tmpPermiso) == 'si')  ? 1 : 0;//valor por defecto
				}
			}
			$ent->permissions = ($ent->name == 'Superadmin') ? array('superuser' => 1) : $permisos;



			$this->grupo->update($ent);

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => $message
				)
			));

			return \Redirect::action('Ttt\Panel\GrupoController@ver', $ent->id);

		}
		catch(\Ttt\Panel\Exception\TttException $e)
		{
			$message = 'No se han podido guardar los cambios en el grupo.';
		}
		catch (\Cartalyst\Sentry\Groups\NameRequiredException $e)
		{
			$message = 'El campo nombre es obligatorio.';
		}
		catch (\Cartalyst\Sentry\Groups\GroupExistsException $e)
		{
			$message = 'Ya existe un grupo con ese nombre y los nombres han de ser únicos.';
		}
		catch (\Cartalyst\Sentry\Groups\GroupNotFoundException $e)
		{
			$message = $e->getMessage();
		}

		\Session::flash('messages', array(
			array(
				'class' => 'alert-danger',
				'msg'   => $message
			)
		));

		return \Redirect::action('Ttt\Panel\GrupoController@ver', $ent->id)
																		->withInput();
	}

	/**
	* Intenta actualizar un elemento existente
	* @return void
	*/
	public function borrar($id = null)
	{
		$message = 'Grupo eliminado correctamente.';
		try
		{
			$group = $this->grupo->findById($id);
			if($group->name == 'Superadmin')
			{
				throw new \Cartalyst\Sentry\Groups\GroupNotFoundException('No se puede eliminar el grupo Superadmin');
			}
			$group->delete();

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => $message
				)
			));

			return \Redirect::action('Ttt\Panel\GrupoController@index');

		}
		catch (\Cartalyst\Sentry\Groups\GroupNotFoundException $e)
		{
			$message = $e->getMessage();
		}

		\Session::flash('messages', array(
			array(
				'class' => 'alert-danger',
				'msg'   => $message
			)
		));

		return \Redirect::action('Ttt\Panel\GrupoController@ver', $group->id);
	}
}
