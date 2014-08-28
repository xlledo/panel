<?php

namespace Ttt\Panel\Repo\Paginas;

use Illuminate\Validation\Factory as Validator;
use Illuminate\Support\Facades\File;

class Pagina extends \Eloquent{

        protected $table            = 'paginas';
        public static $table_i18n     = 'paginas_i18n';

        protected $modelI18n    = 'PaginaI18n';

        //Atributos
	   protected $fillable = array('creado_por',
                                    'actualizado_por'
                                    );

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
     * @return PaginasI18n
    */

        public function traducciones()
        {
                return $this->hasMany('Ttt\Panel\Repo\Paginas\PaginasI18n', 'item_id');
        }    

}