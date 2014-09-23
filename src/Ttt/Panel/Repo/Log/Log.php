<?php
namespace Ttt\Panel\Repo\Log;

class Log extends \Eloquent{

	protected $fillable = array('class', 'keyName', 'keyVal', 'texto');

	public $validator = null;

	public function usuario()
	{
		return $this->belongsTo('\Ttt\Panel\Repo\Usuario\User', 'usuario', 'id');
	}
}
