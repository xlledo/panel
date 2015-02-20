<?php

namespace Ttt\Panel\Repo\Fichero\Extensions;

trait FicheroTrait {

    function crearFichero()
    {
        $message = 'Fichero creado correctamente';

        try{
            $fichero = \Input::file('fichero');
            $nombre_fichero='';

            if(\Input::hasFile('fichero')){

                $fichero            = \Input::file('fichero');

                $nombre_fichero_canonical   = \Illuminate\Support\Str::slug( substr($fichero->getClientOriginalName(), 0, strlen($fichero->getClientOriginalName())- (strlen($fichero->getClientOriginalExtension()))));
                $nombre_fichero             = $nombre_fichero_canonical . '.' . $fichero->getClientOriginalExtension();

                $path_completo  = $this->_upload_folder . date("Y") . '/' . date("m") . '/';
                $mime           = $fichero->getMimeType();

                //-- Generamos el nombre del fichero

                $i=1;
                while(file_exists($path_completo . $nombre_fichero)){
                    $nombre_fichero = \Illuminate\Support\Str::slug($fichero->getClientOriginalName(),'-') . '_'.$i . '.' . $fichero->getClientOriginalExtension();
                    $i++;
                }
                //-- Guardamos el fichero en la ruta
                $fichero->move($path_completo , $nombre_fichero);
            }

            if($nombre_fichero == ''){
                throw new \Ttt\Panel\Exception\TttException('Error fichero no seleccionado');
            }

            $this->_fichero_nombre = $nombre_fichero;

            $data = array(
                    'nombre'  => (\Input::get('nombre')) ?: $nombre_fichero_canonical,
                    'fichero' => $nombre_fichero,
                    'usuario' => \Sentry::getUser()['id'],
                    'ruta'    => $path_completo,
                    'mime'    => $mime,
                    'peso'    => '',
                    'categoria_id' => \Input::get('categoria_id'),
                    'titulo_defecto'        => \Input::get('titulo_defecto'),
                    'alt_defecto'           => \Input::get('alt_defecto'),
                    'enlace_defecto'        => \Input::get('enlace_defecto'),
                    'descripcion_defecto'   => \Input::get('descripcion_defecto'),
                    'fichero_original'      => $fichero //Pasamos el fichero para propositos de validacion
                );

                try {  //Si falla al obtener el peso, lo ponemos a 0
                    $data['peso'] = $fichero->getSize();
                } catch (\RuntimeException $ex) {
                    $data['peso'] = '';
                }

            // Hay que validar los campos concretos de la relación
            // En esta validación tambien entran los campos comunes de los ficheros

            if(!$this->validarCamposEspecificos()->passes()){
                throw new \Ttt\Panel\Exception\TttException('Errores de validación');
            }

            $ficheroId = $this->ficheroForm->save($data);

            \Session::flash('messages', array(
                array(
                    'class' => 'alert-success',
                    'msg'   => $message
                )
            ));

            //-- Si el fichero se crea desde otro módulo
            //-- lo redirigimos de nuevo allí

            if(\Input::get('asociar') == 1){
                    $this->guardarCamposEspecificos(NULL, $ficheroId);
            }

            if(!\Input::get('origin',FALSE))
            {
                $url = action(get_called_class() . '@ver', \Pila::getUltimaReferencia()['retrievingValue']) . '?direction=backward';
                return \Redirect::to($url);
            }else{
                return \Redirect::to(\Input::get('origin'));
            }

        } catch (\Ttt\Panel\Exception\TttException $ex) {

            $message = $ex->getMessage();
        }

        \Session::flash('messages', array(
            array(
                'class' => 'alert-danger',
                'msg'   => $message
            )
        ));

        $url = action(get_called_class() . '@nuevoFichero') . '?direction=keep';

        return \Redirect::to($url)
                                    ->withInput()
                                    ->withErrors($this->ficheroForm->errors());

    }

    function verFichero($id = null)
    {
        $message = '';

        $ultimaReferencia = \Pila::getUltimaReferencia();

        // Sacamos el namespace  para obtener el nombre del
        // workbench
            $namespace = explode('\\', get_called_class());

            if( $namespace[0] == 'Ttt')
            {
                $workbench = $namespace[1] . '::';
            } else {
                $workbench = 'panel::';
            }
        
        
        //-- Cogemos el elemento de la tabla pivotee

        $pivot = $this->ficheroPivot->find($id);

            if( $fichero = $this->fichero->byId($pivot->fichero()->first()->id) ){

            //$fichero  = $this->fichero->byId($id);

            $fichero->nombre = ! is_null(\Input::old('nombre')) ?: $fichero->nombre;

            $fichero->titulo_defecto        = ! is_null(\Input::old('titulo_defecto')) ?: $fichero->titulo_defecto;
            $fichero->alt_defecto           = ! is_null(\Input::old('alt_defecto')) ?: $fichero->alt_defecto;
            $fichero->descripcion_defecto   = ! is_null(\Input::old('descripcion_defecto')) ?: $fichero->descripcion_defecto;
            $fichero->enlace_defecto        = ! is_null(\Input::old('enlace_defecto')) ?: $fichero->enlace_defecto;
            
            
            $item_id    = $ultimaReferencia['retrievingValue'];
            $pivot_id   = $id;

            $this->_fichero_nombre = $fichero->nombre;

            try{
                $this->obtenerCamposEspecificos($id, $item_id, $pivot_id, TRUE);

            }catch(\Ttt\Panel\Exception\TttException $e){
                $message = 'No se han podido guardar los cambios. Por favor revise los campos marcados.';
            }

            \View::share('title', 'Edicion del fichero ' . $fichero->nombre);
            \View::share('action_fichero', 'edit');

            \View::share('from_url', \Input::get('from_url')?:'');
            \View::share('item_id', ($ultimaReferencia['retrievingValue'])?:'');
            \View::share('pivot', $pivot);
            \View::share('pivot_id', $id);

            \View::share('item', $fichero);
            
            if(\Input::get('categoria'))
            {
                //$ruta = lcfirst($workbench) . $this->_views_dir .  '.' . \Input::get('categoria') . '._editar';
                return \View::make(lcfirst($workbench) . $this->_views_dir .  '.' . \Input::get('categoria') . '._editar');
            }else{
                return \View::make(lcfirst($workbench) . $this->_views_dir . '.ficheros._editar');
            }

        }
    }

    function actualizarFichero()
    {
        $message = 'Fichero actualizado correctamente';
        $actualizacionOK = FALSE;

        $id = \Input::get('id');
        $pivot_id = \Input::get('pivot_id');
        
        

        try{

            //$pivot = \Ttt\Panel\Repo\Paginas\PaginasFicheros::find($pivot_id);
            //$fichero = $this->fichero->byId(\Input::get('id'));
            
            $pivot = $this->ficheroPivot->find($pivot_id);
            $fichero = \Ttt\Panel\Repo\Fichero\Fichero::find($pivot->fichero()->first()->id);
            
            //$fichero = $this->fichero->byId($pivot->fichero()->first()->id);

            $this->_fichero_nombre = $fichero->nombre;



            $data = array(
                'id'        => $fichero->id,
                'nombre'    => (\Input::get('nombre')) ?: $this->_fichero_nombre,
                'titulo_defecto' => \Input::get('titulo_defecto'),
                'alt_defecto' => \Input::get('alt_defecto'),
                'enlace_defecto' => \Input::get('enlace_defecto'),
                'descripcion_defecto' => \Input::get('descripcion_defecto'),
                'categoria_id' => \Input::get('categoria_id')
            );


            if(\Input::hasFile('fichero')){

                $fic = \Input::file('fichero');
                $fic->move($fichero->ruta, $fichero->fichero);

                    try {  //Si falla al obtener el peso, lo ponemos a 0
                        $data['peso'] = $fic->getSize();
                    } catch (\RuntimeException $ex) {
                        $data['peso'] = '';
                    }
            }
            
            //Atualizamos el fichero
            $ficheroId = $this->ficheroForm->update($data);

            $item_id    = \Input::get('item_id');
            
            $result = $this->guardarCamposEspecificos($pivot_id);
            

            \View::share('item_id', $item_id);
            \View::share('pivot_id', $pivot_id);

            \Session::flash('messages', array(
                                array(
                                    'class' => 'alert-success',
                                    'msg'   => $message
                                )
            ));

            //Si se cambia el ID del fichero
            //Se genera un nuevo ID para la tabla Pivot
            $id_resultado = (is_numeric($result))? $pivot_id + 1 : $pivot_id;

            return $this->verFichero($id_resultado);

         }
            catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex){
                    $message = $ex->getMessage();
                    return \Redirect::action( get_called_class() . '@verFichero', $pivot_id);
                    }
            catch(\Ttt\Panel\Exception\TttException $e){

                //llega aquí
                    $message = 'No se han podido guardar los cambios. Por favor revise los campos marcados.';

                    $this->mandarALaVista();

                    \Session::flash('messages', array(
                                        array(
                                            'class' => 'alert-danger',
                                            'msg'   => $message
                                        )
                    ));

                    return $this->verFichero($pivot->id)
                                 ->withErrors($this->validarCamposEspecificos());

                }

                \Session::flash('messages', array(
                                    array(
                                        'class' => 'alert-danger',
                                        'msg'   => $message
                                    )
                ));

                $url = action(get_called_class() . '@verFichero', $fichero->id) . '?direction=keep';

                return \Redirect::to($url)
                                        ->withInput()
                                        ->withErrors($this->ficheroForm->errors());
    }


        public function nuevoFichero()
        {

            // Sacamos el namespace  para obtener el nombre del
            // workbench
            $namespace = explode('\\', get_called_class());

            if( $namespace[0] == 'Ttt')
            {
                $workbench = $namespace[1] . '::';
            } else {
                $workbench = 'panel::';
            }

            $item = new \stdClass();
            $item->nombre = \Input::old('nombre') ?: '';

            $item->titulo_defecto   = \Input::old('titulo_defecto')?:'';
            $item->alt_defecto      = \Input::old('alt_defecto')?:'';
            $item->descripcion_defecto = \Input::old('descripcion_defecto')?:'';
            $item->enlace_defecto      = \Input::old('enlace_defecto')?:'';

            $item_id = \Input::old('from_id');

            $this->_fichero_nombre = $item->nombre;

            \View::share('title', 'Creacion de un nuevo fichero');

            return \View::make(lcfirst($workbench) . $this->_views_dir .  '.ficheros._add')
                                    ->with('item', $item)
                                    ->with('item_id', $item_id)
                                    ->with('from_id', $item_id)
                                    ->withErrors($this->validarCamposEspecificos())
                                    ->with('from_url', (\Input::get('from_url')?: ''))
                                    ->with('action', 'create' );
        }

   public function getFicherosAjaxForDataTables()
        {
       
            $solo_imagenes = \Input::get('solo_imagenes', FALSE);
        
            //$ficherosTodos = \Ttt\Panel\Repo\Fichero\Fichero::all();
            
            $page   = \Input::get('draw',1);
            $start  = \Input::get('start',0);   
            $length = \Input::get('length',10); //items en cada página
            $originUrl = \Input::get('originUrl');
            $seachArray = (array)\Input::get('search');
            
            $queryFicheros = \Ttt\Panel\Repo\Fichero\Fichero::query();
            
            if( $solo_imagenes )
            {
                $queryFicheros->where('mime','LIKE','%image%');
            }
            
            if( $seachArray['value']!='' )
            {
                $queryFicheros->where('nombre','LIKE', '%' . $seachArray['value'] . '%');
            }
            
            $ficherosTodos = $queryFicheros->get();
            
            $queryFicheros->take($length);
            $queryFicheros->skip($start);
            
            $ficheros = $queryFicheros->get();
            
            $array_result = array();
            
            $solo_imagenes = FALSE;
            
            foreach($ficheros as $fic)
            {
                    $arr_fic = array(
                        ($fic->esImagen()) ?  "<img src='"  . $fic->getStreamBase64($fic->getSize(50)) .  "'  width='50' />" : '-',
                         $fic->nombre,
                         ucfirst($fic->maker->first_name . ' ' .$fic->maker->last_name),
                         $fic->created_at->format('d/m/Y'),
                        '<a href="' . \URL::to('admin/' . static::$moduleSlug. '/asociar_fichero/' . $fic->id) . '?from=' . \Input::get('from') . '&multiple=' . \Input::get('multiple') .  '&categoria=' . \Input::get('categoria') . '&originUrl=' . $originUrl .  '" class="btn btn-xs btn-primary">Seleccionar</a>'
                    );
                    $array_result[]=$arr_fic;
            }
            
            $array_json = array();
            
            $array_json['data'] = $array_result;
            $array_json['draw'] = $page;
            $array_json['recordsTotal'] = count($ficherosTodos);
            $array_json['recordsFiltered'] = count($ficherosTodos);
            
            return json_encode($array_json);
        }                 
        
}
