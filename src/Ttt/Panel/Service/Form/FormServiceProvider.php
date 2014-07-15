<?php namespace Ttt\Panel\Service\Form;

namespace Ttt\Panel\Service\Form;

use Illuminate\Support\ServiceProvider;

use Ttt\Panel\Service\Form\Modulo\ModuloForm;
use Ttt\Panel\Service\Form\Modulo\ModuloFormLaravelValidator;

use Ttt\Panel\Service\Form\Variablesglobales\VariablesglobalesForm;
use Ttt\Panel\Service\Form\Variablesglobales\VariablesglobalesFormLaravelValidator;

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
        
        $app->bind('Ttt\Panel\Service\Form\Variablesglobales\VariablesglobalesForm', function($app)
        {
            return new VariablesglobalesForm(
                new VariablesglobalesFormLaravelValidator( $app['validator'] ),
                $app->make('Ttt\Panel\Repo\Variablesglobales\VariablesglobalesInterface')
            );
        });        
        
        
    }

}
