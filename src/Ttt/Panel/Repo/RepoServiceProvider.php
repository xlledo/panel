<?php
namespace Ttt\Panel\Repo;

use Ttt\Panel\Repo\Modulo\Modulo;
use Ttt\Panel\Repo\Modulo\EloquentModulo;

use Ttt\Panel\Repo\Variablesglobales\Variablesglobales;
use Ttt\Panel\Repo\Variablesglobales\EloquentVariablesglobales;

use Ttt\Panel\Repo\Traducciones\Traduccion;
use Ttt\Panel\Repo\Traducciones\EloquentTraducciones;
use Ttt\Panel\Repo\Traducciones\Traduccion_i18n;

use Ttt\Panel\Repo\Revisiones\Revision;
use Ttt\Panel\Repo\Grupo\SentryGrupo;

use Ttt\Panel\Repo\Usuario\SentryUsuario;

use Ttt\Panel\Repo\Categoria\Categoria;
use Ttt\Panel\Repo\Categoria\EloquentCategoria;

use Ttt\Panel\Repo\Idioma\Idioma;
use Ttt\Panel\Repo\Idioma\EloquentIdioma;

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
        
        $this->app->bind('Ttt\Panel\Repo\Revisiones\Revision', function($app)
        {
            return new Revision();
        });
          
        $this->app->bind('Ttt\Panel\Repo\Traducciones\TraduccionesInterface', function($app)
        {
           return new EloquentTraducciones(
                   new Traduccion, new Traduccion_i18n
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

        $this->app->bind('Ttt\Panel\Repo\Categoria\CategoriaInterface', function($app)
        {
            return new EloquentCategoria(
                new Categoria
            );
        });

        $this->app->bind('Ttt\Panel\Repo\Idioma\IdiomaInterface', function($app)
        {
            return new EloquentIdioma(
                new Idioma
            );
        });
    }
}
