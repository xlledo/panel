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
    
    protected $_config_ficheros = array();
    
    public function __construct(FicheroInterface $fichero, FicheroForm $ficheroForm)
    {
        parent::__construct();
        
        $this->fichero = $fichero;
        $this->ficheroForm = $ficheroForm;
        
        if(! \Sentry::getUser()->hasAccess('ficheros::borrar'))
            {
                unset($this->acciones_por_lote['delete']);
            }        

         $this->_config_ficheros = Config::get('panel::ficheros');    
         
         $categoria_ficheros = \Ttt\Panel\Repo\Categoria\Categoria::where('slug','=','ficheros')->get()->first();
         $categorias_ficheros = ($categoria_ficheros) ? $categoria_ficheros->getDescendants()->toArray() : array();
         
         $_categorias_ficheros_array = array();
         foreach($categorias_ficheros as $cat){
             $_categorias_ficheros_array[$cat['id']] = $cat['nombre'];
         }
         
         \View::share('_categorias_ficheros_array', $_categorias_ficheros_array);
         View::share('config_ficheros', $this->_config_ficheros);
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
            $item->titulo_defecto   = Input::old('titulo_defecto')?:'';
            $item->alt_defecto      = Input::old('alt_defecto')?:'';
            $item->descripcion_defecto = Input::old('descripcion_defecto')?:'';
            $item->enlace_defecto      = Input::old('enlace_defecto')?:'';
            $item->categoria_id        = Input::old('categoria_id')?:'';
            
            View::share('title', 'Creacion de un nuevo fichero');
            return View::make('panel::ficheros.form')
                                    ->with('item', $item)
                                    ->with('from_url', (Input::get('from_url')?: ''))
                                    ->with('action', 'create' );
            
        }
        
        public function crear()
        {
            $message = 'Fichero creado correctamente';
            
            try{
                $fichero = Input::file('fichero');
                
                $nombre_fichero = '';
                
                if(Input::hasFile('fichero'))
                {

                    if(true)
                    {
                        $fichero = Input::file('fichero');
                        
                        $nombre_fichero_canonical   = \Illuminate\Support\Str::slug( substr($fichero->getClientOriginalName(), 0, strlen($fichero->getClientOriginalName())- (strlen($fichero->getClientOriginalExtension()))));
                        $nombre_fichero             = $nombre_fichero_canonical . '.' . $fichero->getClientOriginalExtension();
                        
                        $path_completo  = $this->_upload_folder . date("Y") . '/' . date("m") . '/';
                        $mime           = $fichero->getMimeType();

                        /**
                                                * Generamos el nombre del fichero,
                                                * si ya existe lo numeramos
                                                */
                        $i=1;
                        while(file_exists($path_completo . $nombre_fichero)){
                            $nombre_fichero = \Illuminate\Support\Str::slug($fichero->getClientOriginalName(),'-') . '_'.$i . '.' . $fichero->getClientOriginalExtension();
                            $i++;
                        }

                        //-- Guardamos el fichero en la ruta
                        $fichero->move($path_completo , $nombre_fichero);
                        
                    }else{
                        throw new \Ttt\Panel\Exception\TttException('Errores de validacion en el Fichero');
                    }
                }
                
                //Comprobar que nombre fichero existe y es válido
                if($nombre_fichero == ''){
                    //Si no existe el nombre del fichero es por que no se ha subido ninguno
                    throw new \Ttt\Panel\Exception\TttException('Error fichero no seleccionado');
                }
                
                
                
                $data = array(
                    'nombre'  => (Input::get('nombre')) ?: $nombre_fichero_canonical, 
                    'fichero' => $nombre_fichero,
                    'usuario' => \Sentry::getUser()['id'],
                    'ruta'    => $path_completo,
                    'mime'    => $mime,
                    'peso'    => '',
                    'categoria_id' => Input::get('categoria_id'),
                    'titulo_defecto'        => Input::get('titulo_defecto'),
                    'alt_defecto'           => Input::get('alt_defecto'),
                    'enlace_defecto'        => Input::get('enlace_defecto'),
                    'descripcion_defecto'   => Input::get('descripcion_defecto'),
                    'fichero_original'      => $fichero //Pasamos el fichero para propositos de validacion
                );
                
                try { //Si falla al obtener el peso, lo ponemos a 0
                    $data['peso'] = $fichero->getSize();
                } catch (\RuntimeException $ex) {
                    $data['peso'] = '';
                }
                
                /*
                                * Primero subimos el fichero, si sube correctamente
                                * entonces guardamos el registro en la BBDD
                                */
                
                $ficheroId = $this->ficheroForm->save($data);
                
                \Session::flash('messages', array(
                        array(
                            'class' => 'alert-success',
                            'msg'   => $message
                        )
                ));

                //-- Si el fichero se crea desde otro módulo 
                //-- lo redirigimos de nuevo allí
                if(Input::get('asociar')== 1){
                    $url_redirect = \URL::to(Input::get('accion_asociar')) . '/' . $ficheroId . '?from=' . Input::get('from_id');
                    return \Redirect::to( $url_redirect );
                }

                return \Redirect::action('Ttt\Panel\FicherosController@ver', $ficheroId);
                
            } catch (\Ttt\Panel\Exception\TttException $e) {
                $message = $e->getMessage();
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
        
        public function ver($id = null)
        {
            $message = '';
            
            if( $id ){
                
                $fichero  = $this->fichero->byId($id);
                $fichero->nombre = ! is_null(Input::old('nombre')) ? Input::old('nombre') : $fichero->nombre;
                $fichero->titulo_defecto        = ! is_null(Input::old('titulo_defecto')) ? Input::old('titulo_defecto') : $fichero->titulo_defecto;
                $fichero->alt_defecto           = ! is_null(Input::old('alt_defecto')) ? Input::old('alt_defecto') : $fichero->alt_defecto;
                $fichero->descripcion_defecto   = ! is_null(Input::old('descripcion_defecto')) ? Input::old('descripcion_defecto') : $fichero->descripcion_defecto;
                $fichero->enlace_defecto        = ! is_null(Input::old('enlace_defecto')) ? Input::old('enlace_defecto') : $fichero->enlace_defecto;
                        
                View::share('title', 'Edicion del fichero' . $fichero->nombre);
                
                return View::make('panel::ficheros.form')
                                ->with('action', 'edit')
                                ->with('from_url', (Input::get('from_url')?: ''))
                                ->with('item', $fichero);
                
            }else{
                
                return \Redirect::action('Ttt\Panel\FicherosController@index');
            }
        }
        
        public function actualizar()
        {
            $message = 'Fichero Actualizado correctamente.';
            
            try{
                $fichero = $this->fichero->byId(Input::get('id'));
                
                //Eliminamos las miniaturas anteriores
                $fichero->limpiarCacheMiniaturas();
                
                //--Guardamos el fichero
                $fichero->nombre                = (Input::get('nombre'))?:$fichero->fichero;
                $fichero->titulo_defecto        = Input::get('titulo_defecto');
                $fichero->alt_defecto           = Input::get('alt_defecto');
                $fichero->descripcion_defecto   = Input::get('descripcion_defecto');
                $fichero->enlace_defecto        = Input::get('enlace_defecto');
                $fichero->categoria_id          = Input::get('categoria_id');
                
                $data = array(
                    'id'                    => $fichero->id,
                    'nombre'                => $fichero->nombre,
                    'titulo_defecto'        => $fichero->titulo_defecto,
                    'alt_defecto'           => $fichero->alt_defecto,
                    'descripcion_defecto'   => $fichero->descripcion_defecto,
                    'enlace_defecto'        => $fichero->enlace_defecto,
                    'categoria_id'          => $fichero->categoria_id
                );
                
                
                //--Subimos la imagen si tenemos
                if(Input::hasFile('fichero')){

                    $fic = Input::file('fichero');
                    $fic->move($fichero->ruta, $fichero->fichero);
                    
                    try {  //Si falla al obtener el peso, lo ponemos a 0
                        $data['peso'] = $fic->getSize();
                    } catch (\RuntimeException $ex) {
                        $data['peso'] = '';
                    }                    
                }
                
                $this->ficheroForm->update($data);
                
                //$fichero->save();
                
                \Session::flash('messages', array(
                                array(
                                    'class'=>'alert-success',
                                    'msg' => $message
                                )
                ));
                
                if(Input::get('from_url')){
                    return \Redirect::to(Input::get('from_url'));
                }
                
                return \Redirect::action('Ttt\Panel\FicherosController@ver', $fichero->id);
                
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex){
                $message = $ex->getMessage();
                return \Redirect::action('Ttt\Panel\FicherosController@index');
            } catch(\Ttt\Panel\Exception\TttException $e){
                $message = 'No se han podido guardar los cambios. Por favor revise los campos marcados.';
            }
            
            \Session::flash('messages', array(
                array(
                    'class' => 'alert-danger',
                    'msg'   => $message
                )
            ));
            
            return \Redirect::action('Ttt\Panel\FicherosController@ver', $fichero->id)
                                                                            ->withInput()
                                                                            ->withErrors($this->ficheroForm->errors());
            
        }
        
        public function borrar($id = null)
        {
            //-- Hemos de borrar fisicamente el fichero (unlink)
            $msg = 'Fichero borrado correctamente';
            
            if( $id ){
            
                $fichero = $this->fichero->byId($id);
                $result = FALSE;
                
                    if(file_exists($fichero->ruta . $fichero->fichero)){
                        $result = unlink($fichero->ruta . $fichero->fichero);
                    }
                
                $this->fichero->delete($fichero->id);
                
                if($result){
                    \Session::flash('messages', array(
                                        array(
                                            'class' => 'alert-success',
                                            'msg'   => $msg
                                        )
                    ));
                }
                
            }else{
                    \Session::flash('messages', array(
                                        array(
                                            'class' => 'alert-success',
                                            'msg'   => 'Error al borrar el fichero, fichero no encontrado'
                                        )
                    ));
            }
            
            return \Redirect::action('Ttt\Panel\FicherosController@index');
        }
        
        
        public function accionesPorLote()
        {
            $input = Input::only('item', 'accion');
            
            try{
                if(!array_key_exists($input['accion'], $this->acciones_por_lote))
                {
                    throw new \Ttt\Panel\Exception\TttException;
                }
                
                foreach($input['item'] as $itemId){
                        if(! method_exists($this->fichero, $input['accion']))
				{
					throw new \Ttt\Exception\TttException;
				}
				call_user_func_array(array($this->fichero, $input['accion']), array($itemId, \Sentry::getUser()['id']));
                }
            }
            catch(\Ttt\Panel\Exception\TttException $e)
            {
                    $mensaje = 'La acción indicada no existe';
            }
            catch(\Ttt\Panel\Exception\BatchActionException $e)
            {
                    $mensaje = $e->getMessage();
            }
            
            return \Redirect::action('Ttt\Panel\FicherosController@index');

        }
        
    	protected function getParams()
	{
                $input = array_merge(Input::only($this->allowed_url_params));

		$input[Config::get('panel::app.orderBy')]  = !is_null($input[Config::get('panel::app.orderBy')]) ? $input[Config::get('panel::app.orderBy')] : 'nombre';
		$input[Config::get('panel::app.orderDir')] = !is_null($input[Config::get('panel::app.orderDir')]) ? $input[Config::get('panel::app.orderDir')] : 'asc';

		return $input;
	}
}