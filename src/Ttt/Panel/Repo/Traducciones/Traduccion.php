<?php

namespace Ttt\Panel\Repo\Traducciones;

use Illuminate\Validation\Factory as Validator;

class Traduccion extends \Eloquent{
        
        protected $table            = 'traducciones';
        public static $table_i18n     = 'traducciones_i18n';
        
        protected $modelI18n    = 'Traduccion_i18n';
    
        //Atributos
	protected $fillable = array('creado_por',
                                    'actualizado_por',
                                    'clave');

        //Atributos Traducibles
        public static $atributosTraducibles = array('texto');
        
	public $validator = null;
       
        /* 
                CONTROL DE VERSIONES
                No aplicamos por el moment control de Versiones
                Cuando el módulo este acabado, haremos la implementación
                protected $camposVersionables = array('clave', 'valor'); //Campos versionables
                protected $controlDeVersiones = TRUE; //Activa o desactiva el control de versiones
                */
        
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
        * @return Traduccion_i18n
        */
        
        public function traducciones()
        {
                return $this->hasMany('Ttt\Panel\Repo\Traducciones\Traduccion_i18n', 'item_id');
        }
        
        
        /**
        * Devuelve una traduccion de un item, desde su Item principal
        * 
        * @param  String $idioma
        * @return 
        */
        
        public function traduccion($idioma)
        {
                $traduccion = \DB::table(self::$table_i18n)
                                        ->where('item_id', $this->id)
                                        ->where('idioma', $idioma)
                                        ->first();
                return $traduccion;
        }
        
        /**
         * Devuelve una traducción completa con el join de campos 
         * 
         * @param Int $id Id del elemento master
         * @param String $idioma Idioma
         */
        
        public static function getTraduccion($id, $idioma = null)
        {
            
            $item      = self::find($id)->toArray();
            
            $item_i18n = \DB::table(self::$table_i18n)
                                ->where('item_id',$id)
                                ->where('idioma', $idioma)
                                ->take(1)
                                ->get();
            
            foreach(self::$atributosTraducibles as $attr)
            {
                $item[$attr] = $item_i18n[0]->{$attr};
            }
            
            return $item;
            
        }
        
}
