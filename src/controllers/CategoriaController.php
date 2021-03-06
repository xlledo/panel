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
	protected $_titulo = 'Taxonomías';

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
		$assets['js'][] = asset('packages/ttt/panel/components/bootstrap/js/jquery.nestable.min.js');
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
		$item->protegida     = Input::old('protegida') ? Input::old('protegida') : FALSE;

		\Pila::reset()
			->push(array(
				'titulo'          => 'Taxonomías',
				'url'             => action('Ttt\Panel\CategoriaController@index'),
				'eloquent'        => NULL,
				'eloquentMethod'  => NULL,
				'retrievingField' => NULL,
				'retrievingValue' => NULL,
				'reference'       => FALSE,
				'pestania'        => FALSE
			))->push(array(
				'titulo'          => 'Nuevo árbol de Taxonomías',
				'url'             => action('Ttt\Panel\CategoriaController@nuevoArbol'),
				'eloquent'        => NULL,
				'eloquentMethod'  => NULL,
				'retrievingField' => NULL,
				'retrievingValue' => NULL,
				'reference'       => FALSE,
				'pestania'        => FALSE
			))->store();

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
				'visible'   => 1,
				'protegida' => Input::has('protegida') ? Input::get('protegida') : FALSE
			);


			$nodo = $this->categoriaForm->createRoot($data);

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => $message
				)
			));

			return \Redirect::action('Ttt\Panel\CategoriaController@verRaiz', $nodo->id);
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
	public function verRaiz($id = null)
	{
		$message = '';
		try
		{
			$item = $this->categoria->rootById($id);

			$item->nombre    = !is_null(Input::old('nombre')) ? Input::old('nombre') : $item->nombre;
			$item->protegida = Input::old('protegida') ? Input::old('protegida') : $item->protegida;
                        $item->slug      = Input::old('slug') ? Input::old('slug') : $item->slug;

			\Pila::reset()
				->push(array(
					'titulo'          => 'Taxonomías',
					'url'             => action('Ttt\Panel\CategoriaController@index'),
					'eloquent'        => NULL,
					'eloquentMethod'  => NULL,
					'retrievingField' => NULL,
					'retrievingValue' => NULL,
					'reference'       => FALSE,
					'pestania'        => FALSE
				))->push(array(
					'titulo'          => $item->nombre,
					'url'             => action('Ttt\Panel\CategoriaController@verArbol', $id),
					'eloquent'        => NULL,
					'eloquentMethod'  => NULL,
					'retrievingField' => NULL,
					'retrievingValue' => NULL,
					'reference'       => FALSE,
					'pestania'        => FALSE
				))->push(array(
					'titulo'          => 'Ver raíz',
					'url'             => action('Ttt\Panel\CategoriaController@verArbol', $id),
					'eloquent'        => NULL,
					'eloquentMethod'  => NULL,
					'retrievingField' => NULL,
					'retrievingValue' => NULL,
					'reference'       => FALSE,
					'pestania'        => FALSE
				))->store();

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
				'visible'   => 1,
				'protegida' => Input::has('protegida') ? Input::get('protegida') : FALSE,
                                'slug'      => Input::get('slug')
			);

			$root = $this->categoriaForm->updateRoot($data, $item);

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => $message
				)
			));

			return \Redirect::action('Ttt\Panel\CategoriaController@verRaiz', $item->id);

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

		return \Redirect::action('Ttt\Panel\CategoriaController@verRaiz', $item->id)
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
		$message = '';
		try
		{
			$root = $this->categoria->rootById($id);

			\Pila::reset()
				->push(array(
					'titulo'          => 'Taxonomías',
					'url'             => action('Ttt\Panel\CategoriaController@index'),
					'eloquent'        => NULL,
					'eloquentMethod'  => NULL,
					'retrievingField' => NULL,
					'retrievingValue' => NULL,
					'reference'       => FALSE,
					'pestania'        => FALSE
				))->push(array(
					'titulo'          => $root->nombre,
					'url'             => action('Ttt\Panel\CategoriaController@verArbol', $id),
					'eloquent'        => NULL,
					'eloquentMethod'  => NULL,
					'retrievingField' => NULL,
					'retrievingValue' => NULL,
					'reference'       => FALSE,
					'pestania'        => FALSE
				))->push(array(
					'titulo'          => 'Nueva taxonomía en ' . $root->nombre,
					'url'             => action('Ttt\Panel\CategoriaController@nuevo', $id),
					'eloquent'        => NULL,
					'eloquentMethod'  => NULL,
					'retrievingField' => NULL,
					'retrievingValue' => NULL,
					'reference'       => FALSE,
					'pestania'        => FALSE
				))->store();

			$item = $this->categoria->createModel();
			$item->nombre        = Input::old('nombre') ? Input::old('nombre') : '';
			$item->valor         = Input::old('valor') ? Input::old('valor') : '';
			$item->visible       = Input::old('visible') ? Input::old('visible') : FALSE;
			$item->parent_id     = $id;

			View::share('title', 'Nueva categoría en ' . $root->nombre);
			return View::make('panel::categorias.form')
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

		return \Redirect::action('Ttt\Panel\CategoriaController@index');
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
			$root = $this->categoria->rootById(Input::get('parent_id'));

			$message = 'Nueva taxonomía creada correctamente en ' . $root->nombre;

			$data =  array(
				'nombre'    => Input::get('nombre'),
				'valor'     => Input::get('valor'),
				'visible'   => Input::has('visible') ? Input::get('visible') : FALSE,
				'protegida' => $root->protegida
			);

			$nodo = $this->categoriaForm->createChild($data, $root);

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => $message
				)
			));

			return \Redirect::action('Ttt\Panel\CategoriaController@ver', $nodo->id);
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

		return \Redirect::action('Ttt\Panel\CategoriaController@nuevo', $root->id)
									->withInput()
									->withErrors($this->categoriaForm->errors());
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
			$item = $this->categoria->childById($id);

			$item->nombre    = ! is_null(Input::old('nombre')) ? Input::old('nombre') : $item->nombre;
			$item->visible   = Input::old('visible') ? Input::old('visible') : $item->visible;
			$item->valor     = Input::old('valor') ? Input::old('valor') : $item->valor;
                        $item->slug      = ! is_null(Input::old('slug')) ? Input::old('slug') : $item->slug;

			\Pila::reset()
				->push(array(
					'titulo'          => 'Taxonomías',
					'url'             => action('Ttt\Panel\CategoriaController@index'),
					'eloquent'        => NULL,
					'eloquentMethod'  => NULL,
					'retrievingField' => NULL,
					'retrievingValue' => NULL,
					'reference'       => FALSE,
					'pestania'        => FALSE
				))->push(array(
					'titulo'          => $item->getRoot()->nombre,
					'url'             => action('Ttt\Panel\CategoriaController@verArbol', $item->getRoot()->id),
					'eloquent'        => NULL,
					'eloquentMethod'  => NULL,
					'retrievingField' => NULL,
					'retrievingValue' => NULL,
					'reference'       => FALSE,
					'pestania'        => FALSE
				))->push(array(
					'titulo'          => $item->nombre,
					'url'             => action('Ttt\Panel\CategoriaController@nuevo', $id),
					'eloquent'        => NULL,
					'eloquentMethod'  => NULL,
					'retrievingField' => NULL,
					'retrievingValue' => NULL,
					'reference'       => FALSE,
					'pestania'        => FALSE
				))->store();

			View::share('title', 'Edición de subcategoría ' . $item->nombre);
			return View::make('panel::categorias.form')
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

		return \Redirect::action('Ttt\Panel\CategoriaController@index');
	}

	/**
	* Intenta actualizar la información de un nodo
	*
	* @return void
	*/
	public function actualizar()
	{
		$message = 'Taxonomía actualizada correctamente.';
		try
		{
			$item = $this->categoria->childById(Input::get('id'));

			$data =  array(
				'nombre'    => Input::get('nombre'),
				'visible'   => Input::has('visible') ? Input::get('visible') : FALSE,
				'valor'     => Input::get('valor'),
                                'slug'      => Input::get('slug')
			);

			$this->categoriaForm->updateChild($data, $item);

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => $message
				)
			));
                        
                        return \Redirect::action('Ttt\Panel\CategoriaController@ver', $item->id);
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

		return \Redirect::action('Ttt\Panel\CategoriaController@ver', $item->id)
																		->withInput()
																		->withErrors($this->categoriaForm->errors());
	}

	/**
	* Intenta borrar un árbol completo
	*
	* @return void
	*/
	public function borrarArbol($id = null)
	{
		$message = 'Árbol de taxonomías eliminado correctamente.';

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
		$message = 'Categoría eliminada correctamente.';

		$categoria = $this->categoria->byId($id);

		$root = $categoria->getRoot();

		$categoria->delete();

		\Session::flash('messages', array(
			array(
				'class' => 'alert-success',
				'msg'   => $message
			)
		));

		//redirigimos a la estructura draggable del árbol
		return \Redirect::action('Ttt\Panel\CategoriaController@verArbol', $root->id);
	}

	/**
	* Muestra la estructura completa de un árbol de categorías
	*
	* @return void
	*/
	public function verArbol($id)
	{
		$message = '';
		try
		{

			/*
			$morocco = $this->categoria->childById(80);
			$germany = $this->categoria->childById(84);
			$uganda  = $this->categoria->childById(79);

			$germany->moveToRightOf($morocco);
			$uganda->moveToRightOf($germany);
			$morocco->moveToLeftOf($uganda);
			*/


			$item = $this->categoria->rootById($id);

			\Pila::reset()
				->push(array(
					'titulo'          => 'Taxonomías',
					'url'             => action('Ttt\Panel\CategoriaController@index'),
					'eloquent'        => NULL,
					'eloquentMethod'  => NULL,
					'retrievingField' => NULL,
					'retrievingValue' => NULL,
					'reference'       => FALSE,
					'pestania'        => FALSE
				))->push(array(
					'titulo'          => $item->nombre,
					'url'             => action('Ttt\Panel\CategoriaController@verArbol', $id),
					'eloquent'        => NULL,
					'eloquentMethod'  => NULL,
					'retrievingField' => NULL,
					'retrievingValue' => NULL,
					'reference'       => FALSE,
					'pestania'        => FALSE
				))->store();

			View::share('title', 'Vista completa del árbol ' . $item->nombre);
			return View::make('panel::categorias.ver')
									->with('root', $item)
									->with('tree', $item->getDescendantsAndSelf()->toHierarchy());

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
	* Ordena alfabéticamente un árbol de categorías
	*
	* @return void
	*/
	public function ordenarAlfabeticamente($id)
	{
		$message = 'El árbol ha sido ordenado alfabéticamente.';
		try
		{
			$item = $this->categoria->rootById($id);

			$item->makeTreeOrdered();

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => $message
				)
			));
			return \Redirect::action('Ttt\Panel\CategoriaController@verArbol', $item->id);

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

			$root = $this->categoria->rootById(Input::get('root_id'));

	        $cadena_arbol = Input::get('allTree');
        	$array_arbol  =  json_decode($cadena_arbol,true);

			$popArray = array_shift($array_arbol);
			$treeRoot     = $popArray['id'];
			$childrenRoot = $popArray['children'];

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

			$response['message'] = 'Reordenado el árbol correctamente.';

		}
		catch(\Ttt\Panel\Exception\TttException $e)
		{
			$response['error']   = TRUE;
			$response['message'] = $e->getMessage();
		}

		return $response;//automáticamente devuelve un objeto JSON
	}
}
