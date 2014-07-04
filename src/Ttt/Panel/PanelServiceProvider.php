<?php namespace Ttt\Panel;

use Illuminate\Support\ServiceProvider;

class PanelServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('ttt/panel');

		include __DIR__ . '/../../routes.php';
		//include __DIR__ . '/../../filters.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerAdmin();
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

	/**
	* Takes all the components of Panel and glues them
	* together to create Admin.
	*
	* @return void
	*/
	protected function registerAdmin()
	{
		$this->app['panel'] = $this->app->share(function($app)
		{
			return new Panel();
		});
	}

}
