<?php
namespace Ttt\Panel;

use \Config;
use \Input;
use \Paginator;
use \Sentry;
use \View;
use Ttt\Panel\Repo\CategoriaTraducible\CategoriaInterface;
use Ttt\Panel\Service\Form\CategoriaTraducible\CategoriaForm;
use Ttt\Panel\Core\AbstractCrudController;

class CategoriaTraducibleController extends AbstractCrudController{

	protected $_views_dir = 'categorias-traducibles';
	protected $_titulo = 'Categorías traducibles';

	public static $moduleSlug = 'categorias-traducibles';

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

		View::share('idioma_predeterminado', $this->_defaultIdioma);
		View::share('todos_idiomas', $this->_todosIdiomas);
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
		return View::make('panel::categoriastraducibles.index')
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
		$sufijo  = '_';
		$sufijo .= Input::old('clave_idioma_campos') ?:'';

		$fillData = array(
			$this->_defaultIdioma->codigo_iso_2 => array(
				'nombre' => Input::old('nombre' . $sufijo) ? Input::old('nombre' . $sufijo) : '',
			),
			'visible'                         => Input::old('visible' . $sufijo) ? Input::old('visible' . $sufijo) : FALSE,
			'protegida'                       => Input::old('protegida' . $sufijo) ? Input::old('protegida' . $sufijo) : FALSE
		);
		$item = $this->categoria->createNode($fillData);

		View::share('title', 'Crear árbol de categorías.');
		View::share('action', 'createArbol');
		View::share('item', $item);
		return View::make('panel::categoriastraducibles.form');
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
			$sufijo  = '_';
			$sufijo .= Input::get('clave_idioma_campos') ?:'';

			$data =  array(
				'nombre'              => Input::get('nombre' . $sufijo),
				'visible'             => Input::has('visible' . $sufijo) ? Input::get('visible' . $sufijo) : FALSE,
				'protegida'           => Input::has('protegida' . $sufijo) ? Input::get('protegida' . $sufijo) : FALSE,
				'idioma'              => $this->_defaultIdioma->codigo_iso_2,
				'clave_idioma_campos' => Input::get('clave_idioma_campos')
			);

			$nodo = $this->categoriaForm->createRoot($data);

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => $message
				)
			));

			return \Redirect::action('Ttt\Panel\CategoriaTraducibleController@verRaiz', $nodo->id);
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

		return \Redirect::action('Ttt\Panel\CategoriaTraducibleController@nuevoArbol')
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

			$sufijo  = '_';
			$sufijo .= Input::old('clave_idioma_campos') ?:'';


			$item = $this->categoria->rootById($id);

			$idioma = Input::old('idioma' . $sufijo) ?: $this->_defaultIdioma->codigo_iso_2;

			$esNuevaTraduccion = ! is_null(Input::old('nueva_traduccion')) && Input::old('nueva_traduccion');

			if($esNuevaTraduccion)
			{
				$item->traduccion('new')->nombre = ! is_null(Input::old('nombre' . $sufijo)) ? Input::old('nombre' . $sufijo) : $item->traduccion($idioma)->nombre;
				$item->traduccion('new')->idioma = 'new';
			}else{
				$item->traduccion($idioma)->nombre = ! is_null(Input::old('nombre' . $sufijo)) ? Input::old('nombre' . $sufijo) : $item->traduccion($idioma)->nombre;
				$item->traduccion($idioma)->idioma = $idioma;
			}
			$item->visible   = Input::old('visible' . $sufijo) ? Input::old('visible' . $sufijo) : $item->visible;
			$item->protegida = Input::old('protegida' . $sufijo) ? Input::old('protegida' . $sufijo) : $item->protegida;

			/*echo '<pre>';
			print_r($item->toArray());
			echo '</pre>';exit;*/

			View::share('title', 'Edición del árbol ' . $item->nombre);
			View::share('action', 'editArbol');
			View::share('item', $item);
			return View::make('panel::categoriastraducibles.form');

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

		return \Redirect::action('Ttt\Panel\CategoriaTraducibleController@index');
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
			$sufijo  = '_';
			$sufijo .= Input::get('clave_idioma_campos') ?:'';

			$item = $this->categoria->rootById(Input::get('id'));

			$idioma = Input::get('idioma' . $sufijo);
			$data =  array(
				'nombre'              => Input::get('nombre' . $sufijo),
				'visible'             => Input::has('visible' . $sufijo) ? Input::get('visible' . $sufijo) : FALSE,
				'protegida'           => Input::has('protegida' . $sufijo) ? Input::get('protegida' . $sufijo) : FALSE,
				'idioma'              => $idioma,
				'clave_idioma_campos' => Input::get('clave_idioma_campos')
			);

			$root = $this->categoriaForm->updateRoot($data, $item);

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => $message
				)
			));

			return \Redirect::to('admin/categorias-traducibles/ver-raiz/' . $item->id . '#datos-' . $idioma);

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

		$esNuevaTraduccion = ! is_null(Input::get('nueva_traduccion')) && Input::get('nueva_traduccion');

		$pestania = $esNuevaTraduccion ? 'new' : $idioma;

		/*echo '<pre>';
		print_r($this->categoriaForm->errors());
		echo '</pre>';exit;*/

		return \Redirect::to('admin/categorias-traducibles/ver-raiz/' . $item->id . '#datos-' . $pestania)
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

			$sufijo  = '_';
			$sufijo .= Input::old('clave_idioma_campos') ?:'';

			$root = $this->categoria->rootById($id);

			$fillData = array(
				$this->_defaultIdioma->codigo_iso_2 => array(
					'nombre' => Input::old('nombre' . $sufijo) ? Input::old('nombre' . $sufijo) : '',
				),
				'visible'                         => Input::old('visible' . $sufijo) ? Input::old('visible' . $sufijo) : FALSE,
				'protegida'                       => Input::old('protegida' . $sufijo) ? Input::old('protegida' . $sufijo) : FALSE,
				'parent_id'                       => $root->id,
				'valor'                           => Input::old('valor' . $sufijo) ? Input::old('valor' . $sufijo) : '',
			);
			$item = $this->categoria->createNode($fillData);

			View::share('title', 'Nueva categoría en ' . $root->nombre);
			return View::make('panel::categoriastraducibles.form')
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

		return \Redirect::action('Ttt\Panel\CategoriaTraducibleController@index');
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

			$message = 'Nueva categoría creada correctamente en ' . $root->nombre;

			$sufijo  = '_';
			$sufijo .= Input::get('clave_idioma_campos') ?:'';

			$data =  array(
				'nombre'              => Input::get('nombre' . $sufijo),
				'valor'               => Input::get('valor' . $sufijo) == '' ? NULL : Input::get('valor' . $sufijo),
				'visible'             => Input::has('visible' . $sufijo) ? Input::get('visible' . $sufijo) : FALSE,
				'protegida'           => Input::has('protegida' . $sufijo) ? Input::get('protegida' . $sufijo) : FALSE,
				'idioma'              => $this->_defaultIdioma->codigo_iso_2,
				'clave_idioma_campos' => Input::get('clave_idioma_campos')
			);

			$nodo = $this->categoriaForm->createChild($data, $root);

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => $message
				)
			));

			return \Redirect::action('Ttt\Panel\CategoriaTraducibleController@ver', $nodo->id);
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

		return \Redirect::action('Ttt\Panel\CategoriaTraducibleController@nuevo', $root->id)
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

			$sufijo  = '_';
			$sufijo .= Input::old('clave_idioma_campos') ?:'';


			$item = $this->categoria->childById($id);

			$idioma = Input::old('idioma' . $sufijo) ?: $this->_defaultIdioma->codigo_iso_2;

			$esNuevaTraduccion = ! is_null(Input::old('nueva_traduccion')) && Input::old('nueva_traduccion');

			if($esNuevaTraduccion)
			{
				$item->traduccion('new')->nombre = ! is_null(Input::old('nombre' . $sufijo)) ? Input::old('nombre' . $sufijo) : $item->traduccion($idioma)->nombre;
				$item->traduccion('new')->idioma = 'new';
			}else{
				$item->traduccion($idioma)->nombre = ! is_null(Input::old('nombre' . $sufijo)) ? Input::old('nombre' . $sufijo) : $item->traduccion($idioma)->nombre;
				$item->traduccion($idioma)->idioma = $idioma;
			}
			$item->visible   = Input::old('visible' . $sufijo) ? Input::old('visible' . $sufijo) : $item->visible;
			$item->protegida = Input::old('protegida' . $sufijo) ? Input::old('protegida' . $sufijo) : $item->protegida;
			$item->valor     = Input::old('valor' . $sufijo) ? Input::old('valor' . $sufijo) : $item->valor;


			View::share('title', 'Edición de subcategoría ' . $item->nombre);
			return View::make('panel::categoriastraducibles.form')
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

		return \Redirect::action('Ttt\Panel\CategoriaTraducibleController@index');
	}

	/**
	* Intenta actualizar la información de un nodo
	*
	* @return void
	*/
	public function actualizar()
	{
		$message = 'Categoría actualizada correctamente.';
		try
		{

			$sufijo  = '_';
			$sufijo .= Input::get('clave_idioma_campos') ?:'';

			$item = $this->categoria->childById(Input::get('id'));

			$idioma = Input::get('idioma' . $sufijo);
			$data =  array(
				'nombre'              => Input::get('nombre' . $sufijo),
				'valor'               => Input::get('valor' . $sufijo),
				'visible'             => Input::has('visible' . $sufijo) ? Input::get('visible' . $sufijo) : FALSE,
				'protegida'           => Input::has('protegida' . $sufijo) ? Input::get('protegida' . $sufijo) : FALSE,
				'idioma'              => $idioma,
				'clave_idioma_campos' => Input::get('clave_idioma_campos')
			);
			$item = $this->categoriaForm->updateChild($data, $item);

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => $message
				)
			));

			return \Redirect::to('admin/categorias-traducibles/ver/' . $item->id . '#datos-' . $idioma);
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

		$esNuevaTraduccion = ! is_null(Input::get('nueva_traduccion')) && Input::get('nueva_traduccion');

		$pestania = $esNuevaTraduccion ? 'new' : $idioma;

		/*echo '<pre>';
		print_r($this->categoriaForm->errors());
		echo '</pre>';exit;*/

		return \Redirect::to('admin/categorias-traducibles/ver/' . $item->id . '#datos-' . $pestania)
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
		$message = 'Árbol de categorías eliminado correctamente.';

		$categoria = $this->categoria->byId($id);

		$categoria->delete();

		\Session::flash('messages', array(
			array(
				'class' => 'alert-success',
				'msg'   => $message
			)
		));

		return \Redirect::action('Ttt\Panel\CategoriaTraducibleController@index');
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
		return \Redirect::action('Ttt\Panel\CategoriaTraducibleController@verArbol', $root->id);
	}

	/**
	* Intenta borrar una traducción de un nodo
	*
	* @return void
	*/
	public function borrarTraduccion($id = null, $idioma = null)
	{
		$message = 'Traducción de categoría eliminada correctamente.';

		$categoria = $this->categoria->byId($id);

		$root = $categoria->getRoot();

		$categoria->traduccion($idioma)->delete();

		\Session::flash('messages', array(
			array(
				'class' => 'alert-success',
				'msg'   => $message
			)
		));

		//redirigimos a la estructura draggable del árbol
		if($categoria->isRoot())
		{
			return \Redirect::action('Ttt\Panel\CategoriaTraducibleController@verRaiz', $categoria->id);
		}else{
			return \Redirect::action('Ttt\Panel\CategoriaTraducibleController@ver', $categoria->id);
		}

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

			$item = $this->categoria->rootById($id);

			View::share('title', 'Vista completa del árbol ' . $item->nombre);
			return View::make('panel::categoriastraducibles.ver')
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

		return \Redirect::action('Ttt\Panel\CategoriaTraducibleController@index');
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

			$item->makeTreeOrdered($item);

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => $message
				)
			));
			return \Redirect::action('Ttt\Panel\CategoriaTraducibleController@verArbol', $item->id);

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

		return \Redirect::action('Ttt\Panel\CategoriaTraducibleController@index');
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
			$childrenRoot = isset($popArray['children']) ? $popArray['children'] : array();

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
