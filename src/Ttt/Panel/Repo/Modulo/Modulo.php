<?php
namespace Ttt\Panel\Repo\Modulo;

class Modulo extends \Ttt\Panel\Core\Database\Extensions\Model{

	protected $fillable = array('creado_por', 'actualizado_por', 'slug', 'visible', 'nombre');

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
