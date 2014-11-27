<?php
namespace Ttt\Panel;

use \Config;
use \Input;
use \Sentry;
use \View;
use Ttt\Panel\Repo\Grupo\GrupoInterface;
use Ttt\Panel\Core\AbstractCrudController;

class GrupoController extends AbstractCrudController{

	protected $_views_dir = 'grupos';
	protected $_titulo = 'Grupos';

	public static $moduleSlug = 'grupos';

	protected $grupo;

	public function __construct(GrupoInterface $grupo)
	{
		parent::__construct();

		$this->grupo     = $grupo;
	}

	public function index()
	{
		/*echo '<pre>';
		print_r(Config::get('panel::acciones'));
		echo '</pre>';exit;*/

		View::share('title', 'Listado de Grupos');

		$input = array_merge(Input::only(Config::get('panel::app.orderBy'), Config::get('panel::app.orderDir')));

		$order[Config::get('panel::app.orderBy')] = ! is_null($input[Config::get('panel::app.orderBy')]) ? $input[Config::get('panel::app.orderBy')] : 'name';
		$order[Config::get('panel::app.orderDir')] = ! is_null($input[Config::get('panel::app.orderDir')]) ? $input[Config::get('panel::app.orderDir')] : 'asc';

		//recogemos los grupos, aquí no paginaremos, ya que no va a ser algo corriente tener 200 grupos
		$items = $this->grupo->findAllBy($order);

		View::share('items', $items);
		return View::make('panel::grupos.index')
									->with('currentUrl', \URL::current())
									->with('params', $order);
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
		foreach(\Panel::getConfigMergedForFile() as $moduloKey => $acciones)
		{
			foreach($acciones as $actionKey => $metodos)
			{
				$tmpPermiso = $moduloKey . '::' . $actionKey;
				$permisos[$tmpPermiso] = (Input::old($tmpPermiso) && Input::old($tmpPermiso) == 'si')  ? 1 : 0;//valor por defecto
			}
		}
		$item->permissions = $permisos;

		$acciones = \Panel::getConfigMergedForFile();
		ksort($acciones);

		View::share('title', 'Creación de nuevo grupo.');
		return View::make('panel::grupos.form')
								->with('item', $item)
								->with('configGroupsOrderedByKey', $acciones)
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
			foreach(\Panel::getConfigMergedForFile() as $moduloKey => $acciones)
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
			foreach(\Panel::getConfigMergedForFile() as $moduloKey => $acciones)
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

			$acciones = \Panel::getConfigMergedForFile();
			ksort($acciones);

			View::share('title', 'Edición del grupo ' . $item->name);
			return View::make('panel::grupos.form')
									->with('action', 'edit')
									->with('configGroupsOrderedByKey', $acciones)
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
			foreach(\Panel::getConfigMergedForFile() as $moduloKey => $acciones)
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
