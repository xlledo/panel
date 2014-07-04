<?php namespace Ttt\Panel\Facades;

use Illuminate\Support\Facades\Facade;

class Panel extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'panel';
	}

}
