<?php
namespace Ttt\Panel;

use \Config;
use \Input;
use \Paginator;
use \View;
use Ttt\Panel\Repo\Variablesglobales\VariablesglobalesInterface;
use Ttt\Panel\Service\Form\Variablesglobales\VariablesglobalesForm;

use Ttt\Panel\Repo\Variablesglobales\Variablesglobales;

use Ttt\Panel\Core\AbstractCrudController;

class VariablesglobalesController extends AbstractCrudController{

	protected $_views_dir = 'variablesglobales';
	protected $_titulo = 'Variables Globales';

	protected $variablesglobale;

	protected $variablesglobalesForm;

	protected $allowed_url_params = array(
		'clave', 'ordenPor', 'ordenDir', 'creado_por'
	);

	protected $acciones_por_lote = array(
		'delete'    => 'Borrar'
	);

	public function __construct(VariablesglobalesInterface $variablesglobale, VariablesglobalesForm $variablesglobalesForm)
	{
                parent::__construct();

		$this->variablesglobale 		= $variablesglobale;
		$this->variablesglobalesForm            = $variablesglobalesForm;
	}

	public function index()
	{
            
            
		View::share('title', 'Listado de ' . $this->_titulo);

		//recogemos la página
		$pagina  = Input::get('pagina', 1);
		$perPage = Config::get('panel::app.perPage', 1);

		$params = $this->getParams();

		//recogemos la paginación
		$pageData = $this->variablesglobale->byPage($pagina, $perPage, $params);

		$variablesglobales = Paginator::make(
                                        $pageData->items,
                                        $pageData->totalItems,
                                        $perPage
                                    );

		$variablesglobales->appends($params);

		View::share('items', $variablesglobales);

		return View::make('panel::variablesglobales.index')
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
		$item->clave    = Input::old('clave') ? Input::old('clave') : '';
		$item->valor    = Input::old('valor') ? Input::old('valor') : '';
                
		//return Input::all();
		View::share('title', 'Creación de una nueva variable global.');
		return View::make('panel::variablesglobales.form')
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
				'clave'       => Input::get('clave'),
				'valor'       => Input::get('valor'),
                                'usuario'     =>  \Sentry::getUser()['id']
			);


			$variablesglobalesId = $this->variablesglobalesForm->save($data);

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => $message
				)
			));
			//return \Redirect::action('Ttt\Panel\ModuloController@ver', $moduloId);
                        return \Redirect::action('Ttt\Panel\VariablesglobalesController@index');

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

		return \Redirect::action('Ttt\Panel\VariablesglobalesController@nuevo')
									->withInput()
									->withErrors($this->variablesglobalesForm->errors());
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
			$item = $this->variablesglobale->byId($id);
			$item->clave   = ! is_null(Input::old('clave')) ? Input::old('clave') : $item->clave;
			$item->valor   = ! is_null(Input::old('valor')) ? Input::old('valor') : $item->valor;

			View::share('title', 'Edición de la variable ' . $item->clave);
			return View::make('panel::variablesglobales.form')
									->with('action', 'edit')
									->with('item', $item);

		}
		catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e)
		{
			$message = $e->getMessage();
			return \Redirect::action('Ttt\Panel\VariablesglobalesController@index');
		}

		\Session::flash('messages', array(
			array(
				'class' => 'alert-danger',
				'msg'   => $message
			)
		));

                return \Redirect::action('Ttt\Panel\VariablesglobalesController@index');
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
			$ent = $this->variablesglobale->byId(Input::get('id'));
                        
                        $var = Variablesglobales::find(Input::get('id'));
                        
                        //Refactorizacion 
//			$data = array(
//				'id'            => $ent->id,
//				'usuario'       => \Sentry::getUser()['id'],
//				'clave'         => Input::get('clave'),
//				'valor'         => Input::get('valor')
//			);
                        
                        //$var->actualizado_por = \Sentry::getUser()['id']; //Meter un observer/evento para los actulizados_por, creado_por
                        
                        $var->clave   = Input::get('clave');
                        $var->valor   = Input::get('valor');
                        
                        $var->save();
                        
                        

			//$moduloId = $this->variablesglobalesForm->update($data);

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => $message
				)	
		));

			return \Redirect::action('Ttt\Panel\VariablesglobalesController@ver', $var->id);

		}
		catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e)
		{
			$message = $e->getMessage();
			return \Redirect::action('Ttt\Panel\VariablesglobalesController@index');
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

		return \Redirect::action('Ttt\Panel\VariablesglobalesController@ver', $ent->id)
										->withInput()
										->withErrors($this->variablesglobaleForm->errors());
	}        
        
/**
	* Intenta actualizar un elemento existente
	* @return void
	*/
	public function borrar($id = null)
	{
		$message = 'Variable eliminada correctamente.';
		try
		{
                    
			//$ent = $this->modulo->byId(Input::get('id'));
			if(! $this->variablesglobale->delete($id))
			{
				throw new \Ttt\Panel\Exception\TttException;
			}

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => $message
				)
			));
                        

			return \Redirect::action('Ttt\Panel\VariablesglobalesController@index');

		}
		catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e)
		{
			$message = $e->getMessage();
			return \Redirect::action('Ttt\Panel\VariablesglobalesController@index');
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

		return \Redirect::action('Ttt\Panel\VariablesglobalesController@ver', $id);
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
				if(! method_exists($this->variablesglobale, $input['accion']))
				{
					throw new \Ttt\Exception\TttException;
				}

				call_user_func_array(array($this->variablesglobale, $input['accion']), array($itemId, \Sentry::getUser()['id']));
			}

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => 'La acción ' . $this->acciones_por_lote[$input['accion']] . ' se ha ejecutado correctamente.'
				)
			));

			return \Redirect::action('Ttt\Panel\VariablesglobalesController@index');

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
        
	protected function getParams()
	{
		$input = array_merge(Input::only($this->allowed_url_params));
                
		$input[Config::get('panel::app.orderBy')]  = !is_null($input[Config::get('panel::app.orderBy')]) ? $input[Config::get('panel::app.orderBy')] : 'clave';
		$input[Config::get('panel::app.orderDir')] = !is_null($input[Config::get('panel::app.orderDir')]) ? $input[Config::get('panel::app.orderDir')] : 'asc';
                
		return $input;
	}
}
