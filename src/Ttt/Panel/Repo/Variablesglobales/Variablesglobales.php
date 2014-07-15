<?php
namespace Ttt\Panel\Repo\Variablesglobales;

use Illuminate\Validation\Factory as Validator;

class Variablesglobales extends \Eloquent{

	protected $fillable = array('creado_por', 'actualizado_por', 'clave', 'valor');

	public $validator = null;
        
        public static function boot()
        {
            parent::boot();
            
//            //Asignamos eventos para guardar y actualizar
//            Variablesglobales::creating(function($item)
//            {
//                    $item->creado_por = \Sentry::getUser()['id'];
//                    $item->actualizado_por = \Sentry::getUser()['id'];
//            });
//            
//            Variablesglobales::updating(function($item)
//            {
//                    $item->actualizado_por = \Sentry::getUser()['id'];
//            });
        }
        
	public function maker()
	{
		return $this->belongsTo('Cartalyst\Sentry\Users\Eloquent\User', 'creado_por');
	}

	public function updater()
	{
		return $this->belongsTo('Cartalyst\Sentry\Users\Eloquent\User', 'actualizado_por');
	}
     
}
