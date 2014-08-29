<?php

namespace Ttt\Panel;

use \Config;
use \Input;
use \Paginator;
use \View;

use Ttt\Panel\Repo\Paginas\PaginasInterface;
use Ttt\Panel\Service\Form\Paginas\PaginasForm;

use Ttt\Panel\Repo\Paginas\Pagina;
use Ttt\Panel\Repo\Paginas\PaginasI18n;

use Ttt\Panel\Core\AbstractCrudController;

class PaginasController extends AbstractCrudController
{

	protected $_views_dir = 'paginas';
	protected $_titulo = 'Paginas';

	public static $moduleSlug = 'paginas';    
    
        protected $pagina;
        protected $paginaForm;
        
	protected $allowed_url_params = array(
		'clave', 'ordenPor', 'ordenDir', 'creado_por'
	);

	protected $acciones_por_lote = array(
		'delete'    => 'Borrar'
	);
        
    public function __construct(    PaginasInterface $pagina, 
                                    PaginasForm $paginaForm ) {
        parent::__construct();

        $this->pagina       = $pagina;
        $this->paginaForm   = $paginaForm;

        $this->_idioma_predeterminado = Repo\Idioma\Idioma::where('principal','=',1)->get();
        $this->_todos_idiomas         = Repo\Idioma\Idioma::all();

        View::share('idioma_predeterminado', $this->_idioma_predeterminado->first());
        View::share('todos_idiomas', $this->_todos_idiomas);        
    }   
    
        public function index()
        {
            
            View::share('title','Listado de ' . $this->_titulo);
            
            //Recogemos la página
            $pagina     = Input::get('pagina', 1);
            $perPage    = Config::get('panel::app.perPage', 1);
            
            $params = $this->getParams();
            
            //Recogemos la paginación
            $pageData = $this->pagina->byPage($pagina, $perPage, $params);
           
            $paginas = Paginator::make(
                        $pageData->items,
                        $pageData->totalItems,
                        $perPage );
            
            $paginas->appends($params);
            
            View::share('items', $paginas);
            
            return View::make('panel::paginas.index')
                                ->with('params', $params)
                                ->with('currentUrl', \URL::current())
                                ->with('accionesPorLote', $this->acciones_por_lote);
        }    

        /*
         * Formulario de creacion de pagina
         */
    
        public function nuevo()
        {
            
            $item = new \stdClass();
            $item->titulo = Input::old('titulo') ? Input::old('titulo') : '';
            $item->texto  = Input::old('texto') ? Input::old('texto') : '';
            
            View::share('title', 'Creacion de una nueva página');
            return View::make('panel::paginas.form')
                                        ->with('item', $item)
                                        ->with('action','create');
            
        }
        
        /**
         * Formulario de edición
         */
        
        public function ver($id = null)
        {
            if( $id )
            {
                $item = $this->pagina->byId($id);
                
                View::share('title', 'Editar elemento');
                return View::make('panel::paginas.form')
                                    ->with('item',$item)
                                    ->with('action','edit');
            }
        }
        
        /**
         * Crear un item
         */
        
        public function crear()
        {
            $message = 'Pagina creada correctamente.';
            
            try{
                $data = array(
                    'titulo' => Input::get('titulo'),
                    'texto' => Input::get('texto'),
                    'idioma' => Input::get('idioma'),
                    'creado_por' => \Sentry::getUser()['id'],
                    'actualizado_por' => \Sentry::getUser()['id']
                );
                
                $paginaId = $this->paginaForm->save($data);
                
                \Session::flash('messages', array(
                    array(
                        'class' => 'alert-success',
                        'msg'   => $message
                    )
                ));
                
                return \Redirect::action('Ttt\Panel\PaginasController@ver', $paginaId);
                
            } catch (\Ttt\Panel\Exception\TttException $ex) {
                $message = 'Existen errores de validación';
            }
            
            \Session::flash('messages', array(
                array(
                    'class' => 'alert-danger',
                    'msg'   => $message
                )
            ));
            
            return \Redirect::action('Ttt\Panel\PaginasController@nuevo')
                                        ->withInput()
                                        ->withErrores($this->paginaForm->errors());
            
        }
        
        
        protected function getParams()
        {
            $input = array_merge(Input::only($this->allowed_url_params));
            
            $input[Config::get('panel::app.orderBy')] = !is_null($input[Config::get('panel::app.orderBy')]) ? $input[Config::get('panel::app.orderBy')] : 'id';
            $input[Config::get('panel::app.orderDir')] = !is_null($input[Config::get('panel::app.orderDir')]) ? $input[Config::get('panel::app.orderDir')] : 'asc';

            return $input;
        }
}