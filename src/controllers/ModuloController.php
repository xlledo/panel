<?php
namespace Ttt\Panel;

use \Config;
use \Input;
use \Paginator;
use \View;
use Ttt\Panel\Repo\Modulo\ModuloInterface;
use Ttt\Panel\Service\Form\Modulo\ModuloForm;
use Ttt\Panel\Core\AbstractCrudController;

class ModuloController extends AbstractCrudController{

	protected $_views_dir = 'modulos';
	protected $_titulo = 'Modulos';

	public static $moduleSlug = 'modulos';

	protected $modulo;

	protected $moduloForm;

	protected $allowed_url_params = array(
		'nombre', 'ordenPor', 'ordenDir', 'creado_por'
	);

	protected $acciones_por_lote = array(
		'visible'   => 'Visible',
		'noVisible' => 'No visible',
		'delete'    => 'Borrar'
	);

	public function __construct(ModuloInterface $modulo, ModuloForm $moduloForm)
	{
		parent::__construct();

		$this->modulo     = $modulo;
		$this->moduloForm = $moduloForm;
	}

	public function index()
	{
		View::share('title', 'Listado de Módulos');

		//recogemos la página
		$pagina  = Input::get(Config::get('panel::app.pageName', 'pg'), 1);
		$perPage = Config::get('panel::app.perPage', 1);

		$params = $this->getParams();

		//recogemos la paginación
		$pageData = $this->modulo->byPage($pagina, $perPage, $params);

		$modulos = Paginator::make(
			$pageData->items,
			$pageData->totalItems,
			$perPage
		);

		//debemos añadir los parámetros de la url
		$modulos->appends($params);

		View::share('items', $modulos);
		return View::make('panel::modulos.index')
								->with('params', $params)
								->with('currentUrl', \URL::current())
								->with('accionesPorLote', $this->acciones_por_lote);
	}

	/**
	* Muestra el formulario de creación
	* @return void
	*/
	public function nuevo()
	{
		$item = new \StdClass;
		$item->nombre  = Input::old('nombre') ? Input::old('nombre') : '';
		$item->visible = Input::old('visible') ? Input::old('visible') : '0';
		//return Input::all();
		View::share('title', 'Creación de nuevo módulo.');
		return View::make('panel::modulos.form')
								->with('item', $item)
								->with('action', 'create');
	}

	/**
	* Intenta crear un nuevo elemento
	* @return void
	*/
	public function crear()
	{
		$message = 'Módulo creado correctamente.';
		try
		{

			$data = array(
				'usuario'      => \Sentry::getUser()['id'],
				'nombre'       => Input::get('nombre'),
				'visible'      => (int) Input::has('visible')
			);


			$moduloId = $this->moduloForm->save($data);

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => $message
				)
			));
			return \Redirect::action('Ttt\Panel\ModuloController@ver', $moduloId);

		}
		catch(\Ttt\Panel\Exception\TttException $e)
		{
			$message = 'Existen errores de validación';
		}

		\Session::flash('messages', array(
			array(
				'class' => 'alert-danger',
				'msg'   => $message
			)
		));

		return \Redirect::action('Ttt\Panel\ModuloController@nuevo')
									->withInput()
									->withErrors($this->moduloForm->errors());
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
			//$ent = $this->modulo->byId(Input::get('id'));
			$item = $this->modulo->byId($id);
			$item->nombre   = ! is_null(Input::old('nombre')) ? Input::old('nombre') : $item->nombre;
			$item->visible  = Input::old('visible') ? Input::old('visible') : $item->visible;

			View::share('title', 'Edición del módulo ' . $item->nombre);
			return View::make('panel::modulos.form')
									->with('action', 'edit')
									->with('item', $item);

		}
		catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e)
		{
			$message = $e->getMessage();
			return \Redirect::action('Ttt\Panel\ModuloController@index');
		}

		\Session::flash('messages', array(
			array(
				'class' => 'alert-danger',
				'msg'   => $message
			)
		));

		return \Redirect::action('Ttt\Panel\ModuloController@index');
	}

	/**
	* Intenta actualizar un elemento existente
	* @return void
	*/
	public function actualizar()
	{
		$message = 'Módulo actualizado correctamente.';
		try
		{
			$ent = $this->modulo->byId(Input::get('id'));

			$data = array(
				'id'           => $ent->id,
				'usuario'      => \Sentry::getUser()['id'],
				'nombre'       => Input::get('nombre'),
				'visible'      => (int) Input::has('visible')
			);

			$moduloId = $this->moduloForm->update($data);

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => $message
				)
			));

			return \Redirect::action('Ttt\Panel\ModuloController@ver', $ent->id);

		}
		catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e)
		{
			$message = $e->getMessage();
			return \Redirect::action('Ttt\Panel\ModuloController@index');
		}
		catch(\Ttt\Panel\Exception\TttException $e)
		{
			$message = 'Existen errores de validación';
		}

		\Session::flash('messages', array(
			array(
				'class' => 'alert-danger',
				'msg'   => $message
			)
		));

		return \Redirect::action('Ttt\Panel\ModuloController@ver', $ent->id)
																		->withInput()
																		->withErrors($this->moduloForm->errors());
	}

	/**
	* Intenta actualizar un elemento existente
	* @return void
	*/
	public function borrar($id = null)
	{
		$message = 'Módulo eliminado correctamente.';
		try
		{
			//$ent = $this->modulo->byId(Input::get('id'));
			if(! $this->modulo->delete($id))
			{
				throw new \Ttt\Panel\Exception\TttException;
			}

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => $message
				)
			));

			return \Redirect::action('Ttt\Panel\ModuloController@index');

		}
		catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e)
		{
			$message = $e->getMessage();
			return \Redirect::action('Ttt\Panel\ModuloController@index');
		}
		catch(\Ttt\Panel\Exception\TttException $e)
		{
			$message = 'No puede eliminarse el elemento.';
		}

		\Session::flash('messages', array(
			array(
				'class' => 'alert-danger',
				'msg'   => $message
			)
		));

		return \Redirect::action('Ttt\Panel\ModuloController@ver', $moduloId);
	}

	/**
	* Ejecuta una acción sobre un conjunto de elementos
	* @throws \Ttt\Exception\BatchActionException
	* @return void
	*/
	public function accionesPorLote()
	{
		$input = Input::only('item', 'accion');

		try{

			if(! array_key_exists($input['accion'], $this->acciones_por_lote))
			{
				throw new \Ttt\Panel\Exception\TttException;
			}

			foreach($input['item'] as $itemId)
			{
				if(! method_exists($this->modulo, $input['accion']))
				{
					throw new \Ttt\Exception\TttException;
				}

				call_user_func_array(array($this->modulo, $input['accion']), array($itemId, \Sentry::getUser()['id']));
			}

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => 'La acción ' . $this->acciones_por_lote[$input['accion']] . ' se ha ejecutado correctamente.'
				)
			));

			return \Redirect::action('Ttt\Panel\ModuloController@index');

		}
		catch(\Ttt\Panel\Exception\TttException $e)
		{
			$mensaje = 'La acción indicada no existe';
		}
		catch(\Ttt\Panel\Exception\BatchActionException $e)
		{
			$mensaje = $e->getMessage();
		}

		//error al intentar ejecutar la acción
		\Session::flash('messages', array(
			array(
				'class' => 'alert-danger',
				'msg'   => $mensaje
			)
		));

		return \Redirect::action('Ttt\Panel\DashboardController@index');
	}

	/**
	* Cambia el estado de un elemento del modelo de Visible a No Visible y viceversa
	*/
	public function visibleNoVisible()
	{

		$response = array(
			'error'   => FALSE,
			'message' => '',
			'id'      => Input::get('id'),
			'visible' => null
		);

		try
		{
			if(! \Request::ajax())
			{
				throw new \Ttt\Panel\Exception\TttException("Petición no válida, este recurso solo es accesible mediante AJAX");
			}

			$ent = $this->modulo->byId(Input::get('id'));

			$ent->visible = $ent->visible ? 0 : 1;

			$this->modulo->update(
				array(
					'id'      => $ent->id,
					'nombre'  => $ent->nombre,
					'usuario' => \Sentry::getUser()['id'],
					'visible' => $ent->visible
				)
			);

			$ent->update();

			$response['visible'] = $ent->visible;
			$response['message'] = 'Cambiado el estado correctamente.';

		}
		catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e)
		{
			$response['error']   = TRUE;
			$response['message'] = $e->getMessage();
		}
		catch(\Ttt\Panel\Exception\TttException $e)
		{
			$response['error']   = TRUE;
			$response['message'] = $e->getMessage();
		}

		return $response;//automáticamente devuelve un objeto JSON
	}

	protected function getParams()
	{
		$input = array_merge(Input::only($this->allowed_url_params));

		$input[Config::get('panel::app.orderBy')]  = !is_null($input[Config::get('panel::app.orderBy')]) ? $input[Config::get('panel::app.orderBy')] : 'nombre';
		$input[Config::get('panel::app.orderDir')] = !is_null($input[Config::get('panel::app.orderDir')]) ? $input[Config::get('panel::app.orderDir')] : 'asc';

		return $input;
	}

}
