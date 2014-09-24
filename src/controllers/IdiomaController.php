<?php
namespace Ttt\Panel;

use \Config;
use \Input;
use \Paginator;
use \View;
use Ttt\Panel\Repo\Idioma\IdiomaInterface;
use Ttt\Panel\Service\Form\Idioma\IdiomaForm;
use Ttt\Panel\Core\AbstractCrudController;

class IdiomaController extends AbstractCrudController{

	protected $_views_dir = 'idiomas';
	protected $_titulo = 'Idiomas';

	public static $moduleSlug = 'idiomas';

	protected $idioma;

	protected $idiomaForm;

	protected $allowed_url_params = array(
		'nombre', 'codigo_iso_2', 'ordenPor', 'ordenDir', 'creado_por'
	);

	protected $acciones_por_lote = array(
		'visible'   => 'Visible',
		'noVisible' => 'No visible',
		'delete'    => 'Borrar'
	);

	public function __construct(IdiomaInterface $idioma, IdiomaForm $idiomaForm)
	{
		parent::__construct();

		$this->idioma     = $idioma;
		$this->idiomaForm = $idiomaForm;

		if(! \Sentry::getUser()->hasAccess('idiomas::editar'))
		{
			unset($this->acciones_por_lote['visible']);
			unset($this->acciones_por_lote['noVisible']);
		}
		if(! \Sentry::getUser()->hasAccess('idiomas::borrar'))
		{
			unset($this->acciones_por_lote['delete']);
		}
	}

	public function index()
	{
		View::share('title', 'Listado de Idiomas');

		//recogemos la página
		$pagina  = Input::get(Config::get('panel::app.pageName', 'pg'), 1);
		$perPage = Config::get('panel::app.perPage', 1);

		$params = $this->getParams();

		//recogemos la paginación
		$pageData = $this->idioma->byPage($pagina, $perPage, $params);

		$idiomas = Paginator::make(
			$pageData->items,
			$pageData->totalItems,
			$perPage
		);

		//debemos añadir los parámetros de la url
		$idiomas->appends($params);

		View::share('items', $idiomas);
		return View::make('panel::idiomas.index')
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
		$item->nombre       = Input::old('nombre') ? Input::old('nombre') : '';
		$item->codigo_iso_2 = Input::old('codigo_iso_2') ? Input::old('codigo_iso_2') : '';
		$item->codigo_iso_3 = Input::old('codigo_iso_3') ? Input::old('codigo_iso_3') : '';
		$item->visible      = Input::old('visible') ? Input::old('visible') : '0';
		$item->principal    = Input::old('principal') ? Input::old('principal') : '0';
		//return Input::all();
		View::share('title', 'Creación de nuevo idioma.');
		return View::make('panel::idiomas.form')
								->with('item', $item)
								->with('action', 'create');
	}

	/**
	* Intenta crear un nuevo elemento
	* @return void
	*/
	public function crear()
	{
		$message = 'Idioma creado correctamente.';
		try
		{

			$data = array(
				'nombre'       => Input::get('nombre'),
				'codigo_iso_2' => Input::get('codigo_iso_2'),
				'codigo_iso_3' => Input::get('codigo_iso_3'),
				'visible'      => (int) Input::has('visible'),
				'principal'    => (int) Input::has('principal')
			);


			$idioma = $this->idiomaForm->save($data);//se invoca para crear

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => $message
				)
			));
			return \Redirect::action('Ttt\Panel\IdiomaController@ver', $idioma->id);

		}
		catch(\Ttt\Panel\Exception\TttException $e)
		{
			$message = 'No se han podido guardar los cambios. Por favor revise los campos marcados.';
		}

		\Session::flash('messages', array(
			array(
				'class' => 'alert-danger',
				'msg'   => $message
			)
		));

		return \Redirect::action('Ttt\Panel\IdiomaController@nuevo')
									->withInput()
									->withErrors($this->idiomaForm->errors());
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
			$item = $this->idioma->byId($id);
			$item->nombre       = ! is_null(Input::old('nombre')) ? Input::old('nombre') : $item->nombre;
			$item->codigo_iso_2 = ! is_null(Input::old('codigo_iso_2')) ? Input::old('codigo_iso_2') : $item->codigo_iso_2;
			$item->codigo_iso_3 = ! is_null(Input::old('codigo_iso_3')) ? Input::old('codigo_iso_3') : $item->codigo_iso_3;
			$item->visible      = Input::old('visible') ? Input::old('visible') : $item->visible;
			$item->principal    = Input::old('principal') ? Input::old('principal') : $item->principal;

			View::share('title', 'Edición del idioma ' . $item->nombre);
			return View::make('panel::idiomas.form')
									->with('action', 'edit')
									->with('item', $item);

		}
		catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e)
		{
			$message = $e->getMessage();
		}

		\Session::flash('messages', array(
			array(
				'class' => 'alert-danger',
				'msg'   => $message
			)
		));

		return \Redirect::action('Ttt\Panel\IdiomaController@index');
	}

	/**
	* Intenta actualizar un elemento existente
	* @return void
	*/
	public function actualizar()
	{
		$message = 'Idioma actualizado correctamente.';
		try
		{
			$ent = $this->idioma->byId(Input::get('id'));

			$data = array(
				'id'           => $ent->id,
				'nombre'       => Input::get('nombre'),
				'codigo_iso_2' => Input::get('codigo_iso_2'),
				'codigo_iso_3' => Input::get('codigo_iso_3'),
				'visible'      => (int) Input::has('visible'),
				'principal'    => (int) Input::has('principal')
			);

			$modulo = $this->idiomaForm->update($data);

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => $message
				)
			));

			return \Redirect::action('Ttt\Panel\IdiomaController@ver', $ent->id);

		}
		catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e)
		{
			$message = $e->getMessage();
			\Session::flash('messages', array(
				array(
					'class' => 'alert-danger',
					'msg'   => $message
				)
			));
			return \Redirect::action('Ttt\Panel\IdiomaController@index');
		}
		catch(\Ttt\Panel\Exception\TttException $e)
		{
			$message = 'No se han podido guardar los cambios. Por favor revise los campos marcados.';
		}

		\Session::flash('messages', array(
			array(
				'class' => 'alert-danger',
				'msg'   => $message
			)
		));

		return \Redirect::action('Ttt\Panel\IdiomaController@ver', $ent->id)
																		->withInput()
																		->withErrors($this->idiomaForm->errors());
	}

	/**
	* Intenta actualizar un elemento existente
	* @return void
	*/
        
	public function borrar($id = null)
	{
		$message = 'Idioma eliminado correctamente.';
		try
		{
			if(! $this->idioma->delete($id))
			{
				throw new \Ttt\Panel\Exception\TttException;
			}

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => $message
				)
			));

			return \Redirect::action('Ttt\Panel\IdiomaController@index');

		}
		catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e)
		{
			$message = $e->getMessage();
			\Session::flash('messages', array(
				array(
					'class' => 'alert-danger',
					'msg'   => $message
				)
			));
			return \Redirect::action('Ttt\Panel\IdiomaController@index');
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

		return \Redirect::action('Ttt\Panel\IdiomaController@ver', $id);
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
				if(! method_exists($this->idioma, $input['accion']))
				{
					throw new \Ttt\Exception\TttException;
				}

				call_user_func_array(array($this->idioma, $input['accion']), array($itemId));
			}

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => 'La acción ' . $this->acciones_por_lote[$input['accion']] . ' se ha ejecutado correctamente.'
				)
			));

			return \Redirect::action('Ttt\Panel\IdiomaController@index');

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

			$ent = $this->idioma->byId(Input::get('id'));

			$ent->visible = $ent->visible ? 0 : 1;

			$this->idioma->update(
				array(
					'id'      => $ent->id,
					'visible' => $ent->visible
				)
			);

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
