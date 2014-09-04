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
        
        //Cargamos el config 
         $this->_config_ficheros = Config::get('panel::ficheros');    
         View::share('config_ficheros', $this->_config_ficheros);
         
         //Cargamos todos los ficheros
         View::share('ficheros_todos', Repo\Fichero\Fichero::all());
         
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
                
                //Adjuntos
                View::share('ficheros', $item->ficheros());
                View::share('pagina', $item);
                
                $params = $this->getParams();
                
                return View::make('panel::paginas.form')
                                    //->with('params', $params)
                                    ->with('item',$item)
                                    ->with('currentUrl', \URL::current())
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
        
        /**
         * Actualizar un Item
         * 
         */
        
        public function actualizar()
        {
            $message = 'Página guardada correctamente';
            
            try{
                
                //Cogemos la tabla master
                $this->pagina = $pagina = $this->pagina->byId(Input::get('item_id'));
                
                //Cogemos la traducción
                $pagina_i18n = Pagina::find(Input::get('item_id'))
                                                ->traducciones()
                                                ->where('idioma','=',Input::get('idioma'))
                                                ->first();
                
                
                $data = array(
                        'id'        => Input::get('item_id'),
                        'titulo'    => Input::get('titulo'),
                        'texto'     => Input::get('texto'),
                        'idioma'    => Input::get('idioma'),
                        'usuario'   => \Sentry::getUser()['id']
                );
                
                //Campos traducibles
                $pagina_i18n->texto  = Input::get('texto');
                $pagina_i18n->titulo = Input::get('titulo');

                if( $this->paginaForm->update($data)
                    && $pagina_i18n->save() )
                {
                    
                    //Paginas guardadas correctamente
                    \Session::flash('messages', array(
                                    array(
                                        'class' => 'alert-success',
                                        'msg'   => $message
                                    )
                    ));
                    
                    return \Redirect::to('admin/paginas/ver/' . $pagina->id);
                    
                }
                
            } catch (\Ttt\Panel\Exception\TttException $ex) {
                    $message = 'Existen errores de validación' ;
            }
            
            \Session::flash('messages', array(
                                array(
                                            'class' => 'alert-danger',
                                            'msg'   => $message
                                )
            ));
           

            //-- Cargamos los errores en un array por idioma
            //-- para luego mostrarlos en el form de idioma que toque
            $errores = array();
            $errores[Input::get('idioma')] = $this->paginaForm->errors();
            
            \Session::flash('idioma_error', Input::get('idioma'));
            
            $idioma_redireccion = empty(Input::get('idioma')) ? 'nuevatraduccion' : Input::get('idioma');
            return \Redirect::to('admin/paginas/ver/' . Input::get('item_id') . '#datos-' . $idioma_redireccion)
                                                        ->withInput()
                                                        ->withErrors($this->paginaForm->errors());
        }
        
        
        public function asociarFichero($id = null)
        {
            //-- Recuperamos el fichero
            if( $fichero = Repo\Fichero\Fichero::find($id) && $pagina = $this->pagina->byId(Input::get('from')) ){
                
                //-- Hay que pasar relaciones aquí
                $pagina->ficheros()->attach($fichero);
                
                \Session::flash('messages', array(
                                    array(
                                            'class' => 'alert-success',
                                            'msg'   => 'Fichero asociado correctamente'
                                    )
                ));
                
            }else{
                
                \Session::flash('messages', array(
                                    array(
                                            'class' => 'alert-danger',
                                            'msg'   => 'Error'
                                    )
                ));
            }
                return \Redirect::to('admin/paginas/ver/' . Input::get('from'));
        }
        
        protected function getParams()
        {
            $input = array_merge(Input::only($this->allowed_url_params));
            
            $input[Config::get('panel::app.orderBy')] = !is_null($input[Config::get('panel::app.orderBy')]) ? $input[Config::get('panel::app.orderBy')] : 'id';
            $input[Config::get('panel::app.orderDir')] = !is_null($input[Config::get('panel::app.orderDir')]) ? $input[Config::get('panel::app.orderDir')] : 'asc';

            return $input;
        }
}