<?php

namespace Ttt\Panel\Repo\Fichero\Extensions;

trait FicheroStandardControllerTrait {
    
    
    public function asociarFichero($id = null)
        {
            
            //-- Recuperamos el fichero
            if(     $fichero = \Ttt\Panel\Repo\Fichero\Fichero::find($id) 
                    && $item = call_user_func($this->_configAdjuntos['eloquentItem'] . '::find',\Input::get('from'))){

                //-- Creamos la relacion
                $item->ficheros()->attach($id);
                
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
                return \Redirect::to('admin/'. static::$moduleSlug . '/ver/' . \Input::get('from').'#ficheros');
        }
        
        
    /**
         * 
         * Elimina la asociación entre Item y Fichero
         * 
         * @param type $id
         * @return type
         */

    public function desasociarFichero($id = null)
        {
            //-- Recuperamos el fichero
            if(     $fichero = \Ttt\Panel\Repo\Fichero\Fichero::find($id)
                    && $item = call_user_func($this->_configAdjuntos['eloquentItem'] . '::find',\Input::get('from')) ){
                
                $item->ficheros()->detach($id);
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
            
            return \Redirect::to('admin/' . static::$moduleSlug . '/ver/' . $item->id . '#ficheros');
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
                        
                        $pivot = call_user_func($this->_configAdjuntos['eloquentPivot']. '::find', $id);
                        
                        //-- Si hay elemento Pivot, es por que la relación ya existe
                        if($pivot){
                        
                                //-- Obtenemos la página
                                
                                $item   = $pivot->{$this->_configAdjuntos['relation']}->first();

                                //-- Solo cuando actualizamos guardamos los campos directamente 
                                if( $id ){
                                        if($this->validarCamposEspecificos()->passes())
                                        {
                                            $pivot_id = $id;
                                            
                                            $ficheroPivot = call_user_func($this->_configAdjuntos['eloquentPivot'] . '::find', $pivot_id);
                                            
                                            //Si cambiamos la relación, creamos una nueva
                                            if($pivot->fichero_id != \Input::get('fichero_id')){
                                                //Borramos la relacion y creamos uno nuevo
                                                $ficheroPivot->delete();
                                                $r = $item->ficheros()->attach(\Input::get('fichero_id'), $datosEspecificos);
                                                return \Input::get('fichero_id');
                                            }else{

                                                $ficheroPivot->titulo      = $datosEspecificos['titulo'];
                                                $ficheroPivot->alt         = $datosEspecificos['alt'];
                                                $ficheroPivot->enlace      = $datosEspecificos['enlace'];
                                                $ficheroPivot->descripcion = $datosEspecificos['descripcion'];
                                                $ficheroPivot->idioma      = $datosEspecificos['idioma'];
                                                
                                                $ficheroPivot->save();
                                                
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
                                    $item->ficheros()->attach($id, $datosEspecificos);
                                    return TRUE;
                            }else{
                                return FALSE; // Igual hay que mandar una excepcion
                            }
                    }else{ //-- Si no la hay es un elemento nuevo
                        
                        
                        $item_id = \Input::get('from_id');
                        $datosEspecificos = $this->obtenerCamposEspecificos(NULL, $item_id, NULL, TRUE);
                        
                        //¿Validacion? 
                        
                        unset($datosEspecificos['nombre']);
                        
                        $item = call_user_func($this->_configAdjuntos['eloquentItem'].'::find', $item_id);
                        $item->ficheros()->attach($fichero_id, $datosEspecificos);
                                        
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

    public function obtenerCamposEspecificos( $ficheroId = null, $itemId = null, $pivot_id = null, $enviarAVista = FALSE ) {
        
        try{
                            
                $item          = call_user_func($this->_configAdjuntos['eloquentItem'] . '::find', $itemId);
                $ficheros      = $item->ficheros()->getResults();
                $ficherosPivot = $item->ficheros()
                                      ->where($this->_configAdjuntos['pivotTable'] . '.id', $pivot_id)
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
        
}