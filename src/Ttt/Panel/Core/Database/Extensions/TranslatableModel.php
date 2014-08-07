<?php
namespace Ttt\Panel\Core\Database\Extensions;

abstract class TranslatableModel extends \Eloquent{

	public static $table_i18n     = 'categorias_traducibles_i18n';

	protected $modelI18n    = 'CategoriatraducibleI18n';

	//Atributos Traducibles
	public $atributosTraducibles = array('nombre');


	/**
	* Recoge un atributo del modelo propio o de la traducción
	* @return mixed
	*/
	public function getAttribute($key, $idioma = null)
	{
		if ($this->isTranslatableAttribute($key))
		{
			$traduccion = $this->traduccion($idioma);

			if(! $traduccion)
			{
				return 'No existe la traducción [' . $key . '] en el idioma';
			}

			return $traduccion->$key;
		}
		return parent::getAttribute($key);
	}

	/**
	* Recoge una traducción concreta del elemento
	* @param string $idioma
	* @return \Ttt\Panel\Repo\Categoriatraducible\Categoria|FALSE
	*/
	public function traduccion($idioma = null)
	{
		$idioma = is_null($idioma) ? \App::make('Ttt\Panel\Repo\Idioma\IdiomaInterface')->idiomaPrincipal()->codigo_iso_2 : $idioma;
		foreach($this->traducciones as $traduccion)
		{
			if($traduccion->idioma == $idioma)
			{
				return $traduccion;
			}
		}

		return FALSE;
	}

	/**
	* Comprueba si un atributo es traducible
	* @param string $key
	* @return bool
	*/
	public function isTranslatableAttribute($key)
	{
		return in_array($key, $this->atributosTraducibles);
	}
}
