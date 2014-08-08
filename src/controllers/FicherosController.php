<?php

namespace Ttt\Panel;

use \Config;
use \Input;
use \Paginator;
use \view;

use Ttt\Panel\Repo\Fichero\FicheroInterface;
use Ttt\Panel\Service\Form\Fichero\FicheroForm;

use Ttt\Panel\Core\AbstractCrudController;

class FicherosController extends AbstractCrudController
{
    
    protected $_views_dir = 'ficheros';
    protected $_titulo = 'Ficheros';
    
    public static $moduleSlug = 'ficheros';
    
    protected $fichero;
    protected $ficheroForm;
    
    protected $allowed_url_params = array(
            'nombre','ordenPor','ordenDir','creado_por'
    );
    
    protected $acciones_por_lote = array(
        'delete' => 'Borrar'
    );
    
    
    
    /* Para poner en un fichero de configuracion */
    protected $_validacion_fichero = array();
    protected $_upload_folder = 'uploads/';
    protected $_config_ficheros = array();
    
    public function __construct(FicheroInterface $fichero, FicheroForm $ficheroForm)
    {
        parent::__construct();
        
        $this->fichero = $fichero;
        $this->ficheroForm = $ficheroForm;
        
//        if(! \Sentry::getUser()->hasAccess('ficheros::borrar'))
//            {
//                unset($this->acciones_por_lote['delete']);
//            }        

         $this->_config_ficheros = Config::get('panel::ficheros');    
         
    }
    
    public function index()
    {
      
		View::share('title', 'Listado de ' . $this->_titulo);

		//recogemos la página
		$pagina  = Input::get('pagina', 1);
		$perPage = Config::get('panel::app.perPage', 1);

		$params = $this->getParams();

		//recogemos la paginación
		$pageData = $this->fichero->byPage($pagina, $perPage, $params);
                
		$ficheros = Paginator::make(
                                        $pageData->items,
                                        $pageData->totalItems,
                                        $perPage
                                    );

		$ficheros->appends($params);

		View::share('items', $ficheros);

		return View::make('panel::ficheros.index')
                                        ->with('params', $params)
					->with('currentUrl', \URL::current())
					->with('accionesPorLote', $this->acciones_por_lote);

  
    }
    
        public function nuevo()
        {
            $item = new \stdClass();
            $item->nombre = Input::old('nombre') ? Input::old('nombre'): '';
            
            View::share('title', 'Creacion de un nuevo fichero');
            return View::make('panel::ficheros.form')
                                    ->with('item', $item)
                                    ->with('action', 'create' );
            
        }
        
        public function crear()
        {
            $message = 'Modulo creado correctamente';
            
            try{
                $fichero = Input::file('fichero');
                
                if(Input::hasFile('fichero'))
                {
                    $fichero = Input::file('fichero');
                    $nombre_fichero = \Illuminate\Support\Str::slug($fichero->getClientOriginalName(),'-') . '.' . $fichero->getClientOriginalExtension();
                    $path_completo  = $this->_upload_folder . date("Y") . '/' . date("m") . '/';
                    $mime           = $fichero->getMimeType();
                    
                    $i=1;
                    while(file_exists($path_completo . $nombre_fichero)){
                        $nombre_fichero = \Illuminate\Support\Str::slug($fichero->getClientOriginalName(),'-') . '_'.$i . '.' . $fichero->getClientOriginalExtension();
                        $i++;
                    }
                    
                    //-- Guardamos el fichero en la ruta
                    $fichero->move($path_completo , $nombre_fichero) ;
                }
                
                $data = array(
                    'nombre'  => Input::get('nombre'),
                    'fichero' => $nombre_fichero,
                    'usuario' => \Sentry::getUser()['id'],
                    'ruta'    => $path_completo,
                    'mime'    => $mime,
                    'tipo'    => 'imagenes',
                    'fichero_original' => $fichero //Pasamos el fichero para propositos de validacion
                );
                
                /*
                 * Primero subimos la imagen, si sube correctamente
                 * guardamos el registro en la BBDD
                 */
                
                $ficheroId = $this->ficheroForm->save($data);
                
                \Session::flash('messages', array(
                        array(
                            'class' => 'alert-success',
                            'msg'   => $message
                        )
                ));
                
                return \Redirect::action('Ttt\Panel\FicherosController@index');
                
            } catch (Ttt\Panel\Exception\TttException $e) {
                $message = 'Existen errores de valcidacion';
            }
            
            \Session::flash('messages', array(
                array(
                    'class' => 'alert-danger',
                    'msg'   => $message
                )));
            
            return \Redirect::action('Ttt\Panel\FicherosController@nuevo')
                                            ->withInput()
                                            ->withErrors($this->ficheroForm->errors());
                    
        }

    	protected function getParams()
	{	
	$input = array_merge(Input::only($this->allowed_url_params));

		$input[Config::get('panel::app.orderBy')]  = !is_null($input[Config::get('panel::app.orderBy')]) ? $input[Config::get('panel::app.orderBy')] : 'nombre';
		$input[Config::get('panel::app.orderDir')] = !is_null($input[Config::get('panel::app.orderDir')]) ? $input[Config::get('panel::app.orderDir')] : 'asc';

		return $input;
	}
}