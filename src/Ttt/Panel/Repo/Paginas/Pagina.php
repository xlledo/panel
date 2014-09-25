<?php

namespace Ttt\Panel\Repo\Paginas;

use Illuminate\Validation\Factory as Validator;
use Illuminate\Support\Facades\File;

class Pagina extends \Eloquent{

        protected $table             = 'paginas';
        public static $table_i18n    = 'paginas_i18n';

        protected $modelI18n    = 'PaginaI18n';
        
        protected $tablaFicheros = 'paginas_ficheros';

        //Atributos
	   protected $fillable = array('creado_por',
                                       'actualizado_por'
                                      );

        //Atributos Traducibles
        public static $atributosTraducibles = array('titulo', 'texto');

	public $validator = null;

                            /*
                            CONTROL DE VERSIONES
                            No aplicamos por el moment control de Versiones
                            Cuando el módulo este acabado, haremos la implementación
                            protected $camposVersionables = array('clave', 'valor'); //Campos versionables
                            protected $controlDeVersiones = TRUE; //Activa o desactiva el control de versiones
                            */

        
        //Many to many para ficheros
        public function ficheros()
        {
            return $this->belongsToMany('Ttt\Panel\Repo\Fichero\Fichero', 'paginas_ficheros', 'pagina_id', 'fichero_id')
                                                            ->withPivot('id', 'titulo', 'alt', 'enlace', 'descripcion');
        }
        
	public function maker()
	{
		return $this->belongsTo('Cartalyst\Sentry\Users\Eloquent\User', 'creado_por');
	}

	public function updater()
	{
		return $this->belongsTo('Cartalyst\Sentry\Users\Eloquent\User', 'actualizado_por');
	}

    /**
     * Devuelve todas las traducciones de un item
     *
     * @return PaginasI18n
    */

        public function traducciones()
        {
                return $this->hasMany('Ttt\Panel\Repo\Paginas\PaginaI18n', 'item_id');
        }    


        /**
        * Devuelve una traduccion de un item, desde su Item principal
        *
        * @param  String $idioma
        * @return
        */

        public function traduccion( $idioma = null )
        {
            if( $idioma ){
                
                $traduccion = \DB::table(self::$table_i18n)
                                        ->where('item_id', $this->id)
                                        ->where('idioma', $idioma)
                                        ->first();
                return $traduccion;
                
            }else{
                $idioma = \App::make('Ttt\Panel\Repo\Idioma\IdiomaInterface')->idiomaPrincipal()->codigo_iso_2;

                //$traducciones = $this->traducciones()->getResults(); 
                
                foreach($this->traducciones()->getResults() as $traduccion){
                    
                    if($traduccion->idioma == $idioma){
                        return $traduccion;
                    }
                    
                }
            }
        }
        
        
        /**
         * Coje un atributo bien de la pagina master, o bien del idioma principal
         */
        
        public function getAttribute($key, $idioma = null)
        {
            if(in_array($key, self::$atributosTraducibles)){
                $traduccion = $this->traduccion($idioma);
                
                if( !$traduccion ){
                    return 'No existe la traduccion[' . $key . '] en el idioma';
                }
                
                return $traduccion->$key;
                
            }
            
            return parent::getAttribute($key);
            
        }
        
        
        
        
}