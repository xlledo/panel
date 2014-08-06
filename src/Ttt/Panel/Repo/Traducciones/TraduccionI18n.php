<?php
namespace Ttt\Panel\Repo\Traducciones;

use Illuminate\Validation\Factory as Validator;

class TraduccionI18n extends \Eloquent{

        protected $table = 'traducciones_i18n';

        //Atributos
	protected $fillable = array('item_id',
                                    'idioma',
                                    'texto');

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

        public function traduccion()
        {
                return $this->belongsTo('Ttt\Panel\Repo\Traducciones\Traduccion', 'id');
        }

}
