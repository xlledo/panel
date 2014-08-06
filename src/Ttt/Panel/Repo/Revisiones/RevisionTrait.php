<?php

namespace Ttt\Panel\Repo\Revisiones;

trait RevisionTrait {
    
    private     $datosPreRevision = array();
    protected   $MAX_REVISIONES = 5;
    
    /**
    * Create the event listeners for the saving and saved events
    * This lets us save revisions whenever a save is made, no matter the
    * http method.
    *
    */
    public static function boot()
    {
        parent::boot();

        static::saving(function($model)
        {
            $model->preSave();
        });

        static::saved(function($model)
        {
            $model->postSave();
        });


    }
   
    
    public function historialDeRevisiones()
    {
        return $this->morphMany('\Ttt\Panel\Repo\Revisiones\Revision','versionable');
    }
    
    /**
     * 
     * Prepara el elemento para el guardado de la version, almacenando el valor anterior
     * 
     * 
     */
    
    public function preSave()
    {
        if (!isset($this->controlDeVersiones) || $this->controlDeVersiones) 
        {
            $campos_versiones = array();
            
      
            
            //-- Guardamos todos los campos versionables
            foreach ($this->camposVersionables as $campo)
            {
                
            //-- Obtenemos última revision
            $version_anterior = Revision::where('revisionable_type','=',  get_class())
                                    ->where('clave',$campo) 
                                    ->take(1)
                                    ->orderBy('created_at','desc')
                                    ->get();
            
            $valor_viejo = ($version_anterior && $version_anterior->first()) 
                                                ? $version_anterior->first()->valor_nuevo : '';                
                
                $version_actual = array(
                    'clave' => $campo,
                    'revisionable_id' => $this->id,
                    'valor_viejo' => $valor_viejo
                );
                
                $campos_versiones[] = $version_actual;
            }
            $this->datosPreRevision= $campos_versiones;
        }
    }
    
    
    /**
     * Guardado de la Version, este método es invocado cuando ya tenemos ID
     * del elemento 
     * 
     */
    
    public function postSave()
    {
        if(!isset($this->controlDeVersiones) || $this->controlDeVersiones) 
        {
            $revisiones = array();
            
            foreach($this->datosPreRevision as $pre_revision)
            {
                // Solo guardamos las revisiones que hayan modificado el valor
                if($pre_revision['valor_viejo'] != $this->{$pre_revision['clave']})
                {
                        $revisiones[] = array(
                            'revisionable_type' => get_class($this),
                            'revisionable_id'   => $this->id,
                            'clave'             => $pre_revision['clave'],
                            'valor_viejo'       => $pre_revision['valor_viejo'],
                            'valor_nuevo'       => $this->{$pre_revision['clave']},
                            'created_at'        => new \DateTime(),
                            'updated_at'        => new \DateTime(),
                            'modificado_por'    => \Sentry::getUser()['id']
                        );
                }
            }
            
            if(count($revisiones)>0) //Si hay revisiones las insertamos
            {
                $revision  = new Revision();
                \DB::table($revision->getTable())->insert($revisiones);
            }
            
            // Buscamos revisiones anteriores, eliminamos las nateriores a MAX_REVISIONES

            foreach($revisiones as $rev)
            {
                
                //die(var_dump($rev['clave']));
                $revs_anteriores = Revision::where('revisionable_type', '=', get_class())
                                             ->where('clave', $rev['clave'])
                                             ->orderBy('created_at','desc')
                                             ->take(1)
                                             ->skip($this->MAX_REVISIONES)
                                             ->get();
                
                //Si hay alguna revision anterior, la borramos
                if($revs_anteriores->first())
                {
                    $revs_anteriores->first()->delete();
                }
            }

        }
    }
    
    
    /**
     * Devuelve las versiones de un modelo
     * 
     * 
     * @param String $clave Clave del campo
     * @return String Valor
     */
            
        public function versionesByClave($clave = null)
        {
            if($clave)
            {   $v = new \Ttt\Panel\Repo\Revisiones\Revision();
                $versiones = \Illuminate\Support\Facades\DB::table($v->getTable())
                                                                    ->where('revisionable_type', get_class())
                                                                    ->where('revisionable_id', $this->id)
                                                                    ->where('clave', $clave)
                                                                    ->orderBy('created_at','desc')
                                                                    ->get();
                
                return $versiones;
            }
        }
    
    
}