<?php
namespace Ttt\Panel\Repo\Idioma;

class Idioma extends \Eloquent{

	protected $fillable = array('principal', 'visible', 'nombre', 'codigo_iso_2', 'codigo_iso_3');

	public $validator = null;
}
