<?php
namespace Ttt\Panel\Repo;

use Ttt\Panel\Repo\Modulo\Modulo;
use Ttt\Panel\Repo\Modulo\EloquentModulo;

use Ttt\Panel\Repo\Variablesglobales\Variablesglobales;
use Ttt\Panel\Repo\Variablesglobales\EloquentVariablesglobales;


use Illuminate\Support\ServiceProvider;

class RepoServiceProvider extends ServiceProvider{

    /**
    * Register the bindingt
    * @return void
    */
    public function register()
    {

        $this->app->bind('Ttt\Panel\Repo\Modulo\ModuloInterface', function($app)
        {
            return new EloquentModulo(
                new Modulo
            );
        });

        $this->app->bind('Ttt\Panel\Repo\Variablesglobales\VariablesglobalesInterface', function($app)
        {
            return new EloquentVariablesglobales(
                new Variablesglobales
            );
        });
    }
}
