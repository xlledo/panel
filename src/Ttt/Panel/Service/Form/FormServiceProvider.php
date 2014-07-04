<?php namespace Ttt\Panel\Service\Form;

use Illuminate\Support\ServiceProvider;
use Ttt\Panel\Service\Form\Modulo\ModuloForm;
use Ttt\Panel\Service\Form\Modulo\ModuloFormLaravelValidator;

class FormServiceProvider extends ServiceProvider {

    /**
     * Register the binding
     *
     * @return void
     */
    public function register()
    {
        $app = $this->app;

        $app->bind('Ttt\Panel\Service\Form\Modulo\ModuloForm', function($app)
        {
            return new ModuloForm(
                new ModuloFormLaravelValidator( $app['validator'] ),
                $app->make('Ttt\Panel\Repo\Modulo\ModuloInterface')
            );
        });
    }

}
