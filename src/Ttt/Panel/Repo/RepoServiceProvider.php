<?php
namespace Ttt\Panel\Repo;

use Ttt\Panel\Repo\Modulo\Modulo;
use Ttt\Panel\Repo\Modulo\EloquentModulo;

use Ttt\Panel\Repo\Variablesglobales\Variablesglobales;
use Ttt\Panel\Repo\Variablesglobales\EloquentVariablesglobales;

use Ttt\Panel\Repo\Traducciones\Traduccion;
use Ttt\Panel\Repo\Traducciones\EloquentTraducciones;
use Ttt\Panel\Repo\Traducciones\TraduccionI18n;

use Ttt\Panel\Repo\Revisiones\Revision;
use Ttt\Panel\Repo\Grupo\SentryGrupo;

use Ttt\Panel\Repo\Usuario\SentryUsuario;

use Ttt\Panel\Repo\Categoria\Categoria;
use Ttt\Panel\Repo\Categoria\EloquentCategoria;

use Ttt\Panel\Repo\Idioma\Idioma;
use Ttt\Panel\Repo\Idioma\EloquentIdioma;

use Ttt\Panel\Repo\Categoriatraducible\Categoria as CategoriaTraducible;
use Ttt\Panel\Repo\Categoriatraducible\EloquentCategoria as EloquentCategoriaTraducible;
use Ttt\Panel\Repo\Categoriatraducible\CategoriaI18n as CategoriaTraducibleI18n;


use Ttt\Panel\Repo\Fichero\Fichero;
use Ttt\Panel\Repo\Fichero\EloquentFichero;

//use Ttt\Panel\Repo\Paginas\Pagina;
//use Ttt\Panel\Repo\Paginas\PaginaI18n;
//use Ttt\Panel\Repo\Paginas\EloquentPaginas;

use Ttt\Panel\Repo\Menu\Menu;
use Ttt\Panel\Repo\Menu\EloquentMenu;

use Ttt\Panel\Repo\Log\Log;
use Ttt\Panel\Repo\Log\EloquentLog;

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
                   new Traduccion, new TraduccionI18n
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

        $this->app->bind('Ttt\Panel\Repo\Fichero\FicheroInterface', function($app)
        {
            return new EloquentFichero(
                    new Fichero
            );
        });

//        $this->app->bind('Ttt\Panel\Repo\Paginas\PaginasInterface', function($app)
//        {
//            return new EloquentPaginas(
//                    new Pagina, new PaginaI18n
//                    );
//        });

        $this->app->bind('Ttt\Panel\Repo\Categoriatraducible\CategoriaInterface', function($app)
        {
            return new EloquentCategoriaTraducible(
                new CategoriaTraducible, new CategoriaTraducibleI18n
            );
        });

        $this->app->bind('Ttt\Panel\Repo\Menu\MenuInterface', function($app)
        {
            return new EloquentMenu(
                new Menu
            );
        });
        
        $this->app->bind('Ttt\Panel\Repo\Log\LogInterface', function($app)
        {
            return new EloquentLog(
                new Log
            );
        });
    }
}
