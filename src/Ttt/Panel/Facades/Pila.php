<?php namespace Ttt\Panel\Facades;

use Illuminate\Support\Facades\Facade;

class Pila extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'pila';
	}

}
