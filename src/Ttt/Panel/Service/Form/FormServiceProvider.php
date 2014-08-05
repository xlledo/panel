<?php namespace Ttt\Panel\Service\Form;

namespace Ttt\Panel\Service\Form;

use Illuminate\Support\ServiceProvider;

use Ttt\Panel\Service\Form\Modulo\ModuloForm;
use Ttt\Panel\Service\Form\Modulo\ModuloFormLaravelValidator;

use Ttt\Panel\Service\Form\Variablesglobales\VariablesglobalesForm;
use Ttt\Panel\Service\Form\Variablesglobales\VariablesglobalesFormLaravelValidator;

use Ttt\Panel\Service\Form\Usuario\UsuarioForm;
use Ttt\Panel\Service\Form\Usuario\UsuarioFormLaravelValidator;

use Ttt\Panel\Service\Form\Traducciones\TraduccionesForm;
use Ttt\Panel\Service\Form\Traducciones\TraduccionesFormLaravelValidator;

use Ttt\Panel\Service\Form\Categoria\CategoriaForm;
use Ttt\Panel\Service\Form\Categoria\CategoriaFormLaravelValidator;

class FormServiceProvider extends ServiceProvider {

    /**
     * Register the binding
     *
     * @return void
     */
    public function register()
    {
        $app = $this->app;

        $app->bind('Ttt\Panel\Service\Form\Traducciones\TraduccionesForm', function($app)     
        {
            return new TraduccionesForm(
                   new TraduccionesFormLaravelValidator( $app['validator'] ), 
                    $app->make('Ttt\Panel\Repo\Traducciones\TraduccionesInterface')
           );
        });        
        
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

        $app->bind('Ttt\Panel\Service\Form\Usuario\UsuarioForm', function($app)
        {
            return new UsuarioForm(
                new UsuarioFormLaravelValidator( $app['validator'] ),
                $app->make('Ttt\Panel\Repo\Usuario\UsuarioInterface')
            );
        });

        $app->bind('Ttt\Panel\Service\Form\Categoria\CategoriaForm', function($app)
        {
            return new CategoriaForm(
                new CategoriaFormLaravelValidator( $app['validator'] ),
                $app->make('Ttt\Panel\Repo\Categoria\CategoriaInterface')
            );
        });


    }

}
