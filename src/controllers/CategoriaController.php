<?php
namespace Ttt\Panel;

use \Config;
use \Input;
use \Paginator;
use \Sentry;
use \View;
use Ttt\Panel\Repo\Categoria\CategoriaInterface;
use Ttt\Panel\Service\Form\Categoria\CategoriaForm;
use Ttt\Panel\Core\AbstractCrudController;

class CategoriaController extends AbstractCrudController{

	protected $_views_dir = 'categorias';
	protected $_titulo = 'Categorías';

	public static $moduleSlug = 'categorias';

	protected $categoria;
	protected $categoriaForm;

	protected $allowed_url_params = array(
		'nombre', 'ordenPor', 'ordenDir'
	);

	public function __construct(CategoriaInterface $categoria, CategoriaForm $categoriaForm)
	{
		parent::__construct();

		$this->categoria     = $categoria;
		$this->categoriaForm = $categoriaForm;
	}

	protected function _setDefaultAssets()
	{
		parent::_setDefaultAssets();

		$assets = \View::shared('assets');
		$assets['js'][] = asset('packages/ttt/panel/js/categorias.js');

		\View::share('assets', $assets);

	}

	/**
	* Muestra el listado de Raíces de categorías existentes
	*
	* @return void
	*/
	public function index()
	{

		View::share('title', 'Listado de Árboles de categorías');

		$order[Config::get('panel::app.orderBy')] = Input::has(Config::get('panel::app.orderBy')) ? Input::get(Config::get('panel::app.orderBy')) : 'nombre';
		$order[Config::get('panel::app.orderDir')] = Input::has(Config::get('panel::app.orderDir')) ? Input::get(Config::get('panel::app.orderDir')) : 'asc';

		//no paginamos, porque no va a ser habitual tener 200 árboles
		$rootItems = $this->categoria->findAllRootsBy($order[Config::get('panel::app.orderBy')], $order[Config::get('panel::app.orderDir')]);

		View::share('items', $rootItems);
		return View::make('panel::categorias.index')
									->with('currentUrl', \URL::current())
									->with('params', $order);
	}

	/**
	* Muestra el formulario de creación de nuevo árbol
	*
	* @return void
	*/
	public function nuevoArbol()
	{
		$item = $this->categoria->createModel();
		$item->nombre        = Input::old('nombre') ? Input::old('nombre') : '';
		$item->visible       = Input::old('visible') ? Input::old('visible') : FALSE;
		$item->protegida     = Input::old('protegida') ? Input::old('protegida') : FALSE;

		View::share('title', 'Crear árbol de categorías.');
		return View::make('panel::categorias.form')
								->with('item', $item)
								->with('action', 'createArbol');
	}

	/**
	* Intenta crear un nuevo árbol de categorías
	*
	* @return void
	*/
	public function crearArbol()
	{
		$message = 'Árbol creado correctamente.';
		try
		{
			$data =  array(
				'nombre'    => Input::get('nombre'),
				'visible'   => Input::has('visible') ? Input::get('visible') : FALSE,
				'protegida' => Input::has('protegida') ? Input::get('protegida') : FALSE
			);


			$nodo = $this->categoriaForm->createRoot($data);

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => $message
				)
			));

			return \Redirect::action('Ttt\Panel\CategoriaController@editarArbol', $nodo->id);
		}
		catch(\Ttt\Panel\Exception\TttException $e)
		{
			$message = $e->getMessage();
		}

		\Session::flash('messages', array(
			array(
				'class' => 'alert-danger',
				'msg'   => $message
			)
		));

		return \Redirect::action('Ttt\Panel\CategoriaController@nuevoArbol')
									->withInput()
									->withErrors($this->categoriaForm->errors());
	}

	/**
	* Muestra el formulario de edición de un nodo raíz
	*
	* @return void
	*/
	public function editarArbol($id = null)
	{
		$message = '';
		try
		{
			$item = $this->categoria->rootById($id);

			View::share('title', 'Edición del árbol ' . $item->nombre);
			return View::make('panel::categorias.form')
									->with('action', 'editArbol')
									->with('item', $item);

		}
		catch(\Ttt\Panel\Exception\TttException $e)
		{
			$message = $e->getMessage();
		}

		\Session::flash('messages', array(
			array(
				'class' => 'alert-danger',
				'msg'   => $message
			)
		));

		return \Redirect::action('Ttt\Panel\CategoriaController@index');
	}

	/**
	* Intenta actualizar la información de una raíz
	*
	* @return void
	*/
	public function actualizarRaiz()
	{
		$message = 'Raíz actualizada correctamente.';
		try
		{
			$item = $this->categoria->rootById(Input::get('id'));

			$data =  array(
				'nombre'    => Input::get('nombre'),
				'visible'   => Input::has('visible') ? Input::get('visible') : FALSE,
				'protegida' => Input::has('protegida') ? Input::get('protegida') : FALSE
			);

			$root = $this->categoriaForm->updateRoot($data, $item);

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => $message
				)
			));

			return \Redirect::action('Ttt\Panel\CategoriaController@editarArbol', $item->id);

		}
		catch(\Ttt\Panel\Exception\TttException $e)
		{
			$message = $e->getMessage();
		}

		\Session::flash('messages', array(
			array(
				'class' => 'alert-danger',
				'msg'   => $message
			)
		));

		return \Redirect::action('Ttt\Panel\CategoriaController@editarArbol', $item->id)
																		->withInput()
																		->withErrors($this->categoriaForm->errors());
	}

	/**
	* Muestra el formulario de creación de un nuevo nodo dentro de un árbol
	* @param $id int el id de la raíz en la que se quiere crear el nodo
	*
	* @return void
	*/
	public function nuevo($id = null)
	{

	}

	/**
	* Intenta crear un nuevo nodo dentro de un árbol
	*
	* @return void
	*/
	public function crear()
	{

	}

	/**
	* Muestra el formulario de edición de un nodo
	*
	* @return void
	*/
	public function ver($id = null)
	{
		$message = '';
		try
		{
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
	* Intenta actualizar la información de un nodo
	*
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
	* Intenta borrar un árbol completo
	*
	* @return void
	*/
	public function borrarArbol($id = null)
	{
		$message = 'Árbol de categorías eliminado correctamente.';

		$categoria = $this->categoria->byId($id);

		$categoria->delete();

		\Session::flash('messages', array(
			array(
				'class' => 'alert-success',
				'msg'   => $message
			)
		));

		return \Redirect::action('Ttt\Panel\CategoriaController@index');
	}

	/**
	* Intenta borrar un nodo completo dentro de un árbol
	*
	* @return void
	*/
	public function borrar($id = null)
	{

	}
}
