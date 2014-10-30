<?php

namespace Ttt\Panel;

use \Config;
use \Input;
use \Paginator;
use \View;

use Ttt\Panel\Repo\Paginas\PaginasInterface;
use Ttt\Panel\Service\Form\Paginas\PaginasForm;
use Ttt\Panel\Repo\Paginas\EloquentPaginasFicheros;

use Ttt\Panel\Repo\Fichero\FicheroInterface;
use Ttt\Panel\Service\Form\Fichero\FicheroForm;

use Ttt\Panel\Repo\Paginas\Pagina;
use Ttt\Panel\Repo\Paginas\PaginasI18n;

use Ttt\Panel\Repo\Fichero\Extensions\FicheroPivotInterface;

use Ttt\Panel\Core\AbstractCrudController;

use Ttt\Panel\Repo\Fichero\Extensions\FicheroControllerInterface;

class PaginasController extends AbstractCrudController implements FicheroControllerInterface
{

        use \Ttt\Panel\Repo\Fichero\Extensions\FicheroTrait;
    
	protected $_views_dir = 'paginas';
	protected $_titulo = 'Páginas';

	public static $moduleSlug = 'paginas';    

        public static $tablaFicheros = 'paginas_ficheros';
        
        protected $pagina;
        protected $paginaForm;
        protected $fichero;
        protected $ficheroForm;
        protected $ficheroPivot;
        
        


        protected $allowed_url_params = array(
		'titulo', 'ordenPor', 'ordenDir', 'creado_por'
	);

	protected $acciones_por_lote = array(
		'delete'    => 'Borrar'
	);
        
        protected $acciones_por_lote_ficheros = array(
                'desasociarFichero' => 'Desasociar'
        );
        
        protected $_fichero_nombre = '';
        protected $_fichero_original;


        protected $_idioma_predeterminado;
        protected $_todos_idiomas;
        
        public function __construct( PaginasInterface $pagina, 
                                     PaginasForm $paginaForm,
                                     FicheroInterface $fichero,
                                     FicheroForm $ficherosForm
                               
                                    ) 
        {
        
            parent::__construct();

            $this->pagina       = $pagina;
            $this->paginaForm   = $paginaForm;
            $this->fichero      = $fichero;
            $this->ficheroForm  = $ficherosForm;
            
            $this->ficheroPivot = new Ttt\Panel\Repo\Paginas\PaginasFicheros;

            $this->_idioma_predeterminado = Ttt\Panel\Repo\Idioma\Idioma::where('principal','=',1)->get();
            $this->_todos_idiomas         = Ttt\Panel\Repo\Idioma\Idioma::all();

            View::share('idioma_predeterminado', $this->_idioma_predeterminado->first());
            View::share('todos_idiomas', $this->_todos_idiomas);      
            
            //Cargamos el config 
             $this->_config_ficheros = Config::get('panel::ficheros');    
             View::share('config_ficheros', $this->_config_ficheros);

             //Cargamos todos los ficheros
             View::share('ficheros_todos', Ttt\Panel\Repo\Fichero\Fichero::all());
             
             //Por defecto la accion para el fichero es "create"
             View::share('action_fichero', 'create');

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
                
                $item_nuevatraduccion =  new \stdClass();
                $item_nuevatraduccion->titulo = \Input::old('titulo') ?: '';
                $item_nuevatraduccion->texto = \Input::old('texto') ?: '';
                
                View::share('title', 'Editar elemento');
                
                //Adjuntos
                View::share('ficheros', $item->ficheros());
                View::share('pagina', $item);
                
                $params = $this->getParams();
                
                return View::make('panel::paginas.form')
                                    //->with('params', $params)
                                    ->with('item',$item)
                                    ->with('item_nuevatraduccion', $item_nuevatraduccion)
                                    ->with('item_id', $item->id)
                                    ->with('acciones_por_lote_ficheros', $this->acciones_por_lote_ficheros)
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
                    'titulo' => Input::get('titulo')?: \Input::old('titulo'),
                    'texto' => Input::get('texto')?: \Input::old('texto'),
                    'idioma' => Input::get('idioma'),
                    'slug'   => Input::get('slug'),
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
                $message = 'No se han podido guardar los cambios. Por favor revise los campos marcados.';                
                // El idioma al crear nuevo siempre es el predeterminado
                \Session::flash('idioma_error', $this->_idioma_predeterminado->first()->codigo_iso_2);
            }
            
            \Session::flash('messages', array(
                array(
                    'class' => 'alert-danger',
                    'msg'   => $message
                )
            ));
            
            $errores = $this->paginaForm->errors();
            
            return \Redirect::action('Ttt\Panel\PaginasController@nuevo')
                                        ->withInput()
                                        ->withErrors($this->paginaForm->errors());
        }
        
        /**
                * Actualizar un Item
                * 
                */
        
        public function actualizar()
        {
            $message = 'Página guardada correctamente';
            $nueva_traduccion = FALSE;
            
            try{
                
                //Cogemos la tabla master
                $pagina = $this->pagina->byId(Input::get('item_id'));
                
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
                        'slug'      => Input::get('slug'),
                        'usuario'   => \Sentry::getUser()['id']
                );
                $nueva_traduccion = $pagina_i18n ? FALSE : TRUE;
                $pagina_i18n = $pagina_i18n?: new Ttt\Panel\Repo\Paginas\PaginaI18n;
                
                $posibleSlug = (Input::get('slug')!='') ? Input::get('slug') : Input::get('titulo');
                
                $checkId = (($nueva_traduccion)? FALSE : $pagina_i18n->id);
                
                //Campos traducibles
                $pagina_i18n->texto     = Input::get('texto');
                $pagina_i18n->titulo    = Input::get('titulo');
                $pagina_i18n->idioma    = Input::get('idioma');
                $pagina_i18n->slug      = $this->pagina->slug($posibleSlug, $checkId, Input::get('idioma'));
                $pagina_i18n->item_id   = Input::get('item_id');
                
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
                    
                    
                    
                    return \Redirect::to('admin/paginas/ver/' . $pagina->id . '#datos-'. $pagina_i18n->idioma);
                    
                }
                
            } catch (\Ttt\Panel\Exception\TttException $ex) {
                    $message = 'No se han podido guardar los cambios. Por favor revise los campos marcados.' ;
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
            
            return \Redirect::to('admin/paginas/ver/' . Input::get('item_id') . '#datos-' . (($nueva_traduccion) ? 'nuevatraduccion' : $idioma_redireccion))
                                                        ->withInput()
                                                        ->withErrors($this->paginaForm->errors());
        }
        
        
        public function asociarFichero($id = null)
        {
            //-- Recuperamos el fichero
            if(     $fichero = Repo\Fichero\Fichero::find($id) 
                    && $pagina = $this->pagina->byId(Input::get('from')) ){

                //-- Creamos la relacion
                $pagina->ficheros()->attach($id);
                
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
                return \Redirect::to('admin/paginas/ver/' . Input::get('from').'#ficheros');
        }
        
        
        /**
         * 
         * Elimina la asociación entre fichero y pagina
         * 
         * @param type $id
         * @return type
         */
        
        public function desasociarFichero($id = null)
        {
            //-- Recuperamos el fichero
            if(     $fichero = Repo\Fichero\Fichero::find($id) 
                    && $pagina = $this->pagina->byId(Input::get('from')) ){
                
                $pagina->ficheros()->detach($id);
                \Session::flash('messages', array(
                            array(
                                'class' => 'alert-success',
                                'msg'   => 'Fichero desasociado correctamente'
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
            
            return \Redirect::to('admin/paginas/ver/' . $pagina->id . '#ficheros');
        }

        /**
	* Intenta actualizar un elemento existente
	* @return void
	*/
        
	public function borrar($id = null)
	{
		$message = 'Página eliminada correctamente.';
		try
		{

                        $r = $this->pagina->delete($id);
			//$ent = $this->modulo->byId(Input::get('id'));
			if(! $r )
			{
				throw new \Ttt\Panel\Exception\TttException;
			}

			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => $message
				)
			));


			return \Redirect::action('Ttt\Panel\PaginasController@index');

		}
		catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e)
		{
			$message = $e->getMessage();
			return \Redirect::action('Ttt\Panel\PaginasController@index');
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

		return \Redirect::action('Ttt\Panel\PaginasController@index');
	}

        /**
         * Borrado de una traduccion asociada
         *
         * @return type
         */

        public function borrarTraduccion($id = null)
        {
            $message = 'Traduccion eliminada correctamente';

            if($id)
            {
                $pagina_i18n = Ttt\Panel\Repo\Paginas\PaginaI18n::find($id);
                
                
                if($pagina_i18n->delete() && $item_id = $pagina_i18n->item_id)
                {
                    \Session::flash('messages', array(
                            array(
                                'class' =>'alert-success',
                                'msg'   => $message
                            )
                    ));
                    return \Redirect::action('Ttt\Panel\PaginasController@ver', $item_id);
                }
            }

            return \Redirect::action('Ttt\Panel\PaginasController@index');
        }        

        /**
	* Ejecuta una acción sobre un conjunto de elementos
	* @throws \Ttt\Exception\BatchActionException
	* @return void
	*/
	public function accionesPorLote()
	{
		$input = Input::only('item', 'accion', 'from');

		try{
                    
			if(    !array_key_exists($input['accion'], $this->acciones_por_lote) 
                            && !array_key_exists($input['accion'], $this->acciones_por_lote_ficheros) ) 
			{
				throw new \Ttt\Panel\Exception\TttException;
			}

			foreach($input['item'] as $itemId)
			{
				if(! method_exists($this->pagina, $input['accion']))
				{
					throw new \Ttt\Exception\TttException;
				} 

                                    
				call_user_func_array(array($this->pagina, $input['accion']), array($itemId, \Sentry::getUser()['id'], Input::get('from')));
			}

                        if(array_key_exists($input['accion'], $this->acciones_por_lote)) { $accion = $this->acciones_por_lote[$input['accion']];  } 
                        if(array_key_exists($input['accion'], $this->acciones_por_lote_ficheros)) { $accion = $this->acciones_por_lote_ficheros[$input['accion']]; }
                        
			\Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => 'La acción ' . $accion . ' se ha ejecutado correctamente.'
				)));
                        
                        
                        //Si se recibe un campo from de un item, redirigimos a la página, no al index
                        $from = \Input::get('from');
                        return (\Input::get('from')) ? \Redirect::action('Ttt\Panel\PaginasController@ver',$from) :  \Redirect::action('Ttt\Panel\PaginasController@index');

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
            
            $input[Config::get('panel::app.orderBy')] = !is_null($input[Config::get('panel::app.orderBy')]) ? $input[Config::get('panel::app.orderBy')] : 'id';
            $input[Config::get('panel::app.orderDir')] = !is_null($input[Config::get('panel::app.orderDir')]) ? $input[Config::get('panel::app.orderDir')] : 'asc';

            return $input;
        }

        
        public function guardarCamposEspecificos($id = null, $fichero_id = null) {
            
                    $datosEspecificos = array(
                        'titulo'      => \Input::get('titulo_defecto'),
                        'alt'         => \Input::get('alt_defecto'),
                        'enlace'      => \Input::get('enlace_defecto'),
                        'descripcion' => \Input::get('descripcion_defecto'),
                        'idioma'      => \Input::get('idioma')
                    );
                    
                        //-- Obtenemos el elemento de la tabla Pivote
                        $pivot = \Ttt\Panel\Repo\Paginas\PaginasFicheros::find($id);
                        
                        //-- Si hay elemento Pivot, es por que la relación ya existe
                        if($pivot){
                        
                                //-- Obtenemos la página
                                $pagina = $pivot->pagina()->first();

                                //-- Solo cuando actualizamos guardamos los campos directamente 
                                if( $id ){
                                        if($this->validarCamposEspecificos()->passes())
                                        {
                                            $pivot_id = $id;

                                            $ficherosPivot = $pagina
                                                                ->ficheros()
                                                                ->where(self::$moduleSlug . '_ficheros.id', $pivot_id)
                                                                ->get();

                                            //Si cambiamos la relación, creamos una nueva
                                            
                                            if($pivot->fichero_id != \Input::get('fichero_id')){
                                                //Borramos la relacion y creamos uno nuevo
                                                $ficherosPivot->first()->pivot->delete();
                                                $r = $pagina->ficheros()->attach(\Input::get('fichero_id'), $datosEspecificos);
                                                return \Input::get('fichero_id');
                                            }else{
                                                
                                                $ficherosPivot->first()->pivot->titulo      = $datosEspecificos['titulo'];
                                                $ficherosPivot->first()->pivot->alt         = $datosEspecificos['alt'];
                                                $ficherosPivot->first()->pivot->enlace      = $datosEspecificos['enlace'];
                                                $ficherosPivot->first()->pivot->descripcion = $datosEspecificos['descripcion'];
                                                $ficherosPivot->first()->pivot->idioma      = $datosEspecificos['idioma'];
                                                
                                                $ficherosPivot->first()->pivot->save();
                                            return TRUE;
                                            }
                                        }else{
                                            throw new \Ttt\Panel\Exception\TttException('Errores de validacion');
                                        }
                                }

                            //-- Recuperamos el fichero y validamos campos especificos
                            if( $fichero = $this->fichero->byId($pivot->fichero->first()->id)
                                && $this->validarCamposEspecificos()->passes()
                                ){
                                    $pagina->ficheros()->attach($id, $datosEspecificos);
                                    return TRUE;
                            }else{
                                return FALSE; // Igual hay que mandar una excepcion
                            }
                    }else{ //-- Si no la hay es un elemento nuevo
                        
                        $pagina_id = \Input::get('from_id');
                        $datosEspecificos = $this->obtenerCamposEspecificos(NULL, $pagina_id, NULL, TRUE);
                        
                        //¿Validacion? 
                        
                        unset($datosEspecificos['nombre']);
                        
                        $pagina = $this->pagina->byId($pagina_id)
                                       ->ficheros()
                                       ->attach($fichero_id, $datosEspecificos);
                                        
                        return TRUE;
                    }
        }

        public function validarCamposEspecificos() {

            $validator = \Validator::make(
                            array( //La validacion siempre se hará sobre un get
                                'nombre' => $this->_fichero_nombre,
                                'titulo' => \Input::get('titulo_defecto'),
                                'alt'    => \Input::get('alt_defecto'),
                                'enlace' => \Input::get('enlace_defecto'),
                                'descripcion' => \Input::get('descripcion_defecto')),
                            array(
                                'nombre' => 'required|max:255',
                                'titulo' => 'max:255',
                                'alt'    => 'max:255',
                                'enlace' => 'max:255',
                                'descripcion' => 'max:255'),
                            array(
                                'required'          => 'El campo :attribute es obligatorio',
                                'max'               => 'El :attribute no puede ser mayor de :max caracteres.',
                                'mimes'             => 'Tipo no permitido, compruebe la extensión del fichero'
                            )
                );
            
            return $validator;
        }

    public function obtenerCamposEspecificos( $ficheroId = null, $itemId = null, $pivot_id = null, $enviarAVista = FALSE ) {
        
        try{
                $pagina        = $this->pagina->byId($itemId);
                $ficheros      = $pagina->ficheros()->getResults();
                $ficherosPivot = Pagina::find($itemId)
                                            ->ficheros()
                                            ->where(self::$moduleSlug . '_ficheros.id', $pivot_id)
                                            ->get();
            
            if( $ficherosPivot->count() > 0 )
            {
                $camposEspecificos = $ficherosPivot->first()->pivot->toArray();
   
            }else{ //Si no tiene tabla pivote es que hemos cambiado el fichero
                $camposEspecificos = array( 'nombre'        => \Input::old('nombre')?:\Input::get('nombre'),
                                            'idioma'        => \Input::old('idioma')?:\Input::get('idioma'),
                                            'titulo'        => \Input::old('titulo')?:\Input::get('titulo'),
                                            'alt'           => \Input::old('alt')?:\Input::get('alt'),
                                            'enlace'        => \Input::old('enlace')?:\Input::get('enlace'),
                                            'descripcion'   => \Input::old('descripcion')?:\Input::get('descripcion'));
                
            }
        }  catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e){
                //-- Si no encuentra los modelos, repopulamos los datos de la vista
                $camposEspecificos = array( 'nombre'        => (\Input::old('nombre'))?:\Input::get('nombre'),
                                            'idioma'        => (\Input::old('idioma'))?:\Input::get('idioma'),
                                            'titulo'        => (\Input::old('titulo'))?:\Input::get('titulo'),
                                            'alt'           => (\Input::old('alt'))?:\Input::get('alt'),
                                            'enlace'        => (\Input::old('enlace'))?:\Input::get('enlace'),
                                            'descripcion'   => (\Input::old('descripcion')))?:\Input::get('descripcion');
                return $camposEspecificos;
        }
        
        if($enviarAVista){
               
            $this->mandarALaVista($camposEspecificos);
        }
        return $camposEspecificos;
        
    }
    
    function mandarALaVista($data = null)
    {
        if( !$data ){
            $data = array(  'titulo'        => \Input::old('titulo'),
                            'alt'           => \Input::old('alt'),
                            'enlace'        => \Input::old('enlace'),
                            'descripcion'   => \Input::old('descripcion'),
                            'idioma'        => \Input::old('idioma')
                            );
        }
        
                \View::share('titulo',      $data['titulo']);
                \View::share('alt',         $data['alt']);
                \View::share('enlace',      $data['enlace']);
                \View::share('descripcion', $data['descripcion']);
                \View::share('idioma',      $data['idioma']);
                
                return $data;
    }
    

            protected function _setDefaultAssets() 
        {
            parent::_setDefaultAssets();

            $assets = \View::shared('assets');
            $assets['js'][] = asset('packages/ttt/panel/components/jquery.datatables/jquery.datatables.min.js');
            $assets['js'][] = asset('packages/ttt/panel/components/jquery.datatables/bootstrap-adapter/js/datatables.js');
            $assets['css'][] = asset('packages/ttt/panel/components/jquery.datatables/bootstrap-adapter/css/datatables.css');
            
            \View::share('assets', $assets);
            
        }
}