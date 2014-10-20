<?php
namespace Ttt\Panel\Repo\Usuario;

interface UsuarioInterface extends \Cartalyst\Sentry\Users\ProviderInterface{

    /**
    * Devuelve el listado de módulos y el total de los mismos
    * @param $page int
    * @param $limit int
    * @param $params array los parámetros para los filtros y la ordenación
    * @return object Object con Items y totalItems para la paginación
    */
    public function byPage($page = 1, $limit = 10, $params = array());

    /**
    * Actualiza un grupo
    * @param $group array
    * @return boolean
    */
    public function update(\Cartalyst\Sentry\Users\UserInterface $user);

    public function newQuery();
}
