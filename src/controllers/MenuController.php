<?php
namespace Ttt\Panel;

use \Config;
use \Input;
use \Paginator;
use \Sentry;
use \View;
use Ttt\Panel\Repo\Menu\MenuInterface;
use Ttt\Panel\Service\Form\Menu\MenuForm;
use Ttt\Panel\Core\AbstractCrudController;

class MenuController extends AbstractCrudController{

	protected $_views_dir = 'menu';
	protected $_titulo = 'Menú';

	public static $moduleSlug = 'menu';

	protected $menu;
	protected $menuForm;

	public function __construct(MenuInterface $menu, MenuForm $menuForm)
	{
		parent::__construct();

		$this->menu     = $menu;
		$this->menuForm = $menuForm;
	}

	protected function _setDefaultAssets()
	{
		parent::_setDefaultAssets();

		$assets = \View::shared('assets');
		$assets['js'][] = asset('packages/ttt/panel/components/bootstrap/js/jquery.nestable.min.js');
		$assets['js'][] = asset('packages/ttt/panel/js/categorias.js');//usaremos el mismo js que en categorías porque solo usa la ordenación

		\View::share('assets', $assets);

	}

	/**
	* No permitimos crear árboles, solo habrá uno y directamente accedemos a su estructura
	*
	* @return void
	*/
	public function index()
	{

		$message = '';
		try
		{
			$item = $this->menu->byId(1);//recogemos directamente la única raíz que debe existir
			//si no tenemos raíz la creamos
			if(! $item)
			{
				$item = $this->menu->createRoot(array(
					'nombre'  => 'Menú Panel',
					'visible' => 1
				));
			}

			\Pila::reset()
				->push(array(
					'titulo'          => $item->nombre,
					'url'             => action('Ttt\Panel\MenuController@index'),
					'eloquent'        => NULL,
					'eloquentMethod'  => NULL,
					'retrievingField' => NULL,
					'retrievingValue' => NULL,
					'reference'       => FALSE,
					'pestania'        => FALSE
				))->store();

			View::share('title', 'Estructura de navegación del panel de control');
			return View::make('panel::menu.ver')
									->with('root', $item)
									->with('tree', $item->getDescendants()->toHierarchy());
									//->with('tree', $item->getDescendantsAndSelf()->toHierarchy());

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

		return \Redirect::action('Ttt\Panel\DashboardController@index');//si no existe la raíz redirigimos al dashboard porque no hay listado
	}

	/**
	* Muestra el formulario de creación de un nuevo nodo dentro de un árbol
	* @param $id int el id de la raíz en la que se quiere crear el nodo
	*
	* @return void
	*/
	public function nuevo($id = null)
	{
		$message = '';
		try
		{
			$root = $this->menu->rootById($id);

			$item = $this->menu->createModel();
			$item->nombre        = Input::old('nombre') ? Input::old('nombre') : '';
			$item->ruta          = Input::old('ruta') ? Input::old('ruta') : '';
			$item->icono         = Input::old('icono') ? Input::old('icono') : '';
			$item->visible       = Input::old('visible') ? Input::old('visible') : FALSE;
			$item->parent_id     = $id;

			if(Input::old('modulo_id'))
			{
				$item->modulo()->associate(\App::make('Ttt\Panel\Repo\Modulo\ModuloInterface')->byId(Input::old('modulo_id')));
			}

			View::share('title', 'Nueva opción de menú en ' . $root->nombre);

			View::share('modulos', \App::make('Ttt\Panel\Repo\Modulo\ModuloInterface')->getAll());

			\Pila::reset()
				->push(array(
					'titulo'          => $root->nombre,
					'url'             => action('Ttt\Panel\MenuController@index'),
					'eloquent'        => NULL,
					'eloquentMethod'  => NULL,
					'retrievingField' => NULL,
					'retrievingValue' => NULL,
					'reference'       => FALSE,
					'pestania'        => FALSE
				))->push(array(
					'titulo'          => 'Nueva opción de menú en ' . $root->nombre,
					'url'             => action('Ttt\Panel\MenuController@nuevo'),
					'eloquent'        => NULL,
					'eloquentMethod'  => NULL,
					'retrievingField' => NULL,
					'retrievingValue' => NULL,
					'reference'       => FALSE,
					'pestania'        => FALSE
				))->store();

			return View::make('panel::menu.form')
									->with('action', 'create')
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

		return \Redirect::action('Ttt\Panel\MenuController@index');
	}

	/**
	* Intenta crear un nuevo nodo dentro de un árbol
	*
	* @return void
	*/
	public function crear()
	{
		try
		{
			$root = $this->menu->rootById(Input::get('parent_id'));

			$message = 'Nueva opción de menú creada correctamente en ' . $root->nombre;

			$data =  array(
				'nombre'    => Input::get('nombre'),
				'ruta'      => Input::get('ruta'),
				'icono'     => Input::get('icono') != '' ? Input::get('icono') : NULL,
				'modulo_id' => Input::get('modulo_id') != '' ? Input::get('modulo_id') : NULL,
				'visible'   => Input::has('visible') ? Input::get('visible') : FALSE,
			);

			$nodo = $this->menuForm->createChild($data, $root);

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => $message
				)
			));

			return \Redirect::action('Ttt\Panel\MenuController@ver', $nodo->id);
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

		return \Redirect::action('Ttt\Panel\MenuController@nuevo', $root->id)
									->withInput()
									->withErrors($this->menuForm->errors());
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
			$item = $this->menu->childById($id);

			$item->nombre    = ! is_null(Input::old('nombre')) ? Input::old('nombre') : $item->nombre;
			$item->icono     = ! is_null(Input::old('icono')) ? Input::old('icono') : $item->icono;
			$item->ruta      = ! is_null(Input::old('ruta')) ? Input::old('ruta') : $item->ruta;
			$item->visible   = Input::old('visible') ? Input::old('visible') : $item->visible;
			$item->modulo_id = Input::old('modulo_id') ? Input::old('modulo_id') : $item->modulo_id;

			\Pila::reset()
				->push(array(
					'titulo'          => $item->getRoot()->nombre,
					'url'             => action('Ttt\Panel\MenuController@index'),
					'eloquent'        => NULL,
					'eloquentMethod'  => NULL,
					'retrievingField' => NULL,
					'retrievingValue' => NULL,
					'reference'       => FALSE,
					'pestania'        => FALSE
				))->push(array(
					'titulo'          => $item->nombre,
					'url'             => action('Ttt\Panel\MenuController@ver', $id),
					'eloquent'        => NULL,
					'eloquentMethod'  => NULL,
					'retrievingField' => NULL,
					'retrievingValue' => NULL,
					'reference'       => FALSE,
					'pestania'        => FALSE
				))->store();

			View::share('title', 'Edición de opción de menú ' . $item->nombre);
			View::share('modulos', \App::make('Ttt\Panel\Repo\Modulo\ModuloInterface')->getAll());
			return View::make('panel::menu.form')
									->with('action', 'edit')
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

		return \Redirect::action('Ttt\Panel\MenuController@index');
	}

	/**
	* Intenta actualizar la información de un nodo
	*
	* @return void
	*/
	public function actualizar()
	{
		$message = 'Opción de menú actualizada correctamente.';
		try
		{
			$item = $this->menu->childById(Input::get('id'));

			$data =  array(
				'nombre'    => Input::get('nombre'),
				'ruta'      => Input::get('ruta'),
				'icono'     => Input::get('icono') != '' ? Input::get('icono') : NULL,
				'modulo_id' => Input::get('modulo_id') != '' ? Input::get('modulo_id') : NULL,
				'visible'   => Input::has('visible') ? Input::get('visible') : FALSE,
			);

			$this->menuForm->updateChild($data, $item);

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => $message
				)
			));
			return \Redirect::action('Ttt\Panel\MenuController@ver', $item->id);
		}
		catch(\Ttt\Panel\Exception\TttException $e)
		{
			$message = $e->getMessage();

			\Session::flash('messages', array(
				array(
					'class' => 'alert-danger',
					'msg'   => $message
				)
			));
		}

		return \Redirect::action('Ttt\Panel\MenuController@ver', $item->id)
																		->withInput()
																		->withErrors($this->menuForm->errors());
	}

	/**
	* Intenta borrar un nodo completo dentro de un árbol
	*
	* @return void
	*/
	public function borrar($id = null)
	{
		$message = 'Opción de menú eliminada correctamente.';

		$categoria = $this->menu->byId($id);

		$root = $categoria->getRoot();

		$categoria->delete();

		\Session::flash('messages', array(
			array(
				'class' => 'alert-success',
				'msg'   => $message
			)
		));

		//redirigimos a la estructura draggable del árbol
		return \Redirect::action('Ttt\Panel\MenuController@index');
	}

	/**
	* Ordena alfabéticamente un árbol de categorías
	*
	* @return void
	*/
	public function ordenarAlfabeticamente($id)
	{
		$message = 'El árbol ha sido ordenado alfabéticamente.';
		try
		{
			$item = $this->menu->rootById($id);

			$item->makeTreeOrdered();
			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => $message
				)
			));
			return \Redirect::action('Ttt\Panel\MenuController@index');

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

		return \Redirect::action('Ttt\Panel\MenuController@index');
	}

	public function ordenar()
	{
		$response = array(
			'error'   => FALSE,
			'message' => '',
			'root_id' => Input::get('root_id')
		);

		try
		{
			if(! \Request::ajax())
			{
				throw new \Ttt\Panel\Exception\TttException("Petición no válida, este recurso solo es accesible mediante AJAX");
			}

			$root = $this->menu->rootById(Input::get('root_id'));

	        $cadena_arbol = Input::get('allTree');

        	$array_arbol  =  json_decode($cadena_arbol,true);

			//$popArray = array_shift($array_arbol);
			$treeRoot     = $root->id;
			$childrenRoot = $array_arbol;

			if($root->id != $treeRoot)
			{
				throw new \Ttt\Panel\Exception\TttException("La raíz del árbol no es correcta.");
			}

			$newStructure = $root->reorderTreeFrom($root, $childrenRoot);
			/*
			$newStructure = $root->reorderTreeFrom($root, $childrenRoot);
			$root->deleteTree();
			$root->makeTree($newStructure);
			*/

			$response['message'] = 'Reordenado el menú correctamente.';

		}
		catch(\Ttt\Panel\Exception\TttException $e)
		{
			$response['error']   = TRUE;
			$response['message'] = $e->getMessage();
		}

		return $response;//automáticamente devuelve un objeto JSON
	}
}
