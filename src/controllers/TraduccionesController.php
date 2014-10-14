<?php

namespace Ttt\Panel;

use \Config;
use \Input;
use \Paginator;
use \View;

use Ttt\Panel\Repo\Traducciones\TraduccionesInterface;
use Ttt\Panel\Service\Form\Traducciones\TraduccionesForm;

use Ttt\Panel\Repo\Traducciones\Traduccion;
use Ttt\Panel\Repo\Traducciones\TraduccionI18n;

use Ttt\Panel\Core\AbstractCrudController;

class TraduccionesController extends AbstractCrudController
{

	protected $_views_dir = 'traducciones';
	protected $_titulo = 'Traducciones';

	public static $moduleSlug = 'traducciones';

        //Para desarrollo SOLO, hasta que se implementa
        //el módulo idiomas

        public $_idioma_predeterminado = 'es';
        public $_todos_idiomas = array(
                array(  'nombre'=>'español',
                        'codigo_iso'=>'es'),
                array(  'nombre'=>'inglés',
                        'codigo_iso'=>'en'),
                array(  'nombre'=>'frances',
                        'codigo_iso'=>'fr'));

        // -------------

	protected $traduccion;

	protected $traduccionForm;

	protected $allowed_url_params = array(
		'clave', 'texto', 'ordenPor', 'ordenDir', 'creado_por'
	);

	protected $acciones_por_lote = array(
		'delete'    => 'Borrar'
	);

	public function __construct(TraduccionesInterface $traduccion,
                                    TraduccionesForm $traduccionForm)
	{
                parent::__construct();

		$this->traduccion         = $traduccion;
		$this->traduccionForm     = $traduccionForm;

                $this->_idioma_predeterminado = Repo\Idioma\Idioma::where('principal','=',1)->get();
                $this->_todos_idiomas         = Repo\Idioma\Idioma::all();

                View::share('idioma_predeterminado', $this->_idioma_predeterminado->first());
                View::share('todos_idiomas', $this->_todos_idiomas);
                
                
                //Todo: Chequear permisos para unsetear acciones por lote
	}

        public function index()
        {
            View::share('title','Listado de ' . $this->_titulo);

            //Recogemos la página
            $pagina     = Input::get('pagina', 1);
            $perPage    = Config::get('panel::app.perPage', 1);

            $params = $this->getParams();

            //Recogemos la paginación
            $pageData = $this->traduccion->byPage($pagina, $perPage, $params);

            $traducciones = Paginator::make(
                    $pageData->items,
                    $pageData->totalItems,
                    $perPage);
            $traducciones->appends($params);

            View::share('items', $traducciones);

            return View::make('panel::traducciones.index')
                                ->with('params', $params)
                                ->with('currentUrl', \URL::current())
                                ->with('accionesPorLote', $this->acciones_por_lote);
        }


        /**
         * Formulario de creacion de traducción
         * @return Void
         */
        public function nuevo()
        {
            $item = new \stdClass();
            $item->clave = Input::old('clave') ? Input::old('clave') : '';
            $item->texto = Input::old('texto') ? Input::old('texto') : '';

            View::share('title', 'Creacion de una nueva traducción');
            return View::make('panel::traducciones.form')
                                            ->with('item', $item)
                                            ->with('action', 'create');
        }

        /**
         * Formulario de edición
         *
         * @param int $id
         */

        public function ver($id = null)
        {
            if($id)
            {
                $item = Traduccion::find($id);
                
                $item_nuevatraduccion = new \stdClass();
                
                $item_nuevatraduccion->texto = \Input::old('texto') ?: '';
                $item_nuevatraduccion->clave = \Input::old('clave') ?: $item->clave;

                View::share('title', 'Editar elemento');
                return View::make('panel::traducciones.form')
                                    ->with('item', $item)
                                    ->with('item_nuevatraduccion', $item_nuevatraduccion)
                                    ->with('action', 'edit');

            }
        }


        /**
         * Crea un Item
         *
         * @return void
         */

        public function crear()
        {
            $message = 'Traducción creada correctamente.';

            try{
                $data = array(
                    'clave' => Input::get('clave'),
                    'texto' => Input::get('texto'),
                    'idioma' => Input::get('idioma'),
                    'creado_por' => \Sentry::getUser()['id'],
                    'actualizado_por' => \Sentry::getUser()['id']
                );

                $traduccionId = $this->traduccionForm->save($data);

                //die(var_dump($traduccionId));

                \Session::flash('messages', array(
                    array(
                            'class' => 'alert-success',
                            'msg'   => $message
                    )
                ));
                return \Redirect::action('Ttt\Panel\TraduccionesController@ver', $traduccionId);
                
            } catch (\Ttt\Panel\Exception\TttException $ex) {
                $message = 'No se han podido guardar los cambios. Por favor revise los campos marcados.';
            }

            \Session::flash('messages', array(
			array(
				'class' => 'alert-danger',
				'msg'   => $message
			)
		));

            return \Redirect::action('Ttt\Panel\TraduccionesController@nuevo')
                                    ->withInput()
                                    ->withErrors($this->traduccionForm->errors());

        }


        /**
        * Actualizar un Item
        *
        * @return void
        */

        public function actualizar()
        {
        $message = 'Traducción guardada correctamente';
        $nueva_traduccion = FALSE; 
        
            try{

                //Cogemos tabla master traduccón
                $this->traduccion = $traduccion = Traduccion::find(Input::get('item_id'));
                
                $this->traduccion->clave = Input::get('clave');

                // Cogemos la traducción
                $traduccion_i18n = Traduccion::find(Input::get('item_id'))
                                                        ->traducciones()
                                                        ->where('idioma','=', Input::get('idioma'))
                                                        ->first();

                $nueva_traduccion = $traduccion_i18n ? FALSE : TRUE;
                $traduccion_i18n = $traduccion_i18n ?: new TraduccionI18n;
                //Cargamos campos traducibles
                $traduccion_i18n->texto     = Input::get('texto');
                $traduccion_i18n->item_id   = Input::get('item_id');
                $traduccion_i18n->idioma    = Input::get('idioma');

                $data = array(
                        'id'    => Input::get('item_id'),
                        'clave' => Input::get('clave'),
                        'texto' => Input::get('texto'),
                        'idioma' => Input::get('idioma'),
                        'usuario' => \Sentry::getUser()['id']
                );

                if($this->traduccionForm->update($data)
                    && $traduccion_i18n->save())
                    {
                        //Traducciones guardadas correctamente
                        \Session::flash('messages', array(
                                        array(
                                                'class' => 'alert-success',
                                                'msg'   => $message
                                        )
                                    ));

                        //Guardamos los ficheros
                        Traduccion::guardarFicheros();
                        
                        return \Redirect::to('admin/traducciones/ver/' . $traduccion->id . '#datos-' . Input::get('idioma'));
                    }

                //die(var_dump($traduccion_i18n));

            } catch (\Ttt\Panel\Exception\TttException $ex) {
                $message = 'No se han podido guardar los cambios. Por favor revise los campos marcados.';
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
            $errores[Input::get('idioma')] = $this->traduccionForm->errors();

            \Session::flash('idioma_error', Input::get('idioma'));
            $idioma_redireccion = empty(Input::get('idioma')) ? 'nuevatraduccion' : Input::get('idioma');
            return \Redirect::to('admin/traducciones/ver/' . Input::get('item_id') . '#datos-' . (($nueva_traduccion) ? 'nuevatraduccion' : $idioma_redireccion))
                                            ->withInput()
                                            ->withErrors($this->traduccionForm->errors());

            }



        /**
         * Borrado de un Item traduccion completo (tambien sus traducciones asociadas)
         *
         * @return type
         */

        public function borrar($id = null)
        {


            $traduccion = Traduccion::find($id);
            $message = 'Traduccion eliminada correctamente';

            if($traduccion)
            {
                $traduccion->delete();

                \Session::flash('messages', array(
				array(
					'class' => 'alert-success',
					'msg'   => $message
				)
                            ));

                return \Redirect::action('Ttt\Panel\TraduccionesController@index');
            }
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
                $traduccion_i18n = TraduccionI18n::find($id);

                //$tr = $traduccion_i18n->traduccion(); 
                
                if($traduccion_i18n->delete() && $item_id = $traduccion_i18n->item_id)
                {
                    \Session::flash('messages', array(
                            array(
                                'class' =>'alert-success',
                                'msg'   => $message
                            )
                    ));
                    return \Redirect::action('Ttt\Panel\TraduccionesController@ver', $item_id);
                }

            }

            return \Redirect::action('Ttt\Panel\TraduccionesController@index');

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
                if(!array_key_exists($input['accion'], $this->acciones_por_lote)){

                    throw new \Ttt\Panel\Exception\TttException;
                }

                foreach($input['item'] as $itemId){
                    if(!method_exists($this->traduccion, $input['accion'])){
                        throw new \Ttt\Exception\TttException;
                    }
                        call_user_func(array($this->traduccion, $input['accion']), array($itemId, \Sentry::getUser()['id']));
                }

                \Session::flash('messages', array(
                    array(
                            'class'=> 'alert-success',
                            'msg'  => 'La accion ' . $this->acciones_por_lote[$input['accion']] . ' se ha ejecutado correctamente'
                    )
                ));

                return \Redirect::action('Ttt\Panel\TraduccionesController@index');

            } catch (\Ttt\Panel\Exception\TttException $e) {
                    $mensaje = 'La acción indicada no existe' ;
            } catch (\Ttt\Panel\Exception\BatchActionException $e){
                    $mensaje = $e->getMessage();
            }

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
