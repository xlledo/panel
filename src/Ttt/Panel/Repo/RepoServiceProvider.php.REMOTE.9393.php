<?php
namespace Ttt\Panel\Repo;

use Ttt\Panel\Repo\Modulo\Modulo;
use Ttt\Panel\Repo\Modulo\EloquentModulo;

use Ttt\Panel\Repo\Variablesglobales\Variablesglobales;
use Ttt\Panel\Repo\Variablesglobales\EloquentVariablesglobales;

use Ttt\Panel\Repo\Grupo\SentryGrupo;

use Ttt\Panel\Repo\Usuario\SentryUsuario;


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

        $this->app->bind('Ttt\Panel\Repo\Grupo\GrupoInterface', function($app)
        {
            return new SentryGrupo();
        });

        $this->app->bind('Ttt\Panel\Repo\Usuario\UsuarioInterface', function($app)
        {
            return new SentryUsuario($app['sentry.hasher'], 'Ttt\Panel\Repo\Usuario\User');
        });
    }
}
