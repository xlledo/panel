<?php
namespace Ttt\Panel\Repo\Variablesglobales;

use Illuminate\Validation\Factory as Validator;



class Variablesglobales extends \Eloquent{

        use \Ttt\Panel\Repo\Revisiones\RevisionTrait;
    
	protected $fillable = array('creado_por', 'actualizado_por', 'clave', 'valor');

	public $validator = null;
        
        protected $camposVersionables = array('clave', 'valor'); //Campos versionables
        
        protected $controlDeVersiones = TRUE; //Activa o desactiva el control de versiones

        
	public function maker()
	{
		return $this->belongsTo('Cartalyst\Sentry\Users\Eloquent\User', 'creado_por');
	}

	public function updater()
	{
		return $this->belongsTo('Cartalyst\Sentry\Users\Eloquent\User', 'actualizado_por');
	}
        
        public function versiones()
        {
            return $this->morphMany('Ttt\Panel\Repo\Revisiones\Revision','revisionable');
        }

     
}
