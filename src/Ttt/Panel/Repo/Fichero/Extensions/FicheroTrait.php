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
                $nombre_fichero = \Illuminate\Support\Str::slug($fichero->getClientOriginalName(),'-') . '.' . $fichero->getClientOriginalExtension();
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
                    'nombre'  => \Input::get('nombre'),
                    'fichero' => $nombre_fichero,
                    'usuario' => \Sentry::getUser()['id'],
                    'ruta'    => $path_completo,
                    'mime'    => $mime,
                    'titulo_defecto'        => \Input::get('titulo_defecto'),
                    'alt_defecto'           => \Input::get('alt_defecto'),
                    'enlace_defecto'        => \Input::get('enlace_defecto'),
                    'descripcion_defecto'   => \Input::get('descripcion_defecto'),
                    'fichero_original'      => $fichero //Pasamos el fichero para propositos de validacion
                );
            
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
                    $this->guardarCamposEspecificos($ficheroId);
            }
                
            return \Redirect::action(get_class() . '@index');

        } catch (\Ttt\Panel\Exception\TttException $ex) {
            
            $message = $ex->getMessage();
        }
        
        \Session::flash('messages', array(
            array(
                'class' => 'alert-danger',
                'msg'   => $message
            )
        ));
        
        return \Redirect::action(get_class() . '@nuevoFichero')
                                    ->withInput()
                                    ->withErrors($this->ficheroForm->errors());
        
    }
    
    function verFichero($id = null)
    {
            $message = '';
        
        if( $fichero = $this->fichero->byId($id)){

            $fichero  = $this->fichero->byId($id);
            $fichero->nombre = ! is_null(\Input::old('nombre')) ?: $fichero->nombre;
            
            $fichero->titulo_defecto        = ! is_null(\Input::old('titulo_defecto')) ?: $fichero->titulo_defecto;
            $fichero->alt_defecto           = ! is_null(\Input::old('alt_defecto')) ?: $fichero->alt_defecto;
            $fichero->descripcion_defecto   = ! is_null(\Input::old('descripcion_defecto')) ?: $fichero->descripcion_defecto;
            $fichero->enlace_defecto        = ! is_null(\Input::old('enlace_defecto')) ?: $fichero->enlace_defecto;
            
            $item_id    = \Input::get('item_id');
            $pivot_id   = \Input::get('pivot_id');    
 
            $this->_fichero_nombre = $fichero->nombre; 
            
            //TODO: Comprobar entrada item_id / pivot_id
            try{
                $this->obtenerCamposEspecificos($id, $item_id, $pivot_id, TRUE);
                
            }catch(\Ttt\Panel\Exception\TttException $e){
                $message = 'Existen errores de validación';
                
            }
            
            \View::share('title', 'Edicion del fichero ' . $fichero->nombre);
            \View::share('action_fichero', 'edit');
            
            \View::share('from_url', \Input::get('from_url')?:'');
            \View::share('item_id', \Input::get('item_id')?:'');
            \View::share('pivot_id', \Input::get('pivot_id')?:'');
            
            \View::share('item', $fichero);
            
            return \View::make('panel::' . $this->_views_dir . '.ficheros._editar');
                                
        
        }
    }
    
    function actualizarFichero()
    {
        $message = 'Fichero actualizado correctamente';
        $actualizacionOK = FALSE;
        
        try{
            $fichero = $this->fichero->byId(\Input::get('id'));
            $this->_fichero_nombre = $fichero->nombre;
            
            if(\Input::hasFile('fichero')){
                $fic = \Input::file('fichero');
                $fic->move($fichero->ruta, $fichero->fichero);
            }
            
            $data = array(
                'id'        => $fichero->id,
                'nombre'    => \Input::get('nombre')
            );
            
            $item_id    = \Input::get('item_id');
            $pivot_id   = \Input::get('pivot_id');  
                        
            $this->guardarCamposEspecificos(\Input::get('id'));
            
            \View::share('item_id', $item_id);
            \View::share('pivot_id', $pivot_id);
                        
            \Session::flash('messages', array(
                                array(
                                    'class' => 'alert-success',
                                    'msg'   => $message
                                )
            ));
            
            return $this->verFichero($fichero->id);
            
         }
            catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex){
                    $message = $ex->getMessage();
                    return \Redirect::action( get_class() . '@verFichero', $fichero->id);
                    } 
            catch(\Ttt\Panel\Exception\TttException $e){
                    $message = 'Existen errores de validación';

                    \Session::flash('messages', array(
                                        array(
                                            'class' => 'alert-danger',
                                            'msg'   => $message
                                        )
                    ));
                    
                    return $this->verFichero($fichero->id);
                    
                }

                \Session::flash('messages', array(
                                    array(
                                        'class' => 'alert-danger',
                                        'msg'   => $message
                                    )
                ));
            
               return \Redirect::action(get_class() . '@verFichero', $fichero->id)
                                                ->withInput()
                                                ->withErrors($this->ficheroForm->errors());
    }
    
    
        public function nuevoFichero()
        {
            $item = new \stdClass();
            $item->nombre = \Input::old('nombre') ?: '';
            
            $item->titulo_defecto   = \Input::old('titulo_defecto')?:'';
            $item->alt_defecto      = \Input::old('alt_defecto')?:'';
            $item->descripcion_defecto = \Input::old('descripcion_defecto')?:'';
            $item->enlace_defecto      = \Input::old('enlace_defecto')?:'';
            
            $item_id = \Input::old('from_id');
            
            $this->_fichero_nombre = $item->nombre;
            
            \View::share('title', 'Creacion de un nuevo fichero');
            
            //Hay que pasar los errores
            return \View::make('panel::' . $this->_views_dir .  '.ficheros._add')
                                    ->with('item', $item)
                                    ->with('item_id', $item_id)
                                    ->with('from_id', $item_id)
                                    ->withErrors($this->validarCamposEspecificos())
                                    ->with('from_url', (\Input::get('from_url')?: ''))
                                    ->with('action', 'create' );
        }
}