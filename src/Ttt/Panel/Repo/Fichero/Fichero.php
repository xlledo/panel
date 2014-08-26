<?php
namespace Ttt\Panel\Repo\Fichero;

class Fichero extends \Eloquent{

        protected $table = 'ficheros';
    
	protected $fillable = array('nombre',
                                    'fichero', 
                                    'titulo_defecto',
                                    'alt_defecto',
                                    'descripcion_defecto',
                                    'enlace_defecto',
                                    'tipo',
                                    'ruta',
                                    'mime',
                                    'peso',
                                    'dimensiones',
                                    'creado_por',
                                    'actualizado_por'   
                                    );

	public $validator = null;
        
	public function maker()
	{
		return $this->belongsTo('Cartalyst\Sentry\Users\Eloquent\User', 'creado_por');
	}

	public function updater()
	{
		return $this->belongsTo('Cartalyst\Sentry\Users\Eloquent\User', 'actualizado_por');
	}                
}