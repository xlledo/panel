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
         * Elimina la asociación entre fichero y pagina
         * 
         * @param type $id
         * @return type
         */
        
    public function desasociarFichero($id = null)
        {
            //-- Recuperamos el fichero
            if(     $fichero = Repo\Fichero\Fichero::find($id) 
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

                                            $ficherosPivot = $item
                                                                ->ficheros()
                                                                ->where($this->_configAdjuntos['itemTable'] . '_ficheros.id', $pivot_id)
                                                                ->get();

                                            //Si cambiamos la relación, creamos una nueva
                                            
                                            if($pivot->fichero_id != \Input::get('fichero_id')){
                                                //Borramos la relacion y creamos uno nuevo
                                                $ficherosPivot->first()->pivot->delete();
                                                $r = $item->ficheros()->attach(\Input::get('fichero_id'), $datosEspecificos);
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
}