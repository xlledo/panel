<?php
namespace Ttt\Panel\Exception;

use Illuminate\Support\ServiceProvider;

class ExceptionServiceProvider extends ServiceProvider{

    /**
    * Register the bindingt
    * @return void
    */
    public function register()
    {
        $app = $this->app;

        //bind nuestro manejador de excepciones
        $app['ttt.notificable.exception'] = $app->share(function($app)
        {
            return new NotifyHandler($app['ttt.notifier']);
        });
    }

    public function boot()
    {
        $app = $this->app;

        //registramos manejador de errores en Laravel
        $app->error(function(TttNotificableException $e) use ($app)
        {
            $app['ttt.notificable.exception']->handle($e);
        });
    }
}
